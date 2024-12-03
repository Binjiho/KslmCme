<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EducationExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
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
            '노출여부',
            '교육유형',
            '교육명',
            '수강기간',

            '등록일',
            '신청자',
        ];
    }

    public function map($data): array
    {
        $educationConfig = $this->educationConfig;
        $lectureConfig = $this->lectureConfig;

        $tmp_date = $data->edu_sdate->format('Y-m-d');
        if($data->edu_limit_yn != 'N'){
            $tmp_date .= ' ~ '.$data->edu_edate->format('Y-m-d');
        }else{
            $tmp_date .= ' ~ 기한없음';
        }

        return [
            $this->total - ($this->row++),
            $educationConfig['hide'][$data->hide] ?? '',
            $educationConfig['category'][$data->category] ?? '',
            $data->title,
            $tmp_date,

            $data->created_at->format('Y-m-d'),
            ($data->sac_cnt() ?? 0).'명',
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
