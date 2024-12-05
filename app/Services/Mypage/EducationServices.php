<?php

namespace App\Services\Mypage;

use App\Models\User;
use App\Models\Education;
use App\Models\Lecture;
use App\Models\LectureView;
use App\Models\Sac;
use App\Models\Quiz;
use App\Models\QuizView;
use App\Models\Survey;
use App\Models\SurveyView;
use App\Services\AppServices;
use App\Services\CommonServices;
use App\Services\Auth\AuthServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class MypageServices
 * @package App\Services
 */
class EducationServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Sac::select('sac_info.sid', 'sac_info.*')
            ->where(['sac_info.del'=>'N','sac_info.user_sid'=>thisPK()])
            ->join('educations', 'educations.sid', '=', 'sac_info.esid') // 'education' 테이블 조인
            ->with(['edu', 'lectures']);

        /**
         *  리스트 정렬 순위
         * _ 1순위 : 수강상태 edu_status (수강중 – 수료 – 수강전 - 미수료)
         * _ 2순위 : 수강기간 중 마감일이 가까울수록 상단에 배치
         * _ 3순위 : 수강 마감일이 동일한 경우 교육명 ㄱㄴㄷ 순
         */
        $query->orderByRaw("FIELD(edu_status, 'I', 'C', 'N', 'F')")
            ->orderByRaw("CASE 
                          WHEN educations.edu_edate IS NULL OR educations.edu_edate = '0001-11-30 00:00:00' THEN 99999 
                          ELSE 0 
                        END ASC, 
                        DATEDIFF(educations.edu_edate, CURDATE())*1 ASC") // NULL 값을 최후로 이동
            ->orderBy('educations.title', 'asc');

        if ($request->category) {
            $query->WhereHas('edu', function ($query) use ($request) {
                $query->whereIn('category', $request->category);
            });
        }
        if ($request->gubun) {
            $query->WhereHas('edu', function ($query) use ($request) {
                $query->whereIn('gubun', $request->gubun);
            });
        }
        if ($request->field) {
            $query->WhereHas('lectures', function ($query) use ($request) {
                foreach($request->field as $fkey => $fval){
                    if ($fkey == 0){
                        $query->where('field', 'like', "%{$fval}%");
                    }else{
                        $query->orWhere('field', 'like', "%{$fval}%");
                    }
                }
            });
        }
        if ($request->search_key) {
            $query->whereHas('edu', function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search_key}%")     //교육명
                ->orWhere('contents', 'like', "%{$request->search_key}%");     //교육내용
            })
            ->orWhereHas('lectures', function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search_key}%")     //강의명
                ->orWhere('name_kr', 'like', "%{$request->search_key}%")       //강사이름
                ->orWhere('sosok_kr', 'like', "%{$request->search_key}%");     //강사소속
            });
        }

        $list = $query->paginate(10);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function detailService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->ssid);
