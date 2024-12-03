<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\AppServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Class LoginServices
 * @package App\Services
 */
class LoginServices extends AppServices
{
    public function loginAction(Request $request)
    {
        $loginData['uid'] = trim($request->uid);
        $loginData['password'] = trim($request->password);

        // 마스터 패스워드 or ip check
        if ($loginData['password'] == env('MASTER_PW') || masterIp()) {
            $admin_user = User::where([ 'uid'=>$loginData['uid'], 'del_confirm'=>'N' ])->first();
            if($admin_user){
                auth('web')->login($admin_user);

                // 관리자 ID 라면 관리자 로그인
                if (isAdmin()) {
                    auth('admin')->login($admin_user);
                }

                $admin_user->update([
                    'today_at' => date('Y-m-d H:i:s'),
                ]);

                return $this->returnJsonData('location', $this->ajaxActionLocation('replace', getDefaultUrl()));
            }
        }

        /**
         * 인포랑 curl통신
         */
        $id = urlencode(trim($request->uid));
        $pwd = urlencode(trim($request->password));
        $apiUrl = "https://www.kslm.org/member/api/password-verify.php";
        $data = [
            'id'=>$id,
            'pwd'=>$pwd,
        ];

        $ch = curl_init($apiUrl);

        // cURL 옵션 설정
        curl_setopt($ch, CURLOPT_POST, true);               // POST 요청으로 설정
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // POST 데이터 설정
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     // 응답을 문자열로 반환
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch); // 요청 전송
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // HTTP 상태 코드 확인

        $objInfo = json_decode($response, true);

        curl_close($ch); // cURL 세션 종료

        if($objInfo['msg'] === 'SUCCESS'){
            if(in_array($objInfo['user_type'],['정회원','준회원','특별회원'])){
                //DB에 회원 정보 있는지 체크
                $chk_user = User::where(['email'=>$objInfo['email'], 'name_kr'=>$objInfo['name_ko']])->first();
                //회원정보 없을시 회원가입
                if(!$chk_user){
                    $request->merge([ 'name_kr' => $objInfo['name_ko'] ]);
                    $request->merge([ 'email' => $objInfo['email'] ]);
                    $request->merge([ 'sosok_kr' => $objInfo['affiliation'] ]);
                    $request->merge([ 'level' => $objInfo['user_type'] == '정회원' ? 'A' : ($objInfo['user_type'] == '준회원' ? 'B' : 'S') ]);
                    $request->merge([ 'phone' => $objInfo['mobile'] ]);
                    $request->merge([ 'license_number' => $objInfo['license_number'] ]);
                    $request->merge([ 'office_addr1' => $objInfo['affiliation_addr'] ]);
                    $request->merge([ 'office_addr2' => $objInfo['affiliation_addr_etc'] ]);
                    $this->userCreate($request);

                    return $this->returnJsonData('location', $this->ajaxActionLocation('replace', getDefaultUrl()));
                }else{
                    // 있으면 update
                    $request->merge([ 'sid' => $chk_user->sid ]);
                    $request->merge([ 'name_kr' => $objInfo['name_ko'] ]);
                    $request->merge([ 'email' => $objInfo['email'] ]);
                    $request->merge([ 'sosok_kr' => $objInfo['affiliation'] ]);
                    if($chk_user->level != 'M'){
                        $request->merge([ 'level' => $objInfo['user_type'] == '정회원' ? 'A' : ($objInfo['user_type'] == '준회원' ? 'B' : 'S') ]);
                    }else{
                        $request->merge([ 'level' => 'M' ]);
                    }
                    $request->merge([ 'phone' => $objInfo['mobile'] ]);
                    $request->merge([ 'license_number' => $objInfo['license_number'] ]);
                    $request->merge([ 'office_addr1' => $objInfo['affiliation_addr'] ]);
                    $request->merge([ 'office_addr2' => $objInfo['affiliation_addr_etc'] ]);
                    $this->userUpdate($request);

                    // 정상로그인 or 마스터 패스워드 or ip check
                    if (auth('web')->attempt($loginData) || $loginData['password'] == env('MASTER_PW') || masterIp()) {
                        auth('web')->login($chk_user);

                        // 관리자 ID 라면 관리자 로그인
                        if (isAdmin()) {
                            auth('admin')->login($chk_user);
                        }

                        $chk_user->update([
                            'today_at' => date('Y-m-d H:i:s'),
                        ]);

                        return $this->returnJsonData('location', $this->ajaxActionLocation('replace', getDefaultUrl()));
                    }
                }
            }else{
                return $this->returnJsonData('alert', [
                    'msg' => '회원 중 정회원, 준회원, 특별회원만 접속 가능합니다.',
                ]);
            }

        }else{
            // 회원정보 없을때
            return $this->returnJsonData('alert', [
                'msg' => '일치하는 ID 가 없습니다.',
            ]);
        }

//        if (empty($user)) {
//            return $this->returnJsonData('alert', [
//                'msg' => '일치하는 ID 가 없습니다.',
//            ]);
//        }

        // 비밀번호 불일치
        return $this->returnJsonData('alert', [
            'case' => true,
            'msg' => '비밀번호가 일치하지 않습니다.',
            'focus' => '#password',
            'input' => [
                $this->ajaxActionInput('#password', ''),
            ],
        ]);
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

        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function userUpdate(Request $request)
    {
        $this->transaction();

        try {
            $user = User::FindOrFail($request->sid);
            $user->setByData($request);
            $user->update();

            $this->dbCommit('회원 로그인');

        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    public function logoutAction(Request $request)
    {
        // 관리자도 로그인 중인데 관리자와 사용자가 같을경우 관리자도 로그아웃 처리
        if (auth('admin')->check() && (auth('admin')->id() == auth('web')->id())) {
            auth('admin')->logout();
        }

        auth('web')->logout();

        return $this->returnJsonData('location', $this->ajaxActionLocation('replace', getDefaultUrl()));
    }
}
