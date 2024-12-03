<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Services\Mypage\EducationServices;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    private $myeduServices;

    public function __construct()
    {
        $this->myeduServices = (new EducationServices());

        view()->share([
            'userConfig' => getConfig('user'),
            'educationConfig' => getConfig('education'),
            'lectureConfig' => getConfig('lecture'),
            'sacConfig' => getConfig('sac'),
            'main_menu' => 'M3',
            'sub_menu' => 'S1',
        ]);
    }

    //###########################################교육수강###################################################
    public function index(Request $request)
    {
        view()->share([
            'low_menu' => 'SS1',
        ]);
        return view('mypage.education.index', $this->myeduServices->indexService($request));
    }

    public function detail(Request $request)
    {
        view()->share([
            'low_menu' => 'SS1',
        ]);
        return view('mypage.education.detail', $this->myeduServices->detailService($request));
    }

    public function play(Request $request)
    {
        view()->share([
            'low_menu' => 'SS1',
        ]);
        return view('mypage.education.play_'.$request->type, $this->myeduServices->playService($request));
    }

    public function quiz(Request $request)
    {
        view()->share([
            'low_menu' => 'SS1',
        ]);
        return view('mypage.education.quiz.upsert', $this->myeduServices->quizService($request));
    }

    public function requiz(Request $request)
    {
        view()->share([
            'low_menu' => 'SS1',
        ]);
        return view('mypage.education.quiz.upsert', $this->myeduServices->requizService($request));
    }
    public function quiz_result(Request $request)
    {
        view()->share([
            'low_menu' => 'SS1',
        ]);
        return view('mypage.education.quiz.result', $this->myeduServices->quizResultService($request));
    }
    public function survey(Request $request)
    {
        view()->share([
            'low_menu' => 'SS1',
        ]);
        return view('mypage.education.survey.upsert', $this->myeduServices->surveyService($request));
    }
    public function survey_result(Request $request)
    {
        view()->share([
            'low_menu' => 'SS1',
        ]);
        return view('mypage.education.survey.result', $this->myeduServices->surveyResultService($request));
    }

    public function data(Request $request)
    {
        return $this->myeduServices->dataAction($request);
    }
}
