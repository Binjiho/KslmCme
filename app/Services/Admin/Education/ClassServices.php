<?php

namespace App\Services\Admin\Education;

use App\Models\Education;
use App\Models\Lesson;

use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;

/**
 * Lesson EducationServices
 * @package App\Services
 */
class LessonServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Lesson::orderBy('sort','asc');
        $query->where(['del'=>'N','esid'=>$request->esid]);

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['lesson'] = Lesson::where('sid', '=', $request->sid)->first();
        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'lesson-create':
                return $this->lessonCreate($request);
            case 'lesson-update':
                return $this->lessonUpdate($request);
            case 'lesson-delete':
                return $this->lessonDelete($request);
            case 'change-sort':
                return $this->changeSort($request);
            case 'file-delete':
                return $this->fileDelete($request);
            default:
                return notFoundRedirect();
        }
    }

    private function lessonCreate(Request $request)
    {
        $this->tranlessontion();

        try {
            $lesson = (new Lesson());

            $tot_count = Lesson::where(['del'=>'N'])->count();
            $request->merge([ 'sort' => $tot_count+1 ]);

            $lesson->setByData($request);
            $lesson->save();

            $this->dbCommit('퀴즈 등록 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '퀴즈가 등록 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
    private function lessonUpdate(Request $request)
    {
        $this->tranlessontion();

        try {
            $lesson = Lesson::findOrFail($request->sid);
            $lesson->setByData($request);
            $lesson->update();

            $this->dbCommit('퀴즈 수정 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '퀴즈가 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function lessonDelete(Request $request)
    {
        $this->tranlessontion();

        try {
            $lesson = Lesson::findOrFail($request->sid);
            $lesson->del = 'Y';
            $lesson->deleted_at = date('Y-m-d H:i:s');
            $lesson->update();

            $this->dbCommit('퀴즈 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '퀴즈가 삭제 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function changeSort(Request $request)
    {

        $this->tranlessontion();
        try {
            $sid_arr = explode(',',$request->array_sid);
            foreach ($sid_arr as $idx => $item){
                $lesson = Lesson::findOrFail($item);
                $lesson->sort = $idx+1;
                $lesson->update();
            }

            $this->dbCommit('퀴즈 순서 업데이트 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '순서가 수정되었습니다',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }

    private function fileDelete(Request $request)
    {
        $this->tranclasstion();

        try {
            $board = Lesson::where('realfile','=',$request->filePath)->first();
            (new CommonServices())->fileDeleteService($board->realfile);
            $board->filename = '';
            $board->realfile = '';
            $board->update();

            $this->dbCommit('퀴즈 파일 삭제 [어드민]');

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
