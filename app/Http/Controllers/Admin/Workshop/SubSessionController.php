<?php

namespace App\Http\Controllers\Admin\Workshop;

use App\Http\Controllers\Controller;
use App\Services\Admin\Workshop\SubSessionServices;
use Illuminate\Http\Request;

class SubSessionController extends Controller
{
    private $subsessionService;

    public function __construct()
    {
        $this->subsessionService = (new SubSessionServices());

        view()->share([
            'workshopConfig' => getConfig('workshop'),
            'main_key' => 'M4',
        ]);
    }

    public function index(Request $request)
    {
        return view('admin.workshop.subsession.index', $this->subsessionService->indexService($request));
    }
    public function sub_collective(Request $request)
    {
        return view('admin.workshop.subsession.collective', $this->subsessionService->collectiveService($request));
    }

    public function excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->subsessionService->indexService($request);
    }

    public function data(Request $request)
    {
        return $this->subsessionService->dataAction($request);
    }
}
