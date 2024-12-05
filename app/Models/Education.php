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

class Education extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'educations';

    protected $primaryKey = 'sid';

    protected $guarded = [
        'sid',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'regist_sdate',
        'regist_edate',
        'edu_sdate',
        'edu_edate',
    ];

    protected $casts = [

    ];

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->category = $data['category'] ?? null;
        $this->gubun = $data['gubun'] ?? null;
        $this->hide = $data['hide'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->contents = $data['contents'] ?? null;
        $this->condition_yn = $data['condition_yn'] ?? null;
        $this->pre_esid = $data['pre_esid'] ?? null;
        $this->quiz_yn = $data['quiz_yn'] ?? null;
        $this->quiz_cnt = $data['quiz_cnt'] ?? null;
        $this->pass_cnt = $data['pass_cnt'] ?? null;
        $this->survey_yn = $data['survey_yn'] ?? null;
        $this->certi_yn = $data['certi_yn'] ?? null;
        $this->certi_code = $data['certi_code'] ?? null;
        $this->regist_sdate = $data['regist_sdate'] ?? null;
        $this->regist_edate = $data['regist_edate'] ?? null;
        $this->regist_limit_yn = $data['regist_limit_yn'] ?? null;
        $this->edu_sdate = $data['edu_sdate'] ?? null;
        $this->edu_edate = $data['edu_edate'] ?? null;
        $this->edu_limit_yn = $data['edu_limit_yn'] ?? null;
        $this->free_yn = $data['free_yn'] ?? null;
        $this->cost = $data['cost'] ?? null;
        $this->pay_method = $data['pay_method'] ?? null;
        $this->bank_name = $data['bank_name'] ?? null;
        $this->account_num = $data['account_num'] ?? null;
        $this->account_name = $data['account_name'] ?? null;
        $this->pay_info = $data['pay_info'] ?? null;

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
            $directory = 'education/thumbnail';
            $uploadFile = (new CommonServices())->fileUploadService($thumbnail, $directory);
            $this->realfile = $uploadFile['realfile'];
            $this->filename = $uploadFile['filename'];
        }
    }

    public function downloadUrl() //첨부 파일 다운로드
    {
        return route('download', [
            'type' => 'only',
            'tbl' => 'educations',
            'sid' => enCryptString($this->sid),
        ]);
    }
    public function education_relation()
    {
        return $this->hasMany(EduLecList::class, 'esid', 'sid');
    }

    public function lectures()
    {
        return $this->hasManyThrough(
            Lecture::class,      // 최종 타겟 모델
            EduLecList::class,   // 중간 모델
            'esid',              // EduLecList에서 외래 키 (education sid)
            'sid',               // Lecture에서 외래 키 (lecture sid)
            'sid',               // Education에서 로컬 키 (education sid)
            'lsid'               // EduLecList에서 로컬 키 (lecture sid)
        )
        ->whereHas('lecture_relation.edu', function ($query) {
            $query->where('hide', 'N');
        });
    }

    public function LecturesWithHideN($query)
    {
        return $query->whereHas('lectures', function ($query) {
            $query->where('educations.hide', 'N');
        });
    }
    public function selfEducation($sid)
    {
        return Education::where(['sid'=>$sid])->first();
    }

    public function getHeart($sid)
    {
        return Heart::where(['user_sid'=>thisPK(), 'esid'=>$sid, 'del'=>'N'])->first();
    }

    public function isEduOpen()
    {
        if ( $this->edu_limit_yn == 'N'){
            if( date('Y-m-d') < $this->edu_sdate->format('Y-m-d') ){
                return false;
            }else{
                return true;
            }
        }else{
            if( ( date('Y-m-d') < $this->edu_sdate->format('Y-m-d') || date('Y-m-d') > $this->edu_edate->format('Y-m-d') ) ){
                return false;
            }else{
                return true;
            }
        }
    }

    public function isRegistOpen()
    {
        if ( $this->regist_limit_yn == 'N'){
            if( date('Y-m-d') < $this->regist_sdate->format('Y-m-d') ){
                return false;
            }else{
                return true;
            }
        }else{
            if( ( date('Y-m-d') < $this->regist_sdate->format('Y-m-d') || date('Y-m-d') > $this->regist_edate->format('Y-m-d') ) ){
                return false;
            }else{
                return true;
            }
        }
    }

    public function sac_cnt($type = null)
    {
        if($type == 'complete'){
            return Sac::where(['esid'=>$this->sid, 'del'=>'N', 'complete_yn'=>'Y'])->whereNull('del_request')->count();
        }else if($type == 'education'){
            return Sac::where(['esid'=>$this->sid, 'del'=>'N', 'edu_status'=>'C'])->whereNull('del_request')->count();
        }else if($type == 'quiz'){
            return Sac::where(['esid'=>$this->sid, 'del'=>'N', 'quiz_status'=>'C'])->whereNull('del_request')->count();
        }else if($type == 'survey'){
            return Sac::where(['esid'=>$this->sid, 'del'=>'N', 'survey_status'=>'C'])->whereNull('del_request')->count();
        }else {
            return Sac::where(['esid'=>$this->sid, 'del'=>'N'])->whereNull('del_request')->count();
        }
    }


}
