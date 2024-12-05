<?php

namespace App\Services\Mypage;

use App\Models\User;
use App\Models\Sac;
use App\Models\Heart;
use App\Services\AppServices;
use App\Services\CommonServices;
use App\Services\Auth\AuthServices;
use App\Services\MailRealSendServices;

use Illuminate\Http\Request;

/**
 * Class MypageServices
 * @package App\Services
 */
class MypageServices extends AppServices
{
    public function indexService(Request $request)
    {
        $this->data['user'] = thisUser();
        return $this->data;
    }

//    public function upsertService(Request $request)
//    {
//        $this->data['user'] = thisUser();
//        $this->data['captcha'] = (new CommonServices())->captchaMakeService();
//
//        return $this->data;
//    }

    public function listService(Request $request)
    {
        $query = Sac::where(['sac_info.user_sid'=>thisPK(),'educations.del' => 'N'])
            ->join('educations', 'educations.sid', '=', 'sac_info.esid') // 'education' 테이블 조인
            ->select('sac_info.*', 'educations.sid as education_sid') // List other columns as needed
            ->withTrashed() // soft delete를 무시하고 조회!!!
            ->orderByDesc('sac_info.sid');

        $list = $query->paginate(10);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function receiptService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->ssid);
        return $this->data;
    }

    //이수증 출력
    public function certiListService(Request $request)
    {
//        $query = Sac::where(['user_sid'=>thisPK(),'del'=>'N','complete_yn' => 'Y'])
//            ->orderBy('sid','ASC');
        $query = Sac::where(['sac_info.del'=>'N','sac_info.user_sid'=>thisPK(), 'sac_info.complete_yn' => 'Y' ,'educations.del' => 'N' ,'educations.certi_yn' => 'Y'])
            ->join('educations', 'educations.sid', '=', 'sac_info.esid') // 'education' 테이블 조인
            ->select('sac_info.*', 'educations.sid as education_sid') // List other columns as needed
            ->orderByDesc('sac_info.sid');
        $list = $query->paginate(10);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function certiDetailService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->ssid);
        return $this->data;
    }

    //관심교육
    public function interestListService(Request $request)
    {
        $query = Heart::where(['del'=>'N','user_sid'=>thisPK(), 'type' => 'E'])
            ->orderByDesc('sid');
        $list = $query->paginate(10);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'sac-delete-free':
                return $this->sacDeleteFree($request);
            case 'sac-delete':
                return $this->sacDelete($request);
            case 'change-heart':
                return $this->changeHeart($request);
            default:
                return notFoundRedirect();
        }
    }

    private function sacDeleteFree(Request $request)
    {
        $this->transaction();

        try {
            $sac_info = Sac::findOrFail($request->sid);

            $sac_info->del_request = 'C'; //취소신청완료
            $sac_info->del_request_at = date('Y-m-d H:i:s');
            $sac_info->update();

            $sac_info->delete();

            $this->dbCommit('교육취소-무료 생성');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '취소 완료되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);

        } catch (\Exception $e) {
            return $this->dbRollback($e,true);
        }
    }

    private function sacDelete(Request $request)
    {
        $this->transaction();
        try {
            $sac_info = Sac::findOrFail($request->sid);

            $sac_info->bank_name = $request->bank_name;
            $sac_info->account_name = $request->account_name;
            $sac_info->account_no = $request->account_no;
//            $sac_info->pay_status = 'R'; //취소신청처리중
            $sac_info->del_request = 'I'; //취소신청
            $sac_info->del_request_at = date('Y-m-d H:i:s');
            $sac_info->update();

            //메일 발송
            $mailData = [
                'receiver_name' => $sac_info->user->name_kr,
                'receiver_email' => $sac_info->user->email,
                'body' => view("template.sac-cancle", ['sac_info'=>$sac_info])->render(),
                'etc' => $sac_info,
            ];

            $mailResult = (new MailRealSendServices())->mailSendService($mailData, 'sac-cancle');

            if ($mailResult != 'suc') {
                return $mailResult;
            }
            // END 메일발송

            $this->dbCommit('교육취소-유료 생성');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '교육취소 신청이 완료 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true)
            ]);

        } catch (\Exception $e) {
            return $this->dbRollback($e,true);
        }
    }

    private function changeHeart(Request $request)
    {
        $this->transaction();

        try {
            $heart = Heart::where(['user_sid'=>thisPK(), 'esid'=>$request->esid, 'del'=>'N'])->first();
            if($heart){
                $heart->delete();
                $target_msg = "관심교육 설정이 해제되었습니다.";
            }else{
                $heart = (new Heart());
                $request->merge([ 'user_sid' => thisPk() ]);
                $heart->setByData($request);
                $heart->save();
                $target_msg = "관심교육으로 설정되었습니다. 나의 강의실에서 확인하실 수 있습니다.";
            }

            $this->dbCommit('마이페이지-관심교육 하트 신청');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => $target_msg,
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e,true);
        }
    }
}
