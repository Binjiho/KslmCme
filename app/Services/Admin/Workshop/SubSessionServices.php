<?php

namespace App\Services\Admin\Workshop;

use App\Models\Workshop;
use App\Models\Session;
use App\Models\SubSession;
//use App\Exports\SessionExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Class SessionServices
 * @package App\Services
 */
class SubSessionServices extends AppServices
{
    public function indexService(Request $request)
    {
        $this->data['session'] = Session::where(['wsid'=>$request->wsid,'reg_num'=>$request->reg_num])->first();
        $this->data['sub_session'] = SubSession::where(['wsid'=>$request->wsid,'reg_num'=>$request->reg_num])->get();
        return $this->data;
    }

    public function collectiveService(Request $request)
    {
        $this->data['workshop'] = Workshop::where(['sid'=>$request->wsid])->first();
        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'subsession-update':
                return $this->subsessionUpdate($request);
            case 'subsession-delete':
                return $this->subsessionDelete($request);
            case 'change-hide':
                return $this->changeHide($request);
            case 'change-sort':
                return $this->changeSort($request);
            case 'collective-create':
                return $this->collectiveCreate($request);
            case 'file-delete':
                return $this->fileDelete($request);
            default:
                return notFoundRedirect();
        }
    }
    private function subsessionCreate(Request $request)
    {
        $this->transaction();

        try {
            $subsession = (new Session());

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

            $subsession->setByData($request);
            $subsession->save();

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
    private function subsessionUpdate(Request $request)
    {
        $this->transaction();

        try {
            foreach ($request->field_arr as $key => $val){
                $subsession = SubSession::findOrFail($key);
                $request->merge([ 'field' => $request->field_arr[$key] ]);
                $request->merge([ 'title' => $request->title_arr[$key] ]);
                $request->merge([ 'pname' => $request->pname_arr[$key] ]);
                $request->merge([ 'psosok' => $request->psosok_arr[$key] ]);
                $request->merge([ 'video_link' => $request->video_link_arr[$key] ]);
                $request->merge([ 'file_key' => $key ]);
                $subsession->setByData($request);
                $subsession->update();
            }

            $this->dbCommit('서브세션정보 수정 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '서브세션정보가 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function subsessionDelete(Request $request)
    {
        $this->transaction();

        try {
            $subsession = SubSession::findOrFail($request->sid);
            $subsession->del = 'Y';
            $subsession->deleted_at = date('Y-m-d H:i:s');
            $subsession->update();

            $this->dbCommit('서브세션정보 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '서브세션정보가 삭제 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
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
                $subsession = Session::findOrFail($item);
                $subsession->sort = $idx+1;
                $subsession->update();
            }

            $this->dbCommit('서브세션 순서 업데이트 [어드민]');

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
            $data = json_decode($request->data ?? [], true);
            
            foreach ($data as $index => $item) {
                $item['wsid'] = $request->wsid;

                $subsession = (new SubSession());

                $item['field'] = explode(",",$item['tmp_field']);

                $subsession->setByData($item);
                $subsession->save();

            }

            $this->dbCommit('서브세션 다중 등록 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '등록 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function fileDelete(Request $request)
    {
        $this->transaction();

        try {
            if($request->fileType == 'absfile'){
                $board = SubSession::where('abs_realfile','=',$request->filePath)->first();
                (new CommonServices())->fileDeleteService($board->abs_realfile);
                $board->abs_filename = '';
                $board->abs_realfile = '';
            }else{
                $board = SubSession::where('realfile','=',$request->filePath)->first();
                (new CommonServices())->fileDeleteService($board->realfile);
                $board->filename = '';
                $board->realfile = '';
            }

            $board->update();

            $this->dbCommit('Subsession 파일 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '파일이 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
}
