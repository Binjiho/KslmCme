<?php

namespace App\Services\Admin\Education;

use App\Models\Education;
use App\Models\Survey;

use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;

/**
 * Class EducationServices
 * @package App\Services
 */
class SurveyServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Survey::orderBy('sort','asc');
        $query->where(['del'=>'N','esid'=>$request->esid]);

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['survey'] = Survey::where('sid', '=', $request->sid)->first();
        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function graphService(Request $request)
    {
        $this->data['survey'] = Survey::where('sid', '=', $request->sid)->first();
        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'survey-create':
                return $this->surveyCreate($request);
            case 'survey-update':
                return $this->surveyUpdate($request);
            case 'survey-delete':
                return $this->surveyDelete($request);
            case 'change-sort':
                return $this->changeSort($request);
            case 'file-delete':
                return $this->fileDelete($request);
            default:
                return notFoundRedirect();
        }
    }

    private function surveyCreate(Request $request)
    {
        $this->transaction();

        try {
            $survey = (new Survey());

            $tot_count = Survey::where(['del'=>'N'])->count();
            $request->merge([ 'sort' => $tot_count+1 ]);

            $survey->setByData($request);
            $survey->save();

            $this->dbCommit('설문 등록 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '설문이 등록 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
    private function surveyUpdate(Request $request)
    {
        $this->transaction();

        try {
            $survey = Survey::findOrFail($request->sid);
            $survey->setByData($request);
            $survey->update();

            $this->dbCommit('설문 수정 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '설문이 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function surveyDelete(Request $request)
    {
        $this->transaction();

        try {
            $survey = Survey::findOrFail($request->sid);
            $survey->del = 'Y';
            $survey->deleted_at = date('Y-m-d H:i:s');
            $survey->update();

            $this->dbCommit('설문 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '설문이 삭제 되었습니다.',
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
                $survey = Survey::findOrFail($item);
                $survey->sort = $idx+1;
                $survey->update();
            }

            $this->dbCommit('설문 순서 업데이트 [어드민]');

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
