<?php

use App\Filters\PendidikanAdminCsrfFilter;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class AdminDiklatSecurityTest extends CIUnitTestCase
{
    public function testMissingTokenRejectsAdminApiMutation(): void
    {
        $request = service('request');
        $request->setMethod('POST');
        $request->getUri()->setPath('/pendidikan/admin/diklat/api/ci/delete/13');
        $request->setGlobal('post', []);
        $request->setBody('{}');
        $response = (new PendidikanAdminCsrfFilter())->before($request);
        $this->assertSame(403, $response->getStatusCode());
        $this->assertFalse(json_decode($response->getBody(), true)['success']);
    }

    public function testWrongTokenIsRejected(): void
    {
        $request = service('request');
        $request->setMethod('POST');
        $request->getUri()->setPath('/pendidikan/admin/diklat/api/stase');
        $request->setHeader('X-CSRF-TOKEN', str_repeat('0', 32));
        $response = (new PendidikanAdminCsrfFilter())->before($request);
        $this->assertSame(403, $response->getStatusCode());
    }

    public static function mutations(): array
    {
        return [['POST'], ['PUT'], ['PATCH'], ['DELETE']];
    }

    #[DataProvider('mutations')]
    public function testValidHeaderTokenAllowsMutationAndRemainsUsable(string $method): void
    {
        $security = PendidikanAdminCsrfFilter::security();
        $token = $security->getHash();
        $request = service('request');
        $request->setMethod($method);
        $request->setHeader($security->getHeaderName(), $token);
        $filter = new PendidikanAdminCsrfFilter();
        $this->assertNull($filter->before($request));
        $this->assertNull($filter->before($request));
        $this->assertSame($token, PendidikanAdminCsrfFilter::security()->getHash());
    }

    public function testOrdinaryPasswordFormAcceptsHiddenToken(): void
    {
        $security = PendidikanAdminCsrfFilter::security();
        $request = service('request');
        $request->setMethod('POST');
        $request->setGlobal('post', [$security->getTokenName() => $security->getHash()]);
        $this->assertNull((new PendidikanAdminCsrfFilter())->before($request));
    }

    public function testTokenBecomesInvalidWhenSessionEnds(): void
    {
        $security = PendidikanAdminCsrfFilter::security();
        $request = service('request');
        $request->setMethod('POST');
        $request->getUri()->setPath('/pendidikan/admin/diklat/api/ci');
        $request->setHeader($security->getHeaderName(), $security->getHash());
        session()->remove($security->getTokenName());
        $response = (new PendidikanAdminCsrfFilter())->before($request);
        $this->assertSame(403, $response->getStatusCode());
    }

    public function testReadOnlyRequestsAreAllowed(): void
    {
        $request = service('request');
        $request->setMethod('GET');
        $this->assertNull((new PendidikanAdminCsrfFilter())->before($request));
    }

    public function testCsrfConfigurationDoesNotAffectOtherModules(): void
    {
        $config = config('Security');
        $original = [$config->csrfProtection, $config->tokenName, $config->regenerate];
        PendidikanAdminCsrfFilter::security();
        $this->assertSame($original, [$config->csrfProtection, $config->tokenName, $config->regenerate]);
        $this->assertSame(
            ['before' => ['pendidikan/admin/diklat', 'pendidikan/admin/diklat/*']],
            config('Filters')->filters['pendidikan_admin_csrf']
        );
    }

    public function testDeleteCiRouteRequiresPost(): void
    {
        $routes = service('routes');
        $routes->loadRoutes();
        $route = 'pendidikan/admin/diklat/api/ci/delete/([0-9]+)';
        $this->assertArrayNotHasKey($route, $routes->getRoutes('GET'));
        $this->assertArrayHasKey($route, $routes->getRoutes('POST'));
    }

    public static function adminViews(): array
    {
        return array_map(fn ($name) => [$name], [
            'dashboard', 'institusi', 'ci', 'stase', 'stase_detail', 'pengajuan', 'user', 'user_detail', 'user_profesi',
        ]);
    }

    #[DataProvider('adminViews')]
    public function testUserControlledTextIsEscaped(string $name): void
    {
        $evil = "O'Neil<img src=x onerror=alert(1)>";
        $student = ['id' => 42, 'nama_lengkap' => $evil, 'nama_institusi' => $evil, 'nim' => $evil, 'institusi_id' => 7, 'status' => 'Disetujui'];
        $ci = ['id' => 13, 'nama_lengkap' => $evil, 'id_profesi' => 1, 'id_unit_kerja' => 3, 'available' => true, 'has_overlap' => false];
        $institution = ['id' => 7, 'nama_institusi' => $evil, 'status_verifikasi' => 'pending', 'created_at' => '2026-10-01'];
        $stase = ['id' => 1, 'nama_stase' => $evil, 'nama_profesi' => $evil, 'ruangan' => '3', 'tanggal_mulai' => '2026-10-01', 'tanggal_akhir' => '2026-10-31'];
        $data = [
            'menu' => $name, 'title' => $evil, 'tab' => 'explorer', 'viewMode' => 'list', 'subTab' => 'documents',
            'institusiList' => [$institution], 'institusi' => $institution, 'pendingList' => [$institution], 'pendingCount' => 1,
            'mahasiswaList' => [$student], 'ciList' => [$ci], 'staseList' => [$stase], 'stase' => $stase,
            'profesiList' => [], 'unitKerjaList' => [], 'ruanganList' => [['id_unit_kerja' => 3, 'nama_unit' => $evil]],
            'mappedRooms' => [], 'stats' => [], 'profesi' => $evil, 'mahasiswaByProfesi' => [], 'totalPengajuan' => 1,
            'detail' => ['pengajuan' => ['id' => 5, 'nama_program' => $evil, 'nama_institusi' => $evil, 'status' => 'Menunggu', 'catatan_admin' => $evil]],
            'counts' => ['inbox' => 1, 'approved' => 0, 'revision' => 0, 'declined' => 0],
        ];
        $html = view('Pendidikan/AdminDiklat/' . $name, $data, ['saveData' => false]);
        $this->assertStringNotContainsString('<img src=x onerror=alert(1)>', $html);
        $this->assertStringContainsString('&lt;img', $html);
        $this->assertStringContainsString('admin-csrf-token', $html);
    }

    public function testFlashMessageCannotCloseScript(): void
    {
        session()->setFlashdata('error', "O'Neil</script><img src=x onerror=alert(1)>");
        $html = view('Pendidikan/AdminDiklat/layout/footer', [], ['saveData' => false]);
        $this->assertStringNotContainsString('</script><img', $html);
        $this->assertStringContainsString('\\u003C', $html);
    }

    public function testRejectionReasonIsNotInsertedAsRawHtml(): void
    {
        $source = file_get_contents(APPPATH . 'Views/Pendidikan/AdminDiklat/user.php');
        $this->assertStringContainsString('escapeAdminDiklatHtml(m.alasan_penolakan)', $source);
    }
}
