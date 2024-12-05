<?php

namespace App\Services\Admin\Education;

use App\Models\Education;
use App\Models\Sac;

use App\Services\AppServices;
use App\Services\CommonServices;
use App\Services\MailRealSendServices;
use App\Exports\SacExcel;
use App\Exports\SacCancleExcel;
use Illuminate\Http\Request;

/**
 * Class EducationServices
 * @package App\Services
 */
class SacServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = Sac::orderBy('sid','desc');
        $query->where(['del'=>'N','esid'=>$request->esid])->whereNull('del_request');

        if ($request->pay_status) {
            $query->where('pay_status', 'like', "%{$request->pay_status}%");
        }
        if($request->search && $request->keyword) {
            $query->whereHas('user',function ($query) use ($request) {
                $query->where($request->search, 'like', "%{$request->keyword}%");
            });
        }

        $this->data['tot_cnt'] = $query->count();

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['total'] = $query->count();
            $this->data['collection'] = $query->lazy();
            return (new CommonServices())->excelDownload(new SacExcel($this->data), date('Y-m-d').'_신청회원정보');
        }

        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function deletedService(Request $request)
    {
        $query = Sac::orderBy('sid','desc');
        $query->where(['esid'=>$request->esid]);
        $query->whereNotNull('deleted_at');
        $query->withTrashed();

        if ($request->del_request) {
            $query->where('del_request', 'like', "%{$request->del_request}%");
        }
        if($request->search && $request->keyword) {
            $query->whereHas('user',function ($query) use ($request) {
                $query->where($request->search, 'like', "%{$request->keyword}%");
            });
        }

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['total'] = $query->count();
            $this->data['collection'] = $query->lazy();
            return (new CommonServices())->excelDownload(new SacCancleExcel($this->data), date('Y-m-d').'_취소신청정보');
        }


        $list = $query->paginate(20);
        $this->data['list'] = setListSeq($list);

        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['sac'] = Sac::where('sid', '=', $request->sid)->first();
        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function graphService(Request $request)
    {
        $this->data['sac'] = Sac::where('sid', '=', $request->sid)->first();
        $this->data['education'] = Education::findOrFail($request->esid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'sac-create':
                return $this->sacCreate($request);
            case 'sac-update':
                return $this->sacUpdate($request);
            case 'sac-delete':
                return $this->sacDelete($request);
            case 'change-sort':
                return $this->changeSort($request);
            case 'change-status':
                return $this->changeStatus($request);
            case 'change-cancle_status':
                return $this->changeCancleStatus($request);
            case 'change-restore':
                return $this->changeRestore($request);
            case 'file-delete':
                return $this->fileDelete($request);
            default:
                return notFoundRedirect();
        }
    }

    private function sacCreate(Request $request)
    {
        $this->transaction();

        try {
            $sac = (new Sac());

            $tot_count = Sac::where(['del'=>'N'])->count();
            $request->merge([ 'sort' => $tot_count+1 ]);

            $sac->setByData($request);
            $sac->save();

            $this->dbCommit('퀴즈 등록 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '퀴즈가 등록 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }
    private function sacUpdate(Request $request)
    {
        $this->transaction();

        try {
            $sac = Sac::findOrFail($request->sid);
            $sac->setByData($request);
            $sac->update();

            $this->dbCommit('퀴즈 수정 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '퀴즈가 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function sacDelete(Request $request)
    {
        $this->transaction();

        try {
            $sac = Sac::findOrFail($request->sid);
            $sac->del = 'Y';
            $sac->deleted_at = date('Y-m-d H:i:s');
            $sac->update();

            $this->dbCommit('퀴즈 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '퀴즈가 삭제 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function changeSort(Request $request)
    {

        $this->transaction();
        try {
            $sid_arr = explode(',',$request->array_sid);
            foreach ($sid_arr as $idx => $item){
                $sac = Sac::findOrFail($item);
                $sac->sort = $idx+1;
                $sac->update();
            }

            $this->dbCommit('퀴즈 순서 업데이트 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '순서가 수정되었습니다',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }

    private function changeStatus(Request $request)
    {
        $this->transaction();

        try {
            $sac = Sac::findOrFail($request->sid);
            if($request->target == 'C'/*결제완료*/){
                $sac->pay_status = $request->target;
                $sac->pay_at = date('Y-m-d H:i:s');
            }else if($request->target == 'T'){
                $sac->del_request = 'C'; //취소완료
                $sac->deleted_at = date('Y-m-d H:i:s');

                //메일 발송
                $mailData = [
                    'receiver_name' => $sac->user->name_kr,
                    'receiver_email' => $sac->user->email,
                    'body' => view("template.sac-refund", ['sac_info'=>$sac])->render(),
                    'etc' => $sac,
                ];

                $mailResult = (new MailRealSendServices())->mailSendService($mailData, 'sac-refund');

                if ($mailResult != 'suc') {
                    return $mailResult;
                }
                // END 메일발송
            }else{
                $sac->pay_status = $request->target;
                $sac->pay_at = '';
            }
            $sac->update();

            $this->dbCommit('교육신청 - 결제상태변경 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '상태가 변경 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function changeCancleStatus(Request $request)
    {
        $this->transaction();

        try {
            $sac = Sac::findOrFail($request->sid);

            if($request->target == 'C'){
                $sac->del_request = 'C'; //취소완료
                $sac->deleted_at = date('Y-m-d H:i:s');
                $sac->del = 'Y';

                //메일 발송
                $mailData = [
                    'receiver_name' => $sac->user->name_kr,
                    'receiver_email' => $sac->user->email,
                    'body' => view("template.sac-refund", ['sac_info'=>$sac])->render(),
                    'etc' => $sac,
                ];

                $mailResult = (new MailRealSendServices())->mailSendService($mailData, 'sac-refund');

                if ($mailResult != 'suc') {
                    return $mailResult;
                }
                // END 메일발송
            }else{
                $sac->del_request = 'I';
                $sac->deleted_at = null;
                $sac->del = 'N';
            }
            $sac->update();

            $this->dbCommit('교육신청 - 취소상태변경 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '상태가 변경 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function changeRestore(Request $request)
    {
        $this->transaction();

        try {
            $sac = Sac::withTrashed()->where(['sid'=>$request->sid])->first(); //findOrFail 은 deleted_at is null이 붙음

            $sac->del_request = null;
            $sac->del_request_at = null;
            $sac->del = 'N';
            $sac->deleted_at = null;

            $sac->update();

            $this->dbCommit('교육신청 - 취소신청 복구 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '상태가 변경 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
    private function fileDelete(Request $request)
    {
        $this->transaction();

        try {
            $board = Sac::where('realfile','=',$request->filePath)->first();
            (new CommonServices())->fileDeleteService($board->realfile);
            $board->filename = '';
            $board->realfile = '';
            $board->update();

            $this->dbCommit('퀴즈 파일 삭제 [어드민]');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '파일이 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e, true);
        }
    }

}
