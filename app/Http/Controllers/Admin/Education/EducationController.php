<?php

namespace App\Http\Controllers\Admin\Education;

use App\Http\Controllers\Controller;
use App\Services\Admin\Education\EducationServices;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    private $educationServices;

    public function __construct()
    {
        $this->educationServices = (new EducationServices());

        view()->share([
            'educationConfig' => getConfig('education'),
            'main_key' => 'M2',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.education.index', $this->educationServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view('admin.education.upsert', $this->educationServices->upsertService($request));
    }

    public function payinfo(Request $request)
    {
        return view('admin.education.detail.popup.payInfo', $this->educationServices->payInfoService($request));
    }
    public function refundinfo(Request $request)
    {
        return view('admin.education.detail.popup.refundInfo', $this->educationServices->payInfoService($request));
    }

    public function excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->educationServices->indexService($request);
    }

    public function data(Request $request)
    {
        return $this->educationServices->dataAction($request);
    }
}
