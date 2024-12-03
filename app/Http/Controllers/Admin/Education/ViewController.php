<?php

namespace App\Http\Controllers\Admin\Education;

use App\Http\Controllers\Controller;
use App\Services\Admin\Education\ViewServices;
use App\Services\Admin\Education\ClassServices;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    private $viewServices;

    public function __construct()
    {
        $this->viewServices = (new ViewServices());

        view()->share([
            'sacConfig' => getConfig('sac'),
            'main_key' => 'M2',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.education.detail.view.index', $this->viewServices->indexService($request));
    }

    public function view_excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->viewServices->indexService($request);
    }


    public function data(Request $request)
    {
        return $this->lessonServices->dataAction($request);
    }
}
