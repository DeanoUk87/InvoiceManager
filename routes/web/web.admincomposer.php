<?php
/*
|--------------------------------------------------------------------------
| Web Routes - Admincomposer
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your admincomposer . These
| routes are loaded by the RouteServiceProvider within a group which
| contains the web middleware group.
|
*/
Route::prefix('admin/admincomposer')->group(function () {
    Route::get('', 'Admin\AdmincomposerController@index')->name('admincomposer.index');
    Route::get('getdata', 'Admin\AdmincomposerController@getdata')->name('admincomposer.getdata');
    Route::get('details/{id}', 'Admin\AdmincomposerController@details')->name('admincomposer.details');
    Route::get('create', 'Admin\AdmincomposerController@insert')->name('admincomposer.create');
    Route::post('store', 'Admin\AdmincomposerController@store')->name('admincomposer.store');
    Route::get('edit/{id}', 'Admin\AdmincomposerController@edit')->name('admincomposer.edit');
    Route::post('update/{id}', 'Admin\AdmincomposerController@update')->name('admincomposer.update');
    Route::delete('delete/{id}', 'Admin\AdmincomposerController@destroy')->name('admincomposer.delete');
    Route::post('delete/multi', 'Admin\AdmincomposerController@deletemulti')->name('admincomposer.deletemulti');
    Route::get('send-preview/{id}', 'Admin\AdmincomposerController@sendPreview')->name('admincomposer.send.preview');
    Route::get('send-mail/{id}', 'Admin\AdmincomposerController@sendEmails')->name('admincomposer.send.mail');
});
