<?php

use App\Controllers\Pelatihan\Admin\Feedback;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Config as Database;
use CodeIgniter\Test\CIUnitTestCase;

final class PelatihanFeedbackRatingTest extends CIUnitTestCase
{
    private BaseConnection $ratingsDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ratingsDb = Database::connect('tests', false);
        if ($this->ratingsDb->DBDriver !== 'SQLite3' || $this->ratingsDb->database !== ':memory:') {
            throw new LogicException('Feedback tests require an isolated in-memory database.');
        }

        $schemas = [
            'materi_pelatihan' => 'id INTEGER PRIMARY KEY, pelatihan_id INTEGER, judul TEXT, urutan INTEGER',
            'pejabat_ttd_pelatihan' => 'id INTEGER PRIMARY KEY, nama_pejabat TEXT, gelar_depan TEXT, gelar_belakang TEXT',
            'narasumber_pelatihan' => 'id INTEGER PRIMARY KEY, pejabat_ttd_id INTEGER, pelatihan_id INTEGER, sesi_id INTEGER',
            'master_penyelenggara' => 'id INTEGER PRIMARY KEY, nama TEXT',
            'penyelenggara_pelatihan' => 'id INTEGER PRIMARY KEY, penyelenggara_id INTEGER, pelatihan_id INTEGER, sesi_id INTEGER',
            'kuesioner_master_pelatihan' => 'id INTEGER PRIMARY KEY, pelatihan_id INTEGER, pertanyaan TEXT',
            'peserta_kuesioner_rating_pelatihan' => 'id INTEGER PRIMARY KEY, peserta_pelat_id INTEGER, sesi_id INTEGER, kuesioner_id INTEGER, materi_id INTEGER, narasumber_id INTEGER, penyelenggara_id INTEGER, nilai_rating INTEGER',
        ];
        foreach ($schemas as $name => $schema) {
            $table = $this->ratingsDb->prefixTable($name);
            $this->ratingsDb->query("CREATE TABLE {$table} ({$schema})");
        }

