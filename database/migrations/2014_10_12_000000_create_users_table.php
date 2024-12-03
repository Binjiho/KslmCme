<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('sid');
            $table->string('uid')->comment('ID');
            $table->string('password')->comment('비밀번호');
            $table->string('level', 10)->comment('회원등급');
            $table->string('email')->comment('이메일');
            $table->string('name_kr')->comment('이름');
            $table->string('license_number')->nullable()->comment('면허번호');
            $table->enum('is_admin', ['N', 'Y'])->default('N')->comment('관리자 유무');
            $table->timestamps();
            $table->timestamp('password_at')->nullable()->comment('비밀번호 변경 시간');
            $table->softDeletes();
        });

        \App\Models\User::create([
            'uid' => 'webmaster',
            'password' => \Illuminate\Support\Facades\Hash::make('123123'),
            'level' => '1',
            'email' => 'sh.han@m2community.co.kr',
            'name_kr' => '엠투커뮤니티',
            'license_number' => '123123',
            'is_admin' => 'Y',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
