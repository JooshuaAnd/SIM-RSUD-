<?php

use App\Controllers\Pendidikan\AdminDiklat;
use App\Controllers\Pendidikan\Institusi\Pengajuan;
use App\Models\PengajuanPraktikPendidikanModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;

final class PengajuanDokumenPenilaianTest extends CIUnitTestCase
{
    private BaseConnection $documentDb;
    private array $tables = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->documentDb = \Config\Database::connect('tests');
        if ($this->documentDb->DBDriver !== 'SQLite3' || $this->documentDb->database !== ':memory:') {
            throw new LogicException('Document tests require an isolated in-memory database.');
        }
        $schemas = [
            'pengajuan_praktik_pendidikan' => 'id INTEGER PRIMARY KEY, institusi_id INTEGER, nama_program TEXT, tanggal_mulai TEXT, tanggal_selesai TEXT, jumlah_peserta INTEGER, status TEXT, catatan_admin TEXT, file_proposal TEXT, file_surat_pengantar TEXT, file_logbook TEXT, file_panduan TEXT, file_daftar_mhs TEXT, file_kompetensi TEXT, file_sk_pembimbing TEXT, file_bukti_bayar TEXT, file_dokumen_penilaian TEXT, created_at TEXT, updated_at TEXT',
            'institusi_pendidikan' => 'id INTEGER PRIMARY KEY, user_id INTEGER, nama_institusi TEXT, nama_kontak TEXT, no_telp TEXT, alamat TEXT, status_verifikasi TEXT, revisi_dikirim_at TEXT, file_mou TEXT, file_permohonan TEXT, file_lainnya TEXT, created_at TEXT, updated_at TEXT',
            'users_pendidikan' => 'id INTEGER PRIMARY KEY, email TEXT',
            'mahasiswa_pendidikan' => 'id INTEGER PRIMARY KEY, institusi_id INTEGER, status TEXT',
            'penempatan_peserta_pendidikan' => 'id INTEGER PRIMARY KEY, pengajuan_id INTEGER, mahasiswa_id INTEGER',
            'profesi_pelatihan' => 'id_profesi INTEGER PRIMARY KEY, nama_profesi TEXT',
            'dokumen_institusi' => 'id INTEGER PRIMARY KEY, institusi_id INTEGER',
        ];
        foreach ($schemas as $name => $schema) {
            $table = $this->documentDb->prefixTable($name);
            $this->documentDb->query("CREATE TABLE {$table} ({$schema})");
            $this->tables[] = $table;
        }
        $this->documentDb->table('institusi_pendidikan')->insert([
            'id' => 7, 'nama_institusi' => 'Institusi Uji', 'nama_kontak' => 'PJ Uji',
            'no_telp' => '081234567890', 'status_verifikasi' => 'approved',
        ]);
        $this->documentDb->table('pengajuan_praktik_pendidikan')->insert([
            'id' => 42, 'institusi_id' => 7, 'nama_program' => 'Praktik Uji',
            'tanggal_mulai' => '2026-10-01', 'tanggal_selesai' => '2026-10-31',
            'jumlah_peserta' => 0, 'status' => 'Menunggu',
            'file_dokumen_penilaian' => 'penilaian-uji.pdf',
        ]);
        session()->set(['institusi_id' => 7, 'account_status' => 'approved']);
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            $this->documentDb->query("DROP TABLE {$table}");
        }
        parent::tearDown();
    }

    private function controller(string $class): object
    {
        $controller = new $class();
        $controller->initController(service('request'), service('response'), service('logger'));
        return $controller;
    }

    private function assertDocumentLink(string $html): void
    {
        $this->assertStringContainsString('Dokumen Penilaian', $html);
        $this->assertStringContainsString(base_url('uploads/dokumen_pengajuan/penilaian-uji.pdf'), html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    public function testModelPersistsAndReplacesDocumentWithoutLosingOtherFiles(): void
    {
        $model = new PengajuanPraktikPendidikanModel();
        $id = $model->insert(['institusi_id' => 7, 'file_proposal' => 'proposal.pdf', 'file_dokumen_penilaian' => 'awal.pdf']);
        $this->assertSame('awal.pdf', $model->find($id)['file_dokumen_penilaian']);
        $this->assertTrue($model->update($id, ['status' => 'Menunggu']));
        $this->assertSame('awal.pdf', $model->find($id)['file_dokumen_penilaian']);
        $this->assertTrue($model->update($id, ['file_dokumen_penilaian' => 'revisi.pdf']));
        $this->assertSame('revisi.pdf', $model->find($id)['file_dokumen_penilaian']);
        $this->assertSame('proposal.pdf', $model->find($id)['file_proposal']);
    }

    public function testInstitutionEditDisplaysCurrentDocument(): void
    {
        $this->assertDocumentLink($this->controller(Pengajuan::class)->edit(42));
    }

    public function testInstitutionDetailDisplaysDocument(): void
    {
        $this->assertDocumentLink($this->controller(Pengajuan::class)->detail(42));
    }

    public function testAdminSubmissionDetailDisplaysDocument(): void
    {
        $this->assertDocumentLink($this->controller(AdminDiklat::class)->pengajuanDetail(42));
    }

    public function testAdminInstitutionDocumentsDisplaysDocument(): void
    {
        $this->assertDocumentLink($this->controller(AdminDiklat::class)->institusiDetail(7));
    }

    public function testAdminDisplaysAdditionalInstitutionDocumentWithCorrectLinks(): void
    {
        $this->documentDb->table('institusi_pendidikan')->where('id', 7)->update([
            'file_lainnya' => 'tambahan.pdf', 'file_mou' => 'mou.pdf', 'file_permohonan' => 'permohonan.pdf',
        ]);
        $html = html_entity_decode($this->controller(AdminDiklat::class)->institusiDetail(7), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $this->assertTrue(str_contains($html, 'Dokumen Tambahan Institusi'));
        $url = base_url('pendidikan/admin/diklat/api/institusi/file/7/lainnya');
        $this->assertTrue(str_contains($html, 'href="' . $url . '"'));
        $this->assertTrue(str_contains($html, 'href="' . $url . '?download=1"'));
        foreach (['mou', 'permohonan'] as $jenis) {
            $this->assertTrue(str_contains($html, 'href="' . base_url('pendidikan/admin/diklat/api/institusi/file/7/' . $jenis) . '"'));
        }
    }

    public function testAdminRejectsUnknownDocumentType(): void
    {
        $response = $this->controller(AdminDiklat::class)->file(7, 'invalid');
        $this->assertSame(404, $response->getStatusCode());
        $this->assertFalse(json_decode($response->getBody(), true)['success']);
    }

    public function testAdminCanViewAndDownloadExactAdditionalDocument(): void
    {
        $fixture = tempnam(WRITEPATH . 'uploads/dokumen_institusi/', 'additional_doc_test_');
        try {
            $this->documentDb->table('institusi_pendidikan')->where('id', 7)->update(['file_lainnya' => basename($fixture)]);
            foreach ([null, '1'] as $mode) {
                $request = $this->getMockBuilder(\CodeIgniter\HTTP\IncomingRequest::class)->disableOriginalConstructor()->onlyMethods(['getGet'])->getMock();
                $request->method('getGet')->willReturn($mode);
                $controller = new AdminDiklat();
                $controller->initController($request, service('response'), service('logger'));
                $response = $controller->file(7, 'lainnya');
                $this->assertInstanceOf(\CodeIgniter\HTTP\DownloadResponse::class, $response);
                $response->buildHeaders();
                $this->assertStringStartsWith($mode === '1' ? 'attachment;' : 'inline;', $response->getHeaderLine('Content-Disposition'));
                $this->assertStringContainsString(basename($fixture), $response->getHeaderLine('Content-Disposition'));
            }
        } finally {
            unlink($fixture);
        }
    }

    public function testMissingDocumentDoesNotGenerateFileLink(): void
    {
        $this->documentDb->table('pengajuan_praktik_pendidikan')->where('id', 42)->update(['file_dokumen_penilaian' => null]);
        $controller = $this->controller(Pengajuan::class);
        foreach (['edit', 'detail'] as $method) {
            $html = $controller->{$method}(42);
            $this->assertStringContainsString('Dokumen Penilaian', $html);
            $this->assertStringNotContainsString('penilaian-uji.pdf', $html);
        }
    }

    public function testInstitutionCannotViewAnotherInstitutionsSubmission(): void
    {
        session()->set('institusi_id', 8);
        $controller = $this->controller(Pengajuan::class);
        foreach (['edit', 'detail'] as $method) {
            $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $controller->{$method}(42));
        }
    }
}
