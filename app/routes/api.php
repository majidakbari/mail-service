<?php


/*
|--------------------------------------------------------------------------
| Email Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'email', 'as' => 'email.', 'namespace' => 'Email'], function (){
    Route::post('/', 'SendSingleEmailAction')->name('send.single');
    Route::post('/multiple', 'SendMultipleEmailAction')->name('send.multiple');
});


/*
|--------------------------------------------------------------------------
| Log Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'log', 'as' => 'log.', 'namespace' => 'Log'], function (){
    Route::get('/', 'IndexLogsAction')->name('index');
});
