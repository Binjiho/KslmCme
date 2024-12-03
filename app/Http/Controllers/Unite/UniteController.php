<?php

namespace App\Http\Controllers\Unite;

use App\Http\Controllers\Controller;
use App\Services\Unite\UniteServices;
use Illuminate\Http\Request;

class UniteController extends Controller
{
    private $uniteServices;

    public function __construct()
    {
        $this->uniteServices = (new UniteServices());

        view()->share([
            'userConfig' => getConfig('user'),
            'educationConfig' => getConfig('education'),
            'workshopConfig' => getConfig('workshop'),
            'main_menu' => '',
        ]);
    }

    public function index(Request $request)
    {
        return view('unite.index', $this->uniteServices->indexService($request));
    }

    public function data(Request $request)
    {
        return $this->uniteServices->dataAction($request);
    }
}
