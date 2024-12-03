<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// main
Route::controller(\App\Http\Controllers\Admin\Main\MainController::class)->group(function () {
    Route::get('/', 'main')->name('main');
    Route::post('data', 'data')->name('main.data');
});

// member
Route::controller(\App\Http\Controllers\Admin\Member\MemberController::class)->prefix('member')->group(function () {
    Route::get('/', 'index')->name('member');
    Route::get('upsert/{sid}', 'index')->name('member.upsert');
    Route::get('excel', 'excel')->name('member.excel');
    Route::post('data', 'data')->name('member.data');
});

// education
Route::controller(\App\Http\Controllers\Admin\Education\EducationController::class)->prefix('education')->group(function () {
    Route::get('/', 'index')->name('education');
    Route::get('upsert', 'upsert')->name('education.upsert');

    Route::get('payinfo', 'payinfo')->name('education.payinfo');
    Route::get('refundinfo', 'refundinfo')->name('education.refundinfo');

    Route::get('excel', 'excel')->name('education.excel');
    Route::post('data', 'data')->name('education.data');

    Route::controller(\App\Http\Controllers\Admin\Education\DetailLectureController::class)->prefix('lecture')->group(function () {
        Route::get('{esid}', 'index')->name('education.lecture');
        Route::get('upsert/{esid}', 'upsert')->name('education.lecture.upsert');

        Route::post('data', 'data')->name('education.lecture.data');
    });
    Route::controller(\App\Http\Controllers\Admin\Education\QuizController::class)->prefix('quiz')->group(function () {
        Route::get('{esid}', 'index')->name('education.quiz');
        Route::get('upsert/{esid}', 'upsert')->name('education.quiz.upsert');
        Route::get('graph/{esid}', 'graph')->name('education.quiz.graph');

        Route::post('data', 'data')->name('education.quiz.data');
    });
    Route::controller(\App\Http\Controllers\Admin\Education\SurveyController::class)->prefix('survey')->group(function () {
        Route::get('{esid}', 'index')->name('education.survey');
        Route::get('upsert/{esid}', 'upsert')->name('education.survey.upsert');
        Route::get('graph/{esid}', 'graph')->name('education.survey.graph');

        Route::post('data', 'data')->name('education.survey.data');
    });
    Route::controller(\App\Http\Controllers\Admin\Education\SacController::class)->prefix('sac')->group(function () {
        Route::get('{esid}', 'index')->name('education.sac');
        Route::get('deleted/{esid}', 'deleted')->name('education.sac.deleted');
        Route::get('upsert/{esid}', 'upsert')->name('education.sac.upsert');
        Route::get('graph/{esid}', 'graph')->name('education.sac.graph');

        Route::get('excel/{esid}', 'excel')->name('education.sac.excel');
        Route::get('cancle_excel/{esid}', 'cancle_excel')->name('education.sac.cancle_excel');
        Route::post('data', 'data')->name('education.sac.data');
    });
    Route::controller(\App\Http\Controllers\Admin\Education\ViewController::class)->prefix('view')->group(function () {
        Route::get('{esid}', 'index')->name('education.view');

        Route::get('view_excel/{esid}', 'view_excel')->name('education.view.excel');
        Route::post('data', 'data')->name('education.view.data');
    });
});

// lecture
Route::controller(\App\Http\Controllers\Admin\Lecture\LectureController::class)->prefix('lecture')->group(function () {
    Route::get('/', 'index')->name('lecture');
    Route::get('upsert', 'upsert')->name('lecture.upsert');
    Route::get('view', 'view')->name('lecture.view');

    Route::get('excel', 'excel')->name('lecture.excel');
    Route::post('data', 'data')->name('lecture.data');
});

// Workshop
Route::controller(\App\Http\Controllers\Admin\Workshop\WorkshopController::class)->prefix('workshop')->group(function () {
    Route::get('/', 'index')->name('workshop');
    Route::get('upsert', 'upsert')->name('workshop.upsert');

    Route::get('log', 'log')->name('workshop.log');
    Route::get('log_excel', 'log_excel')->name('workshop.log.excel');

    Route::get('excel', 'excel')->name('workshop.excel');
    Route::post('data', 'data')->name('workshop.data');

    Route::controller(\App\Http\Controllers\Admin\Workshop\SessionController::class)->prefix('{wsid}/session')->group(function () {
        Route::get('/', 'index')->name('workshop.session');
        Route::get('upsert/', 'upsert')->name('workshop.session.upsert');
        Route::get('collective/', 'collective')->name('workshop.session.collective');

        Route::get('excel', 'excel')->name('workshop.session.excel');
        Route::post('data', 'data')->name('workshop.session.data');
    });

    Route::controller(\App\Http\Controllers\Admin\Workshop\SubSessionController::class)->prefix('{wsid}/subsession')->group(function () {
        Route::get('sub-upsert/{reg_num}', 'index')->name('workshop.subsession');
        Route::get('sub-collective/', 'sub_collective')->name('workshop.subsession.collective');

        Route::post('data', 'data')->name('workshop.subsession.data');
    });
});


// 메일
Route::prefix('mail')->group(function () {
    Route::controller(\App\Http\Controllers\Admin\Mail\MailController::class)->group(function () {
        Route::get('/', 'index')->name("mail");
        Route::get('detail/{sid}', 'detail')->name("mail.detail");
        Route::get('upsert/{sid?}', 'upsert')->name("mail.upsert");
        Route::get('preview/{sid}', 'preview')->name("mail.preview");
        Route::post('data', 'data')->name('mail.data');
    });

    Route::controller(\App\Http\Controllers\Admin\Mail\MailAddressController::class)->prefix('address')->group(function () {
        Route::get('/', 'index')->name("mail.address");
        Route::get('upsert/{sid?}', 'upsert')->name("mail.address.upsert");

        Route::prefix('detail')->group(function () {
            Route::get('{ma_sid}', 'detail')->name("mail.address.detail");
            Route::get('{ma_sid}/upsert-{type}/{sid?}', 'detailUpsert')->name("mail.address.detail.upsert");
        });

        Route::post('data', 'data')->name('mail.address.data');
    });
});

// 접속통계
Route::controller(\App\Http\Controllers\Admin\Stat\StatController::class)->prefix('stat')->group(function () {
    Route::get('/', 'index')->name("stat");
    Route::get('referer', 'referer')->name("stat.referer");
    Route::get('data', 'data')->name("stat.data");
});

// auth
Route::controller(\App\Http\Controllers\Admin\Auth\LoginController::class)->prefix('auth')->group(function () {
    Route::post('logout', 'logout')->name('logout');
});

require __DIR__ . '/common.php';
