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
Route::prefix('admin/sales')->group(function () {
    Route::get('', 'Admin\SalesController@index')->name('sales.index');
    Route::get('getdata', 'Admin\SalesController@getdata')->name('sales.getdata');
    Route::get('details/{id}', 'Admin\SalesController@details')->name('sales.details');
    Route::get('create', 'Admin\SalesController@insert')->name('sales.create');
    Route::post('store', 'Admin\SalesController@store')->name('sales.store');
    Route::get('edit/{id}', 'Admin\SalesController@edit')->name('sales.edit');
    Route::post('update/{id}', 'Admin\SalesController@update')->name('sales.update');
    Route::delete('delete/{id}', 'Admin\SalesController@destroy')->name('sales.delete');
    Route::get('export/pdf', 'Admin\SalesController@exportPDF')->name('sales.pdf');
    Route::get('export/pdf/{id}', 'Admin\SalesController@exportDetailPDF')->name('sales.pdfdetails');
    Route::get('export/{type}', 'Admin\SalesController@exportFile')->name('sales.export');
    Route::get('import/view', 'Admin\SalesController@importExportView')->name('sales.import.view');
    Route::post('import/store', 'Admin\SalesController@importFile')->name('sales.import.store');
    Route::delete('deletefile/{id}', 'Admin\SalesController@destroyFile')->name('sales.deletefile');
    Route::delete('deletefile2/{id}', 'Admin\SalesController@destroyFile2')->name('sales.deletefile2');
    Route::post('delete/multi', 'Admin\SalesController@deletemulti')->name('sales.deletemulti');
    Route::get('truncate', 'Admin\SalesController@truncateTable')->name('sales.truncate');

    /*Exports*/
    Route::get('exports/csvs', 'Admin\SalesExportController@index')->name('sales.index.export');
    Route::get('exports/csvs/{fromdate}/{todate}/{customer}/{invoice}/{sage}', 'Admin\SalesExportController@getdataExport')->name('sales.search.export');
    Route::get('export/{type}/{fromdate}/{todate}/{customer}/{invoice}/{sage}', 'Admin\SalesExportController@csvExports')->name('sales.csvs.exports');

    //Export Select csv to sage
    Route::post('exports/selected', 'Admin\SalesExportController@csvExportSelected')->name('sales.export.selected');
});

Route::prefix('archive/sales')->group(function () {
    Route::get('', 'Archive\SalesController@index')->name('archive.sales.index');
    Route::get('getdata', 'Archive\SalesController@getdata')->name('archive.sales.getdata');
    Route::get('details/{id}', 'Archive\SalesController@details')->name('archive.sales.details');
    Route::get('create', 'Archive\SalesController@insert')->name('archive.sales.create');
    Route::post('store', 'Archive\SalesController@store')->name('archive.sales.store');
    Route::get('edit/{id}', 'Archive\SalesController@edit')->name('archive.sales.edit');
    Route::post('update/{id}', 'Archive\SalesController@update')->name('archive.sales.update');
    Route::delete('delete/{id}', 'Archive\SalesController@destroy')->name('archive.sales.delete');
    Route::get('export/pdf', 'Archive\SalesController@exportPDF')->name('archive.sales.pdf');
    Route::get('export/pdf/{id}', 'Archive\SalesController@exportDetailPDF')->name('archive.sales.pdfdetails');
    Route::get('export/{type}', 'Archive\SalesController@exportFile')->name('archive.sales.export');
    /*Route::get('import/view', 'Archive\SalesController@importExportView')->name('archive.sales.import.view');
    Route::post('import/store', 'Archive\SalesController@importFile')->name('archive.sales.import.store');*/
    Route::delete('deletefile/{id}', 'Archive\SalesController@destroyFile')->name('archive.sales.deletefile');
    Route::delete('deletefile2/{id}', 'Archive\SalesController@destroyFile2')->name('archive.sales.deletefile2');
    Route::post('delete/multi', 'Archive\SalesController@deletemulti')->name('archive.sales.deletemulti');
    Route::get('truncate', 'Archive\SalesController@truncateTable')->name('archive.sales.truncate');

    /*Exports*/
   /* Route::get('exports/csvs', 'Archive\SalesExportController@index')->name('sales.index.export');
    Route::get('exports/csvs/{fromdate}/{todate}/{customer}/{invoice}/{sage}', 'Archive\SalesExportController@getdataExport')->name('archive.sales.search.export');
    Route::get('export/{type}/{fromdate}/{todate}/{customer}/{invoice}/{sage}', 'Archive\SalesExportController@csvExports')->name('archive.sales.csvs.exports');*/
});
