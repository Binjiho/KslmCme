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

class WorkshopLog extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'workshop_log';

    protected $primaryKey = 'sid';

    protected $guarded = [
        'sid',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->user_sid = $data['user_sid'];
            $this->wsid = $data['wsid'];
            $this->sub_sid = $data['sub_sid'];
            $this->log_type = $data['log_type'];
        }
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'wsid');
    }

    public function sub()
    {
        return $this->belongsTo(SubSession::class, 'sub_sid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_sid');
    }
}
