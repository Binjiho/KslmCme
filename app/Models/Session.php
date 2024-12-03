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

class Session extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'sessions';

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

    ];

    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($session) {

            $session->sub_session()->each(function ($file) {
                $file->delete();
            });
            
        });

    }

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->wsid = $data['wsid'];
            $this->reg_num = $data['reg_num'] ?? null;
        }
        $this->updated_at = date('Y-m-d H:i:s');
        $this->date = $data['date'] ?? null;
        $this->room = $data['room'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->chair = $data['chair'] ?? null;
        $this->sort = $data['sort'] ?? 0;
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'wsid');
    }

    public function sub_session($wsid = null)
    {
        $result = $this->hasMany(SubSession::class, 'reg_num', 'reg_num');

        if ($wsid) {
            $result->where('wsid', $wsid);
        }

        return $result;
    }
}
