<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Services\Mypage\MypageServices;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    private $mypageServices;

    public function __construct()
    {
        $this->mypageServices = (new MypageServices());

        view()->share([
            'userConfig' => getConfig('user'),
            'sacConfig' => getConfig('sac'),
            'educationConfig' => getConfig('education'),
            'main_menu' => 'M3',
            'sub_menu' => 'S1',
        ]);
    }

    public function index(Request $request)
    {
        return view('mypage.index', $this->mypageServices->indexService($request));
    }

//    public function upsert(Request $request)
//    {
//        return view('mypage.upsert', $this->mypageServices->upserService($request));
//    }

//###########################################교육신청/취소###################################################
    public function list(Request $request)
    {
        view()->share([
            'low_menu' => 'SS2',
        ]);
        return view('mypage.list.index', $this->mypageServices->listService($request));
    }
    public function payinfo(Request $request)
    {
        return view('mypage.popup.payinfo', $this->mypageServices->receiptService($request));
    }
    public function receipt(Request $request)
    {
        return view('mypage.popup.receipt', $this->mypageServices->receiptService($request));
    }
    public function cancle(Request $request)
    {
        return view('mypage.popup.cancle', $this->mypageServices->receiptService($request));
    }
//###########################################이수증 출력###################################################
    public function certi(Request $request)
    {
        view()->share([
            'low_menu' => 'SS3',
        ]);
        return view('mypage.certi.index', $this->mypageServices->certiListService($request));
    }
    public function certi_detail(Request $request)
    {
        return view('mypage.popup.certi', $this->mypageServices->certiDetailService($request));
    }

//###########################################관심교육###################################################

    public function interest_edu(Request $request)
    {
        view()->share([
            'low_menu' => 'SS4',
        ]);
        return view('mypage.interest.education', $this->mypageServices->interestListService($request));
    }

    public function data(Request $request)
    {
        return $this->mypageServices->dataAction($request);
    }
}
