<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use App\Services\Education\EducationServices;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    private $educationServices;

    public function __construct()
    {
        $this->educationServices = (new EducationServices());

        view()->share([
            'educationConfig' => getConfig('education'),
            'lectureConfig' => getConfig('lecture'),
            'main_menu' => 'M1',
        ]);
    }

    public function index(Request $request)
    {
        return view('education.index', $this->educationServices->indexService($request));
    }

    public function detail(Request $request)
    {
        return view('education.detail.index', $this->educationServices->detailService($request));
    }

    public function upsert(Request $request)
    {
        return view('education.detail.upsert', $this->educationServices->upsertService($request));
    }


    public function data(Request $request)
    {
        return $this->educationServices->dataAction($request);
    }
}
