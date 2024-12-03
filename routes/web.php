<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

//  Route::get('/phpinfo', function () {
//      phpinfo();
//  });

// main
Route::controller(\App\Http\Controllers\Main\MainController::class)->group(function () {
    Route::get('/', 'main')->name('main');
    Route::post('data', 'data')->name('main.data');
});

// unite 통합검색
Route::controller(\App\Http\Controllers\Unite\UniteController::class)->prefix('unite')->group(function () {
    Route::get('/{search_key?}', 'index')->name('unite');
    Route::post('data', 'data')->name('unite.data');
});

// Education 수강/열람신청
Route::controller(\App\Http\Controllers\Education\EducationController::class)->prefix('education')->group(function () {
    Route::get('/', 'index')->name('education');
    Route::get('/{esid}', 'detail')->name('education.detail');
    Route::get('upsert/{esid}', 'upsert')->middleware('auth.check')->name('education.detail.upsert');

    Route::post('data', 'data')->name('education.data');
});

// Workshop 학술자료실
Route::controller(\App\Http\Controllers\Workshop\WorkshopController::class)->prefix('workshop')->group(function () {
    Route::get('/', 'index')->name('workshop');
    Route::get('/{wsid}', 'detail')->name('workshop.detail');
    Route::get('popup/{wsid}', 'popup')->name('workshop.popup');

    Route::post('data', 'data')->name('workshop.data');
});

// mypage
Route::controller(\App\Http\Controllers\Mypage\MypageController::class)->middleware('auth.check')->prefix('mypage')->group(function () {
    Route::get('/', 'index')->name('mypage');

    //교육수강
    Route::controller(\App\Http\Controllers\Mypage\EducationController::class)->group(function () {
        Route::get('/education', 'index')->name('mypage.education');
        Route::get('education/{ssid}', 'detail')->name('mypage.education.detail');
        Route::get('play', 'play')->name('education.play');

        Route::get('quiz/{ssid}', 'quiz')->name('mypage.quiz');
        Route::get('requiz/{ssid}', 'requiz')->name('mypage.requiz');
        Route::get('quiz_result/{ssid}', 'quiz_result')->name('mypage.quiz_result');
        Route::get('survey/{ssid}', 'survey')->name('mypage.survey');
        Route::get('survey_result/{ssid}', 'survey_result')->name('mypage.survey_result');

        Route::post('mypage-education/data', 'data')->name('mypage.education.data');
    });

    //교육신청/취소
    Route::get('/list', 'list')->name('mypage.list');
    Route::get('/payinfo', 'payinfo')->name('mypage.payinfo');
    Route::get('/receipt', 'receipt')->name('mypage.receipt');
    Route::get('/cancle', 'cancle')->name('mypage.cancle');

    //이수증 출력
    Route::get('/certi', 'certi')->name('mypage.certi');
    Route::get('/certi_detail/{ssid}', 'certi_detail')->name('mypage.certi.detail');

    //관심교육
    Route::get('/interest/edu', 'interest_edu')->name('mypage.interest_edu');

    //나의 자료실
    Route::controller(\App\Http\Controllers\Mypage\WorkshopController::class)->group(function () {
        Route::get('/interest/workshop', 'interest_workshop')->name('mypage.interest_workshop');

        Route::get('/workshop_log', 'workshop_log')->name('mypage.workshop_log');

        Route::post('mypage-workshop/data', 'data')->name('mypage.workshop.data');
    });

    //개인 정보
    Route::controller(\App\Http\Controllers\Mypage\MyInfoController::class)->group(function () {
        Route::get('/myInfo', 'myInfo')->name('mypage.myInfo');
    });

    Route::post('data', 'data')->name('mypage.data');
});

// auth
Route::prefix('auth')->group(function () {
    Route::controller(\App\Http\Controllers\Auth\AuthController::class)->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('signup', 'signup')->name('auth.signup');
            Route::get('forget-password', 'forgetPassword')->name('auth.forget-password');
        });

        Route::post('data', 'data')->name('auth.data');
    });

    Route::controller(\App\Http\Controllers\Auth\LoginController::class)->group(function () {
        Route::match(['get', 'post'], 'login', 'login')->middleware('guest')->name('login');
        Route::post('logout', 'logout')->middleware('auth.check')->name('logout');
    });
});

require __DIR__ . '/common.php';
