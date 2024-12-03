<?php

namespace App\Services\Education;

use App\Models\Education;
use App\Models\Sac;
use App\Models\Heart;
//use App\Exports\EducationExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use App\Services\MailRealSendServices;

use Illuminate\Http\Request;

/**
 * Class EducationServices
 * @package App\Services
 */
class EducationServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Education::orderByDesc('created_at')->with(['lectures']);
        $query->where('del', '=', 'N');

        if ( !isDev() ) {
            $query->where('hide', '=', 'N');
        }

        if ($request->category) {
            $query->whereIn('category', $request->category);
        }
        if ($request->gubun) {
            $query->whereIn('gubun', $request->gubun);
        }
        if ($request->field) {
            $query->WhereHas('lectures', function ($query) use ($request) {
                foreach($request->field as $fkey => $fval){
                    if ($fkey == 0){
                        $query->where('field', 'like', "%{$fval}%");
                    }else{
                        $query->orWhere('field', 'like', "%{$fval}%");
                    }
                }
            });
        }
        if ($request->search_key) {
            $query->where(function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search_key}%")     //교육명
                ->orWhere('contents', 'like', "%{$request->search_key}%");     //교육내용
            })
            ->orWhereHas('lectures', function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search_key}%")     //강의명
                ->orWhere('name_kr', 'like', "%{$request->search_key}%")       //강사이름
                ->orWhere('sosok_kr', 'like', "%{$request->search_key}%");     //강사소속
            });
        }

        $list = $query->paginate(12);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function detailService(Request $request)
    {
        $this->data['education'] = Education::findOrFail($request->esid);
        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['education'] = Education::findOrFail($request->esid);
        $this->data['user'] = thisUser();

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'file-delete':
                return $this->fileDelete($request);
            case 'change-hide':
                return $this->changeHide($request);
            case 'change-heart':
                return $this->changeHeart($request);
            case 'education-check':
                return $this->educationCheck($request);
            case 'sac-check':
                return $this->sacCheck($request);
            case 'sac-create':
                return $this->sacCreate($request);
            default:
                return notFoundRedirect();
        }
    }

    private function fileDelete(Request $request)
    {
        $this->transaction();

        try {
            $board = Education::where('realfile','=',$request->filePath)->first();
            (new CommonServices())->fileDeleteService($board->realfile);
            $board->filename = '';
            $board->realfile = '';
            $board->update();

            $this->dbCommit('교육 파일 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '파일이 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
    private function changeHide(Request $request)
    {
        $this->transaction();

        try {
            $edu = Education::findOrFail($request->sid);
            $edu->hide = $request->target;
            $edu->update();

            $this->dbCommit('교육정보 노출 변경 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '노출여부가 변경되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
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

            $this->dbCommit('교육정보 하트 신청');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => $target_msg,
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e,true);
        }
    }

    private function educationCheck(Request $request){
        $edu = Education::findOrFail($request->esid);
        //1.수강조건 체크
        if(!empty($edu->condition_yn) && $edu->condition_yn=='Y'){
            $com_yn = Sac::where(['user_sid'=>thisPk(), 'complete_yn'=>'Y'])->first();
            if(!$com_yn){
                return $this->returnJsonData('result', [
                    'res' => 'notCondition',
                    'msg' => $edu->selfEducation($edu->pre_esid)->title.' 교육 이수 후 신청 가능한 교육입니다.',
                ]);
            }
        }
        //2.이미 신청한 수강인지 체크
        $already_yn = Sac::where(['user_sid'=>thisPk(), 'esid'=>$request->esid, 'del'=>'N'])->first();
        if($already_yn){
            return $this->returnJsonData('result', [
                'res' => 'notCondition',
                'msg' => '이미 신청한 교육입니다.',
            ]);
        }
        //3.신청 기한인지 체크
        if ( $edu->regist_limit_yn == 'N'){
            if( date('Y-m-d') < $edu->regist_sdate->format('Y-m-d') ){
                return $this->returnJsonData('result', [
                    'res' => 'notCondition',
                    'msg' => '교육 신청가능 기간이 아닙니다.',
                ]);
            }
        }else{
            if(  ( date('Y-m-d') < $edu->regist_sdate->format('Y-m-d') || date('Y-m-d') > $edu->regist_edate->format('Y-m-d') ) ){
                return $this->returnJsonData('result', [
                    'res' => 'notCondition',
                    'msg' => '교육 신청가능 기간이 아닙니다.',
                ]);
            }
        }

        return $this->returnJsonData('result', [
            'res' => 'enoughCondition',
            'msg' => '수강 신청 가능한 교육입니다.',
        ]);
    }

    private function sacCheck(Request $request)
    {
        $this->transaction();

        try {
            $edu = Education::findOrFail($request->esid);
            //2.유료/무료 체크
            if(!empty($edu->free_yn) && $edu->free_yn=='Y'){

                $sac = (new Sac());
                $request->merge([ 'user_sid' => thisPk() ]);
                $request->merge([ 'esid' => $request->esid ]);
                $request->merge([ 'pay_method' => 'F' ]);
                $request->merge([ 'pay_status' => 'F' ]);
                $sac->setByData($request);
                $sac->save();

                $user = thisUser();

                //메일 발송
                $mailData = [
                    'receiver_name' => $user->name_kr,
                    'receiver_email' => $user->uid,
                    'body' => view("template.sac-create", ['user' => $user, 'education'=>$edu, 'sac'=>$sac])->render(),
                    'etc' => $sac,
                ];

                $mailResult = (new MailRealSendServices())->mailSendService($mailData, 'sac-create');

                if ($mailResult != 'suc') {
                    return $mailResult;
                }
                // END 메일발송

                $this->dbCommit('교육신청-무료 생성');

                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => '교육신청이 완료되었습니다. 신청된 교육은 나의 강의실 >교육수강현황 메뉴에서 수강 가능합니다.',
                    'location' => $this->ajaxActionLocation('reload'),
                ]);

            }else{

                return $this->returnJsonData('popOpen', [
                    'case' => true, //체크용으로 남겨놓는거 app.common.js에서 확인가능
                    'popOpen' => $this->ajaxActionPopOpen(route('education.detail.upsert',['esid'=>$request->esid]),'education-upsert','700','800'),
                ]);
            }

        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function sacCreate(Request $request)
    {
        $this->transaction();

        try {
            $edu = Education::findOrFail($request->esid);
            $user = thisUser();

            $sac = (new Sac());
            if($edu->quiz_yn == 'Y'){
                $request->merge([ 'quiz_status' => 'U' ]);
            }
            if($edu->survey_yn == 'Y'){
                $request->merge([ 'survey_status' => 'U' ]);
            }
            if($edu->free_yn !== 'Y'){
                $request->merge([ 'tot_pay' => $edu->cost ]);
            }
            $sac->setByData($request);
            $sac->save();


            //메일 발송
            $mailData = [
                'receiver_name' => $user->name_kr,
                'receiver_email' => $user->email,
                'body' => view("template.sac-create", ['user' => $user, 'sac'=>$sac])->render(),
                'etc'=>$sac,
            ];

            $mailResult = (new MailRealSendServices())->mailSendService($mailData, 'sac-create');

            if ($mailResult != 'suc') {
                return $mailResult;
            }
            // END 메일발송

            $this->dbCommit('교육신청-유료 생성');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '신청이 완료되었습니다. 신청된 교육은 마이페이지 > 온라인 강의실 메뉴에서 수강 및 열람 가능합니다.',
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
}
