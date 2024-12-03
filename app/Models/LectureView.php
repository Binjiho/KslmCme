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

class LectureView extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'lecture_view';

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
            $this->created_at = date('Y-m-d H:i:s');
            $this->user_sid = $data['user_sid'] ?? null;
            $this->ssid = $data['ssid'] ?? null;
            $this->esid = $data['esid'] ?? null;
            $this->lsid = $data['lsid'] ?? null;
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->ing_time = $data['ing_time'] ?? null;
        $this->pdf_percent = $data['pdf_percent'] ?? null;
        $this->complete_status = $data['complete_status'] ?? null;
    }

    public function edu()
    {
        return $this->belongsTo(Education::class, 'esid');
    }
    public function lec()
    {
        return $this->belongsTo(Lecture::class, 'lsid');
    }
}
