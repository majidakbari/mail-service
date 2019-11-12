<?php


/*
|--------------------------------------------------------------------------
| Email Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'email', 'name' => 'email.', 'namespace' => 'Email'], function (){
    Route::post('/', 'SendSingleEmailAction');
});
