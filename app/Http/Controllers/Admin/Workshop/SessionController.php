<?php

namespace App\Http\Controllers\Admin\Workshop;

use App\Http\Controllers\Controller;
use App\Services\Admin\Workshop\SessionServices;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    private $sessionServices;

    public function __construct()
    {
        $this->sessionServices = (new SessionServices());

        view()->share([
            'educationConfig' => getConfig('education'),
            'workshopConfig' => getConfig('workshop'),
            'main_key' => 'M4',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S1']);
        return view('admin.workshop.session.index', $this->sessionServices->indexService($request));
    }
    public function upsert(Request $request)
    {
        return view('admin.workshop.session.upsert', $this->sessionServices->upsertService($request));
    }
    public function collective(Request $request)
    {
        return view('admin.workshop.session.collective', $this->sessionServices->collectiveService($request));
    }

    public function excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->sessionServices->indexService($request);
    }

    public function data(Request $request)
    {
        return $this->sessionServices->dataAction($request);
    }
}
