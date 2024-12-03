<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SacCancleExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
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
            '이메일',

            '연락처',
            '신청일',
            '금액',
            '결제방식',
            '결제일',

            '취소신청일',
            '취소상태',
        ];
    }

    public function map($data): array
    {
        $sacConfig = $this->sacConfig;

        if($data->tot_pay == 0){
            $tmp_cost = '무료';
        }else{
            $tmp_cost = number_format($data->tot_pay).'원';
        }

        return [
            $this->total - ($this->row++),
            $data->user->uid ?? '',
            $data->user->name_kr ?? '',
            $data->user->sosok_kr ?? '',
            $data->user->email ?? '',

            $data->user->phone ?? '',
            $data->created_at->format('Y-m-d') ?? '',
            $tmp_cost ?? 0,
            $sacConfig['pay_status'][$data->pay_status] ?? '',
            (isset($data->pay_at) && isValidTimestamp($data->pay_at)) ? $data->pay_at->format('Y-m-d') : '',

            (isset($data->del_request_at) && isValidTimestamp($data->del_request_at)) ? $data->del_request_at->format('Y-m-d') : '',
            $sacConfig['del_request'][$data->del_request] ?? '',
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
