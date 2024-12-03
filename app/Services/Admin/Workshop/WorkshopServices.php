<?php

namespace App\Services\Admin\Workshop;

use App\Models\Workshop;
use App\Models\WorkshopLog;
use App\Models\EduLecList;
use App\Exports\WorkshopExcel;
use App\Exports\WorkshopLogExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Class WorkshopServices
 * @package App\Services
 */
class WorkshopServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Workshop::orderByDesc('created_at');
        $query->where('del', '=', 'N');

        if ($request->category) {
            $query->where('category', 'like', "%{$request->category}%");
        }
        if ($request->gubun) {
            $query->where('gubun', 'like', "%{$request->gubun}%");
        }
        if($request->search && $request->keyword) {
            $query->where($request->search, 'like', "%{$request->keyword}%");
        }

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['total'] = $query->count();
            $this->data['collection'] = $query->lazy();
            return (new CommonServices())->excelDownload(new WorkshopExcel($this->data), date('Y-m-d').'_학술행사정보');
        }

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['workshop'] = Workshop::where(['sid'=>$request->sid])->first();

        return $this->data;
    }
    public function viewService(Request $request)
    {
        $this->data['workshop'] = Workshop::where(['sid'=>$request->wsid])->first();
        
        return $this->data;
    }

    public function logService(Request $request)
    {
        $query = WorkshopLog::orderByDesc('updated_at');
        $query->where('del', '=', 'N');

        $query->where(function ($q) use ($request) {

            if ($request->sdate) {
                $q->where('updated_at','>=',$request->sdate);
            }
            if ($request->edate) {
                $q->where('updated_at','<=',$request->edate.' 23:59:59');
            }
            if ($request->log_type) {
                $q->where('log_type','=',$request->log_type);
            }

            $q->whereHas('workshop', function ($q2) use ($request) {
                $q2->where(function ($q3) use ($request) {
                    // category
                    if ($request->category) {
                        $q3->where(['category'=>$request->category]);
                    }
                    // gubun
                    if ($request->gubun) {
                        $q3->where(['gubun'=>$request->gubun]);
                    }
                    //search
                    if ($request->search && $request->keyword) {
                        $q3->where(function ($q4) use ($request) {
                            $q4->where($request->search, 'like', "%{$request->keyword}%");
                        });
                    }
                });
            });

            $q->whereHas('sub', function ($q2) use ($request) {
                if ($request->field) {
                    $q2->where(function ($q3) use ($request) {
                        foreach ($request->field as $fval) {
                            $q3->orWhere('field', 'like', "%{$fval}%");
                        }
                    });
                }
            });

        });

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['total'] = $query->count();
            $this->data['collection'] = $query->lazy();
            return (new CommonServices())->excelDownload(new WorkshopLogExcel($this->data), date('Y-m-d').'_자료열람로그정보');
        }

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'workshop-create':
                return $this->workshopCreate($request);
            case 'workshop-update':
                return $this->workshopUpdate($request);
            case 'workshop-delete':
                return $this->workshopDelete($request);
            case 'change-hide':
                return $this->changeHide($request);
            case 'file-delete':
                return $this->fileDelete($request);
            default:
                return notFoundRedirect();
        }
    }
    private function workshopCreate(Request $request)
    {
        $this->transaction();

        try {
            $workshop = (new Workshop());

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

            if($request->date_type=='D'/*하루행사*/){
                $endDate = $startDate->copy();
            }

            // 시작 날짜가 끝 날짜와 같거나 작을 때까지 반복
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                // 날짜를 Y-m-d 형식으로 배열에 추가
                $dates[] = $date->format('Y-m-d');
            }

            foreach($dates as $key => $val){
                $date_arr[] = $val;
            }

            if($request->poster_yn == 'Y'){
                $date_arr[] = 'P';
            }
            
            $request->merge([ 'date' => $date_arr ]);

            $workshop->setByData($request);
            $workshop->save();

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
    private function workshopUpdate(Request $request)
    {
        $this->transaction();

        try {
            $workshop = Workshop::findOrFail($request->sid);

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

            if($request->date_type=='D'/*하루행사*/){
                $endDate = $startDate->copy();
            }

            // 시작 날짜가 끝 날짜와 같거나 작을 때까지 반복
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                // 날짜를 Y-m-d 형식으로 배열에 추가
                $dates[] = $date->format('Y-m-d');
            }

            foreach($dates as $key => $val){
                $date_arr[] = $val;
            }

            if($request->poster_yn == 'Y'){
                $date_arr[] = 'P';
            }

            $request->merge([ 'date' => $date_arr ]);

            $workshop->setByData($request);
            $workshop->update();

            $this->dbCommit('학술행사정보 수정 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '학술행사정보가 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function workshopDelete(Request $request)
    {
        $this->transaction();

        try {
            $workshop = Workshop::findOrFail($request->sid);
            $workshop->del = 'Y';
            $workshop->deleted_at = date('Y-m-d H:i:s');
            $workshop->update();

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
            $workshop = Workshop::findOrFail($request->sid);
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

    private function fileDelete(Request $request)
    {
        $this->transaction();

        try {
            if($request->fileType == 'absfile'){
                $board = Workshop::where('abs_realfile','=',$request->filePath)->first();
                (new CommonServices())->fileDeleteService($board->abs_realfile);
                $board->abs_filename = '';
                $board->abs_realfile = '';
            }else if($request->fileType == 'bookfile'){
                $board = Workshop::where('book_realfile','=',$request->filePath)->first();
                (new CommonServices())->fileDeleteService($board->book_realfile);
                $board->book_filename = '';
                $board->book_realfile = '';
            }else if($request->fileType == 'bookfile2'){
                $board = Workshop::where('book_realfile2','=',$request->filePath)->first();
                (new CommonServices())->fileDeleteService($board->book_realfile2);
                $board->book_filename2 = '';
                $board->book_realfile2 = '';
            }else{
                $board = Workshop::where('realfile','=',$request->filePath)->first();
                (new CommonServices())->fileDeleteService($board->realfile);
                $board->filename = '';
                $board->realfile = '';
            }
            $board->update();

            $this->dbCommit('Workshop 파일 삭제 [어드민]');

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
