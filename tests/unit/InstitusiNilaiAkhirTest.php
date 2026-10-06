<?php

use App\Controllers\Pendidikan\Institusi\Pengajuan;
use App\Models\MahasiswaPendidikanModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class InstitusiNilaiAkhirTest extends CIUnitTestCase
{
    public static function scores(): array
    {
        return [
            ['0', '0'], ['100', '100'], ['90', '90'], ['90,5', '90.5'], ['85,25', '85.25'],
            ['', null], ['abc', null], ['90.5', null], ['9e1', null], ['+90', null], ['-1', null],
            ['90,5,1', null], ['90,', null], [',5', null], [' 90 ', null], ["90\n", null],
            ['101', null], ['100,01', null], [['90'], null],
        ];
    }

    #[DataProvider('scores')]
    public function testScoreValidationAndDecimalStorage($score, ?string $stored): void
    {
        session()->set('institusi_id', 7);
        $request = $this->getMockBuilder(IncomingRequest::class)
            ->disableOriginalConstructor()->onlyMethods(['getPost'])->getMock();
        $request->method('getPost')->willReturnCallback(fn ($field) => $field === 'id' ? '42' : $score);
        $model = $this->getMockBuilder(MahasiswaPendidikanModel::class)
            ->disableOriginalConstructor()->onlyMethods(['first', 'update'])->addMethods(['where'])->getMock();
        if ($stored !== null) {
            $model->expects($this->exactly(2))->method('where')->willReturnCallback(function ($field, $value) use ($model) {
                $this->assertContains([$field, $value], [['id', '42'], ['institusi_id', 7]]);
                return $model;
            });
            $model->method('first')->willReturn(['id' => 42, 'institusi_id' => 7]);
            $model->expects($this->once())->method('update')->with('42', ['nilai_akhir' => $stored, 'status' => 'Lulus'])->willReturn(true);
        } else {
            $model->expects($this->never())->method('where');
            $model->expects($this->never())->method('update');
        }
        Factories::injectMock('models', MahasiswaPendidikanModel::class, $model);
        $controller = new Pengajuan();
        $controller->initController($request, service('response'), service('logger'));
        $payload = json_decode($controller->simpan_nilai_akhir()->getBody(), true);
        $this->assertSame($stored !== null, $payload['success']);
    }
}
