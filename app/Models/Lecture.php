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

class Lecture extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'lectures';

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
        'field'  => 'array',
    ];

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->type = $data['type'] ?? null;
        $this->field = $data['field'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->name_kr = $data['name_kr'] ?? null;
        $this->sosok_kr = $data['sosok_kr'] ?? null;
        $this->link_url = $data['link_url'] ?? null;
        $this->lecture_time = $data['lecture_time'] ?? null;
        $this->play_time = $data['play_time'] ?? null;
        $this->play_yn = $data['play_yn'] ?? 'Y';
        $this->keyword = $data['keyword'] ?? null;

        /* pdf 파일 업로드 or 삭제 */
        $pdf_file = $data->file("pdf_file") ?? null; // 썸네일 첨부파일
        $pdf_fileDel = $data->pdf_file_del ?? null; // 썸네일 파일삭제
        // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
        if ($pdf_fileDel && !is_null($this->realfile1)) {
            (new CommonServices())->fileDeleteService($this->realfile1);

            // 썸네일 없다면 기존 파일경로 및 파일명 초기화
            if (is_null($pdf_file)) {
                $this->realfile1 = null;
                $this->filename1 = null;
            }
        }
        // pdf 파일 있을경우 업로드후 경로 저장
        if ($pdf_file) {
            $directory = 'lecture/pdf';
            $uploadFile = (new CommonServices())->fileUploadService($pdf_file, $directory);
            $this->realfile1 = $uploadFile['realfile'];
            $this->filename1 = $uploadFile['filename'];
        }

        /* 교육자료 파일 업로드 or 삭제 */
        $item_file = $data->file("item_file") ?? null; // 썸네일 첨부파일
        $item_fileDel = $data->item_file_del ?? null; // 썸네일 파일삭제
        // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
        if ($item_fileDel && !is_null($this->realfile2)) {
            (new CommonServices())->fileDeleteService($this->realfile2);

            // 썸네일 없다면 기존 파일경로 및 파일명 초기화
            if (is_null($item_file)) {
                $this->realfile2 = null;
                $this->filename2 = null;
            }
        }
        // pdf 파일 있을경우 업로드후 경로 저장
        if ($item_file) {
            $directory = 'lecture/item';
            $uploadFile = (new CommonServices())->fileUploadService($item_file, $directory);
            $this->realfile2 = $uploadFile['realfile'];
            $this->filename2 = $uploadFile['filename'];
        }
    }

    public function downloadUrl($number=1) //첨부 파일 다운로드
    {
        return route('download', [
            'type' => 'only',
            'tbl' => 'lectures',
            'etc' => $number,
            'sid' => enCryptString($this->sid),
        ]);
    }

    public function lecture_relation()
    {
        return $this->hasMany(EduLecList::class, 'lsid', 'sid');
    }

    public function getPercent($ssid,$lsid)
    {
        $percent = 0;
        $lec = Lecture::findOrFail($lsid);
        $lecView = LectureView::where(['user_sid'=>thisPK(), 'ssid'=>$ssid, 'lsid'=>$lsid, 'del'=>'N'])->first();
        if($lecView){
            if($lecView->lec->type == 'V'){
                $percent = round(($lecView->ing_time/$lec->lecture_time)*100);
            }else if($lecView->lec->type == 'P'){
                $percent = $lecView->pdf_percent;
            }
        }
        return $percent;
    }

    public function lec_view()
    {
        return LectureView::where(['lsid'=>$this->sid, 'user_sid'=>thisPK(), 'del'=>'N'])->first();
    }

}
