<?php

namespace App\Services\Admin\Education;

use App\Models\Education;
use App\Models\Sac;
use App\Exports\EducationExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;

/**
 * Class EducationServices
 * @package App\Services
 */
class EducationServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Education::where('del', '=', 'N');

        if ($request->sort) {
            $query->orderBy($request->sort, $request->ord);
        }else{
            $query->orderByDesc('created_at');
        }

        if ($request->category) {
            $query->where('category', 'like', "%{$request->category}%");
        }
        if ($request->gubun) {
            $query->where('gubun', 'like', "%{$request->gubun}%");
        }
        if ($request->hide) {
            $query->where('hide', 'like', "%{$request->hide}%");
        }
        if ($request->title) {
            $query->where('title', 'like', "%{$request->title}%");
        }

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['total'] = $query->count();
            $this->data['collection'] = $query->lazy();
            return (new CommonServices())->excelDownload(new EducationExcel($this->data), date('Y-m-d').'_교육정보');
        }

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['education'] = Education::where(['sid'=>$request->sid])->first();

        $query = Education::where(['del'=>'N']);
        if(!empty($request->sid)){
            $query->whereNotin('sid',[$request->sid]);
        }
        $this->data['pre_edu_list'] = $query->get();

        return $this->data;
    }

    public function payInfoService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->sid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'file-delete':
                return $this->fileDelete($request);
            case 'education-create':
                return $this->educationCreate($request);
            case 'education-update':
                return $this->educationUpdate($request);
            case 'education-delete':
                return $this->educationDelete($request);
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

    private function educationCreate(Request $request)
    {
        $this->transaction();
        
        try {
            $edu = (new Education());
            $edu->setByData($request);
            $edu->save();

            $this->dbCommit('교육정보 등록 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '교육정보가 등록 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
    private function educationUpdate(Request $request)
    {
        $this->transaction();

        try {
            $edu = Education::findOrFail($request->sid);
            $edu->setByData($request);
            $edu->update();

            $this->dbCommit('교육정보 수정 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '교육정보가 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function educationDelete(Request $request)
    {
        $this->transaction();

        try {
            $edu = Education::findOrFail($request->sid);
            $edu->del = 'Y';
            $edu->deleted_at = date('Y-m-d H:i:s');
            $edu->update();

            $this->dbCommit('교육정보 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '교육정보가 삭제 되었습니다.',
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
}
