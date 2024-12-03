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

class Sac extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'sac_info';

    protected $primaryKey = 'sid';

    protected $guarded = [
        'sid',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'del_request_at',
        'send_at',
        'pay_at',
        'complete_at',
        'quiz_at',
        'survey_at',
        'edu_at',
    ];

    protected $casts = [
//        'gubun'  => 'array',
    ];

    public function delete()
    {
        // 필드 값 업데이트
        $this->del = 'Y';
        $this->save();

        // 부모 delete 메서드를 호출하여 실제 삭제 수행
        return parent::delete();
    }

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->user_sid = $data['user_sid'] ?? null;
            $this->esid = $data['esid'] ?? null;
            $this->created_at = date('Y-m-d H:i:s');
            $this->tot_pay = $data['tot_pay'] ?? 0;
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->pay_method = $data['pay_method'] ?? null;
        $this->pay_status = $data['pay_status'] ?? 'I';
        $this->pay_at = $data['pay_at'] ?? null;

        $this->edu_status = $data['edu_status'] ?? 'N';
        $this->quiz_status = $data['quiz_status'] ?? 'N';
        $this->survey_status = $data['survey_status'] ?? 'N';

        $this->send_name= $data['send_name'] ?? null;
        $this->send_at = $data['send_at'] ?? null;
        $this->del = $data['del'] ?? 'N';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_sid');
    }

    public function edu()
    {
        return $this->belongsTo(Education::class, 'esid');
    }

    // lecture_view와의 관계 정의 (user_sid를 기준으로 연관된 lecture_view 항목을 가져옴)
    public function lectureViews()
    {
        return $this->hasMany(LectureView::class, 'ssid', 'sid');
    }

    public function edu_start_at()
    {
        $minCreatedAt = $this->lectureViews()->min('created_at');

        if ($minCreatedAt) {
            return \Carbon\Carbon::parse($minCreatedAt);
        }

        return null; // 값이 없을 경우 null을 반환
    }

    // lecture_view에서 created_at의 최소값을 구하는 함수
    public function getLectureViewMinCreatedAt()
    {
        $minCreatedAt = $this->lectureViews()->min('created_at');
        return $minCreatedAt ? \Carbon\Carbon::parse($minCreatedAt)->format('Y.m.d') : null;
    }

    public function lectures()
    {
        return $this->hasManyThrough(
            Lecture::class,      // 최종 타겟 모델
            EduLecList::class,   // 중간 모델
            'esid',              // EduLecList에서 외래 키 (education sid)
            'sid',               // Lecture에서 외래 키 (lecture sid)
            'esid',              // Sac에서 로컬 키 (education sid)
            'lsid'               // EduLecList에서 로컬 키 (lecture sid)
        );
    }

    public function getLectureCnt($user_sid, $type=null)
    {
        if($type == 'complete'){
            return LectureView::where(['user_sid'=>$user_sid,'ssid'=>$this->sid, 'esid'=>$this->esid, 'complete_status'=>'Y', 'del'=>'N'])->count();
        }else{
            return LectureView::where(['user_sid'=>$user_sid,'ssid'=>$this->sid, 'esid'=>$this->esid, 'del'=>'N'])->count();
        }
    }

    public function getQuizViewCnt($user_sid, $type=null)
    {
        if($type == 'complete'){
            return QuizView::where(['user_sid'=>$user_sid,'ssid'=>$this->sid, 'esid'=>$this->esid, 'del'=>'N'])->whereColumn('my_answer', 'quiz_answer')->count();
        }else if($type == 'percent'){
            $complete_cnt = QuizView::where(['user_sid'=>$user_sid,'ssid'=>$this->sid, 'esid'=>$this->esid, 'del'=>'N'])->whereColumn('my_answer', 'quiz_answer')->count();
            $tot_cnt = QuizView::where(['user_sid'=>$user_sid,'ssid'=>$this->sid, 'esid'=>$this->esid, 'del'=>'N'])->count();
            if($tot_cnt > 0 && $complete_cnt > 0){
                $percent = round( ($complete_cnt/$tot_cnt) * 100 );
            }else{
                $percent = 0;
            }
            return $percent;
        }else {
            return QuizView::where(['user_sid'=>thisPK(),'ssid'=>$this->sid, 'esid'=>$this->esid, 'del'=>'N'])->count();
        }
    }
}
