<?php

namespace App\Http\Controllers\Admin\Education;

use App\Http\Controllers\Controller;
use App\Services\Admin\Education\EducationServices;
use App\Services\Admin\Education\DetailLectureServices;
use Illuminate\Http\Request;

class DetailLectureController extends Controller
{
    private $lectureServices;

    public function __construct()
    {
        $this->lectureServices = (new DetailLectureServices());

        view()->share([
//            'educationConfig' => getConfig('education'),
            'lectureConfig' => getConfig('lecture'),
            'main_key' => 'M2',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.education.detail.lecture.index', $this->lectureServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view('admin.education.detail.lecture.lecture-search', $this->lectureServices->upsertService($request));
    }

    public function lecture(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.education.lecture.index', $this->lectureServices->lectureIndexService($request));
    }

    public function excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->lectureServices->indexService($request);
    }

    public function data(Request $request)
    {
        return $this->lectureServices->dataAction($request);
    }
}
