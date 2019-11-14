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
