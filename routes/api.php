<?php

Route::put('main/{image}', 'ImagesController@main');
Route::get('{resource}/{resourceId?}', 'ImagesController@index');
Route::post('{resource}/{resourceId?}', 'ImagesController@store');
Route::delete('{resource}/{resourceId?}', 'ImagesController@destroy');