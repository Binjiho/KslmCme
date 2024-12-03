<?php

namespace App\Services\Workshop;

use App\Models\Workshop;
use App\Models\WorkshopLog;
use App\Models\Session;
use App\Models\SubSession;
use App\Models\Heart;
//use App\Exports\WorkshopExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use App\Services\MailRealSendServices;

use Illuminate\Http\Request;

/**
 * Class WorkshopServices
 * @package App\Services
 */
class WorkshopServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query_search = Workshop::where('del', '=', 'N')->where('hide', '=', 'N')->with(['sub_session']);

        $query = SubSession::where('del','N')
        ->where(function ($q) use ($request) {
            // 조건 1: field가 있을 경우
            if ($request->field) {
                $q->where(function ($q2) use ($request) {
                    foreach ($request->field as $fval) {
                        $q2->orWhere('field', 'like', "%{$fval}%");
                    }
                });
            }
            // 조건 2: search_key가 있을 경우 SubSession 필드에서 검색
            if ($request->search_key) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('title', 'like', "%{$request->search_key}%")
                        ->orWhere('pname', 'like', "%{$request->search_key}%")
                        ->orWhere('psosok', 'like', "%{$request->search_key}%");
                });
            }

            // 조건 3: Workshop 관계에서 검색
            $q->orWhereHas('workshop', function ($q2) use ($request) {
                $q2->where('hide', '=', 'N')->where(function ($q3) use ($request) {
                    // field
                    if ($request->field) {
                        $q3->where(function ($q4) use ($request) {
                            foreach ($request->field as $fval) {
                                $q4->orWhere('field', 'like', "%{$fval}%");
                            }
                        });
                    }
                    // category
                    if ($request->category) {
                        $q3->where(['category'=>$request->category]);
                    }
                    // gubun
                    if ($request->gubun) {
                        $q3->where(['gubun'=>$request->gubun]);
                    }
                    //search_key
                    if ($request->search_key) {
                        $q3->where(function ($q4) use ($request) {
                            $q4->where('title', 'like', "%{$request->search_key}%")
                                ->orWhere('place', 'like', "%{$request->search_key}%");
                        });
                    }
                });
            });
        });


        if($request->sort == 'asc'){ //최신순 행사 시작일 기준 내림차순 정렬
            $query_search->orderBy('sdate', 'asc');

            $query->with(['workshop' => function ($q) {
                $q->orderBy('sdate', 'asc');
            }]);
        }else{ // 오래된순 행사 시작일 기준 오름차순 정렬
            $query_search->orderBy('sdate', 'desc');

            $query->with(['workshop' => function ($q) {
                $q->orderBy('sdate', 'desc');
            }]);
        }

        $isRequest = false;
        if($request->field){
            $isRequest = true;
        }
        if($request->category){
            $isRequest = true;
        }
        if($request->gubun){
            $isRequest = true;
        }
        if($request->search_key){
            $isRequest = true;
        }

        if($isRequest){
            $this->data['total'] = $query->count();
            $list = $query->paginate(12);
        }else{
            $this->data['total'] = $query_search->count();
            $list = $query_search->paginate(12);
        }
        $this->data['list'] = setListSeq($list);
        $this->data['isRequest'] = $isRequest;

        return $this->data;
    }

    public function detailService(Request $request)
    {
        $this->data['workshop'] = Workshop::findOrFail($request->wsid);

        $query = SubSession::orderBy('sort','asc')->with([
            'workshop',
            'session' => function ($q) use ($request) {
                $q->where('wsid', $request->wsid) // 원하는 조건
                ->orderBy('reg_num', 'asc');
            }
        ]);
        $query->where('del', '=', 'N');
        $query->where('wsid', '=', $request->wsid);

        if ($request->field) {
            $query->where(function ($q) use ($request) {
                foreach ($request->field as $fval) {
                    $q->orWhere('field', 'like', "%{$fval}%");
                }
            });
        }

        if ($request->search_key) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search_key}%")   //강의명
                ->orWhere('pname', 'like', "%{$request->search_key}%")       //강사이름
                ->orWhere('psosok', 'like', "%{$request->search_key}%");     //강사소속
            });
