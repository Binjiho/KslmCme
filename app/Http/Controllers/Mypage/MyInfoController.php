<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Services\Mypage\MypageServices;
use Illuminate\Http\Request;

class MyInfoController extends Controller
{
    private $mypageServices;

    public function __construct()
    {
        $this->mypageServices = (new MypageServices());

        view()->share([
            'userConfig' => getConfig('user'),
            'main_menu' => 'M3',
            'sub_menu' => 'S3',
        ]);
    }

    public function myInfo(Request $request)
    {
        return view('mypage.myinfo.index', $this->mypageServices->indexService($request));
    }

    public function data(Request $request)
    {
        return $this->mypageServices->dataAction($request);
    }
}
