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

class SubSession extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'sub_sessions';

    protected $primaryKey = 'sid';

    protected $guarded = [
        'sid',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'field' => 'array',
    ];

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->wsid = $data['wsid'];
            $this->reg_num = $data['reg_num'];
        }
        $this->updated_at = date('Y-m-d H:i:s');
        $this->field = $data['field'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->pname = $data['pname'] ?? null;
        $this->psosok = $data['psosok'] ?? null;
        $this->video_link = $data['video_link'] ?? null;
        $this->sort = $data['sort'] ?? 0;

        /* 썸네일 파일 업로드 or 삭제 */
        if(!empty($data['file_key'])){
            $thumbnail = $data->file("thumbnail_arr")[$data['file_key']] ?? null; // 썸네일 첨부파일
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
                $directory = 'subsession/thumbnail';
                $uploadFile = (new CommonServices())->fileUploadService($thumbnail, $directory);
                $this->realfile = $uploadFile['realfile'];
                $this->filename = $uploadFile['filename'];
            }
        }


        /* 초록 파일 업로드 or 삭제 */
        if(!empty($data['file_key'])){
            $absfile = $data->file("absfile_arr")[$data['file_key']] ?? null; // 초록 첨부파일
            $absfileDel = $data->absfile_del ?? null; // 썸네일 파일삭제

            // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
            if ($absfileDel && !is_null($this->abs_realfile)) {
                (new CommonServices())->fileDeleteService($this->abs_realfile);

                // 썸네일 없다면 기존 파일경로 및 파일명 초기화
                if (is_null($absfile)) {
                    $this->abs_realfile = null;
                    $this->abs_filename = null;
                }
            }

            // 썸네일 있을경우 업로드후 경로 저장
            if ($absfile) {
                $directory = 'subsession/absfile';
                $uploadFile = (new CommonServices())->fileUploadService($absfile, $directory);
                $this->abs_realfile = $uploadFile['realfile'];
                $this->abs_filename = $uploadFile['filename'];
            }
        }

    }

    public function downloadUrl($etc=NULL) //첨부 파일 다운로드
    {
        return route('download', [
            'type' => 'only',
            'tbl' => 'sub_sessions',
            'etc' => $etc,
            'sid' => enCryptString($this->sid),
        ]);
    }
    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'wsid');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'reg_num', 'reg_num');
    }
}
