<?php

namespace App\Http\Controllers\Admin\Education;

use App\Http\Controllers\Controller;
use App\Services\Admin\Education\EducationServices;
use App\Services\Admin\Education\SurveyServices;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    private $surveyServices;

    public function __construct()
    {
        $this->surveyServices = (new SurveyServices());

        view()->share([
            'educationConfig' => getConfig('education'),
            'surveyConfig' => getConfig('survey'),
            'main_key' => 'M2',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.education.detail.survey.index', $this->surveyServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view('admin.education.detail.survey.upsert', $this->surveyServices->upsertService($request));
    }

    public function survey(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.education.survey.index', $this->surveyServices->surveyIndexService($request));
    }

    public function graph(Request $request)
    {
        return view('admin.education.detail.survey.graph', $this->surveyServices->graphService($request));
    }

    public function excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->surveyServices->indexService($request);
    }

    public function data(Request $request)
    {
        return $this->surveyServices->dataAction($request);
    }
}
