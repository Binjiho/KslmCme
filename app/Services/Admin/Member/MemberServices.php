<?php

namespace App\Services\Admin\Member;

use App\Models\User;
use App\Exports\MemberExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;

/**
 * Class MemberServices
 * @package App\Services
 */
class MemberServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = User::orderByDesc('created_at');

        if ($request->level) {
            $query->where('level', 'like', "%{$request->level}%");
        }
        if ($request->search_type) {
            $query->where($request->search_type, 'like', "%{$request->search_target}%");
        }

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['total'] = $query->count();
            $this->data['collection'] = $query->lazy();
            return (new CommonServices())->excelDownload(new MemberExcel($this->data), date('Y-m-d').'_회원정보');
        }

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['user'] = User::findOrFail($request->sid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'member-login':
                return $this->memberLogin($request);
                
            default:
                return notFoundRedirect();
        }
    }

    private function memberLogin(Request $request)
    {
        $member = User::findOrFail($request->sid);
        auth('web')->login($member);

        $url = env('APP_URL') . '/';
        return $this->returnJsonData('location', $this->ajaxActionLocation('replace', $url));
    }

}
