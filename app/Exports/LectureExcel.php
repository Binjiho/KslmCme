<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LectureExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    private $educationConfig;
    private $lectureConfig;
    private $collection;
    private $total;
    private $row = 0;

    public function __construct($data)
    {
        $this->educationConfig = config('site.education');
        $this->lectureConfig = config('site.lecture');
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
            '강의구분',
            '강의분야',
            '강의명',
            '강사명',

            '강사소속',
            '강의정보(시간/파일)',
            '등록일',
        ];
    }

    public function map($data): array
    {
        $educationConfig = $this->educationConfig;
        $lectureConfig = $this->lectureConfig;

        $field_arr = array();
        foreach($lectureConfig['field'] as $field_key => $field_val){
            if(in_array($field_key, $data->field ?? []) ) {
                $field_arr[] = $field_val;
            }
        }
        $tmp_field = implode(', ',$field_arr) ?? '';

        $tmp_file = '';
        if($data->type == 'P'){
            $tmp_file = $data->filename1;
        }else{
            $tmp_file = $data->lecture_time;
        }

        return [
            $this->total - ($this->row++),
            $lectureConfig['type'][$data->type] ?? '',
            $tmp_field,
            $data->title,
            $data->name_kr,

            $data->sosok_kr,
            $tmp_file,
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