//            ->orWhereHas('workshop', function ($query) use ($request) {
//                $query->where('title', 'like', "%{$request->search_key}%")   //교육명
//                ->orWhere('place', 'like', "%{$request->search_key}%");      //교육소개
//            });
        }

        if ($request->search_key2) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search_key2}%")
                ->orWhere('pname', 'like', "%{$request->search_key2}%")
                ->orWhere('psosok', 'like', "%{$request->search_key2}%");
            });
        }

        if(isset($request->date_tab)){
            if($request->date_tab == 'ALL'){

            }else{
                $query->WhereHas('session', function ($q) use ($request) {
                    $q->where(['date'=>$request->date_tab,'wsid'=>$request->wsid]);
                });
            }
        }

        $this->data['sub_sessions'] = $query->get();

        $tmp_arr = array();
        foreach ($this->data['sub_sessions'] as $sub_session){
//            $tmp_arr[$sub_session->session->room][$sub_session->session->sid][] = $sub_session;
            if(!empty($sub_session->session->sid)){
                $tmp_arr[$sub_session->session->sid][] = $sub_session;
            }
        }
        $this->data['session_arr'] = $tmp_arr;

        return $this->data;
    }

    public function popupService(Request $request)
    {
        $this->data['sub_session'] = SubSession::findOrFail($request->sid);

        /**
         * 영상 시청 시, admin이 아닌 경우 workshop_log 생성
         */
        if(checkUrl()=='web'){
            $this->createWorkshopLogService($request);
        }

        return $this->data;
    }

    private function createWorkshopLogService(Request $request)
    {
        $this->transaction();

        try {
            $sub_session = SubSession::findOrFail($request->sid);

            $workshop_log = (new WorkshopLog());

            $request->merge([ 'user_sid' => thisPk() ]);
            $request->merge([ 'wsid' => $sub_session->wsid ]);
            $request->merge([ 'sub_sid' => $request->sid ]);
            $request->merge(['log_type' => 'V']);

            $workshop_log->setByData($request);
            $workshop_log->save();

            $this->dbCommit('자료 로그 생성 [동영상]');

//            $existed_log = WorkshopLog::where(['del'=>'N', 'user_sid'=>thisPK(), 'wsid'=>$sub_session->wsid, 'sub_sid' => $request->sid ])->first();
//            if($existed_log){
//                $existed_log->updated_at = time();
//                $existed_log->update();
//
//                $this->dbCommit('자료 로그 갱신 [동영상]');
//            }else{
//                $workshop_log = (new WorkshopLog());
//
//                $request->merge([ 'user_sid' => thisPk() ]);
//                $request->merge([ 'wsid' => $sub_session->wsid ]);
//                $request->merge([ 'sub_sid' => $request->sid ]);
//                $request->merge(['log_type' => 'V']);
//
//                $workshop_log->setByData($request);
//                $workshop_log->save();
//
//                $this->dbCommit('자료 로그 생성 [동영상]');
//            }

            return true;
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
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
            default:
                return notFoundRedirect();
        }
    }

    private function fileDelete(Request $request)
    {
        $this->transaction();

        try {
            $board = Workshop::where('realfile','=',$request->filePath)->first();
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
            $edu = Workshop::findOrFail($request->sid);
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
            $heart = Heart::where(['user_sid'=>thisPK(), 'wsid'=>$request->wsid, 'del'=>'N'])->first();
            if($heart){
                $heart->delete();
                $target_msg = "관심자료 설정이 해제되었습니다.";
            }else{
                $heart = (new Heart());
                $request->merge([ 'user_sid' => thisPk() ]);
                $heart->setByData($request);
                $heart->save();
                $target_msg = "관심자료로 설정되었습니다. 나의 자료실에서 확인하실 수 있습니다.";
            }

            $this->dbCommit('자료정보 하트 신청');

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
