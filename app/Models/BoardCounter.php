<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardCounter extends Model
{
    use HasFactory;

    protected $primaryKey = 'sid';

    protected $guarded = [
        'sid',
        'b_sid',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function setByData($data)
    {
        $this->b_sid = $data['sid'];
        $this->ip = request()->ip();
    }
}
