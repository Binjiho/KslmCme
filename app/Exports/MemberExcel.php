<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MemberExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    private $userConfig;
    private $collection;
    private $total;
    private $row = 0;

    public function __construct($data)
    {
        $this->userConfig = config('site.user');
        $this->collection = $data['collection'];
        $this->total = $data['total'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'No',
            '회원등급',
            'ID',
            '이름',
            '근무처',

            '근무처주소',
            '면허번호',
            '연락처',
            '이메일',
        ];
    }

    public function map($data): array
    {
        $userConfig = $this->userConfig;
        
        return [
            $this->total - ($this->row++),
            $userConfig['level'][$data->level] ?? '',
            $data->uid,
            $data->name_kr ?? '',
            $data->sosok_kr ?? '',

            $data->office_addr2 ? $data->office_addr1.' '.$data->office_addr2 :'',
            $data->license_number ?? '',
            $data->phone ?? '',
            $data->email ?? '',
//            $data->created_at->format('Y-m-d'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // HTML을 허용할 셀 범위를 지정
                $event->sheet->getStyle("A:ZZ")->getAlignment()->setWrapText(true);

                // 텍스트 높이 가운데로 정렬
                $event->sheet->getStyle('A:ZZ')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // 텍스트 가운데로 정렬
                $event->sheet->getStyle('A:ZZ')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 폰트 bold & size
                $event->sheet->getDelegate()->getStyle('A1:ZZ1')->getFont()->setBold(true)->setSize(12);
            },
        ];
    }
}
