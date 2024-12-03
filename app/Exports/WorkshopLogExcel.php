<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WorkshopLogExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
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
            '자료분야',

            '자료명',
            '발표자',
            '열람자',
            '열람구분',
            '열람일',
        ];
    }

    public function map($data): array
    {
        $workshopConfig = $this->workshopConfig;

        $field_arr=array();
        foreach($workshopConfig['field'] as $field_key => $field_val){
            if(in_array($field_key, $data->sub->field ?? []) ) {
                $field_arr[] = $field_val;
            }
        }
        $tmp_field = implode(',',$field_arr);

        $tmp_pname = $data->sub->pname ?? '';
        if($data->sub->psosok) $tmp_pname.= '('.$data->sub->psosok.')';

        $tmp_name = $data->user->name_kr ?? '';
//        if($data->user->uid) $tmp_name.= '('.$data->user->uid.')';

        return [
            $this->total - ($this->row++),
            $workshopConfig['category'][$data->workshop->category] ?? '',
            $workshopConfig['gubun'][$data->workshop->gubun] ?? '',
            $data->workshop->title ?? '',
            $tmp_field ?? '',

            $data->sub->title ?? '',
            $tmp_pname,
            $tmp_name,
            $workshopConfig['log_type'][$data->log_type] ?? '',
            $data->updated_at,
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
