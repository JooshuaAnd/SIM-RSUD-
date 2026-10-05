<?php

use App\Controllers\Pendidikan\Institusi\Pengajuan;
use App\Models\MahasiswaPendidikanModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class InstitusiBuktiBayarTest extends CIUnitTestCase
{
    private function controller(?array $record): Pengajuan
    {
        $model = $this->getMockBuilder(MahasiswaPendidikanModel::class)
            ->disableOriginalConstructor()->onlyMethods(['first'])->addMethods(['where'])->getMock();
        $filters = [];
        $model->method('where')->willReturnCallback(function ($field, $value) use ($model, &$filters) {
            $filters[$field] = $value;
            return $model;
        });
        $model->method('first')->willReturnCallback(function () use ($record, &$filters) {
            return $record && ($filters['id'] ?? null) == $record['id']
                && ($filters['institusi_id'] ?? null) == $record['institusi_id'] ? $record : null;
        });
        Factories::injectMock('models', MahasiswaPendidikanModel::class, $model);
        $controller = new Pengajuan();
        $controller->initController(service('request'), service('response'), service('logger'));
        return $controller;
    }

    public static function deniedCases(): array
    {
        return [
            'without session' => [null, 7, 42, 'Lunas', 'proof.png', 401],
            'other institution' => [7, 8, 42, 'Lunas', 'proof.png', 404],
            'other student' => [7, 7, 43, 'Lunas', 'proof.png', 404],
            'awaiting verification' => [7, 7, 42, 'Menunggu Verifikasi', 'proof.png', 403],
            'unpaid' => [7, 7, 42, 'Belum Bayar', 'proof.png', 403],
            'rejected' => [7, 7, 42, 'Ditolak', 'proof.png', 403],
            'missing filename' => [7, 7, 42, 'Lunas', '', 404],
            'missing file' => [7, 7, 42, 'Lunas', 'nonexistent-download-test.png', 404],
            'unix traversal' => [7, 7, 42, 'Lunas', '../../index.php', 404],
            'windows traversal' => [7, 7, 42, 'Lunas', '..\\..\\index.php', 404],
        ];
    }

    #[DataProvider('deniedCases')]
    public function testDeniesInvalidDownload($sessionId, $ownerId, $studentId, $status, $filename, $expected): void
    {
        if ($sessionId !== null) {
            session()->set('institusi_id', $sessionId);
        }
        $response = $this->controller([
            'id' => $studentId, 'institusi_id' => $ownerId,
            'payment_status' => $status, 'file_bukti_bayar' => $filename,
        ])->unduh_bukti_bayar(42);
        $this->assertSame($expected, $response->getStatusCode());
        $this->assertNotInstanceOf(DownloadResponse::class, $response);
    }

    public function testDownloadsExactUploadedFileAsAttachment(): void
    {
        session()->set('institusi_id', 7);
        $temporary = tempnam(FCPATH . 'uploads/dokumen_mahasiswa/', 'download_test_');
        $fixture = $temporary . '.png';
        rename($temporary, $fixture);
        try {
            copy(FCPATH . 'testing_dummy.png', $fixture);
            $response = $this->controller([
                'id' => 42, 'institusi_id' => 7, 'payment_status' => 'Lunas',
                'file_bukti_bayar' => basename($fixture),
            ])->unduh_bukti_bayar(42);
            $this->assertInstanceOf(DownloadResponse::class, $response);
            $response->buildHeaders();
            $this->assertSame(200, $response->getStatusCode());
            $this->assertStringStartsWith('attachment;', $response->getHeaderLine('Content-Disposition'));
            $this->assertStringContainsString(basename($fixture), $response->getHeaderLine('Content-Disposition'));
            $this->assertStringStartsWith('image/png', $response->getHeaderLine('Content-Type'));
            ob_start();
            $response->sendBody();
            $downloaded = ob_get_clean();
            $this->assertSame(file_get_contents($fixture), $downloaded);
        } finally {
            unlink($fixture);
        }
    }
}
