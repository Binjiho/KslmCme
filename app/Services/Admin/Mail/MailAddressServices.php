<?php

namespace App\Services\Admin\Mail;

use App\Models\MailAddress;
use App\Models\MailAddressDetail;
use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class MailAddressServices
 * @package App\Services
 */
class MailAddressServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = MailAddress::withCount('list')->orderByDesc('created_at');

        if($request->search && $request->keyword) {
            $query->where($request->search, 'like', "%{$request->keyword}%");
        }

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $sid = $request->sid;
        $this->data['address'] = empty($sid) ? [] : MailAddress::findOrFail($sid);

        return $this->data;
    }

    public function detailService(Request $request)
    {
        $this->data['address'] = MailAddress::findOrFail($request->ma_sid);

        $query = $this->data['address']->list()->orderByDesc('created_at');

        if($request->search && $request->keyword) {
            $query->where($request->search, 'like', "%{$request->keyword}%");
        }

        $list = $query->paginate(20)->appends(request()->except(['page']));
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function detailUpsertService(Request $request)
    {
        $sid = $request->sid;

        $this->data['addressDetail'] = empty($sid)
            ? null
            : MailAddressDetail::findOrFail($sid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'address-create':
                return $this->addressCreateService($request);
            case 'address-update':
                return $this->addressUpdateService($request);
            case 'address-delete':
                return $this->addressDeleteService($request);
            case 'collective-create':
                return $this->collectiveCreateService($request);
            case 'individual-create':
                return $this->individualCreateService($request);
            case 'individual-update':
                return $this->individualUpdateService($request);
            case 'addressDetail-delete':
                return $this->addressDetailDeleteService($request);
            case 'individual-delete':
                return $this->individualDeleteService($request);
            case 'all-delete':
                return $this->allDeleteService($request);
            default:
                return notFoundRedirect();
        }
    }

    private function addressCreateService(Request $request)
    {
        $this->transaction();

        try {
            $address = (new MailAddress());
            $address->setByData($request);
            $address->save();

            $this->dbCommit('관리자 메일 주소록 생성');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "생성 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function addressUpdateService(Request $request)
    {
        $this->transaction();

        try {
            $address = MailAddress::findOrFail($request->sid);
            $address->setByData($request);
            $address->update();

            $this->dbCommit('관리자 메일 주소록 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "수정 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function addressDeleteService(Request $request)
    {
        $this->transaction();

        try {
            $address = MailAddress::findOrFail($request->sid);
            $address->delete();

            $this->dbCommit('관리자 메일 주소록 삭제');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "삭제 되었습니다.",
                'location' => $this->ajaxActionLocation('reload')
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function collectiveCreateService(Request $request)
    {
        $this->transaction();

        try {
            foreach (json_decode($request->data) ?? [] as $data) {
                if (!empty($data->name) && !empty($data->email)) {
                    $data->ma_sid = $request->ma_sid;

                    $addressDetail = (new MailAddressDetail());
                    $addressDetail->setByData($data);
                    $addressDetail->save();
                }
            }

            $this->dbCommit('관리자 - 주소록 일괄 등록');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "등록 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function individualCreateService(Request $request)
    {
        $this->transaction();

        try {
            $addressDetail = (new MailAddressDetail());
            $addressDetail->setByData($request);
            $addressDetail->save();

            $this->dbCommit('관리자 - 주소록 개별 등록');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "등록 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function individualUpdateService(Request $request)
    {
        $this->transaction();

        try {
            $addressDetail = MailAddressDetail::findOrFail($request->sid);
            $addressDetail->setByData($request);
            $addressDetail->save();

            $this->dbCommit('관리자 - 주소록 개별 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "수정 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function addressDetailDeleteService(Request $request)
    {
        $this->transaction();

        try {
            $addressDetail = MailAddressDetail::findOrFail($request->sid);
            $addressDetail->delete();

            $this->dbCommit('관리자 - 주소록 명단 삭제');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "삭제 되었습니다.",
                'location' => $this->ajaxActionLocation('reload')
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
    private function individualDeleteService(Request $request)
    {
        $this->transaction();

        try {
            foreach ($request->sid as $item_sid){
                $addressDetail = MailAddressDetail::findOrFail($item_sid);
                $addressDetail->delete();
            }

            $this->dbCommit('관리자 - 주소록 명단 개별삭제');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "삭제 되었습니다.",
                'location' => $this->ajaxActionLocation('reload')
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function allDeleteService(Request $request)
    {
        $this->transaction();

        try {
            $addressDetail = MailAddressDetail::where(['ma_sid'=>$request->ma_sid])->get();
            foreach ($addressDetail as $ad){
                $ad->delete();
            }

            $this->dbCommit('관리자 - 주소록 명단 전체삭제');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "삭제 되었습니다.",
                'location' => $this->ajaxActionLocation('reload')
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}
