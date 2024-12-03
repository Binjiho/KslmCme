<?php

namespace App\Models;

use App\Services\CommonServices;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Workshop extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'workshop';

    protected $primaryKey = 'sid';

    protected $guarded = [
        'sid',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'sdate',
        'edate',
    ];

    protected $casts = [
        'date'  => 'array',
        'room'  => 'array',
        'limit_level'  => 'array',
    ];

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->category = $data['category'] ?? null;
        $this->gubun = $data['gubun'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->place = $data['place'] ?? null;
        $this->date_type = $data['date_type'] ?? null;
        $this->sdate = $data['sdate'] ?? null;
        $this->edate = $data['edate'] ?? null;
        $this->date = $data['date'] ?? null;
        $this->room = $data['room'] ?? null;
        $this->limit_level = $data['limit_level'] ?? null;
        $this->main_yn = $data['main_yn'] ?? 'N';
        $this->poster_yn = $data['poster_yn'] ?? 'N';
        $this->hide = $data['hide'] ?? 'N';
        $this->del = $data['del'] ?? 'N';

        /* 썸네일 파일 업로드 or 삭제 */
        $thumbnail = $data->file("thumbnail") ?? null; // 썸네일 첨부파일
        $thumbnailDel = $data->thumbnail_del ?? null; // 썸네일 파일삭제

        // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
        if ($thumbnailDel && !is_null($this->realfile)) {
            (new CommonServices())->fileDeleteService($this->realfile);

            // 썸네일 없다면 기존 파일경로 및 파일명 초기화
            if (is_null($thumbnail)) {
                $this->realfile = null;
                $this->filename = null;
            }
        }

        // 썸네일 있을경우 업로드후 경로 저장
        if ($thumbnail) {
            $directory = 'workshop/thumbnail';
            $uploadFile = (new CommonServices())->fileUploadService($thumbnail, $directory);
            $this->realfile = $uploadFile['realfile'];
            $this->filename = $uploadFile['filename'];
        }


        /* 초록집 파일 업로드 or 삭제 */
        $absfile = $data->file("absfile") ?? null;
        $absfileDel = $data->absfile_del ?? null;

        // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
        if ($absfileDel && !is_null($this->abs_realfile)) {
            (new CommonServices())->fileDeleteService($this->abs_realfile);

            // 없다면 기존 파일경로 및 파일명 초기화
            if (is_null($absfile)) {
                $this->abs_realfile = null;
                $this->abs_filename = null;
            }
        }

        // 있을경우 업로드후 경로 저장
        if ($absfile) {
            $directory = 'workshop/absfile';
            $uploadFile = (new CommonServices())->fileUploadService($absfile, $directory);
            $this->abs_realfile = $uploadFile['realfile'];
            $this->abs_filename = $uploadFile['filename'];
        }


        /* 프로그램북 파일 업로드 or 삭제 */
        $bookfile = $data->file("bookfile") ?? null;
        $bookfileDel = $data->bookfile_del ?? null;

        // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
        if ($bookfileDel && !is_null($this->book_realfile)) {
            (new CommonServices())->fileDeleteService($this->book_realfile);

            // 없다면 기존 파일경로 및 파일명 초기화
            if (is_null($bookfile)) {
                $this->book_realfile = null;
                $this->book_filename = null;
            }
        }

        // 있을경우 업로드후 경로 저장
        if ($bookfile) {
            $directory = 'workshop/bookfile';
            $uploadFile = (new CommonServices())->fileUploadService($bookfile, $directory);
            $this->book_realfile = $uploadFile['realfile'];
            $this->book_filename = $uploadFile['filename'];
        }

        /* 프로그램북 파일 업로드 or 삭제 */
        $bookfile2 = $data->file("bookfile2") ?? null;
        $bookfileDel2 = $data->bookfile_del2 ?? null;

        // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
        if ($bookfileDel2 && !is_null($this->book_realfile2)) {
            (new CommonServices())->fileDeleteService($this->book_realfile2);

            // 없다면 기존 파일경로 및 파일명 초기화
            if (is_null($bookfile2)) {
                $this->book_realfile2 = null;
                $this->book_filename2 = null;
            }
        }

        // 있을경우 업로드후 경로 저장
        if ($bookfile2) {
            $directory = 'workshop/bookfile';
            $uploadFile = (new CommonServices())->fileUploadService($bookfile2, $directory);
            $this->book_realfile2 = $uploadFile['realfile'];
            $this->book_filename2 = $uploadFile['filename'];
        }

    }

    public function downloadUrl($etc=NULL) //첨부 파일 다운로드
    {
        return route('download', [
            'type' => 'only',
            'tbl' => 'workshop',
            'etc' => $etc,
            'sid' => enCryptString($this->sid),
        ]);
    }

    public function sub_session()
    {
        return $this->hasMany(SubSession::class, 'wsid', 'sid');
    }

    public function getHeart($sid)
    {
        return Heart::where(['user_sid'=>thisPK(), 'wsid'=>$sid, 'del'=>'N'])->first();
    }
}
