<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['namespace' => 'Sign'], function(){
    // 控制器在 "App\Http\Controllers\Admin" 命名空间下

//    Route::group(['namespace' => 'User'], function(){
//        // 控制器在 "App\Http\Controllers\Admin\User" 命名空间下
//    });
});