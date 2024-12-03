<?php

namespace App\Console\Commands;

use App\Models\Sac;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckEduStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:edu_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change edu_status Daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 현재 시간 가져오기
        $now = now();

        // 교육 수강일은 있는데 수강기간이 지나버리면 미수료 상태로 UPDATE
        $sacs = Sac::where(['del'=>'N'])->where('edu_status', '!=', 'C')->get();
        foreach ($sacs as $sac){
            if(!empty($sac->edu_start_at())){ //최초 수강일은 있을때
                if($sac->edu->edu_limit_yn != 'N'){
                    if($sac->edu->edu_edate <= $now){
                        Log::channel('scheduleLog')->error("================================== SCHEDULE LOG ===================================");
                        Log::channel('scheduleLog')->error("스케줄러 ChangeEduStatus SAC_SID : {$sac->sid} USER_SID :{$sac->user_sid} preview_edu_status :{$sac->edu_status} 최초수강일 :{$sac->edu_start_at()} 시간 : ".date('Y-m-d H:i:s'));
                        Log::channel('scheduleLog')->error("===================================================================================");

                        $sac->edu_status = 'F'; //미수료
                        $sac->update();
                    }
                }
            }
        }

        return 0;
    }
}
