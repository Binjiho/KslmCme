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

class EduLecList extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'edu_lec_list';

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

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->esid = $data['esid'];
            $this->lsid = $data['lsid'];
        }
        $this->updated_at = date('Y-m-d H:i:s');
        $this->sort = $data['sort'] ?? 0;
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
