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

class Quiz extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'quiz';

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
//        'gubun'  => 'array',
    ];

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->esid = $data['esid'] ?? null;
            $this->created_at = date('Y-m-d H:i:s');
            $this->sort = $data['sort'] ?? 0;
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->quiz = $data['quiz'] ?? null;
        $this->quiz_item_1 = $data['quiz_item_1'] ?? null;
        $this->quiz_item_2 = $data['quiz_item_2'] ?? null;
        $this->quiz_item_3 = $data['quiz_item_3'] ?? null;
        $this->quiz_item_4 = $data['quiz_item_4'] ?? null;
        $this->quiz_item_5 = $data['quiz_item_5'] ?? null;
        $this->answer = $data['answer'] ?? null;

        /* 파일 업로드 or 삭제 */
        $file1 = $data->file("file1") ?? null; // 썸네일 첨부파일
        $file1Del = $data->file1_del ?? null; // 썸네일 파일삭제
        // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
        if ($file1Del && !is_null($this->realfile1)) {
            (new CommonServices())->fileDeleteService($this->realfile1);

            // 썸네일 없다면 기존 파일경로 및 파일명 초기화
            if (is_null($file1)) {
                $this->realfile1 = null;
                $this->filename1 = null;
            }
        }
        // pdf 파일 있을경우 업로드후 경로 저장
        if ($file1) {
            $directory = 'quiz';
            $uploadFile = (new CommonServices())->fileUploadService($file1, $directory);
            $this->realfile1 = $uploadFile['realfile'];
            $this->filename1 = $uploadFile['filename'];
        }

        /* 교육자료 파일 업로드 or 삭제 */
        $file2 = $data->file("file2") ?? null; // 썸네일 첨부파일
        $file2Del = $data->file2_del ?? null; // 썸네일 파일삭제
        // 파일 삭제이면서 기존 썸네일 있을경우 경로에 있는 실제 파일 삭제
        if ($file2Del && !is_null($this->realfile2)) {
            (new CommonServices())->fileDeleteService($this->realfile2);

            // 썸네일 없다면 기존 파일경로 및 파일명 초기화
            if (is_null($file2)) {
                $this->realfile2 = null;
                $this->filename2 = null;
            }
        }
        // pdf 파일 있을경우 업로드후 경로 저장
        if ($file2) {
            $directory = 'quiz';
            $uploadFile = (new CommonServices())->fileUploadService($file2, $directory);
            $this->realfile2 = $uploadFile['realfile'];
            $this->filename2 = $uploadFile['filename'];
        }
    }

    public function downloadUrl() //첨부 파일 다운로드
    {
        return route('download', [
            'type' => 'only',
            'tbl' => 'quiz',
            'sid' => enCryptString($this->sid),
        ]);
    }

    public function edu()
    {
        return $this->belongsTo(Education::class, 'esid');
    }

    public function quiz_view_cnt()
    {
        return QuizView::where(['esid'=>$this->esid, 'qsid'=>$this->sid, 'del'=>'N'])->count();
    }

    public function quiz_static($item, $type)
    {
        if($type == 'cnt'){
            return QuizView::where(['esid'=>$this->esid, 'qsid'=>$this->sid, 'del'=>'N', 'my_answer'=>$item])->count();
        }else{
            $tot_cnt = $this->quiz_view_cnt();
            $target_cnt = $this->quiz_static($item,'cnt');
            return round( ($target_cnt/$tot_cnt)*100 );
        }
    }
}
