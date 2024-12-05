<?php

namespace App\Services\Main;

use App\Models\Workshop;
use App\Models\Education;
use App\Models\EduLecList;
use App\Models\Board;
use App\Models\BoardPopup;
use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class MainServices
 * @package App\Services
 */
class MainServices extends AppServices
{
    public function indexService(Request $request)
    {
        $exceptionBoardPopup = [];
        $allCookies = $request->cookies->all();

        foreach ($allCookies as $key => $val) {
            // 게시판 팝업 오늘하루 보지않기 있는지 체크
            if (strpos($key, 'board-popup-') !== false) {
                $boardSid = (int)str_replace('board-popup-', '', $key);
                $exceptionBoardPopup[] = $boardSid;
            }
        }

        $this->data['workshop_list_a'] = Workshop::where(['del'=>'N','hide'=>'N', 'main_yn'=>'Y', 'category'=>'A'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        $this->data['workshop_list_b'] = Workshop::where(['del'=>'N','hide'=>'N', 'main_yn'=>'Y', 'category'=>'B'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        $this->data['workshop_list_z'] = Workshop::where(['del'=>'N','hide'=>'N', 'main_yn'=>'Y', 'category'=>'Z'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        $this->data['education_list'] = Education::where(['del'=>'N','hide'=>'N'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        $this->data['notice_list'] = Board::where(['code'=>'notice','hide'=>'N', 'main'=>'Y'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 게시판 팝업
        $this->data['boardPopupList'] = Board::withCount('files')
            ->where(['hide' => 'N', 'popup_yn' => 'Y'])
            ->whereNotIn('sid', $exceptionBoardPopup)
            ->whereHas('popups', function ($q) {
                $q->where('popup_sDate', '<=', now()->format('Y-m-d'))
                    ->where('popup_eDate', '>=', now()->format('Y-m-d'));

            })
            ->get();

        return $this->data;
    }

    public function uniteService(Request $request)
    {
        /**
         * 검색의 경우 모든 메뉴에서 일치하는 정보가 있을 경우 노출
         * 수강/열람신청 : 유형/교육명/교육내용/강의명/강의내용/강사/강사소속/키워드
         * 학술자료실 : 행사명/자료구분/자료명/발표자명/발표자소속/키워드
         */
        $query = EduLecList::orderByDesc('created_at')->with(['edu','lec']);

        if ($request->search_key) {
            $query->where(['del' => 'N'])
                ->orWhereHas('edu', function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search_key}%")     //교육명
                    ->orWhere('contents', 'like', "%{$request->search_key}%");     //교육내용
                })
                ->orWhereHas('lec', function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search_key}%")     //강의명
                    ->orWhere('name_kr', 'like', "%{$request->search_key}%")       //강사이름
                    ->orWhere('sosok_kr', 'like', "%{$request->search_key}%");     //강사소속
                    //->orWhere('keyword', 'like', "%{$request->search_key}%"); //키워드
                });
        }

        $this->data['education_cnt'] = $query->count();
        $list = $query->limit(8)->paginate(20);
        $this->data['education_list'] = setListSeq($list);

        $query = Workshop::orderByDesc('created_at')->with(['sub_session']);

        if ($request->search_key) {
            $query->where(['del' => 'N'])
                ->where(function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search_key}%");   //행사명
                })
                ->orWhereHas('sub_session', function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search_key}%")    //자료구분
                    ->orWhere('title', 'like', "%{$request->search_key}%")    //자료명
                    ->orWhere('pname', 'like', "%{$request->search_key}%")    //발표자명
                    ->orWhere('psosok', 'like', "%{$request->search_key}%");  //발표자소속
                });
        }

        $this->data['workshop_cnt'] = $query->count();
        $list = $query->limit(10)->paginate(20);
        $this->data['workshop_list'] = setListSeq($list);

        return $this->data;
    }

    public function popService(Request $request)
    {
        $this->data['board'] = Board::FindOrFail($request->bsid);
        $this->data['board']->files_count = count($this->data['board']->files);
        $this->data['popup'] = BoardPopup::where(['b_sid'=>$request->bsid])->first();
        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'main-popup':
                return $this->mainPopupServices($request);
            default:
                return notFoundRedirect();
        }
    }

    private function mainPopupServices(Request $request)
    {
        $this->data['popup'] = (object)$request->all();
        return $this->returnJsonData('append', [
            $this->ajaxActionHtml('body', view("common.popup.template".$request->popup_skin ?? 0, $this->data)->render())
        ]);
    }
}
