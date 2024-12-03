<?php

namespace App\Services\Board;

use App\Models\Board;
use App\Models\BoardComment;
use App\Models\BoardFile;
use App\Models\BoardPopup;
use App\Models\BoardCounter;
use App\Models\BoardReply;
use App\Models\BoardReplyCounter;
use App\Models\BoardReplyFile;
use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class BoardServices
 * @package App\Services
 */
class BoardServices extends AppServices
{
    private function getNoticeList($code)
    {
        $noticeQuery = Board::where([
            'code' => $code,
            'notice' => 'Y'
        ])
            ->withCount('files')
            ->orderByDesc('sid');

        if (!isAdmin()) {
            $noticeQuery->where('hide', 'N');
        }

        return $noticeQuery->limit('10');
    }
    public function listService(Request $request)
    {
        $code = $request->code;
        $search = $request->search;
        $keyword = $request->keyword;
        $boardConfig = getConfig("board")[$code];

        $query = Board::where('code', $code)->withCount('files')->orderByDesc('sid');

        if($code == 'faq'){

        }else{
            if (!isAdmin()) {
                $query->where('hide', 'N');
            }
        }

        if (!empty($search) && !empty($keyword)) {
            switch ($search) {
                case 'contents':
                    $query->where(function ($q) use ($keyword) {
                        $q->where('subject', 'like', "%{$keyword}%")
                        ->orWhere('contents', 'like', "%{$keyword}%");
                    });
                    break;
                default:
                    $query->where($search, 'like', "%{$keyword}%");
                    break;
            }
        }

        // 게시판 공지 사항 사용시 공지사항 리스트 추가 & 공지사항 제외하고 리스트 뽑기
        if ($boardConfig['use']['notice']) {
            $noticeQuery = $this->getNoticeList($code);

            $this->data['notice_list'] = $noticeQuery->get();
            $query->whereNotIn('sid', $this->data['notice_list']->pluck('sid'));
        }

        $list = $query->paginate($boardConfig['paginate']);
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $sid = $request->sid ?? null;
        $this->data['board'] = empty($sid) ? null : Board::withCount('files')->findOrFail($sid);
        $this->data['popup'] = $this->data['board']->popups ?? null;

        return $this->data;
    }

    public function viewService(Request $request)
    {
        $this->data['board'] = Board::withCount('files')->findOrFail($request->sid);
        $this->refCounter($request); // 조회수 업데이트

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'board-create':
                return $this->boardCreate($request);

            case 'board-update':
                return $this->boardUpdate($request);

            case 'board-delete':
                return $this->boardDelete($request);

            case 'board-reply':
                return $this->boardReply($request);

            case 'board-hide':
                return $this->boardHide($request);

            case 'popup-preview':
                return $this->popupPreview($request);

            default:
                return notFoundRedirect();
        }
    }

    private function listUrl()
    {
        return route('board', ['code' => request()->code]);
    }

    private function boardCreate(Request $request)
    {
        $this->transaction();

        try {
            $board = new Board();
            $board->setByData($request);
            $board->save();

            $this->dbCommit("게시글 등록");

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '게시글이 등록 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl()),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function boardUpdate(Request $request)
    {
        $this->transaction();

        try {
            $board = Board::findOrFail($request->sid);
            $board->setByData($request);
            $board->update();

            $this->dbCommit('게시글 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '게시글이 수정 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl()),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function boardDelete(Request $request)
    {
        $this->transaction();

        try {
            $board = Board::findOrFail($request->sid);
            $board->delete();

            $this->dbCommit('게시글 삭제');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '게시글이 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl()),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function boardReply(Request $request)
    {
        $this->transaction();

        try {
            $board = Board::findOrFail($request->sid);
            $board->contents = $request->contents;
            $board->update();

            $this->dbCommit('게시글 관리자 답변');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '게시글 답변이 완료 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl()),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function boardHide(Request $request)
    {
        $this->transaction();

        try {
            $board = Board::findOrFail($request->sid);
            $board->hide = $request->target ?? 'N';
            $board->update();


            $this->dbCommit('게시글 공개여부 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '게시글의 공개여부가 수정 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl()),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

//    private function boardPopupPreview(Request $request)
//    {
//        $request->merge(['sid' => 0]);
//        $this->data['popup'] = [(object)$request->all()];
//
//        return $this->returnJsonData('append', [
//            $this->ajaxActionHtml('body', view('common.popup.template'.$request->popup_skin, $this->data)->render()),
//        ]);
//    }

    private function popupPreview(Request $request)
    {

        $files = [];
        $popupSkin = $request->popup_skin;

        if ($request->sid != 0) {
            foreach (BoardFile::where('b_sid', $request->sid)->whereNotIn('sid', $request->plupload_file_del ?? [])->get() as $row) {
                $files[] = (object)['filename' => $row->filename, 'download' => $row->download];
            }
        }

        foreach ($request->plupload ?? [] as $key => $val) {
            $files[] = (object)['filename' => $val, 'download' => 0];
        }

        $this->data['board'] = (object)$request->all();
        $this->data['board']->files = $files;
        $this->data['board']->files_count = count($files);

        $this->data['popup'] = (object)[
            'sid' => $request->sid ?? 0,
            'width' => $request->width ?? 500,
            'height' => $request->height ?? 400,
            'position_x' => $request->position_x ?? 0,
            'position_y' => $request->position_y ?? 0,
            'popup_detail' => $request->popup_detail ?? '',
            'popup_link' => $request->popup_link ?? '',
            'popup_skin' => $popupSkin,
            'subject' => $request->subject ?? '',
            'popup_contents' => ($request->popup_select == '1') ? $request->contents : $request->popup_contents,
        ];

        $this->data['preview'] = true;

        return $this->returnJsonData('append', [
            $this->ajaxActionHtml('body', view("common.popup.template{$popupSkin}", $this->data)->render()),
        ]);
    }

    private function refCounter(Request $request)
    {
        // ip 기준으로 조회수 하루에 한번씩
        $check = BoardCounter::whereRaw("DATE_FORMAT(created_at, '%Y%m%d') = ?", [now()->format('Ymd')])
            ->where([
                'b_sid' => $request->sid,
                'ip' => $request->ip()
            ])->exists();


        if (!$check) {
            $boardCounter = new BoardCounter();
            $boardCounter->setByData($request);
            $boardCounter->save();

            $this->data['board']->increment('ref');
        }
    }
}
