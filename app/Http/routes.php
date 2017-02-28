<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['namespace' => 'Api'], function() {
    Route::get('/crawl_images', 'ImagesController@getImages');
    Route::get('/categories', 'ImagesController@getCategories');
});

// This is where the user can see a login button for logging into Google
// Route::get('/', 'HomeController@index');
Route::get('/', function() {
    return redirect('/crawl_images');
});

// This is where the user gets redirected upon clicking the login button on the home page
Route::get('/login', 'HomeController@login');

// Shows a list of things that the user can do in the app
Route::get('/dashboard', 'AdminController@index');

// Shows a list of files in the users' Google drive
Route::get('/files', 'AdminController@files');
Route::get('/updateData', 'AdminController@updateData');

// Allows the user to search for a file in the Google drive
Route::get('/search', 'AdminController@search');

// Allows the user to upload new files
Route::get('/upload', 'AdminController@upload');
Route::post('/upload', 'AdminController@doUpload');

// Allows the user to delete a file
Route::get('/delete/{id}', 'AdminController@delete');

Route::get('/logout', 'AdminController@logout');
