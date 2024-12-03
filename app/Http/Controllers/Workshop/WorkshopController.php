<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Services\Workshop\WorkshopServices;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    private $workshopServices;

    public function __construct()
    {
        $this->workshopServices = (new WorkshopServices());

        view()->share([
            'workshopConfig' => getConfig('workshop'),
            'main_menu' => 'M2',
        ]);
    }

    public function index(Request $request)
    {
        return view('workshop.index', $this->workshopServices->indexService($request));
    }

    public function detail(Request $request)
    {
        return view('workshop.detail.index', $this->workshopServices->detailService($request));
    }

    public function popup(Request $request)
    {
        return view('workshop.detail.popup', $this->workshopServices->popupService($request));
    }

    public function data(Request $request)
    {
        return $this->workshopServices->dataAction($request);
    }
}
