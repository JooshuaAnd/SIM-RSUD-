<?php

use App\Controllers\Pelatihan\Admin\ManajemenPeserta;
use App\Libraries\Pelatihan\FeedbackSessionSummary;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Config as Database;
use CodeIgniter\Test\CIUnitTestCase;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

final class PelatihanSessionFeedbackExportTest extends CIUnitTestCase
{
    private BaseConnection $feedbackDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackDb = Database::connect('tests', false);
        if ($this->feedbackDb->DBDriver !== 'SQLite3' || $this->feedbackDb->database !== ':memory:') {
            throw new LogicException('Session feedback tests require an isolated in-memory database.');
        }

        $schemas = [
            'sesi_interaktif_pelatihan' => 'id INTEGER PRIMARY KEY, pelatihan_id INTEGER, nama_sesi TEXT, tanggal TEXT, waktu TEXT',
            'kategori_evaluasi_pelatihan' => 'id INTEGER PRIMARY KEY, nama_kategori TEXT',
            'kuesioner_master_pelatihan' => 'id INTEGER PRIMARY KEY, pelatihan_id INTEGER, kategori_id INTEGER, pertanyaan TEXT',
            'peserta_kuesioner_rating_pelatihan' => 'id INTEGER PRIMARY KEY, kuesioner_id INTEGER, sesi_id INTEGER, nilai_rating INTEGER',
        ];
        foreach ($schemas as $name => $schema) {
            $table = $this->feedbackDb->prefixTable($name);
            $this->feedbackDb->query("CREATE TABLE {$table} ({$schema})");
        }

