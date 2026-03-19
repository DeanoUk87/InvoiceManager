<?php
/*
|--------------------------------------------------------------------------
| Web Routes - Messagesstatus
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your messagesstatus . These
| routes are loaded by the RouteServiceProvider within a group which
| contains the web middleware group.
|
*/
Route::prefix('admin/messagesstatus')->group(function () {
    Route::get('', 'Admin\MessagesstatusController@index')->name('messagesstatus.index');
    Route::get('getdata', 'Admin\MessagesstatusController@getdata')->name('messagesstatus.getdata');
    Route::get('details/{id}', 'Admin\MessagesstatusController@details')->name('messagesstatus.details');
    Route::get('create', 'Admin\MessagesstatusController@insert')->name('messagesstatus.create');
    Route::post('store', 'Admin\MessagesstatusController@store')->name('messagesstatus.store');
    Route::get('edit/{id}', 'Admin\MessagesstatusController@edit')->name('messagesstatus.edit');
    Route::post('update/{id}', 'Admin\MessagesstatusController@update')->name('messagesstatus.update');
    Route::delete('delete/{id}', 'Admin\MessagesstatusController@destroy')->name('messagesstatus.delete');
    Route::get('export/pdf', 'Admin\MessagesstatusController@exportPDF')->name('messagesstatus.pdf');
    Route::get('export/pdf/{id}', 'Admin\MessagesstatusController@exportDetailPDF')->name('messagesstatus.pdfdetails');
    Route::get('export/{type}', 'Admin\MessagesstatusController@exportFile')->name('messagesstatus.export');
    Route::get('import/view', 'Admin\MessagesstatusController@importExportView')->name('messagesstatus.import.view');
    Route::post('import/store', 'Admin\MessagesstatusController@importFile')->name('messagesstatus.import.store');
    Route::delete('deletefile/{id}', 'Admin\MessagesstatusController@destroyFile')->name('messagesstatus.deletefile');
    Route::delete('deletefile2/{id}', 'Admin\MessagesstatusController@destroyFile2')->name('messagesstatus.deletefile2');
    Route::post('delete/multi', 'Admin\MessagesstatusController@deletemulti')->name('messagesstatus.deletemulti');
});
