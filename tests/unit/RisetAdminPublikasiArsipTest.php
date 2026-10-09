<?php

use App\Controllers\Riset\Admin\Izin;
use App\Controllers\Riset\Admin\Publikasi;
use App\Controllers\Riset\Admin\Review;
use App\Models\DokumenRisetModel;
use App\Models\PengajuanRisetModel;
use App\Models\PublikasiRisetModel;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class RisetAdminPublikasiArsipTest extends CIUnitTestCase
{
    /** Bangun controller tanpa konstruktor (menghindari koneksi DB) lalu suntik model mock. */
    private function make(string $class, array $props, array $post = []): object
    {
        $ref = new ReflectionClass($class);
        $controller = $ref->newInstanceWithoutConstructor();
        foreach ($props as $name => $value) {
            $p = new ReflectionProperty($class, $name);
            $p->setValue($controller, $value);
        }
        $request = service('request');
        $request->setGlobal('post', $post);
        $controller->initController($request, service('response'), service('logger'));
        return $controller;
    }


    public static function approveCases(): array
    {
        return [
            'izin penelitian, bayar valid' => [Izin::class, 'pengajuanModel', PengajuanRisetModel::class, 'konfirmasi_bayar', 'riset/admin/izin/detail/5'],
            'studi pendahuluan, bayar valid' => [Review::class, 'pengajuanModel', PengajuanRisetModel::class, 'konfirmasi_bayar', 'riset/admin/review/detail/5'],
            'izin penelitian, konfirmasi dokumen' => [Izin::class, 'pengajuanModel', PengajuanRisetModel::class, 'konfirmasi_dokumen', 'riset/admin/izin'],
            'studi pendahuluan, revisi' => [Review::class, 'pengajuanModel', PengajuanRisetModel::class, 'revisi', 'riset/admin/review'],
            'publikasi izin, bayar valid' => [Publikasi::class, 'publikasiModel', PublikasiRisetModel::class, 'konfirmasi_bayar_izin', 'riset/admin/publikasi/detail/5'],
            'publikasi upload, diterima' => [Publikasi::class, 'publikasiModel', PublikasiRisetModel::class, 'konfirmasi_bayar_terima', 'riset/admin/publikasi/detail/5'],
            'publikasi, konfirmasi dokumen' => [Publikasi::class, 'publikasiModel', PublikasiRisetModel::class, 'konfirmasi_dokumen', 'riset/admin/publikasi'],
        ];
    }

    #[DataProvider('approveCases')]
    public function testApproveRedirectTarget(string $class, string $prop, string $modelClass, string $status, string $expectedPath): void
    {
        $model = $this->getMockBuilder($modelClass)->disableOriginalConstructor()
            ->onlyMethods(['update', 'first', 'findColumn'])->addMethods(['like'])->getMock();
        $model->method('like')->willReturnSelf();
        $model->method('findColumn')->willReturn([]);
        $model->method('first')->willReturn(null); // nomor surat belum dipakai
        $model->expects($this->once())->method('update');

        $controller = $this->make($class, [$prop => $model], [
            'id' => '5', 'status_validasi' => $status, 'nomor_surat' => '',
        ]);
        $response = $controller->approve();

        $location = $response->getHeaderLine('Location');
        $this->assertSame(rtrim(base_url($expectedPath), '/'), rtrim($location, '/'));
    }

    public function testArsipSubmitRejectsIncompleteForm(): void
    {
        $publikasi = $this->getMockBuilder(PublikasiRisetModel::class)->disableOriginalConstructor()
            ->onlyMethods(['insert'])->getMock();
        $publikasi->expects($this->never())->method('insert');
        $dokumen = $this->getMockBuilder(DokumenRisetModel::class)->disableOriginalConstructor()
            ->onlyMethods(['insert'])->getMock();
        $dokumen->expects($this->never())->method('insert');

        // Hanya sebagian field terisi dan tidak ada berkas.
        $controller = $this->make(Publikasi::class, [
            'publikasiModel' => $publikasi,
            'dokumenModel'   => $dokumen,
        ], ['nama' => 'Budi', 'judul' => 'Judul saja']);

        $response = $controller->arsipSubmit();
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertSame(302, $response->getStatusCode());
    }
}
