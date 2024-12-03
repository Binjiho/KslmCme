<?php

namespace App\Http\Controllers\Admin\Education;

use App\Http\Controllers\Controller;
use App\Services\Admin\Education\EducationServices;
use App\Services\Admin\Education\QuizServices;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    private $quizServices;

    public function __construct()
    {
        $this->quizServices = (new QuizServices());

        view()->share([
            'educationConfig' => getConfig('education'),
            'main_key' => 'M2',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.education.detail.quiz.index', $this->quizServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view('admin.education.detail.quiz.upsert', $this->quizServices->upsertService($request));
    }

    public function graph(Request $request)
    {
        return view('admin.education.detail.quiz.graph', $this->quizServices->graphService($request));
    }


    public function data(Request $request)
    {
        return $this->quizServices->dataAction($request);
    }
}
