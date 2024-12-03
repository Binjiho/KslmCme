<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthServices;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authServices;

    public function __construct()
    {
        $this->authServices = (new AuthServices());

        view()->share([
            'main_key' => '',
            'sub_key' => '',
        ]);
    }

    public function signup(Request $request)
    {
        return view('auth.signup', $this->authServices->signupAction($request));
    }

    public function forgetPassword(Request $request)
    {
        return view('auth.forget-password');
    }

    public function data(Request $request)
    {
        return $this->authServices->dataAction($request);
    }
}
