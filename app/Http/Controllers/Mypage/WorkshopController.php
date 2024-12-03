<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Services\Mypage\WorkshopServices;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    private $myworkService;

    public function __construct()
    {
        $this->myworkService = (new WorkshopServices());

        view()->share([
            'workshopConfig' => getConfig('workshop'),
            'main_menu' => 'M3',
            'sub_menu' => 'S2',
        ]);
    }

    public function interest_workshop(Request $request)
    {
        view()->share([
            'low_menu' => 'SS1',
        ]);
        return view('mypage.interest.workshop', $this->myworkService->indexService($request));
    }
    public function workshop_log(Request $request)
        {
            view()->share([
                'low_menu' => 'SS2',
            ]);
            return view('mypage.workshop.log', $this->myworkService->logService($request));
        }

    public function data(Request $request)
    {
        return $this->myworkService->dataAction($request);
    }
}
