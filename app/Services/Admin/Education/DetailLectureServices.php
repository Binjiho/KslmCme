<?php

namespace App\Services\Admin\Education;

use App\Models\Education;
use App\Models\Lecture;
use App\Models\EduLecList;
use App\Models\Sac;

use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;

/**
 * Class EducationServices
 * @package App\Services
 */
class DetailLectureServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = EduLecList::orderBy('sort','asc');
        $query->where(['del'=>'N','esid'=>$request->esid]);

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $query = Lecture::orderByDesc('created_at');
        $query->where('del', '=', 'N');

        if ($request->type) {
            $query->where('type', 'like', "%{$request->type}%");
        }
        if ($request->gubun) {
            $query->where('gubun', 'like', "%{$request->gubun}%");
        }
        if ($request->search_type) {
            $query->where($request->search_type, 'like', "%{$request->search_target}%");
        }

        $list = $query->paginate(999);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'lecture-create':
                return $this->lectureCreate($request);
            case 'lecture-delete':
                return $this->lectureDelete($request);
            case 'lecture-search':
                return $this->lectureSearch($request);
            case 'change-sort':
                return $this->changeSort($request);
            default:
                return notFoundRedirect();
        }
    }

    private function lectureCreate(Request $request)
    {
        $this->transaction();

        try {
            //이미 등록된 강의인지 확인
            $edu_lec = EduLecList::where(['del'=>'N', 'esid'=>$request->esid, 'lsid'=>$request->lsid])->first();
            if($edu_lec){
                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => '이미 등록된 강의 입니다.',
                    'location' => $this->ajaxActionLocation('reload'),
                ]);
            }else{
                $edu_lec = (new EduLecList());

                $tot_count = EduLecList::where(['del'=>'N'])->count();
                $request->merge([ 'sort' => $tot_count+1 ]);

                $edu_lec->setByData($request);
                $edu_lec->save();

                $this->dbCommit('교육-강의 등록 [어드민]');

                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => '강의가 등록 되었습니다.',
                    'parentsReload' => $this->ajaxActionLocation('reload'),
                ]);
            }

        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }

    private function lectureDelete(Request $request)
    {
        $this->transaction();

        try {
            $edu_lec = EduLecList::findOrFail($request->sid);
            $edu_lec->del = 'Y';
            $edu_lec->deleted_at = date('Y-m-d H:i:s');
            $edu_lec->update();

            $this->dbCommit('교육정보 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '교육정보가 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
    private function lectureSearch(Request $request)
    {
        $keyfield = $request->keyfield;
        $keyword = $request->keyword;

        $query = User::orderBy('name_kr');

        switch ($keyfield) {
            default:
                $query->where($keyfield, 'like', "%{$keyword}%");
                break;
        }

        $this->data['member'] = $query->get();

        return $this->returnJsonData('html', [
            $this->ajaxActionHtml('#member-result', view('admin.group.member.layer.member-result', $this->data)->render()),
        ]);
    }

    private function changeSort(Request $request)
    {
        $this->transaction();
        try {
            $sid_arr = explode(',',$request->array_sid);
            foreach ($sid_arr as $idx => $item){
                $edu_lec = EduLecList::findOrFail($item);
                $edu_lec->sort = $idx+1;
                $edu_lec->update();
            }

            $this->dbCommit('교육-강의 순서 업데이트 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '순서가 수정되었습니다',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }

}
