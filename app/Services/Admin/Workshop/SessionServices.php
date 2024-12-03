<?php

namespace App\Services\Admin\Workshop;

use App\Models\Workshop;
use App\Models\Session;
//use App\Exports\SessionExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Class SessionServices
 * @package App\Services
 */
class SessionServices extends AppServices
{
    public function indexService(Request $request)
    {
        $workshop = Workshop::findOrFail($request->wsid);

        $query = Session::orderBy('sid','asc');
        $query->where('del', '=', 'N');
        $query->where('wsid', '=', $request->wsid);

        if ($request->field) {
            $query->whereHas('sub_session', function ($q) use ($request) {

                if ($request->wsid !== null) {
                    $q->where('wsid', $request->wsid);
                } else {
                    $q->whereNull('wsid');
                }

                $q->where(function ($q2) use ($request) {
                    foreach ($request->field as $fval) {
                        $q2->orWhere('field', 'like', "%{$fval}%");
                    }
                });
            });
        }


        if($request->search && $request->keyword) {
            $query->where($request->search, 'like', "%{$request->keyword}%");
        }

        if($request->date_tab){
            if($request->date_tab == 'ALL'){

            }else{
                $query->where(['date'=>$request->date_tab]);
            }
        }
//        else{
//            $query->where(['date'=>0]);
//        }

        /* 241119
        if($request->room_tab){
            $query->where(['room'=>$request->room_tab]);
        } else {
            $query->where(['room'=>0]);
        }
        */

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['total'] = $query->count();
            $this->data['collection'] = $query->lazy();
            return (new CommonServices())->excelDownload(new SessionExcel($this->data), '학술행사정보');
        }

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        $this->data['workshop'] = Workshop::where(['sid'=>$request->wsid])->first();

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['workshop'] = Workshop::where(['sid'=>$request->wsid])->first();
        $this->data['session'] = Session::where(['sid'=>$request->sid])->first();
        return $this->data;
    }
     public function collectiveService(Request $request)
        {
            $this->data['workshop'] = Session::where(['sid'=>$request->sid])->first();
            return $this->data;
        }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'session-create':
                return $this->sessionCreate($request);
            case 'session-update':
                return $this->sessionUpdate($request);
            case 'session-delete':
                return $this->sessionDelete($request);
            case 'change-hide':
                return $this->changeHide($request);
            case 'change-sort':
                return $this->changeSort($request);
            case 'collective-create':
                return $this->collectiveCreate($request);
            default:
                return notFoundRedirect();
        }
    }
    private function sessionCreate(Request $request)
    {
        $this->transaction();

        try {
            $session = (new Session());

            /**
             * rooms 배열
             */
            /* 241119
            $rooms = array();
            foreach($request->rooms as $key => $val){
                $rooms[] = $val;
            }
            $request->merge([ 'room' => $rooms ]);
            */

            /**
             * date 배열
             */
            // 날짜 배열을 저장할 빈 배열 생성
            $dates = array();
            $date_arr = array();
            // 사용자가 보낸 시작 날짜와 끝 날짜를 받아옵니다
            $startDate = Carbon::parse($request->sdate);
            $endDate = Carbon::parse($request->edate);

            // 시작 날짜가 끝 날짜와 같거나 작을 때까지 반복
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                // 날짜를 Y-m-d 형식으로 배열에 추가
                $dates[] = $date->format('Y-m-d');
            }

            foreach($dates as $key => $val){
                $date_arr[] = $val;
            }
            $request->merge([ 'date' => $date_arr ]);

            $session->setByData($request);
            $session->save();

            $this->dbCommit('학술행사정보 등록 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '학술행사정보가 등록 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
    private function sessionUpdate(Request $request)
    {
        $this->transaction();

        try {
            $session = Session::findOrFail($request->sid);
            $session->setByData($request);
            $session->update();

            $this->dbCommit('세션정보 수정 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '세션정보가 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function sessionDelete(Request $request)
    {
        $this->transaction();

        try {
            $session = Session::findOrFail($request->sid);
            $session->del = 'Y';
            $session->deleted_at = date('Y-m-d H:i:s');
            $session->update();

            $this->dbCommit('학술행사정보 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '학술행사정보가 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
    private function changeHide(Request $request)
    {
        $this->transaction();

        try {
            $workshop = Session::findOrFail($request->sid);
            $workshop->hide = $request->target;
            $workshop->update();

            $this->dbCommit('학술행사정보 노출 변경 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '노출여부가 변경되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function changeSort(Request $request)
    {
        $this->transaction();
        try {
            $sid_arr = explode(',',$request->array_sid);
            foreach ($sid_arr as $idx => $item){
                $session = Session::findOrFail($item);
                $session->sort = $idx+1;
                $session->update();
            }

            $this->dbCommit('세션 순서 업데이트 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '순서가 수정되었습니다',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
    private function collectiveCreate(Request $request)
    {
        $this->transaction();

        try {
            $workshop = Workshop::findOrFail($request->wsid);

            $data = json_decode($request->data ?? [], true);

            foreach ($data as $index => $item) {
                $item['wsid'] = $request->wsid;

                //기존에 등록된 세션번호 arr
                $already_regnum = Session::where(['del'=>'N','wsid'=>$request->wsid])->pluck('reg_num')->toArray();

                if(in_array($item['reg_num'], $already_regnum)){
                    return $this->returnJsonData('alert', [
                        'case' => true,
                        'msg' => '세션번호'.$item['reg_num'].'은 이미 등록된 세션번호 입니다.',
                        'location' => $this->ajaxActionLocation('reload'),
                    ]);
                }

                $session = (new Session());
                /**
                 * date 배열
                 */
                $isDate = $isRoom = false;
                foreach($workshop->date as $d_key => $d_val){
                    if($d_val == $item['tmp_date']) {
                        $isDate = true;
                        $dateKey = $d_key;
                    }
                }

                if($isDate) {
                    $item['date'] = $dateKey;
                }else{
                    return $this->returnJsonData('alert', [
                        'case' => true,
                        'msg' => $item['tmp_date'].' 잘못된 날짜 정보입니다.',
                        'location' => $this->ajaxActionLocation('reload'),
                    ]);
                }

                /* 241119
                foreach($workshop->room as $r_key => $r_val){
                    if($r_val == $item['tmp_room']) {
                        $isRoom = true;
                        $roomKey = $r_key;
                    }
                }
                if($isRoom) {
                    $item['room'] = $roomKey;
                }else{
                    return $this->returnJsonData('alert', [
                        'case' => true,
                        'msg' => $item['tmp_room'].' 잘못된 ROOM 정보입니다.',
                        'location' => $this->ajaxActionLocation('reload'),
                    ]);
                }
                */

                $tot_count = Session::where(['del'=>'N'])->count();
//                $request->merge([ 'sort' => $tot_count+1 ]);
                $item['sort'] = $tot_count+1;

                $session->setByData($item);
                $session->save();

            }

            $this->dbCommit('세션 다중 등록 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '등록 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}
