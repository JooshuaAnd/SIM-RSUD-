<?php

use App\Controllers\Pendidikan\AdminDiklat;
use App\Controllers\Pendidikan\Institusi\Pengajuan;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Config as Database;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class PembayaranVerificationTest extends CIUnitTestCase
{
    private BaseConnection $paymentDb;
    private string $table;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentDb = Database::connect('tests');
        if ($this->paymentDb->DBDriver !== 'SQLite3' || $this->paymentDb->database !== ':memory:') {
            throw new LogicException('Payment tests require an isolated in-memory SQLite database.');
        }
        $this->table = $this->paymentDb->prefixTable('mahasiswa_pendidikan');
        $this->paymentDb->query("CREATE TABLE {$this->table} (
            id INTEGER PRIMARY KEY,
            institusi_id INTEGER,
            payment_status TEXT,
            alasan_penolakan TEXT,
            file_bukti_bayar TEXT,
            invoice_file TEXT,
            nominal INTEGER,
            created_at TEXT,
            updated_at TEXT
        )");
        $this->paymentDb->table('mahasiswa_pendidikan')->insert([
            'id' => 42,
            'institusi_id' => 7,
            'payment_status' => 'Menunggu Verifikasi',
            'alasan_penolakan' => null,
            'file_bukti_bayar' => 'original-proof.png',
            'invoice_file' => 'invoice.pdf',
            'nominal' => 125000,
        ]);
    }

    protected function tearDown(): void
    {
        if (isset($this->table)) {
            $this->paymentDb->query("DROP TABLE {$this->table}");
        }
        parent::tearDown();
    }

    private function verify(array $payload, int $studentId = 42): array
    {
        $request = $this->getMockBuilder(IncomingRequest::class)
            ->disableOriginalConstructor()->onlyMethods(['getJSON'])->getMock();
        $request->method('getJSON')->willReturn($payload);
        $controller = new AdminDiklat();
        $controller->initController($request, service('response')->setStatusCode(200), service('logger'));
        $response = $controller->mahasiswaVerifikasiPembayaran($studentId);
        return [$response->getStatusCode(), json_decode($response->getBody(), true)];
    }

    private function record(): array
    {
        return $this->paymentDb->table('mahasiswa_pendidikan')->where('id', 42)->get()->getRowArray();
    }

    public function testRejectsProofAndPersistsReasonWithoutChangingInvoice(): void
    {
        [$status, $payload] = $this->verify([
            'status' => 'Ditolak',
            'alasan_penolakan' => '  Nominal pada bukti belum sesuai invoice.  ',
        ]);
        $this->assertSame(200, $status);
        $this->assertTrue($payload['success']);
        $record = $this->record();
        $this->assertSame('Ditolak', $record['payment_status']);
        $this->assertSame('Nominal pada bukti belum sesuai invoice.', $record['alasan_penolakan']);
        $this->assertSame('original-proof.png', $record['file_bukti_bayar']);
        $this->assertSame('invoice.pdf', $record['invoice_file']);
        $this->assertEquals(125000, $record['nominal']);
    }

    public static function invalidReasons(): array
    {
        return [[null], [''], ['   '], [['invalid']]];
    }

    #[DataProvider('invalidReasons')]
    public function testRejectRequiresValidReason($reason): void
    {
        [$status, $payload] = $this->verify(['status' => 'Ditolak', 'alasan_penolakan' => $reason]);
        $this->assertSame(422, $status);
        $this->assertFalse($payload['success']);
        $this->assertSame('Alasan penolakan harus diisi!', $payload['message']);
        $this->assertSame('Menunggu Verifikasi', $this->record()['payment_status']);
        $this->assertNull($this->record()['alasan_penolakan']);
    }

    public static function acceptedStatuses(): array
    {
        return [['Lunas'], ['Belum Bayar'], ['Menunggu Verifikasi']];
    }

    #[DataProvider('acceptedStatuses')]
    public function testExistingStatusesStillWork(string $paymentStatus): void
    {
        [$status, $payload] = $this->verify(['status' => $paymentStatus]);
        $this->assertSame(200, $status);
        $this->assertTrue($payload['success']);
        $this->assertSame($paymentStatus, $this->record()['payment_status']);
    }

    public function testUnknownStatusDoesNotChangePayment(): void
    {
        [$status, $payload] = $this->verify(['status' => 'Invalid']);
        $this->assertSame(422, $status);
        $this->assertFalse($payload['success']);
        $this->assertSame('Menunggu Verifikasi', $this->record()['payment_status']);
    }

    public function testMissingStudentReturnsNotFound(): void
    {
        [$status, $payload] = $this->verify(['status' => 'Ditolak', 'alasan_penolakan' => 'Perbaiki bukti.'], 999);
        $this->assertSame(404, $status);
        $this->assertFalse($payload['success']);
        $this->assertSame('Menunggu Verifikasi', $this->record()['payment_status']);
    }

    public function testInstitutionCanReviseRejectedProofAndAdminCanApproveIt(): void
    {
        [, $rejected] = $this->verify(['status' => 'Ditolak', 'alasan_penolakan' => 'Bukti tidak terbaca.']);
        $this->assertTrue($rejected['success']);

        session()->set('institusi_id', 7);
        $file = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()
            ->onlyMethods(['isValid', 'hasMoved', 'getMimeType', 'getSize', 'getRandomName', 'move'])->getMock();
        $file->method('isValid')->willReturn(true);
        $file->method('hasMoved')->willReturn(false);
        $file->method('getMimeType')->willReturn('image/png');
        $file->method('getSize')->willReturn(128);
        $file->method('getRandomName')->willReturn('revised-proof.png');
        $file->expects($this->once())->method('move')
            ->with(FCPATH . 'uploads/dokumen_mahasiswa/', 'revised-proof.png')->willReturn(true);

        $request = $this->getMockBuilder(IncomingRequest::class)->disableOriginalConstructor()
            ->onlyMethods(['getPost', 'getFile'])->getMock();
        $request->method('getPost')->willReturnCallback(fn ($field) => $field === 'mahasiswa_id' ? '42' : null);
        $request->method('getFile')->with('bukti_bayar')->willReturn($file);
        $controller = new Pengajuan();
        $controller->initController($request, service('response')->setStatusCode(200), service('logger'));
        $response = $controller->submit_payment();
        $this->assertTrue(json_decode($response->getBody(), true)['success']);
        $record = $this->record();
        $this->assertSame('Menunggu Verifikasi', $record['payment_status']);
        $this->assertSame('revised-proof.png', $record['file_bukti_bayar']);
        $this->assertNull($record['alasan_penolakan']);
        $this->assertSame('invoice.pdf', $record['invoice_file']);

        [$status, $approved] = $this->verify(['status' => 'Lunas']);
        $this->assertSame(200, $status);
        $this->assertTrue($approved['success']);
        $this->assertSame('Lunas', $this->record()['payment_status']);
        $this->assertNull($this->record()['alasan_penolakan']);
        $this->assertSame('revised-proof.png', $this->record()['file_bukti_bayar']);
    }

    public function testApprovalClearsOldRejectionReason(): void
    {
        $this->paymentDb->table('mahasiswa_pendidikan')->where('id', 42)->update([
            'payment_status' => 'Ditolak', 'alasan_penolakan' => 'Alasan lama.',
        ]);
        [$status, $payload] = $this->verify(['status' => 'Lunas']);
        $this->assertSame(200, $status);
        $this->assertTrue($payload['success']);
        $this->assertSame('Lunas', $this->record()['payment_status']);
        $this->assertNull($this->record()['alasan_penolakan']);
    }
}
