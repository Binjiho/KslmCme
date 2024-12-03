<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WorkshopExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    private $workshopConfig;
    private $collection;
    private $total;
    private $row = 0;

    public function __construct($data)
    {
        $this->workshopConfig = config('site.workshop');
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
            '자료구분',
            '학술대회구분',
            '행사명',
            '행사일',

            '노출여부',
            '등록일',
        ];
    }

    public function map($data): array
    {
        $workshopConfig = $this->workshopConfig;

        $tmp_date = $data->sdate->format('Y-m-d');
        if($data->date_type == 'L'){
            $tmp_date .= ' ~ '.$data->edate->format('Y-m-d');
        }

        return [
            $this->total - ($this->row++),
            $workshopConfig['category'][$data->category] ?? '',
            $workshopConfig['gubun'][$data->gubun] ?? '',
            $data->title ?? '',
            $tmp_date ?? '',

            $workshopConfig['hide'][$data->hide] ?? '',
            $data->created_at->format('Y-m-d'),
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
