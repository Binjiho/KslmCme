<?php

namespace App\Http\Controllers\Admin\Education;

use App\Http\Controllers\Controller;
use App\Services\Admin\Education\EducationServices;
use App\Services\Admin\Education\SacServices;
use Illuminate\Http\Request;

class SacController extends Controller
{
    private $sacServices;

    public function __construct()
    {
        $this->sacServices = (new SacServices());

        view()->share([
            'sacConfig' => getConfig('sac'),
            'main_key' => 'M2',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.education.detail.sac.index', $this->sacServices->indexService($request));
    }

    public function deleted(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.education.detail.sac.deleted', $this->sacServices->deletedService($request));
    }

    public function upsert(Request $request)
    {
        return view('admin.education.detail.sac.upsert', $this->sacServices->upsertService($request));
    }

    public function graph(Request $request)
    {
        return view('admin.education.detail.sac.graph', $this->sacServices->graphService($request));
    }

    public function excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->sacServices->indexService($request);
    }
    public function cancle_excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->sacServices->deletedService($request);
    }

    public function data(Request $request)
    {
        return $this->sacServices->dataAction($request);
    }
}
