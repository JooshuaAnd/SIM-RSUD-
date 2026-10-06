<?php

use App\Controllers\Pendidikan\Mahasiswa\NilaiController;
use App\Models\MahasiswaPendidikanModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class MahasiswaRekapNilaiTest extends CIUnitTestCase
{
    private function controller(?array $record, array $staseList = []): NilaiController
    {
        $model = $this->getMockBuilder(MahasiswaPendidikanModel::class)
            ->disableOriginalConstructor()->onlyMethods(['first'])->addMethods(['select', 'join', 'where'])->getMock();
        $model->method('select')->willReturnSelf();
        $model->method('join')->willReturnSelf();
        $model->expects($this->once())->method('where')->with('mahasiswa_pendidikan.user_id', 100)->willReturnSelf();
        $model->method('first')->willReturn($record);
        Factories::injectMock('models', MahasiswaPendidikanModel::class, $model);

        $controller = $this->getMockBuilder(NilaiController::class)->onlyMethods(['getNilaiStase'])->getMock();
        if ($record && ($record['payment_status'] ?? '') === 'Lunas') {
            $controller->expects($this->once())->method('getNilaiStase')->with(42)->willReturn($staseList);
        } else {
            $controller->expects($this->never())->method('getNilaiStase');
        }
        $controller->initController(service('request'), service('response'), service('logger'));
        return $controller;
    }

    private function student(): array
    {
        return ['id' => 42, 'user_id' => 100, 'nama_lengkap' => 'Mahasiswa Uji', 'nim' => 'NIM-42',
            'nama_institusi' => 'Institusi Uji', 'program_studi' => 'Profesi Dokter',
            'payment_status' => 'Lunas', 'nilai_akhir' => '85.50'];
    }

    public function testNoSessionCannotDownload(): void
    {
        $controller = new NilaiController();
        $controller->initController(service('request'), service('response'), service('logger'));
        $this->assertSame(401, $controller->download()->getStatusCode());
    }

    public static function deniedCases(): array
    {
        return [['missing', 404], ['Belum Invoice', 403], ['Belum Bayar', 403], ['Menunggu Verifikasi', 403], ['Ditolak', 403]];
    }

    #[DataProvider('deniedCases')]
    public function testDeniedDownload(string $status, int $expected): void
    {
        session()->set('user_id', 100);
        $record = $status === 'missing' ? null : array_replace($this->student(), ['payment_status' => $status]);
        $response = $this->controller($record)->download();
        $this->assertSame($expected, $response->getStatusCode());
        $this->assertNotInstanceOf(DownloadResponse::class, $response);
    }

    public static function reportCases(): array
    {
        return [['normal', 3], ['empty', 0], ['multipage', 65]];
    }

    #[DataProvider('reportCases')]
    public function testPdfDownloadUsesLoggedInStudent(string $case, int $count): void
    {
        session()->set('user_id', 100);
        // Supplying someone else's ID must not influence the session-bound lookup.
        $_GET['mahasiswa_id'] = '999';
        $tasks = [];
        for ($i = 0; $i < $count; $i++) {
            $tasks[] = ['nama_tugas' => 'Tugas ' . ($i + 1) . ' - Evaluasi praktik pelayanan dan pemeriksaan pasien',
                'status' => $i === 1 ? 'Belum Dinilai' : 'Selesai', 'nilai' => $i === 1 ? null : ($i === 2 ? 0 : 90)];
        }
        $stases = $count ? [['nama_stase' => 'Stase Penyakit Dalam', 'nama_ruangan' => 'Bangsal Pendidikan',
            'ci_name' => 'Pembimbing Uji', 'tasks' => $tasks],
            ['nama_stase' => 'Stase Bedah', 'nama_ruangan' => null, 'ci_name' => null, 'tasks' => []]] : [];
        $response = $this->controller($this->student(), $stases)->download();
        $this->assertInstanceOf(DownloadResponse::class, $response);
        $response->buildHeaders();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringStartsWith('application/pdf', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('attachment;', $response->getHeaderLine('Content-Disposition'));
        $this->assertStringContainsString('Rekap_Nilai_NIM-42.pdf', $response->getHeaderLine('Content-Disposition'));
        ob_start();
        $response->sendBody();
        $binary = ob_get_clean();
        $this->assertStringStartsWith('%PDF-', $binary);
        $this->assertGreaterThan(1000, strlen($binary));
        if ($directory = getenv('REKAP_NILAI_REVIEW_DIR')) {
            file_put_contents($directory . DIRECTORY_SEPARATOR . $case . '.pdf', $binary);
        }
    }

    public function testReportPreservesZeroAndMissingGradesAndEscapesText(): void
    {
        $html = view('Pendidikan/mahasiswa/rekap_nilai_pdf', [
            'mahasiswa' => array_replace($this->student(), ['nama_lengkap' => '<script>unsafe</script>', 'nilai_akhir' => 0]),
            'staseList' => [['nama_stase' => 'Stase Uji', 'nama_ruangan' => null, 'ci_name' => null,
                'tasks' => [['nama_tugas' => 'Tugas <b>Uji</b>', 'status' => null, 'nilai' => null],
                    ['nama_tugas' => 'Nilai nol', 'status' => 'Selesai', 'nilai' => 0]]]],
        ]);
        $this->assertStringContainsString('&lt;script&gt;unsafe&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>unsafe</script>', $html);
        $this->assertStringContainsString('Tugas &lt;b&gt;Uji&lt;/b&gt;', $html);
        $this->assertStringContainsString('<strong>0</strong>', $html);
        $this->assertStringContainsString('class="score">0</td>', $html);
        $this->assertStringContainsString('Belum dinilai', $html);
        $this->assertStringContainsString('Belum Dikerjakan', $html);
    }

    public function testGradeQueriesAreScopedToStudentStaseAndRoom(): void
    {
        $builder = new class {
            public array $calls = [];
            public string $table = '';
            public function __call($method, $arguments) {
                $this->calls[] = [$this->table, $method, $arguments];
                if ($method === 'getResultArray') {
                    return $this->table === 'stase_ruangan_ci_pendidikan'
                        ? [['stase_id' => 5, 'ruangan_id' => 8]]
                        : [['nama_tugas' => 'Tugas Uji', 'nilai' => 90, 'status' => 'Selesai']];
                }
                return $this;
            }
        };
        $db = $this->getMockBuilder(\CodeIgniter\Database\BaseConnection::class)
            ->disableOriginalConstructor()->onlyMethods(['table'])->getMockForAbstractClass();
        $db->method('table')->willReturnCallback(function ($table) use ($builder) {
            $builder->table = $table;
            return $builder;
        });
        $property = new ReflectionProperty(\CodeIgniter\Database\Config::class, 'instances');
        $original = $property->getValue();
        $group = config('Database')->defaultGroup;
        $property->setValue(null, array_replace($original, [$group => $db]));
        try {
            $method = new ReflectionMethod(NilaiController::class, 'getNilaiStase');
            $stases = $method->invoke(new NilaiController(), 42);
            $this->assertSame(90, $stases[0]['tasks'][0]['nilai']);
            $calls = $builder->calls;
            $membership = array_values(array_filter($calls, fn ($call) => $call[0] === 'stase_ruangan_ci_pendidikan' && $call[1] === 'where'));
            $this->assertCount(1, $membership);
            $this->assertStringContainsString("'\"42\"'", $membership[0][2][0]);
            $this->assertStringContainsString("'42'", $membership[0][2][0]);
            $joins = array_values(array_filter($calls, fn ($call) => $call[0] === 'tugas_pendidikan' && $call[1] === 'join'));
            $this->assertStringContainsString('pengumpulan_tugas_pendidikan.mahasiswa_id = 42', $joins[0][2][1]);
            $this->assertContains(['tugas_pendidikan', 'where', ['tugas_pendidikan.stase_id', 5]], $calls);
            $this->assertContains(['tugas_pendidikan', 'where', ['tugas_pendidikan.ruangan_id', 8]], $calls);
        } finally {
            $property->setValue(null, $original);
        }
    }
}