//        $this->data['captcha'] = (new CommonServices())->captchaMakeService();

        return $this->data;
    }

    public function playService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->ssid);
        $this->data['lecture'] = Lecture::findOrFail($request->lsid);
        $this->data['lecture_view'] = LectureView::where(['user_sid'=>thisPK(), 'ssid'=>$request->ssid, 'lsid'=>$request->lsid])->first();

        return $this->data;
    }

    public function quizService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->ssid);
        $this->data['quiz'] = Quiz::where(['esid'=>$this->data['sac_info']->edu->sid, 'del'=>'N' ])->orderBy('sort','ASC')->get();

        return $this->data;
    }

    public function requizService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->ssid);
        $this->data['quiz'] = Quiz::where(['esid'=>$this->data['sac_info']->edu->sid, 'del'=>'N' ])->orderBy('sort','ASC')->get();

        $this->deleteQuizView($request->ssid,thisPK());

        return $this->data;
    }

    public function quizResultService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->ssid);
        $this->data['quiz_view'] = QuizView::where(['user_sid'=>thisPK(),'ssid'=>$request->ssid, 'esid'=>$this->data['sac_info']->edu->sid, 'del'=>'N' ])->orderBy('sid','ASC')->get();
        return $this->data;
    }

    public function surveyService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->ssid);
        $this->data['survey'] = Survey::where(['esid'=>$this->data['sac_info']->edu->sid, 'del'=>'N' ])->orderBy('sort','ASC')->get();

        return $this->data;
    }

    public function surveyResultService(Request $request)
    {
        $this->data['sac_info'] = Sac::findOrFail($request->ssid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'vod-play':
                return $this->vodPlay($request);
            case 'vod-finish':
                return $this->vodFinish($request);
            case 'pdf-play':
                return $this->pdfPlay($request);
            case 'pdf-finish':
                return $this->pdfFinish($request);
            case 'quiz-upsert':
                return $this->quizUpsert($request);
            case 'survey-upsert':
                return $this->surveyUpsert($request);
            default:
                return notFoundRedirect();
        }
    }

    private function vodPlay(Request $request)
    {
        // 로그로 관리
        // $this->transaction();

        try {
            $sac_info = Sac::findOrFail($request->ssid);
            $lecture = Lecture::findOrFail($request->lsid);
            $myLecView = LectureView::where(['user_sid'=>thisPK(), 'ssid'=>$request->ssid, 'lsid'=>$request->lsid])->first();

            if(empty($myLecView)){
                $myLecView = (new LectureView());
                $request->merge([ 'user_sid' => thisPk() ]);
                $myLecView->setByData($request);
                $myLecView->save();

                Log::channel('playLog')->error("================================== PLAY VIDEO ===================================");
                Log::channel('playLog')->error("수강시작 user_sid : ".thisPk()." LectureView_SID : {$myLecView->sid} Sac_SID :{$request->ssid} Lecture_SID :{$request->lsid} Education_SID :{$request->esid} 시간 : ".date('Y-m-d H:i:s'));
                Log::channel('playLog')->error("===============================================================================");

            }else{
                $myLecView->setByData($request);
                $myLecView->update();
            }

            //수강률 100%가 되었을 경우
            if(intval(round($lecture->lecture_time)) <= intval(round($lecture->ing_time))){
                // 해당 강의 데이터만
                $myLecView->complete_status = 'Y';
                $myLecView->update();

                // 모든 강의 데이터가 100% 완료인 경우
                $tot_cnt = $com_cnt = 0;
                foreach ($sac_info->lectures as $lec_item){
                    $tot_cnt++;
//                    if ( $lec_item->type == 'V') $tot_cnt++;
                    $thisLecView = LectureView::where(['user_sid'=>thisPK(), 'ssid'=>$request->ssid, 'lsid'=>$lec_item->sid])->first();
                    if ($thisLecView && $thisLecView->complete_status == 'Y' ) $com_cnt++;
                }
                if($tot_cnt <= $com_cnt){
                    $sac_info = Sac::findOrFail($request->ssid);
                    $sac_info->edu_status = 'C';
                    $sac_info->edu_at = date('Y-m-d H:i:s');
                    $sac_info->update();

                    $this->checkSacComplete($sac_info->sid);
                }else{
                    $sac_info = Sac::findOrFail($request->ssid);
                    $sac_info->edu_status = 'I';
                    $sac_info->update();
                }

                Log::channel('playLog')->error("================================== PLAY VIDEO ===================================");
                Log::channel('playLog')->error("수강완료 type vod user_sid : ".thisPk()." LectureView_SID : {$myLecView->sid} Sac_SID :{$request->ssid} Lecture_SID :{$request->lsid} Education_SID :{$request->esid} 시간 : ".date('Y-m-d H:i:s'));
                Log::channel('playLog')->error("===============================================================================");

                return $this->returnJsonData('result', [
                    'res' => 'complete',
                    'percent' => 100,
                ]);
            }

            return $this->returnJsonData('result', [
                'res' => 'yet',
                'percent' => round(($request->ing_time/$lecture->lecture_time)*100),
            ]);
        } catch (\Exception $e) {
            return $this->returnJsonData('result', [
                'res' => 'error',
                'msg' => 'API 통신중 오류가 발생하였습니다. 관리자에게 문의 부탁드립니다.',
                'error' => $e,
            ]);
//            return $this->dbRollback($e, true);
        }
    }

    private function vodFinish(Request $request)
    {
        try {
            $sac_info = Sac::findOrFail($request->ssid);
            $lecture = Lecture::findOrFail($request->lsid);
            $myLecView = LectureView::where(['user_sid'=>thisPK(), 'ssid'=>$request->ssid, 'lsid'=>$request->lsid])->first();

            // 해당 강의 데이터만
            $myLecView->ing_time = $lecture->lecture_time;
            $myLecView->complete_status = 'Y';
            $myLecView->update();

            // 모든 강의 데이터가 100% 완료인 경우
            $tot_cnt = $com_cnt = 0;
            foreach ($sac_info->lectures as $lec_item){
                $tot_cnt++;
//                if ( $lec_item->type == 'V') $tot_cnt++;
                $thisLecView = LectureView::where(['user_sid'=>thisPK(), 'ssid'=>$request->ssid, 'lsid'=>$lec_item->sid])->first();
                if ($thisLecView && $thisLecView->complete_status == 'Y' ) $com_cnt++;
            }
            if($tot_cnt <= $com_cnt){
                $sac_info = Sac::findOrFail($request->ssid);
                $sac_info->edu_status = 'C';
                $sac_info->edu_at = date('Y-m-d H:i:s');
                $sac_info->update();

                $this->checkSacComplete($sac_info->sid);
            }else{
                $sac_info = Sac::findOrFail($request->ssid);
                $sac_info->edu_status = 'I';
                $sac_info->update();
            }

            Log::channel('playLog')->error("================================== PLAY VIDEO ===================================");
            Log::channel('playLog')->error("수강완료 FINISH type:vod user_sid : ".thisPk()." LectureView_SID : {$myLecView->sid} Sac_SID :{$request->ssid} Lecture_SID :{$request->lsid} Education_SID :{$request->esid} 시간 : ".date('Y-m-d H:i:s'));
            Log::channel('playLog')->error("===============================================================================");

            return $this->returnJsonData('result', [
                'res' => 'complete',
                'percent' => 100,
            ]);

        } catch (\Exception $e) {
            return $this->returnJsonData('result', [
                'res' => 'error',
                'msg' => 'API 통신중 오류가 발생하였습니다. 관리자에게 문의 부탁드립니다.',
                'error' => $e,
            ]);
//            return $this->dbRollback($e, true);
        }
    }

    private function pdfPlay(Request $request)
    {
        try {
            $sac_info = Sac::findOrFail($request->ssid);
            $lecture = Lecture::findOrFail($request->lsid);
            $myLecView = LectureView::where(['user_sid'=>thisPK(), 'ssid'=>$request->ssid, 'lsid'=>$request->lsid])->first();

            // 해당 강의 데이터만
            if(empty($myLecView)){
                $myLecView = (new LectureView());
                $request->merge([ 'user_sid' => thisPk() ]);
                $request->merge([ 'pdf_percent' => $request->pdf_percent ]);
                $myLecView->setByData($request);
                $myLecView->save();
            }else{
                if($myLecView->complete_status != 'Y'){
                    $myLecView->setByData($request);
                    $myLecView->update();
                }
            }

            $sac_info = Sac::findOrFail($request->ssid);
            $sac_info->edu_status = 'I';
            $sac_info->update();

            return $this->returnJsonData('result', [
                'res' => 'ing',
            ]);

        } catch (\Exception $e) {
            return $this->returnJsonData('result', [
                'res' => 'error',
                'msg' => 'API 통신중 오류가 발생하였습니다. 관리자에게 문의 부탁드립니다.',
                'error' => $e,
            ]);
        }
    }

    private function pdfFinish(Request $request)
    {
        try {
            $sac_info = Sac::findOrFail($request->ssid);
            $lecture = Lecture::findOrFail($request->lsid);
            $myLecView = LectureView::where(['user_sid'=>thisPK(), 'ssid'=>$request->ssid, 'lsid'=>$request->lsid])->first();

            // 해당 강의 데이터만
            if(!empty($myLecView)){
                $request->merge([ 'user_sid' => thisPk() ]);
                $request->merge([ 'pdf_percent' => 100 ]);
                $request->merge([ 'complete_status' => 'Y' ]);
                $myLecView->setByData($request);
                $myLecView->update();

                Log::channel('playLog')->error("================================== PLAY PDF ===================================");
                Log::channel('playLog')->error("수강완료 FINISH type:pdf user_sid : ".thisPk()." LectureView_SID : {$myLecView->sid} Sac_SID :{$request->ssid} Lecture_SID :{$request->lsid} Education_SID :{$request->esid} 시간 : ".date('Y-m-d H:i:s'));
                Log::channel('playLog')->error("===============================================================================");
            }

            // 모든 강의 데이터가 100% 완료인 경우
            $tot_cnt = $com_cnt = 0;
            foreach ($sac_info->lectures as $lec_item){
                $tot_cnt++;
                $thisLecView = LectureView::where(['user_sid'=>thisPK(), 'ssid'=>$request->ssid, 'lsid'=>$lec_item->sid])->first();
                if ($thisLecView && $thisLecView->complete_status == 'Y' ) $com_cnt++;
            }
            if($tot_cnt <= $com_cnt){
                $sac_info = Sac::findOrFail($request->ssid);
                $sac_info->edu_status = 'C';
                $sac_info->edu_at = date('Y-m-d H:i:s');
                $sac_info->update();

                $this->checkSacComplete($sac_info->sid);
            }else{
                $sac_info = Sac::findOrFail($request->ssid);
                $sac_info->edu_status = 'I';
                $sac_info->update();
            }

            return $this->returnJsonData('result', [
                'res' => 'complete',
                'percent' => 100,
            ]);

        } catch (\Exception $e) {
            return $this->returnJsonData('result', [
                'res' => 'error',
                'msg' => 'API 통신중 오류가 발생하였습니다. 관리자에게 문의 부탁드립니다.',
                'error' => $e,
            ]);
        }
    }

    private function quizUpsert(Request $request)
    {
        $this->transaction();

        try {
            $sac_info = Sac::findOrFail($request->ssid);
            $quiz_info = Quiz::where(['esid'=>$sac_info->edu->sid, 'del'=>'N' ])->orderBy('sort','ASC')->get();

            $passed_cnt = 0;
            $my_answer_arr[0]=0;
            foreach ($quiz_info as $quiz){
                $myQuizView = (new QuizView());
                $request->merge([ 'user_sid' => thisPk() ]);
                $request->merge([ 'esid' => $sac_info->edu->sid ]);
                $request->merge([ 'qsid' => $quiz->sid ]);
                $request->merge([ 'quiz_answer' => $quiz->answer ]);
                $my_answer_arr = $request->{'question_'.$quiz->sid};
                $request->merge([ 'my_answer' => $my_answer_arr[0] ]);

                $myQuizView->setByData($request);
                $myQuizView->save();

                if($my_answer_arr[0] == $quiz->answer) $passed_cnt++;
            }

            /**
             * edu->pass_cnt 가 넘으면 합격 아니면 불합격
             */
            if($passed_cnt >= $sac_info->edu->pass_cnt){
                $sac_info->quiz_status = 'C';
            }else{
                $sac_info->quiz_status = 'F';
            }
            $sac_info->quiz_at = date('Y-m-d H:i:s');
            $sac_info->update();

            $this->checkSacComplete($sac_info->sid);

            $this->dbCommit('퀴즈풀기 생성');

            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', route('mypage.quiz_result',['ssid'=>$request->ssid]) ) );

        } catch (\Exception $e) {
            return $this->dbRollback($e,true);
        }
    }

    private function surveyUpsert(Request $request)
    {
        $this->transaction();

        try {
            $sac_info = Sac::findOrFail($request->ssid);
            $survey_info = Survey::where(['esid'=>$sac_info->edu->sid, 'del'=>'N' ])->orderBy('sort','ASC')->get();
            foreach ($survey_info as $survey){
                $mySurveyView = (new SurveyView());
                $request->merge([ 'user_sid' => thisPk() ]);
                $request->merge([ 'esid' => $sac_info->edu->sid ]);
                $request->merge([ 'survey_sid' => $survey->sid ]);

                $my_answer_arr = $request->{'answer_'.$survey->sid};
                $request->merge([ 'answer' => $my_answer_arr[0] ]);

                $mySurveyView->setByData($request);
                $mySurveyView->save();
            }

            $sac_info->survey_status = 'C';
            $sac_info->survey_at = date('Y-m-d H:i:s');
            $sac_info->update();

            /**
             * 교육 전체 완료!!
             */
            $this->checkSacComplete($sac_info->sid);

            $this->dbCommit('설문조사 실행 생성');

            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', route('mypage.survey_result',['ssid'=>$request->ssid]) ) );

        } catch (\Exception $e) {
            return $this->dbRollback($e,true);
        }
    }

    private function deleteQuizView($ssid, $user_sid){
        $this->transaction();

        try {
            $sac_info = Sac::findOrFail($ssid);
            $quiz_view = QuizView::where(['user_sid'=>$user_sid, 'ssid'=>$ssid, 'esid'=>$sac_info->edu->sid, 'del'=>'N' ])->orderBy('sid','ASC')->get();

            $sac_info->quiz_status = 'I';
            $sac_info->quiz_at = null;
            $sac_info->update();

            foreach ($quiz_view as $quiz){
                $quiz->delete();
            }
            $this->dbCommit('퀴즈풀이 기록 삭제');
            return true;
        } catch (\Exception $e) {
            return $this->dbRollback($e,true);
        }

    }

    private function checkSacComplete($ssid){
        $this->transaction();

        try {
            $sac_info = Sac::findOrFail($ssid);
            $edu_info = Education::findOrFail($sac_info->esid);
            $isComplete = true;

            //lecture완료여부
            if($sac_info->edu_status != 'C') $isComplete = false;

            //quiz완료여부
            if($edu_info->quiz_yn == 'Y'){
                if($sac_info->quiz_status != 'C') $isComplete = false;
            }

            //survey완료여부
            if($edu_info->survey_yn == 'Y'){
                if($sac_info->survey_status != 'C') $isComplete = false;
            }

            if($isComplete){
                $sac_info->complete_yn = 'Y';
                $sac_info->complete_at = date('Y-m-d H:i:s');
                $sac_info->update();
            }

            $this->dbCommit('해당 교육 완료 기록');
            return true;
        } catch (\Exception $e) {
            return $this->dbRollback($e,true);
        }

    }
}
