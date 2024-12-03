<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\LoginServices;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $loginServices;

    public function __construct()
    {
        $this->loginServices = (new LoginServices());

        view()->share([
            'main_menu' => '',
            'sub_menu' => '',
        ]);
    }

    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('auth.login');
        }
        return $this->loginServices->loginAction($request);
    }

    public function logout(Request $request)
    {
        return $this->loginServices->logoutAction($request);
    }
}
