<?php

namespace App\Services\Unite;

use App\Models\Workshop;
use App\Models\Education;
use App\Models\EduLecList;
use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class MainServices
 * @package App\Services
 */
class UniteServices extends AppServices
{
    public function indexService(Request $request)
    {
        /**
         * 검색의 경우 모든 메뉴에서 일치하는 정보가 있을 경우 노출
         * 수강/열람신청 : 유형/교육명/교육내용/강의명/강의내용/강사/강사소속/키워드
         * 학술자료실 : 행사명/자료구분/자료명/발표자명/발표자소속/키워드
         */
//        $query = EduLecList::orderByDesc('created_at')->with(['edu','lec']);
//
//        if ($request->search_key) {
//            $query
//                ->WhereHas('edu', function ($query) use ($request) {
//                    $query->where('title', 'like', "%{$request->search_key}%")     //교육명
//                    ->orWhere('contents', 'like', "%{$request->search_key}%");     //교육내용
//                })
//                ->orWhereHas('lec', function ($query) use ($request) {
//                    $query->where('title', 'like', "%{$request->search_key}%")     //강의명
//                    ->orWhere('name_kr', 'like', "%{$request->search_key}%")       //강사이름
//                    ->orWhere('sosok_kr', 'like', "%{$request->search_key}%");     //강사소속
//                    //->orWhere('keyword', 'like', "%{$request->search_key}%"); //키워드
//                })
//                ->groupBy('esid');
//        }else{
//            $query->groupBy('esid');
//        }

        $query = Education::orderByDesc('created_at');
        $query->where('del', '=', 'N')->where('hide', '=', 'N');
        if ($request->search_key) {
            $query->where(function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search_key}%")     //교육명
                ->orWhere('contents', 'like', "%{$request->search_key}%");     //교육내용
            })
            ->orWhereHas('lectures', function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search_key}%")     //강의명
                ->orWhere('name_kr', 'like', "%{$request->search_key}%")       //강사이름
                ->orWhere('sosok_kr', 'like', "%{$request->search_key}%");     //강사소속
            });
        }

        $this->data['education_cnt'] = $query->count();
        $list = $query->limit(8)->paginate(20);
        $this->data['education_list'] = setListSeq($list);

        $query = Workshop::orderByDesc('created_at')->with(['sub_session']);

        if ($request->search_key) {
            $query->where(['del' => 'N']);
            $query->where(['hide' => 'N'])
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

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            default:
                return notFoundRedirect();
        }
    }
}
