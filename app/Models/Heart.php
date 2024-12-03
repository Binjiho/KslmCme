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

class Heart extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'hearts';

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
            $this->user_sid = $data['user_sid'] ?? null;
            $this->esid = $data['esid'] ?? null;
            $this->wsid = $data['wsid'] ?? null;
            $this->type = $data['type'] ?? 'E';
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->del = $data['del'] ?? 'N';
    }

    public function edu()
    {
        return $this->belongsTo(Education::class, 'esid');
    }
    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'wsid');
    }
}
