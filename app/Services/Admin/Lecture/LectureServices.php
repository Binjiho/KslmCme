<?php

namespace App\Services\Admin\Lecture;

use App\Models\Lecture;
use App\Models\EduLecList;
use App\Exports\LectureExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;

/**
 * Class LectureServices
 * @package App\Services
 */
class LectureServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Lecture::orderByDesc('created_at');
        $query->where('del', '=', 'N');

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->field) {
            $query->where(function ($q) use ($request) {
                foreach ($request->field as $fval) {
                    $q->orWhere('field', 'like', "%{$fval}%");
                }
            });
        }
        if($request->search && $request->keyword) {
            $query->where($request->search, 'like', "%{$request->keyword}%");
//            $query->whereHas('user',function ($query) use ($request) {
//
//            });
        }

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['total'] = $query->count();
            $this->data['collection'] = $query->lazy();
            return (new CommonServices())->excelDownload(new LectureExcel($this->data), date('Y-m-d').'_강의정보');
        }

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['lecture'] = Lecture::where(['sid'=>$request->sid])->first();

        return $this->data;
    }
    public function viewService(Request $request)
    {
        $query = EduLecList::orderByDesc('created_at');
        $query->where(['del'=>'N','lsid'=>$request->lsid]);

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);
        
        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'file-delete':
                return $this->fileDelete($request);
            case 'lecture-create':
                return $this->lectureCreate($request);
            case 'lecture-update':
                return $this->lectureUpdate($request);
            case 'lecture-delete':
                return $this->lectureDelete($request);
            case 'change-hide':
                return $this->changeHide($request);
            default:
                return notFoundRedirect();
        }
    }

    private function fileDelete(Request $request)
    {
        $this->transaction();

        try {
            if($request->fileType == 'pdf_file'){
                $lec = Lecture::where('realfile1','=',$request->filePath)->first();
                (new CommonServices())->fileDeleteService($lec->realfile1);
                $lec->filename1 = '';
                $lec->realfile1 = '';
                $lec->update();
            }else if($request->fileType == 'item_file'){
                $lec = Lecture::where('realfile2','=',$request->filePath)->first();
                (new CommonServices())->fileDeleteService($lec->realfile2);
                $lec->filename2 = '';
                $lec->realfile2 = '';
                $lec->update();
            }

            $this->dbCommit('강의자료 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '파일이 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }

    private function lectureCreate(Request $request)
    {
        $this->transaction();

        try {
            $lec = (new Lecture());
            $lec->setByData($request);
            $lec->save();

            $this->dbCommit('강의정보 등록 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '강의정보가 등록 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
    private function lectureUpdate(Request $request)
    {
        $this->transaction();

        try {
            $lec = Lecture::findOrFail($request->sid);
            $lec->setByData($request);
            $lec->update();

            $this->dbCommit('강의정보 수정 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '강의정보가 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function lectureDelete(Request $request)
    {
        $this->transaction();

        try {
            $lec = Lecture::findOrFail($request->sid);
            $lec->del = 'Y';
            $lec->deleted_at = date('Y-m-d H:i:s');
            $lec->update();

            $this->dbCommit('강의정보 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '강의정보가 삭제 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
    private function changeHide(Request $request)
    {
        $this->transaction();

        try {
            $lec = Lecture::findOrFail($request->sid);
            $lec->hide = $request->target;
            $lec->update();

            $this->dbCommit('강의정보 노출 변경 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '노출여부가 변경되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}
