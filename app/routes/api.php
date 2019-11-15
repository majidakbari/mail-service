<?php


/*
|--------------------------------------------------------------------------
| Email Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'email', 'name' => 'email.', 'namespace' => 'Email'], function (){
    Route::post('/', 'SendSingleEmailAction')->name('send.single');
    Route::post('/multiple', 'SendMultipleEmailAction')->name('send.multiple');
});


/*
|--------------------------------------------------------------------------
| Log Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'log', 'name' => 'log.', 'namespace' => 'Log'], function (){
    Route::get('/', 'IndexLogsAction')->name('index');
});
