<?php
/*
|--------------------------------------------------------------------------
| Web Routes - Sales
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your sales . These
| routes are loaded by the RouteServiceProvider within a group which
| contains the web middleware group.
|
*/
Route::prefix('admin/archive-sales')->group(function () {
    Route::get('', 'Admin\SalesArchiveController@index')->name('archive-sales.index');
    Route::get('getdata', 'Admin\SalesArchiveController@getdata')->name('archive-sales.getdata');
    Route::get('details/{id}', 'Admin\SalesArchiveController@details')->name('archive-sales.details');
    Route::get('create', 'Admin\SalesArchiveController@insert')->name('archive-sales.create');
    Route::post('store', 'Admin\SalesArchiveController@store')->name('archive-sales.store');
    Route::get('edit/{id}', 'Admin\SalesArchiveController@edit')->name('archive-sales.edit');
    Route::post('update/{id}', 'Admin\SalesArchiveController@update')->name('archive-sales.update');
    Route::delete('delete/{id}', 'Admin\SalesArchiveController@destroy')->name('archive-sales.delete');
    Route::get('export/pdf', 'Admin\SalesArchiveController@exportPDF')->name('archive-sales.pdf');
    Route::get('export/pdf/{id}', 'Admin\SalesArchiveController@exportDetailPDF')->name('archive-sales.pdfdetails');
    Route::get('export/{type}', 'Admin\SalesArchiveController@exportFile')->name('archive-sales.export');
    Route::get('import/view', 'Admin\SalesArchiveController@importExportView')->name('archive-sales.import.view');
    Route::post('import/store', 'Admin\SalesArchiveController@importFile')->name('archive-sales.import.store');
    Route::delete('deletefile/{id}', 'Admin\SalesArchiveController@destroyFile')->name('archive-sales.deletefile');
    Route::delete('deletefile2/{id}', 'Admin\SalesArchiveController@destroyFile2')->name('archive-sales.deletefile2');
    Route::post('delete/multi', 'Admin\SalesArchiveController@deletemulti')->name('archive-sales.deletemulti');
    Route::get('truncate', 'Admin\SalesArchiveController@truncateTable')->name('archive-sales.truncate');
    Route::get('archive', 'Admin\SalesArchiveController@archiveSalesInvoice')->name('archive-sales.archive');
    Route::get('archive', 'Admin\SalesController@archiveSalesInvoice')->name('sales.archive');

    /*Exports*/
    Route::get('exports/csvs', 'Admin\SalesExportController@index')->name('archive-sales.index.export');
    Route::get('exports/csvs/{fromdate}/{todate}/{customer}/{invoice}/{sage}', 'Admin\SalesExportController@getdataExport')->name('archive-sales.search.export');
    Route::get('export/{type}/{fromdate}/{todate}/{customer}/{invoice}/{sage}', 'Admin\SalesExportController@csvExports')->name('archive-sales.csvs.exports');

    //Export Select csv to sage
    Route::post('exports/selected', 'Admin\SalesExportController@csvExportSelected')->name('archive-sales.export.selected');
});
