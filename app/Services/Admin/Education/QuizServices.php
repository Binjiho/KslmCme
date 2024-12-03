<?php

namespace App\Services\Admin\Education;

use App\Models\Education;
use App\Models\Quiz;

use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;

/**
 * Class EducationServices
 * @package App\Services
 */
class QuizServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Quiz::orderBy('sort','asc');
        $query->where(['del'=>'N','esid'=>$request->esid]);

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['quiz'] = Quiz::where('sid', '=', $request->sid)->first();
        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function graphService(Request $request)
    {
        $this->data['quiz'] = Quiz::where('sid', '=', $request->sid)->first();
        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'quiz-create':
                return $this->quizCreate($request);
            case 'quiz-update':
                return $this->quizUpdate($request);
            case 'quiz-delete':
                return $this->quizDelete($request);
            case 'change-sort':
                return $this->changeSort($request);
            case 'file-delete':
                return $this->fileDelete($request);
            default:
                return notFoundRedirect();
        }
    }

    private function quizCreate(Request $request)
    {
        $this->transaction();

        try {
            $quiz = (new Quiz());

            $tot_count = Quiz::where(['del'=>'N'])->count();
            $request->merge([ 'sort' => $tot_count+1 ]);

            $quiz->setByData($request);
            $quiz->save();

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
    private function quizUpdate(Request $request)
    {
        $this->transaction();

        try {
            $quiz = Quiz::findOrFail($request->sid);
            $quiz->setByData($request);
            $quiz->update();

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

    private function quizDelete(Request $request)
    {
        $this->transaction();

        try {
            $quiz = Quiz::findOrFail($request->sid);
            $quiz->del = 'Y';
            $quiz->deleted_at = date('Y-m-d H:i:s');
            $quiz->update();

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

        $this->transaction();
        try {
            $sid_arr = explode(',',$request->array_sid);
            foreach ($sid_arr as $idx => $item){
                $quiz = Quiz::findOrFail($item);
                $quiz->sort = $idx+1;
                $quiz->update();
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
        $this->transaction();

        try {
            $board = Quiz::where('realfile','=',$request->filePath)->first();
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
