<?php

namespace App\Services\Mypage;

use App\Models\Education;
use App\Models\Workshop;
use App\Models\WorkshopLog;
use App\Models\Sac;
use App\Models\Heart;
use App\Services\AppServices;
use App\Services\CommonServices;
use App\Services\Auth\AuthServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class WorkshopServices
 * @package App\Services
 */
class WorkshopServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Heart::where(['del'=>'N','user_sid'=>thisPK(), 'type' => 'W'])
            ->orderByDesc('sid');
        $list = $query->paginate(10);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function logService(Request $request)
    {
        $query = WorkshopLog::where(['del'=>'N','user_sid'=>thisPK()])
            //워크샵 현재 hide 상태 이거나 del 상태인 경우 안보이게
            ->whereHas('workshop', function ($q) {
                $q->where('hide', 'N')->where('del', 'N');
            })
            ->orderByDesc('sid');

        //검색이 있을 경우
        if($request->search_key) {
            $query->where(function ($q) use ($request) {
                $q->WhereHas('sub',function ($q2) use ($request) {
                    $q2->where('title', 'like', "%{$request->search_key}%")
                        ->orWhere('pname', 'like', "%{$request->search_key}%")
                        ->orWhere('psosok', 'like', "%{$request->search_key}%");
                });

                $q->orWhereHas('workshop', function ($q2) use ($request) {
                    $q2->where('hide', '=', 'N')
                        ->where(function ($q3) use ($request) {
                            $q3->where('title', 'like', "%{$request->search_key}%")
                                ->orWhere('place', 'like', "%{$request->search_key}%");
                    });
                });
            });
        }

        $list = $query->paginate(10);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

//    public function detailService(Request $request)
//    {
//        $this->data['sac_info'] = Sac::findOrFail($request->ssid);
////        $this->data['captcha'] = (new CommonServices())->captchaMakeService();
//
//        return $this->data;
//    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'quiz-upsert':
                return $this->quizUpsert($request);
            case 'change-heart':
                return $this->changeHeart($request);
            default:
                return notFoundRedirect();
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

    private function changeHeart(Request $request)
    {
        $this->transaction();

        try {
            $heart = Heart::where(['user_sid'=>thisPK(), 'wsid'=>$request->wsid, 'del'=>'N'])->first();
            if($heart){
                $heart->delete();
                $target_msg = "관심자료 설정이 해제되었습니다.";
            }else{
                $heart = (new Heart());
                $request->merge([ 'user_sid' => thisPk() ]);
                $heart->setByData($request);
                $heart->save();
                $target_msg = "관심자료로 설정되었습니다. 나의 자료실에서 확인하실 수 있습니다.";
            }

            $this->dbCommit('자료정보 하트 신청');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => $target_msg,
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e,true);
        }
    }

}