        $this->ratingsDb->table('materi_pelatihan')->insertBatch([
            ['id' => 10, 'pelatihan_id' => 1, 'judul' => 'Materi A', 'urutan' => 1],
            ['id' => 11, 'pelatihan_id' => 1, 'judul' => 'Materi B', 'urutan' => 2],
            ['id' => 20, 'pelatihan_id' => 2, 'judul' => 'Materi A', 'urutan' => 1],
        ]);
        $this->ratingsDb->table('pejabat_ttd_pelatihan')->insertBatch([
            ['id' => 50, 'nama_pejabat' => 'Narasumber A', 'gelar_depan' => 'dr.', 'gelar_belakang' => 'Sp.PD'],
            ['id' => 51, 'nama_pejabat' => 'Narasumber B', 'gelar_depan' => null, 'gelar_belakang' => null],
        ]);
        $this->ratingsDb->table('narasumber_pelatihan')->insertBatch([
            ['id' => 100, 'pejabat_ttd_id' => 50, 'pelatihan_id' => 1, 'sesi_id' => 101],
            ['id' => 101, 'pejabat_ttd_id' => 50, 'pelatihan_id' => 1, 'sesi_id' => 102],
            ['id' => 102, 'pejabat_ttd_id' => 51, 'pelatihan_id' => 1, 'sesi_id' => 101],
            ['id' => 200, 'pejabat_ttd_id' => 50, 'pelatihan_id' => 2, 'sesi_id' => 201],
        ]);
        $this->ratingsDb->table('master_penyelenggara')->insertBatch([
            ['id' => 60, 'nama' => 'Penyelenggara A'],
            ['id' => 61, 'nama' => 'Penyelenggara B'],
        ]);
        $this->ratingsDb->table('penyelenggara_pelatihan')->insertBatch([
            ['id' => 100, 'penyelenggara_id' => 60, 'pelatihan_id' => 1, 'sesi_id' => 101],
            ['id' => 101, 'penyelenggara_id' => 60, 'pelatihan_id' => 1, 'sesi_id' => 102],
            ['id' => 102, 'penyelenggara_id' => 61, 'pelatihan_id' => 1, 'sesi_id' => 101],
            ['id' => 200, 'penyelenggara_id' => 60, 'pelatihan_id' => 2, 'sesi_id' => 201],
        ]);
        $this->ratingsDb->table('kuesioner_master_pelatihan')->insertBatch([
            ['id' => 1, 'pelatihan_id' => 1, 'pertanyaan' => 'Kejelasan penyampaian'],
            ['id' => 2, 'pelatihan_id' => 1, 'pertanyaan' => 'Ketepatan waktu'],
            ['id' => 3, 'pelatihan_id' => 2, 'pertanyaan' => 'Pertanyaan pelatihan lain'],
        ]);
    }

    protected function tearDown(): void
    {
        $this->ratingsDb->close();
        parent::tearDown();
    }

    private function stats(): array
    {
        $method = new ReflectionMethod(Feedback::class, 'getAverageRatingStats');
        return $method->invoke(new Feedback(), $this->ratingsDb, 1);
    }

    private function rate(string $entityField, int $entityId, int $participant, int $session, int $score, int $question = 1): void
    {
        $this->ratingsDb->table('peserta_kuesioner_rating_pelatihan')->insert([
            'peserta_pelat_id' => $participant,
            'sesi_id' => $session,
            'kuesioner_id' => $question,
            $entityField => $entityId,
            'nilai_rating' => $score,
        ]);
    }

    public function testNewParticipantFeedbackUpdatesExistingCardsAcrossSessions(): void
    {
        $this->rate('materi_id', 10, 1, 101, 4);
        $this->rate('narasumber_id', 100, 1, 101, 4);
        $this->rate('penyelenggara_id', 100, 1, 101, 4);
        $before = $this->stats();

        $this->rate('materi_id', 10, 2, 101, 2);
        $this->rate('narasumber_id', 101, 2, 102, 2);
        $this->rate('penyelenggara_id', 101, 2, 102, 2);
        $after = $this->stats();

        foreach (['materiStats' => 10, 'narasumberStats' => 50, 'penyelenggaraStats' => 60] as $group => $masterId) {
            $this->assertCount(1, $before[$group]);
            $this->assertCount(1, $after[$group]);
            $this->assertSame($masterId, $after[$group][0]['id']);
            $this->assertSame(4.0, $before[$group][0]['avg_overall']);
            $this->assertSame(3.0, $after[$group][0]['avg_overall']);
            $this->assertSame(2, $after[$group][0]['total_votes']);
            $this->assertCount(1, $after[$group][0]['pertanyaan']);
            $this->assertSame(2, $after[$group][0]['pertanyaan'][0]['total_votes']);
        }
        $this->assertSame('dr. Narasumber A, Sp.PD', $after['narasumberStats'][0]['nama']);
    }

    public function testAverageUsesAllAnswersWhenQuestionVoteCountsDiffer(): void
    {
        $this->rate('narasumber_id', 100, 1, 101, 5);
        $this->rate('narasumber_id', 101, 2, 102, 1);
        $this->rate('narasumber_id', 101, 3, 102, 3);
        $this->rate('narasumber_id', 100, 1, 101, 5, 2);
        $cards = $this->stats()['narasumberStats'];

        $this->assertCount(1, $cards);
        $this->assertSame(3.5, $cards[0]['avg_overall']);
        $this->assertSame(4, $cards[0]['total_votes']);
        $this->assertCount(2, $cards[0]['pertanyaan']);
        $this->assertSame(3.0, $cards[0]['pertanyaan'][0]['avg_rating']);
        $this->assertSame(3, $cards[0]['pertanyaan'][0]['total_votes']);
        $this->assertSame(5.0, $cards[0]['pertanyaan'][1]['avg_rating']);
    }

    public function testDifferentEntitiesAndTrainingsStaySeparate(): void
    {
        foreach (['materi_id' => [10, 11, 20], 'narasumber_id' => [100, 102, 200], 'penyelenggara_id' => [100, 102, 200]] as $field => $ids) {
            $this->rate($field, $ids[0], 1, 101, 4);
            $this->rate($field, $ids[1], 1, 101, 2);
            $this->rate($field, $ids[2], 2, 201, 1, 3);
            $this->rate($field, $ids[0], 2, 101, 1, 3);
        }

        foreach ($this->stats() as $cards) {
            $this->assertCount(2, $cards);
            $this->assertSame(4.0, $cards[0]['avg_overall']);
            $this->assertSame(2.0, $cards[1]['avg_overall']);
            $this->assertSame(1, $cards[0]['total_votes']);
            $this->assertSame(1, $cards[1]['total_votes']);
        }
    }

    public function testNoFeedbackReturnsEmptyCards(): void
    {
        $this->assertSame(['materiStats' => [], 'narasumberStats' => [], 'penyelenggaraStats' => []], $this->stats());
    }
}
