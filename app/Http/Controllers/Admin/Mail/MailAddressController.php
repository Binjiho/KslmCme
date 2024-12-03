<?php

namespace App\Http\Controllers\Admin\Mail;

use App\Http\Controllers\Controller;
use App\Services\Admin\Mail\MailAddressServices;
use Illuminate\Http\Request;

class MailAddressController extends Controller
{
    private $mailAddressServices;

    public function __construct()
    {
        $this->mailAddressServices = (new MailAddressServices());

        view()->share([
            'mailConfig' => getConfig('mail'),
            'main_key' => 'mail',
        ]);
    }

    public function index(Request $request)
    {
        view()->share(['sub_key' => 'S2']);
        return view('admin.mail.address.index', $this->mailAddressServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view('admin.mail.address.upsert', $this->mailAddressServices->upsertService($request));
    }

    public function detail(Request $request)
    {
        return view('admin.mail.address.detail.index', $this->mailAddressServices->detailService($request));
    }

    public function detailUpsert(Request $request)
    {
        return view("admin.mail.address.detail.upsert-{$request->type}", $this->mailAddressServices->detailUpsertService($request));
    }

    public function data(Request $request)
    {
        return $this->mailAddressServices->dataAction($request);
    }
}
