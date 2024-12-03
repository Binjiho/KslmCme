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

class Survey extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'survey';

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
            $this->esid = $data['esid'] ?? null;
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');

        $this->gubun = $data['gubun'] ?? null;
        $this->quiz = $data['quiz'] ?? null;
        $this->quiz_item_1 = $data['quiz_item_1'] ?? null;
        $this->quiz_item_2 = $data['quiz_item_2'] ?? null;
        $this->quiz_item_3 = $data['quiz_item_3'] ?? null;
        $this->quiz_item_4 = $data['quiz_item_4'] ?? null;
        $this->quiz_item_5 = $data['quiz_item_5'] ?? null;
        $this->sort = $data['sort'] ?? 0;
    }

    public function edu()
    {
        return $this->belongsTo(Education::class, 'esid');
    }

    public function survey_view_cnt($type='cnt')
    {
        if($type == 'cnt'){
            return SurveyView::where(['esid'=>$this->esid, 'survey_sid'=>$this->sid, 'del'=>'N'])->count();
        }else{
            return SurveyView::where(['esid'=>$this->esid, 'survey_sid'=>$this->sid, 'del'=>'N'])->get();
        }
    }

    public function survey_static($item, $type)
    {
        if($type == 'cnt'){
            return SurveyView::where(['esid'=>$this->esid, 'survey_sid'=>$this->sid, 'del'=>'N', 'answer'=>$item])->count();
        }else{
            $tot_cnt = $this->survey_view_cnt();
            $target_cnt = $this->survey_static($item,'cnt');
            return round( ($target_cnt/$tot_cnt)*100 );
        }
    }

}
