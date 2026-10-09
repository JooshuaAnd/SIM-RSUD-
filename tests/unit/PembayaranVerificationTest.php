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
    private array $paymentFiles = [];
    private array $createdDirectories = [];
    private string $invoiceName;
    private string $proofName;

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
        foreach (['invoices', 'dokumen_mahasiswa', 'bukti_bayar'] as $name) {
            $directory = FCPATH . 'uploads/' . $name;
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
                $this->createdDirectories[] = $directory;
            }
        }
        $invoice = tempnam(FCPATH . 'uploads/invoices/', 'invoice_test_');
        $proof = tempnam(FCPATH . 'uploads/dokumen_mahasiswa/', 'proof_test_');
        $this->paymentFiles = [$invoice, $proof];
        $this->invoiceName = basename($invoice);
        $this->proofName = basename($proof);
        $this->paymentDb->table('mahasiswa_pendidikan')->insert([
            'id' => 42,
            'institusi_id' => 7,
            'payment_status' => 'Menunggu Verifikasi',
            'alasan_penolakan' => null,
            'file_bukti_bayar' => $this->proofName,
            'invoice_file' => $this->invoiceName,
            'nominal' => 125000,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->paymentFiles as $file) {
            unlink($file);
        }
        foreach (array_reverse($this->createdDirectories) as $directory) {
            rmdir($directory);
        }
        if (isset($this->table)) {
            $this->paymentDb->query("DROP TABLE {$this->table}");
        }
        parent::tearDown();
    }

    private function verify(array $payload, int $studentId = 42): array
    {
        if (($payload['status'] ?? '') === 'Lunas' && !array_key_exists('action', $payload)) {
            $payload['action'] = 'setujui';
        }
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

    #[DataProvider('incompleteInvoices')]
    public function testInvoiceRequiresNominalAndPdf(?string $nominal, bool $hasFile, string $message): void
    {
        $before = $this->record();
        $file = null;
        if ($hasFile) {
            $file = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()
                ->onlyMethods(['getError', 'move'])->getMock();
            $file->method('getError')->willReturn(UPLOAD_ERR_OK);
            $file->expects($this->never())->method('move');
        }
        $request = $this->getMockBuilder(IncomingRequest::class)->disableOriginalConstructor()
            ->onlyMethods(['getPost', 'getFile'])->getMock();
        $request->method('getPost')->with('nominal')->willReturn($nominal);
        $request->method('getFile')->with('invoice_file')->willReturn($file);
        $controller = new AdminDiklat();
        $controller->initController($request, service('response')->setStatusCode(200), service('logger'));
        $response = $controller->mahasiswaUploadInvoice(42);

        $this->assertSame(422, $response->getStatusCode());
        $payload = json_decode($response->getBody(), true);
        $this->assertFalse($payload['success']);
        $this->assertSame($message, $payload['message']);
        $this->assertSame($before, $this->record());
        $this->assertFileExists(FCPATH . 'uploads/invoices/' . $this->invoiceName);
    }

    public static function incompleteInvoices(): array
    {
        return [
            'both missing' => [null, false, 'Nominal dan file invoice PDF wajib diisi.'],
            'nominal only' => ['125000', false, 'File invoice PDF wajib diunggah.'],
            'file only' => ['', true, 'Nominal wajib diisi.'],
            'invalid nominal with file' => ['abc', true, 'Nominal harus berupa angka nol atau lebih.'],
        ];
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
        $this->assertSame($this->proofName, $record['file_bukti_bayar']);
        $this->assertSame($this->invoiceName, $record['invoice_file']);
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
        return [['Lunas']];
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

    public function testCannotSettleWithoutExplicitApproval(): void
    {
        [$status, $payload] = $this->verify(['status' => 'Lunas', 'action' => null]);
        $this->assertSame(422, $status);
        $this->assertFalse($payload['success']);
        $this->assertSame('Menunggu Verifikasi', $this->record()['payment_status']);
    }

    public function testCannotApproveBeforeInstitutionSubmitsProof(): void
    {
        $this->paymentDb->table('mahasiswa_pendidikan')->where('id', 42)->update(['payment_status' => 'Belum Bayar']);
        [$status, $payload] = $this->verify(['status' => 'Lunas']);
        $this->assertSame(409, $status);
        $this->assertFalse($payload['success']);
        $this->assertSame('Belum Bayar', $this->record()['payment_status']);
    }

    public function testVerificationCannotResetPaymentToUnpaid(): void
    {
        [$status] = $this->verify(['status' => 'Belum Bayar']);
        $this->assertSame(422, $status);
        $this->assertSame('Menunggu Verifikasi', $this->record()['payment_status']);
    }

    public static function missingPaymentDocuments(): array
    {
        return [
            'no invoice or proof' => ['Belum Invoice', ['invoice_file' => null, 'file_bukti_bayar' => null]],
            'no invoice' => ['Menunggu Verifikasi', ['invoice_file' => null]],
            'no proof' => ['Menunggu Verifikasi', ['file_bukti_bayar' => null]],
            'blank invoice' => ['Menunggu Verifikasi', ['invoice_file' => '   ']],
            'blank proof' => ['Menunggu Verifikasi', ['file_bukti_bayar' => '']],
            'invoice missing on disk' => ['Menunggu Verifikasi', ['invoice_file' => 'missing-invoice-test.pdf']],
            'proof missing on disk' => ['Menunggu Verifikasi', ['file_bukti_bayar' => 'missing-proof-test.pdf']],
            'invoice traversal' => ['Menunggu Verifikasi', ['invoice_file' => '../../index.php']],
            'proof traversal' => ['Menunggu Verifikasi', ['file_bukti_bayar' => '..\\..\\index.php']],
        ];
    }

    #[DataProvider('missingPaymentDocuments')]
    public function testCannotSettleWithoutUploadedPaymentDocuments(string $currentStatus, array $documents): void
    {
        $this->paymentDb->table('mahasiswa_pendidikan')->where('id', 42)->update($documents + [
            'payment_status' => $currentStatus, 'alasan_penolakan' => 'Catatan tetap.',
        ]);
        $before = $this->record();
        [$status, $payload] = $this->verify(['status' => 'Lunas']);
        $this->assertSame(422, $status);
        $this->assertFalse($payload['success']);
        $this->assertSame($before, $this->record());
    }

    public function testLegacyProofLocationCanStillBeApproved(): void
    {
        $proof = tempnam(FCPATH . 'uploads/bukti_bayar/', 'legacy_proof_test_');
        $this->paymentFiles[] = $proof;
        $this->paymentDb->table('mahasiswa_pendidikan')->where('id', 42)->update(['file_bukti_bayar' => basename($proof)]);
        [$status, $payload] = $this->verify(['status' => 'Lunas']);
        $this->assertSame(200, $status);
        $this->assertTrue($payload['success']);
        $this->assertSame('Lunas', $this->record()['payment_status']);
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
        $revisedProof = tempnam(FCPATH . 'uploads/dokumen_mahasiswa/', 'revised_proof_test_');
        $this->paymentFiles[] = $revisedProof;
        $revisedName = basename($revisedProof);
        $file->method('getRandomName')->willReturn($revisedName);
        $file->expects($this->once())->method('move')
            ->with(FCPATH . 'uploads/dokumen_mahasiswa/', $revisedName)->willReturn(true);

        $request = $this->getMockBuilder(IncomingRequest::class)->disableOriginalConstructor()
            ->onlyMethods(['getPost', 'getFile'])->getMock();
        $request->method('getPost')->willReturnCallback(fn ($field) => match ($field) {
            'mahasiswa_id' => '42',
            'status', 'payment_status' => 'Lunas',
            default => null,
        });
        $request->method('getFile')->with('bukti_bayar')->willReturn($file);
        $controller = new Pengajuan();
        $controller->initController($request, service('response')->setStatusCode(200), service('logger'));
        $response = $controller->submit_payment();
        $this->assertTrue(json_decode($response->getBody(), true)['success']);
        $record = $this->record();
        $this->assertSame('Menunggu Verifikasi', $record['payment_status']);
        $this->assertSame($revisedName, $record['file_bukti_bayar']);
        $this->assertNull($record['alasan_penolakan']);
        $this->assertSame($this->invoiceName, $record['invoice_file']);

        [$status, $approved] = $this->verify(['status' => 'Lunas']);
        $this->assertSame(200, $status);
        $this->assertTrue($approved['success']);
        $this->assertSame('Lunas', $this->record()['payment_status']);
        $this->assertNull($this->record()['alasan_penolakan']);
        $this->assertSame($revisedName, $this->record()['file_bukti_bayar']);
    }

    public function testApprovalClearsOldRejectionReason(): void
    {
        $this->paymentDb->table('mahasiswa_pendidikan')->where('id', 42)->update([
            'payment_status' => 'Menunggu Verifikasi', 'alasan_penolakan' => 'Alasan lama.',
        ]);
        [$status, $payload] = $this->verify(['status' => 'Lunas']);
        $this->assertSame(200, $status);
        $this->assertTrue($payload['success']);
        $this->assertSame('Lunas', $this->record()['payment_status']);
        $this->assertNull($this->record()['alasan_penolakan']);
    }
}
