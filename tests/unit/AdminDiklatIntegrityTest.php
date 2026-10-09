<?php

use App\Controllers\Pendidikan\AdminDiklat;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Config as Database;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class AdminDiklatIntegrityTest extends CIUnitTestCase
{
    private BaseConnection $adminDb;
    private array $tables = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminDb = Database::connect('tests');
        if ($this->adminDb->DBDriver !== 'SQLite3' || $this->adminDb->database !== ':memory:') {
            throw new LogicException('Admin tests require an isolated in-memory database.');
        }
        $schemas = [
            'users_pendidikan' => 'id INTEGER PRIMARY KEY, role_id INTEGER, email TEXT UNIQUE, password TEXT, is_active INTEGER, created_at TEXT, updated_at TEXT',
            'institusi_pendidikan' => 'id INTEGER PRIMARY KEY, user_id INTEGER, nama_institusi TEXT, status_verifikasi TEXT, file_mou TEXT, file_permohonan TEXT, file_lainnya TEXT, revisi_dikirim_at TEXT, created_at TEXT, updated_at TEXT',
            'mahasiswa_pendidikan' => 'id INTEGER PRIMARY KEY, user_id INTEGER, institusi_id INTEGER, id_profesi INTEGER, nama_lengkap TEXT, email TEXT, status TEXT, payment_status TEXT, file_bukti_bayar TEXT, invoice_file TEXT, nominal INTEGER, alasan_penolakan TEXT, created_at TEXT, updated_at TEXT',
            'ci_pendidikan' => 'id INTEGER PRIMARY KEY, user_id INTEGER, nama_lengkap TEXT, email TEXT, nip TEXT, id_profesi INTEGER, id_unit_kerja INTEGER, nomor_telepon TEXT, created_at TEXT, updated_at TEXT',
            'stase_pendidikan' => 'id INTEGER PRIMARY KEY, nama_stase TEXT, profesi_id INTEGER, ruangan TEXT, ci_id INTEGER, tanggal_mulai TEXT, tanggal_akhir TEXT, created_at TEXT, updated_at TEXT',
            'stase_ruangan_ci_pendidikan' => 'id INTEGER PRIMARY KEY, stase_id INTEGER, ruangan_id INTEGER, ci_id INTEGER, mahasiswa_ids TEXT, created_at TEXT, updated_at TEXT',
            'penempatan_peserta_pendidikan' => 'id INTEGER PRIMARY KEY, stase_id INTEGER, mahasiswa_id INTEGER, pengajuan_id INTEGER, status_aktif INTEGER, created_at TEXT, updated_at TEXT',
            'tugas_pendidikan' => 'id INTEGER PRIMARY KEY, stase_id INTEGER, ci_id INTEGER',
            'pengumpulan_tugas_pendidikan' => 'id INTEGER PRIMARY KEY, tugas_id INTEGER, mahasiswa_id INTEGER, nilai INTEGER',
            'logbook_pendidikan' => 'id INTEGER PRIMARY KEY, stase_id INTEGER, penempatan_id INTEGER',
            'penilaian_pendidikan' => 'id INTEGER PRIMARY KEY, penempatan_id INTEGER',
            'profesi_pelatihan' => 'id_profesi INTEGER PRIMARY KEY, nama_profesi TEXT',
            'unit_kerja_pelatihan' => 'id_unit_kerja INTEGER PRIMARY KEY, nama_unit TEXT',
        ];
        foreach ($schemas as $name => $schema) {
            $staseTable = $this->adminDb->prefixTable('stase_pendidikan');
            $ciTable = $this->adminDb->prefixTable('ci_pendidikan');
            $taskTable = $this->adminDb->prefixTable('tugas_pendidikan');
            if (in_array($name, ['stase_ruangan_ci_pendidikan', 'tugas_pendidikan'], true)) {
                $schema .= ", FOREIGN KEY (stase_id) REFERENCES {$staseTable}(id) ON DELETE CASCADE, FOREIGN KEY (ci_id) REFERENCES {$ciTable}(id) ON DELETE CASCADE";
            } elseif ($name === 'pengumpulan_tugas_pendidikan') {
                $schema .= ", FOREIGN KEY (tugas_id) REFERENCES {$taskTable}(id) ON DELETE CASCADE";
            }
            $table = $this->adminDb->prefixTable($name);
            $this->adminDb->query("CREATE TABLE {$table} ({$schema})");
            $this->tables[] = $table;
        }
        $this->adminDb->table('users_pendidikan')->insertBatch([
            ['id' => 10, 'role_id' => 3, 'email' => 'student@example.test', 'password' => 'old', 'is_active' => 1],
            ['id' => 11, 'role_id' => 4, 'email' => 'ci@example.test', 'password' => 'old', 'is_active' => 1],
        ]);
        $this->adminDb->table('institusi_pendidikan')->insert(['id' => 7, 'nama_institusi' => 'Institusi Uji', 'status_verifikasi' => 'approved']);
        $this->adminDb->table('mahasiswa_pendidikan')->insert([
            'id' => 42, 'user_id' => 10, 'institusi_id' => 7, 'id_profesi' => 1,
            'nama_lengkap' => 'Mahasiswa Uji', 'email' => 'student@example.test', 'status' => 'Disetujui',
            'payment_status' => 'Menunggu Verifikasi', 'invoice_file' => 'invoice.pdf', 'file_bukti_bayar' => 'proof.png', 'nominal' => 150000,
        ]);
        $this->adminDb->table('ci_pendidikan')->insert(['id' => 13, 'user_id' => 11, 'nama_lengkap' => 'CI Uji', 'email' => 'ci@example.test', 'id_profesi' => 1, 'id_unit_kerja' => 3]);
        $this->adminDb->table('profesi_pelatihan')->insert(['id_profesi' => 1, 'nama_profesi' => 'Dokter']);
        $this->adminDb->table('unit_kerja_pelatihan')->insertBatch([['id_unit_kerja' => 3, 'nama_unit' => 'Poli'], ['id_unit_kerja' => 4, 'nama_unit' => 'IGD']]);
        $this->adminDb->table('stase_pendidikan')->insert(['id' => 1, 'nama_stase' => 'Stase Uji', 'profesi_id' => 1, 'ruangan' => '3', 'tanggal_mulai' => '2026-10-01', 'tanggal_akhir' => '2026-10-31']);
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            $this->adminDb->query("DROP TABLE {$table}");
        }
        parent::tearDown();
    }

    private function call(string $method, array $payload = [], int $id = 1, ?UploadedFile $file = null): array
    {
        $request = $this->getMockBuilder(IncomingRequest::class)->disableOriginalConstructor()
            ->onlyMethods(['getJSON', 'getPost', 'getFile'])->getMock();
        $request->method('getJSON')->willReturnCallback(fn ($array = false) => $array ? $payload : json_decode(json_encode($payload)));
        $request->method('getPost')->willReturnCallback(fn ($key = null) => $key === null ? $payload : ($payload[$key] ?? null));
        $request->method('getFile')->willReturn($file);
        $controller = new AdminDiklat();
        $controller->initController($request, service('response')->setStatusCode(200), service('logger'));
        $response = $controller->{$method}($id);
        return [$response->getStatusCode(), json_decode($response->getBody(), true)];
    }

    private function row(string $table, int $id): ?array
    {
        return $this->adminDb->table($table)->where('id', $id)->get()->getRowArray();
    }

    private function mapping(array $changes = []): void
    {
        $this->adminDb->table('stase_ruangan_ci_pendidikan')->insert($changes + ['stase_id' => 1, 'ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => '[42]']);
    }

    public function testUsedCiCannotBeDeleted(): void
    {
        $this->mapping();
        [$status] = $this->call('ciApiDelete', [], 13);
        $this->assertSame(409, $status);
        $this->assertNotNull($this->row('ci_pendidikan', 13));
        $this->assertSame(1, $this->adminDb->table('stase_ruangan_ci_pendidikan')->countAllResults());
    }

    public function testCiWithTasksCannotBeDeleted(): void
    {
        $this->adminDb->table('tugas_pendidikan')->insert(['id' => 20, 'stase_id' => 1, 'ci_id' => 13]);
        [$status] = $this->call('ciApiDelete', [], 13);
        $this->assertSame(409, $status);
        $this->assertNotNull($this->row('tugas_pendidikan', 20));
    }

    public function testUnusedCiDeletionRemovesLoginAccount(): void
    {
        [$status] = $this->call('ciApiDelete', [], 13);
        $this->assertSame(200, $status);
        $this->assertNull($this->row('ci_pendidikan', 13));
        $this->assertNull($this->row('users_pendidikan', 11));
    }

    public function testMappedStaseCannotBeDeleted(): void
    {
        $this->mapping();
        [$status] = $this->call('staseDelete');
        $this->assertSame(409, $status);
        $this->assertNotNull($this->row('stase_pendidikan', 1));
    }

    public function testStaseWithTasksCannotBeDeleted(): void
    {
        $this->adminDb->table('tugas_pendidikan')->insert(['id' => 20, 'stase_id' => 1, 'ci_id' => 13]);
        [$status] = $this->call('staseDelete');
        $this->assertSame(409, $status);
    }

    public function testEmptyStaseCanBeDeleted(): void
    {
        [$status] = $this->call('staseDelete');
        $this->assertSame(200, $status);
        $this->assertNull($this->row('stase_pendidikan', 1));
    }

    public function testPaidInvoiceChangeDoesNotResetPayment(): void
    {
        $this->adminDb->table('mahasiswa_pendidikan')->where('id', 42)->update(['payment_status' => 'Lunas']);
        [$status] = $this->call('mahasiswaUploadInvoice', ['nominal' => '175000'], 42);
        $this->assertSame(409, $status);
        $this->assertSame('Lunas', $this->row('mahasiswa_pendidikan', 42)['payment_status']);
        $this->assertEquals(150000, $this->row('mahasiswa_pendidikan', 42)['nominal']);
    }

    public function testValidRoomMappingIsSaved(): void
    {
        [$status, $body] = $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]]);
        $this->assertSame(200, $status);
        $this->assertTrue($body['success']);
        $this->assertSame(1, $this->adminDb->table('stase_ruangan_ci_pendidikan')->countAllResults());
    }

    public static function invalidMappings(): array
    {
        return [
            'foreign room' => [['ruangan_id' => 4]],
            'missing ci' => [['ci_id' => 999]],
            'missing student' => [['mahasiswa_ids' => [999]]],
            'invalid ids' => [['mahasiswa_ids' => ['42oops']]],
        ];
    }

    #[DataProvider('invalidMappings')]
    public function testInvalidMappingLeavesExistingAssignmentUnchanged(array $changes): void
    {
        $this->mapping();
        [$status] = $this->call('staseSaveRoomMapping', $changes + ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]]);
        $this->assertSame(422, $status);
        $this->assertSame('[42]', $this->adminDb->table('stase_ruangan_ci_pendidikan')->get()->getRowArray()['mahasiswa_ids']);
    }

    public function testPendingStudentCannotBeAssigned(): void
    {
        $this->adminDb->table('mahasiswa_pendidikan')->where('id', 42)->update(['status' => 'Menunggu']);
        [$status] = $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]]);
        $this->assertSame(422, $status);
    }

    public function testWrongProfessionCannotBeAssigned(): void
    {
        $this->adminDb->table('mahasiswa_pendidikan')->where('id', 42)->update(['id_profesi' => 2]);
        [$status] = $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]]);
        $this->assertSame(422, $status);
    }

    public function testCiMustBelongToTheMappedRoom(): void
    {
        $this->adminDb->table('ci_pendidikan')->where('id', 13)->update(['id_unit_kerja' => 4]);
        [$status] = $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]]);
        $this->assertSame(422, $status);
    }

    private function otherStase(string $start = '2026-10-15', string $end = '2026-11-15'): void
    {
        $this->adminDb->table('stase_pendidikan')->insert(['id' => 2, 'nama_stase' => 'Stase Lain', 'profesi_id' => 1, 'ruangan' => '3', 'tanggal_mulai' => $start, 'tanggal_akhir' => $end]);
    }

    public function testOverlappingCiCannotBeAssigned(): void
    {
        $this->otherStase();
        $this->mapping(['stase_id' => 2, 'mahasiswa_ids' => '[]']);
        [$status] = $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]]);
        $this->assertSame(422, $status);
    }

    public function testOverlappingStudentCannotBeAssigned(): void
    {
        $this->otherStase();
        $this->adminDb->table('penempatan_peserta_pendidikan')->insert(['mahasiswa_id' => 42, 'stase_id' => 2]);
        [$status] = $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]]);
        $this->assertSame(422, $status);
    }

    public function testBlockedDeletionPreservesTaskSubmissionAndGrade(): void
    {
        $this->adminDb->table('tugas_pendidikan')->insert(['id' => 20, 'stase_id' => 1, 'ci_id' => 13]);
        $this->adminDb->table('pengumpulan_tugas_pendidikan')->insert(['id' => 30, 'tugas_id' => 20, 'mahasiswa_id' => 42, 'nilai' => 85]);
        $this->assertSame(409, $this->call('ciApiDelete', [], 13)[0]);
        $this->assertSame(409, $this->call('staseDelete')[0]);
        $this->assertEquals(85, $this->row('pengumpulan_tugas_pendidikan', 30)['nilai']);
    }

    public function testDirectCiAssignmentAlsoPreventsDeletion(): void
    {
        $this->adminDb->table('stase_pendidikan')->where('id', 1)->update(['ci_id' => 13]);
        $this->assertSame(409, $this->call('ciApiDelete', [], 13)[0]);
    }

    public function testLegacyPlacementPreventsStaseDeletion(): void
    {
        $this->adminDb->table('penempatan_peserta_pendidikan')->insert(['stase_id' => 1, 'mahasiswa_id' => 42]);
        $this->assertSame(409, $this->call('staseDelete')[0]);
    }

    public function testLogbookHistoryPreventsStaseDeletion(): void
    {
        $this->adminDb->table('logbook_pendidikan')->insert(['stase_id' => 1]);
        $this->assertSame(409, $this->call('staseDelete')[0]);
    }

    public function testUnpaidInvoiceCanStillBeUpdated(): void
    {
        $this->adminDb->table('mahasiswa_pendidikan')->where('id', 42)->update([
            'payment_status' => 'Belum Invoice', 'invoice_file' => null,
        ]);
        $file = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()
            ->onlyMethods(['getError', 'hasMoved', 'isValid', 'getMimeType', 'getSize', 'getRandomName', 'move'])->getMock();
        $file->method('getError')->willReturn(UPLOAD_ERR_OK);
        $file->method('hasMoved')->willReturn(false);
        $file->method('isValid')->willReturn(true);
        $file->method('getMimeType')->willReturn('application/pdf');
        $file->method('getSize')->willReturn(1024);
        $file->method('getRandomName')->willReturn('integrity_invoice_test.pdf');
        $file->expects($this->once())->method('move')->with(FCPATH . 'uploads/invoices', 'integrity_invoice_test.pdf');

        $this->assertSame(200, $this->call('mahasiswaUploadInvoice', ['nominal' => '175000'], 42, $file)[0]);
        $this->assertSame('Belum Bayar', $this->row('mahasiswa_pendidikan', 42)['payment_status']);
        $this->assertEquals(175000, $this->row('mahasiswa_pendidikan', 42)['nominal']);
        $this->assertSame('integrity_invoice_test.pdf', $this->row('mahasiswa_pendidikan', 42)['invoice_file']);
    }

    public function testUnpaidInvoiceCannotBeUpdatedWithoutPdf(): void
    {
        $this->adminDb->table('mahasiswa_pendidikan')->where('id', 42)->update(['payment_status' => 'Belum Invoice']);
        $before = $this->row('mahasiswa_pendidikan', 42);
        [$status, $body] = $this->call('mahasiswaUploadInvoice', ['nominal' => '175000'], 42);

        $this->assertSame(422, $status);
        $this->assertFalse($body['success']);
        $this->assertSame('File invoice PDF wajib diunggah.', $body['message']);
        $this->assertSame($before, $this->row('mahasiswa_pendidikan', 42));
    }

    public function testMappingRequiresCorrectCiProfession(): void
    {
        $this->adminDb->table('ci_pendidikan')->where('id', 13)->update(['id_profesi' => 2]);
        $this->assertSame(422, $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]])[0]);
    }

    public function testMappingRequiresCiWhenStudentsSelected(): void
    {
        $this->assertSame(422, $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 0, 'mahasiswa_ids' => [42]])[0]);
    }

    public function testMalformedRoomPayloadIsRejected(): void
    {
        $this->assertSame(422, $this->call('staseSaveRoomMapping', ['ruangan_id' => [3], 'ci_id' => 13, 'mahasiswa_ids' => [42]])[0]);
    }

    public function testMappingRequiresCompletePeriod(): void
    {
        $this->adminDb->table('stase_pendidikan')->where('id', 1)->update(['tanggal_akhir' => null]);
        $this->assertSame(422, $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]])[0]);
    }

    public function testAssignmentRequiresARegisteredProfession(): void
    {
        $this->adminDb->table('profesi_pelatihan')->where('id_profesi', 1)->delete();
        $this->assertSame(422, $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]])[0]);
    }

    public function testAssignmentCanBeCleared(): void
    {
        $this->mapping();
        $this->assertSame(200, $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 0, 'mahasiswa_ids' => []])[0]);
        $this->assertSame(0, $this->adminDb->table('stase_ruangan_ci_pendidikan')->countAllResults());
    }

    public function testBulkMappingIsValidatedBeforeReplacingAnything(): void
    {
        $this->mapping();
        $this->assertSame(422, $this->call('staseSaveRoomMapping', ['mappings' => [
            3 => ['ci_id' => 13, 'mahasiswa_ids' => [42]],
            4 => ['ci_id' => 13, 'mahasiswa_ids' => []],
        ]])[0]);
        $this->assertSame(1, $this->adminDb->table('stase_ruangan_ci_pendidikan')->countAllResults());
    }

    public function testNonOverlappingAssignmentsAreAllowed(): void
    {
        $this->otherStase('2026-11-01', '2026-11-30');
        $this->mapping(['stase_id' => 2]);
        $this->assertSame(200, $this->call('staseSaveRoomMapping', ['ruangan_id' => 3, 'ci_id' => 13, 'mahasiswa_ids' => [42]])[0]);
    }

    public function testLegacyCiAssignmentRejectsOverlap(): void
    {
        $this->otherStase();
        $this->mapping(['stase_id' => 2]);
        $this->assertSame(422, $this->call('staseAssignCi', ['ci_id' => 13])[0]);
    }

    public function testLegacyStudentAssignmentRejectsWrongProfession(): void
    {
        $this->adminDb->table('mahasiswa_pendidikan')->where('id', 42)->update(['id_profesi' => 2]);
        $this->assertSame(422, $this->call('staseAddMahasiswa', ['mahasiswa_ids' => [42]])[0]);
    }
}
