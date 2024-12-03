<?php

namespace App\Http\Controllers\Admin\Lecture;

use App\Http\Controllers\Controller;
use App\Services\Admin\Lecture\LectureServices;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    private $lectureServices;

    public function __construct()
    {
        $this->lectureServices = (new LectureServices());

        view()->share([
            'educationConfig' => getConfig('education'),
            'lectureConfig' => getConfig('lecture'),
            'main_key' => 'M3',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.lecture.index', $this->lectureServices->indexService($request));
    }
    public function upsert(Request $request)
    {
        return view('admin.lecture.upsert', $this->lectureServices->upsertService($request));
    }
    public function view(Request $request)
    {
        return view('admin.lecture.view', $this->lectureServices->viewService($request));
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
