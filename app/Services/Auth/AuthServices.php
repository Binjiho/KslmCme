<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;
use App\Services\MailRealSendServices;

/**
 * Class AuthServices
 * @package App\Services
 */
class AuthServices extends AppServices
{
    public function signupAction(Request $request)
    {
        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'user-create':
                return $this->userCreate($request);

            case 'user-update':
                return $this->userUpdate($request);

            default:
                return notFoundRedirect();
        }
    }

    private function userCreate(Request $request)
    {
        $this->transaction();

        try {
            $user = new User();
            $user->setByData($request);
            $user->save();

            $this->dbCommit('회원가입 완료');

            auth('web')->login($user);

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '가입 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', getDefaultUrl()),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function userUpdate(Request $request)
    {
        $this->transaction();

        try {
            $user = User::findOrFail(thisPK());
            $user->setByData($request);
            $user->update();

            $this->dbCommit('회원정보 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '수정 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', route('mypage')),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}
