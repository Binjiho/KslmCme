<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailSendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_sends', function (Blueprint $table) {
            $table->id('sid');
            $table->string('wiseu_seq')->comment('WiseUMailInterface.SEQ');
            $table->bigInteger('ml_sid')->unsigned()->comment('mail_lists.sid');
            $table->string('receiver_name')->comment('받는사람');
            $table->string('receiver_email')->comment('받는사람 이메일');
            $table->string('subject')->comment('메일제목');
            $table->mediumText('contents')->comment('메일 내용');
            $table->enum('status', ['S', 'F', 'R'])->default('R')->comment('메일발송상태');
            $table->string('status_msg')->default('발송대기')->comment('메일발송상태 메세지');
            $table->timestamps();
            $table->softDeletes();
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE mail_sends comment '메일 발송 내역 테이블'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_sends');
    }
}
