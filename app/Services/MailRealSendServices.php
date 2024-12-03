<?php

namespace App\Services;

use App\Models\MailSend;
use App\Models\WiseUMailBody;
use App\Models\WiseUMailInterface;
use App\Models\WiseUMailLog;
use Illuminate\Support\Facades\DB;

/**
 * Class MailSendServices
 * @package App\Services
 */
class MailRealSendServices extends AppServices
{
    private $mailConfig;

    private $secretariatMail = ''; // 학회사무국 메일

    private $plannerMail = ''; // 기획자 메일

    public function __construct()
    {
        $this->mailConfig = getConfig('mail');
    }

    // 메일 수신 대상 추가
    private function mailSendTartgetAppend($mailData)
    {
        return [
            [ // 사무국
                'receiver_name' => $mailData['receiver_name'],
                'receiver_email' => $this->secretariatMail,
                'body' => $mailData['body'],
            ],

            [ // 기획자
                'receiver_name' => $mailData['receiver_name'],
                'receiver_email' => $this->plannerMail,
                'body' => $mailData['body'],
            ],
        ];
    }

    public function mailSendService($mailData, $case, $additionalData = null)
    {
        switch ($case) {
//            case 'signup':
//                $subject = '회원가입';

//                $data[] = $mailData;
//                $data = array_merge($data, $this->mailSendTartgetAppend($mailData));
//                break;

            case 'sac-create':
                $subject = '[대한진단검사의학회] '.$mailData['etc']->edu->title.' 신청 내역 안내드립니다.';
                $data[] = $mailData;
                break;

            case 'sac-cancle':
                $subject = '[대한진단검사의학회] '.$mailData['etc']->edu->title.' 신청 취소 안내드립니다.';
                $data[] = $mailData;
                break;

            case 'sac-refund':
                $subject = '[대한진단검사의학회] '.$mailData['etc']->edu->title.' 신청 비용 환불 처리 안내드립니다.';
                $data[] = $mailData;
                break;

            // 관리자 발송 메일
            case 'admin-type-send':
            case 'admin-target-resend':
                $subject = $additionalData['subject'];

                $sender = [
                    'sender_name' => $additionalData['sender_name'],
                    'sender_email' => $additionalData['sender_email'],
                ];

                $data = $mailData;
                
            //                foreach ($mailData as $key => $val) {
            //                    $data = array_merge($data, $this->mailSendTartgetAppend($val));
            //                }
                break;

            default:
                return notFoundRedirect();
        }

        return $this->mailSend($data, $subject, $sender ?? null);
    }

    // 메일 발송 로직
    private function mailSend($mailData, $subject, $sender = null)
    {
        $mailConfig = $this->mailConfig;

        $this->transaction();

        try {
            foreach ($mailData as $key => $data) {
                $now = now();
                $seq = $now->timestamp . $now->micro;

                $body = $data['body'];

                $receiver_name = $data['receiver_name'];
                $receiver_email = $data['receiver_email'];

                if ($_SERVER['REMOTE_ADDR'] == '218.235.94.247') {
//                    $receiver_email = 'jh2.park@m2community.co.kr';
                }

                //인터페이스 테이블
                WiseUMailInterface::insert([
                    'ECARE_NO' => $mailConfig['eCareNo'],
                    'RECEIVER_ID' => $seq,
                    'CHANNEL' => 'M',
                    'SEQ' => $seq,
                    'REQ_DT' => $now->format('Ymd'),
                    'REQ_TM' => $now->format('His'),
                    'TMPL_TYPE' => 'T',
                    'RECEIVER_NM' => $receiver_name,
                    'RECEIVER' => $receiver_email,
                    'SENDER_NM' => $sender['sender_name'] ?? $mailConfig['sender_name'],
                    'SENDER' => $sender['sender_email'] ?? $mailConfig['sender_email'],
                    'SUBJECT' => $subject,
                    'SEND_FG' => 'R',
                    'DATA_CNT' => 1,
                ]);

                //메일 body
                WiseUMailBody::insert([
                    'SEQ' => $seq,
                    'DATA_SEQ' => 1,
                    'ATTACH_YN' => 'N',
                    'DATA' => $body,
                ]);

                // 메일 발송내역 저장
                MailSend::insert([
                    'ml_sid' => $data['ml_sid'] ?? 0,
                    'wiseu_seq' => $seq,
                    'receiver_name' => $receiver_name,
                    'receiver_email' => $receiver_email,
                    'subject' => $subject,
                    'contents' => $body,
                ]);
            }

            DB::commit();

            return 'suc';
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    // 메일 발송 상태 업데이트
    public function mailSendStatusUpdate()
    {
        $mailConfig = $this->mailConfig;

        $whereIn = MailSend::where('status', 'R')->whereRaw('DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)')->limit(1000)->pluck('wiseu_seq');

        if (!$whereIn->isEmpty()) {
            $wiseuLog = WiseUMailLog::where('ECARE_NO', $mailConfig['eCareNo'])->whereIn('CUSTOMER_KEY', $whereIn)->get();

            foreach ($wiseuLog as $row) {
                $code = $row->ERROR_CD;

                if (!empty($mailConfig['code'][$code])) {
                    $mail_send = MailSend::where('wiseu_seq', $row->CUSTOMER_KEY)->first();

                    if ($code === '250' || $code === '000') {
                        $mail_send->status = 'S';
                    } else {
                        $mail_send->status = 'F';
                    }

                    $mail_send->status_msg = $mailConfig['code'][$code];
                    $mail_send->update();
                }
            }
        }
    }
}
