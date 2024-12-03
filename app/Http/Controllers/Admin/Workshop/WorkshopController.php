<?php

namespace App\Http\Controllers\Admin\Workshop;

use App\Http\Controllers\Controller;
use App\Services\Admin\Workshop\WorkshopServices;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    private $workshopServices;

    public function __construct()
    {
        $this->workshopServices = (new WorkshopServices());

        view()->share([
            'userConfig' => getConfig('user'),
            'educationConfig' => getConfig('education'),
            'workshopConfig' => getConfig('workshop'),
            'main_key' => 'M4',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.workshop.index', $this->workshopServices->indexService($request));
    }
    public function upsert(Request $request)
    {
        return view('admin.workshop.upsert', $this->workshopServices->upsertService($request));
    }

    public function log(Request $request)
    {
        view()->share(['sub_key' => 'S2']);
        return view('admin.workshop.log.index', $this->workshopServices->logService($request));
    }

    public function log_excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->workshopServices->logService($request);
    }

    public function excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->workshopServices->indexService($request);
    }

    public function data(Request $request)
    {
        return $this->workshopServices->dataAction($request);
    }
}
