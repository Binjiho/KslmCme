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

class SurveyView extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'survey_view';

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
            $this->survey_sid = $data['survey_sid'] ?? null;
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->answer = $data['answer'] ?? null;
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'qsid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_sid');
    }

}