        $this->feedbackDb->table('sesi_interaktif_pelatihan')->insertBatch([
            ['id' => 101, 'pelatihan_id' => 1, 'nama_sesi' => 'Sesi Siang', 'tanggal' => '2026-10-07', 'waktu' => '13:00:00'],
            ['id' => 100, 'pelatihan_id' => 1, 'nama_sesi' => 'Sesi Pagi', 'tanggal' => '2026-10-07', 'waktu' => '09:00:00'],
            ['id' => 200, 'pelatihan_id' => 2, 'nama_sesi' => 'Sesi Pelatihan Lain', 'tanggal' => '2026-10-06', 'waktu' => '09:00:00'],
        ]);
        foreach (['Fasilitator', 'Materi', 'Modul', 'Narasumber', 'Penyelenggara'] as $index => $category) {
            $this->feedbackDb->table('kategori_evaluasi_pelatihan')->insert(['id' => $index + 1, 'nama_kategori' => $category]);
        }
        $this->feedbackDb->table('kuesioner_master_pelatihan')->insertBatch([
            ['id' => 1, 'pelatihan_id' => 1, 'kategori_id' => 1, 'pertanyaan' => 'Penguasaan materi fasilitator'],
            ['id' => 2, 'pelatihan_id' => 1, 'kategori_id' => 2, 'pertanyaan' => 'Kesesuaian materi'],
            ['id' => 3, 'pelatihan_id' => 1, 'kategori_id' => 3, 'pertanyaan' => '=SUM(A1:A2)'],
            ['id' => 4, 'pelatihan_id' => 1, 'kategori_id' => 4, 'pertanyaan' => 'Penyampaian narasumber'],
            ['id' => 5, 'pelatihan_id' => 1, 'kategori_id' => 5, 'pertanyaan' => 'Layanan penyelenggara'],
            ['id' => 6, 'pelatihan_id' => 1, 'kategori_id' => 1, 'pertanyaan' => 'Interaksi fasilitator'],
            ['id' => 7, 'pelatihan_id' => 2, 'kategori_id' => 1, 'pertanyaan' => 'Pertanyaan pelatihan lain'],
        ]);
        foreach ([[1, 5], [1, 1], [6, 5], [2, 4], [3, 5], [4, 3], [5, 2], [7, 1]] as [$question, $score]) {
            $this->feedbackDb->table('peserta_kuesioner_rating_pelatihan')->insert([
                'kuesioner_id' => $question, 'sesi_id' => 100, 'nilai_rating' => $score,
            ]);
        }
        $this->feedbackDb->table('peserta_kuesioner_rating_pelatihan')->insert([
            'kuesioner_id' => 7, 'sesi_id' => 200, 'nilai_rating' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        $this->feedbackDb->close();
        parent::tearDown();
    }

    private function summary(int $training = 1): array
    {
        return (new FeedbackSessionSummary())->getStats($this->feedbackDb, $training);
    }

    private function append(Worksheet $sheet, array $stats, int $row): int
    {
        $method = new ReflectionMethod(ManajemenPeserta::class, 'appendSessionFeedback');
        return $method->invoke(new ManajemenPeserta(), $sheet, $stats, $row);
    }

    public function testSummaryMatchesSessionAndCategoryRatingsAndIncludesUnansweredSessions(): void
    {
        $stats = $this->summary();
        $this->assertSame([100, 101], array_column($stats, 'id'));
        $this->assertSame(3.6, $stats[0]['avg_overall']);
        $this->assertSame(7, $stats[0]['total_votes']);
        $this->assertSame(['Fasilitator', 'Materi', 'Modul', 'Narasumber', 'Penyelenggara'], array_keys($stats[0]['kategori']));
        $fasilitator = $stats[0]['kategori']['Fasilitator'];
        $this->assertSame(3.7, $fasilitator['avg_overall']);
        $this->assertSame(3, $fasilitator['total_votes']);
        $this->assertSame([3.0, 5.0], array_column($fasilitator['pertanyaan'], 'avg_rating'));
        $this->assertSame([2, 1], array_column($fasilitator['pertanyaan'], 'total_votes'));
        $this->assertNull($stats[1]['avg_overall']);
        $this->assertSame(0, $stats[1]['total_votes']);
        foreach ($stats[1]['kategori'] as $category) {
            $this->assertSame([], $category['pertanyaan']);
            $this->assertNull($category['avg_overall']);
        }
    }

    public function testXlsxPreservesSessionDetailsBeforeParticipantDetails(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Data Feedback');
        $sheet->setCellValue('A1', 'SUMMARY FEEDBACK PELATIHAN');
        $nextRow = $this->append($sheet, $this->summary(), 8);
        $participantRow = $nextRow + 2;
        $sheet->setCellValue("A{$participantRow}", 'DETAIL FEEDBACK PER PESERTA');

        $file = tempnam(sys_get_temp_dir(), 'session_feedback_');
        try {
            (new XlsxWriter($spreadsheet))->save($file);
            $loaded = (new XlsxReader())->load($file);
            $exported = $loaded->getActiveSheet();
            $this->assertSame('SUMMARY FEEDBACK PELATIHAN', $exported->getCell('A1')->getValue());
            $this->assertSame('DETAIL FEEDBACK PER SESI', $exported->getCell('A8')->getValue());
            $this->assertSame('DETAIL FEEDBACK PER PESERTA', $exported->getCell("A{$participantRow}")->getValue());
            $this->assertGreaterThan(10, $participantRow);
            $this->assertSame('SESI: Sesi Pagi', $exported->getCell('A10')->getValue());
            $this->assertEquals(3.6, $exported->getCell('D10')->getValue());
            $this->assertEquals(7, $exported->getCell('E10')->getValue());
            $this->assertSame('KATEGORI: Fasilitator', $exported->getCell('A11')->getValue());
            $this->assertEquals(3.7, $exported->getCell('D11')->getValue());
            $this->assertEquals(3, $exported->getCell('E11')->getValue());
            $this->assertEquals(3.0, $exported->getCell('D12')->getValue());
            $this->assertEquals(2, $exported->getCell('E12')->getValue());
            $this->assertSame('0.0', $exported->getStyle('D12')->getNumberFormat()->getFormatCode());
            $this->assertTrue($exported->getStyle('A12')->getAlignment()->getWrapText());
            $this->assertArrayHasKey('A8:E8', $exported->getMergeCells());
            $this->assertArrayHasKey('A11:C11', $exported->getMergeCells());

            $labels = [];
            for ($row = 8; $row < $nextRow; $row++) {
                $label = $exported->getCell("A{$row}")->getValue();
                $labels[] = $label;
                if ($label === '=SUM(A1:A2)') {
                    $this->assertSame(DataType::TYPE_STRING, $exported->getCell("A{$row}")->getDataType());
                }
                if ($label === 'SESI: Sesi Siang') {
                    $this->assertSame('-', $exported->getCell("D{$row}")->getValue());
                    $this->assertEquals(0, $exported->getCell("E{$row}")->getValue());
                }
            }
            foreach (['Fasilitator', 'Materi', 'Modul', 'Narasumber', 'Penyelenggara'] as $category) {
                $this->assertSame(2, count(array_keys($labels, 'KATEGORI: ' . $category, true)));
            }
            $this->assertContains('Belum ada jawaban untuk kategori ini pada sesi ini.', $labels);
            $this->assertContains('=SUM(A1:A2)', $labels);
            $loaded->disconnectWorksheets();
        } finally {
            unlink($file);
            $spreadsheet->disconnectWorksheets();
        }
    }

    public function testTrainingWithoutSessionsExportsAnEmptyState(): void
    {
        $stats = $this->summary(99);
        $this->assertSame([], $stats);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->assertSame(10, $this->append($sheet, $stats, 8));
        $this->assertSame('DETAIL FEEDBACK PER SESI', $sheet->getCell('A8')->getValue());
        $this->assertSame('Belum ada sesi pada pelatihan ini.', $sheet->getCell('A9')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
