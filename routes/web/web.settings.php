<?php
/*
|--------------------------------------------------------------------------
| Web Routes - Settings
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your settings . These
| routes are loaded by the RouteServiceProvider within a group which
| contains the web middleware group.
|
*/
Route::prefix('admin/usersettings')->group(function () {
    Route::get('', 'Admin\SettingsController@index')->name('usersettings.index');
    Route::get('getdata', 'Admin\SettingsController@getdata')->name('usersettings.getdata');
    Route::get('details/{id}', 'Admin\SettingsController@details')->name('usersettings.details');
    Route::get('create', 'Admin\SettingsController@insert')->name('usersettings.create');
    Route::post('store', 'Admin\SettingsController@store')->name('usersettings.store');
    Route::get('edit/{id}', 'Admin\SettingsController@edit')->name('usersettings.edit');
    Route::post('update/{id}', 'Admin\SettingsController@update')->name('usersettings.update');
    Route::delete('delete/{id}', 'Admin\SettingsController@destroy')->name('usersettings.delete');
    Route::get('export/pdf', 'Admin\SettingsController@exportPDF')->name('usersettings.pdf');
    Route::get('export/pdf/{id}', 'Admin\SettingsController@exportDetailPDF')->name('usersettings.pdfdetails');
    Route::get('export/{type}', 'Admin\SettingsController@exportFile')->name('usersettings.export');
    Route::get('import/view', 'Admin\SettingsController@importExportView')->name('usersettings.import.view');
    Route::post('import/store', 'Admin\SettingsController@importFile')->name('usersettings.import.store');
    Route::delete('deletefile/{id}', 'Admin\SettingsController@destroyFile')->name('usersettings.deletefile');
    Route::delete('deletefile2/{id}', 'Admin\SettingsController@destroyFile2')->name('usersettings.deletefile2');
    Route::post('delete/multi', 'Admin\SettingsController@deletemulti')->name('usersettings.deletemulti');
});
