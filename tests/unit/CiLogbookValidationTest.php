<?php

use App\Controllers\Pendidikan\Ci\LogbookController;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class CiLogbookValidationTest extends CIUnitTestCase
{
    public static function cases(): array
    {
        return [
            'approved' => ['Disetujui', true, true, true],
            'revision' => ['Revisi', true, true, true],
            'rejected' => ['Ditolak', true, true, true],
            'default status' => [null, true, true, true],
            'invalid status' => ['Tidak Valid', true, true, false],
            'pending is not CI validation' => ['Pending', true, true, false],
            'missing logbook' => ['Disetujui', true, false, false],
            'non AJAX' => ['Disetujui', false, true, false],
        ];
    }

    #[DataProvider('cases')]
    public function testValidation($status, $ajax, $exists, $success): void
    {
        $request = $this->getMockBuilder(IncomingRequest::class)
            ->disableOriginalConstructor()->onlyMethods(['isAJAX', 'getJSON'])->getMock();
        $request->method('isAJAX')->willReturn($ajax);
        $request->method('getJSON')->willReturn([
            'status_validasi' => $status, 'catatan_ci' => '  Catatan CI  ',
        ]);
        $db = new class ($exists) {
            public array $updates = [];
            public array $lookups = [];
            private $id;
            public function __construct(private bool $exists) {}
            public function table($table) { $this->lookups[] = $table; return $this; }
            public function where($field, $id) { $this->id = $id; return $this; }
            public function get() { return $this; }
            public function getRowArray() { return $this->exists ? ['id' => $this->id] : null; }
            public function update($data) { $this->updates[] = ['id' => $this->id, 'data' => $data]; return true; }
        };
        $controller = new LogbookController();
        foreach (['request' => $request, 'response' => service('response'), 'db' => $db] as $name => $value) {
            (new ReflectionProperty($controller, $name))->setValue($controller, $value);
        }
        $response = $controller->validateLogbook(42);
        if (!$ajax) {
            $this->assertSame(403, $response->getStatusCode());
        } else {
            $payload = json_decode($response->getBody(), true);
            $this->assertSame($success, $payload['success']);
            if (!$success) {
                $this->assertSame($exists ? 'Status validasi tidak valid' : 'Logbook tidak ditemukan', $payload['message']);
            }
        }
        $this->assertCount($success ? 1 : 0, $db->updates);
        if ($success) {
            $this->assertSame(42, $db->updates[0]['id']);
            $this->assertSame($status ?? 'Disetujui', $db->updates[0]['data']['status_validasi']);
            $this->assertSame('Catatan CI', $db->updates[0]['data']['catatan_ci']);
            $this->assertNotEmpty($db->updates[0]['data']['updated_at']);
        } elseif (!$ajax || !in_array($status, ['Disetujui', 'Revisi', 'Ditolak'], true)) {
            $this->assertSame([], $db->lookups);
        }
    }
}
