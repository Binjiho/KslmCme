<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ViewExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    private $sacConfig;
    private $collection;
    private $total;
    private $row = 0;

    public function __construct($data)
    {
        $this->sacConfig = config('site.sac');
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
            '아이디',
            '이름',
            '소속',
            '수강 완료 강의 수',

            '퀴즈',
            '설문',
            '수강시작',
            '수강종료',
            '수료상태',
        ];
    }

    public function map($data): array
    {
        $sacConfig = $this->sacConfig;

        $tmp_cnt = $data->getLectureCnt($data->user_sid,'complete').' / '.$data->lectures()->count();

        return [
            $this->total - ($this->row++),
            $data->user->uid ?? '',
            $data->user->name_kr ?? '',
            $data->user->sosok_kr ?? '',
            $tmp_cnt,

            $data->edu->quiz_yn == 'N' ? '-' : ($data->quiz_status == 'C' ? '합격' : '불합격'),
            $data->edu->survey_yn == 'N' ? '-' : ($data->survey_status == 'C' ? '완료' : '대기'),
            !empty($data->edu_start_at()) ? $data->edu_start_at()->format('Y-m-d') : '',
            !empty($data->edu_at) ? $data->edu_at->format('Y-m-d') : '',
            $sacConfig['edu_status'][$data->edu_status ?? ''] ?? ''
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
