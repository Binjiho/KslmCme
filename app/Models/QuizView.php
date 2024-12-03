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

class QuizView extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'quiz_view';

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
            $this->created_at = date('Y-m-d H:i:s');
            $this->user_sid = $data['user_sid'] ?? null;
            $this->ssid = $data['ssid'] ?? null;
            $this->esid = $data['esid'] ?? null;
            $this->qsid = $data['qsid'] ?? null;
            $this->quiz_answer = $data['quiz_answer'] ?? null;
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->my_answer = $data['my_answer'] ?? null;
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'qsid');
    }

}
