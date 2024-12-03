<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'sid';
    protected $table = 'user_binfo';

    protected $guarded = [
        'sid',
    ];

    protected $dates = [
        'password_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'today_at',
        'del_confirm_date',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [

    ];

    public function setByData($data)
    {
        if(empty($this->sid)) {
            $this->uid = $data['uid'];
            $this->name_kr = $data['name_kr'];
            $this->password = Hash::make($data['password']);
            $this->password_at = date('Y-m-d H:i:s');
        }

        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->tel = $data['tel'];
        $this->sosok_kr = $data['sosok_kr'];
        $this->sosok_en = $data['sosok_en'];
        $this->depart_kr = $data['depart_kr'];
        $this->depart_en = $data['depart_en'];
        $this->position = $data['position'];
        $this->office_zipcode = $data['office_zipcode'];
        $this->office_addr1 = $data['office_addr1'];
        $this->office_addr2 = $data['office_addr2'];
        $this->level = $data['level'];
        $this->license_number = $data['license_number'];

    }

}
