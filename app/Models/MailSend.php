<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailSend extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sid';

    protected $guarded = [
        'sid',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function mail()
    {
        return $this->belongsTo(MailList::class, 'ml_sid');
    }
}
