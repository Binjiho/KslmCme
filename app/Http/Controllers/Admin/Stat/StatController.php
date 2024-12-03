<?php

namespace App\Http\Controllers\Admin\Stat;

use App\Http\Controllers\Controller;
use App\Services\Admin\Stat\StatServices;
use Illuminate\Http\Request;

class StatController extends Controller
{
    private $statServices;

    public function __construct()
    {
        $this->statServices = (new StatServices());
        view()->share([
            'statConfig' => getConfig('stat'),
            'main_key' => 'stat',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.stat.index', $this->statServices->indexService($request));
    }

    public function referer(Request $request)
    {
        view()->share(['sub_key' => 'S2']);
        return view('admin.stat.referer', $this->statServices->refererService($request));
    }

    public function data(Request $request)
    {
        return $this->statServices->dataAction($request);
    }
}
