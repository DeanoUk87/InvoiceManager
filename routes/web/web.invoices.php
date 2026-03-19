<?php
/*
|--------------------------------------------------------------------------
| Web Routes - Invoices
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your invoices . These
| routes are loaded by the RouteServiceProvider within a group which
| contains the web middleware group.
|
*/
Route::prefix('admin/invoices')->group(function () {
    Route::get('', 'Admin\InvoicesController@index')->name('invoices.index');
    //Route::get('getdata', 'Admin\InvoicesController@getdata')->name('invoices.getdata');
    Route::get('getdata/{fromdate}/{todate}/{customer}/{invoice}/{printer}', 'Admin\InvoicesController@getdata')->name('invoices.search');

    Route::get('details/{id}', 'Admin\InvoicesController@details')->name('invoices.details');
    Route::get('create', 'Admin\InvoicesController@insert')->name('invoices.create');
    Route::post('store', 'Admin\InvoicesController@store')->name('invoices.store');
    Route::get('edit/{id}', 'Admin\InvoicesController@edit')->name('invoices.edit');
    Route::post('update/{id}', 'Admin\InvoicesController@update')->name('invoices.update');
    Route::delete('delete/{id}', 'Admin\InvoicesController@destroy')->name('invoices.delete');
    Route::get('export/pdf/{fromdate}/{todate}/{customer}/{invoice}', 'Admin\InvoicesController@exportPDF')->name('invoices.pdf');
    Route::get('export/{type}/{fromdate}/{todate}/{customer}/{invoice}', 'Admin\InvoicesController@exportFile')->name('invoices.export');
    Route::get('import/view', 'Admin\InvoicesController@importExportView')->name('invoices.import.view');
    Route::post('import/store', 'Admin\InvoicesController@importFile')->name('invoices.import.store');
    Route::delete('deletefile/{id}', 'Admin\InvoicesController@destroyFile')->name('invoices.deletefile');
    Route::delete('deletefile2/{id}', 'Admin\InvoicesController@destroyFile2')->name('invoices.deletefile2');
    Route::post('delete/multi', 'Admin\InvoicesController@deletemulti')->name('invoices.deletemulti');

    Route::get('mass-maker', 'Admin\InvoicesController@SelectAllGroup')->name('invoices.mass.maker');
    Route::get('mass-mail/{account}', 'Admin\InvoicesController@massMail')->name('invoices.mass.mail');
    Route::get('preview/{account}/{invno}/{date}/{printer}', 'Admin\InvoicesController@invoice')->name('invoices.preview');
    Route::get('export/pdf/{account}/{invno}/{date}', 'Admin\InvoicesController@exportDetailPDF')->name('invoices.pdfdetails');
    Route::get('export/csv/{account}/{invno}/{date}', 'Admin\InvoicesController@exportDetailExcel')->name('invoices.exceldetails');
    Route::get('massmail', 'Admin\InvoicesController@massMail')->name('invoices.massmail');
    Route::post('auto-search', 'Admin\InvoicesController@invoiceAuto')->name('invoice.auto');
    Route::post('send-invoice', 'Admin\InvoicesController@sendInvoiceToMail')->name('invoices.sendmail');
    Route::get('send-preview', 'Admin\InvoicesController@sendPreview')->name('send.preview');
});

Route::prefix('archive/invoices')->group(function () {
    Route::get('', 'Archive\InvoicesController@index')->name('archive.invoices.index');
    Route::get('getdata/{fromdate}/{todate}/{customer}/{invoice}/{printer}', 'Archive\InvoicesController@getdata')->name('archive.invoices.search');
    Route::get('details/{id}', 'Archive\InvoicesController@details')->name('archive.invoices.details');
    Route::get('export/pdf/{fromdate}/{todate}/{customer}/{invoice}', 'Archive\InvoicesController@exportPDF')->name('archive.invoices.pdf');
    Route::get('export/{type}/{fromdate}/{todate}/{customer}/{invoice}', 'Archive\InvoicesController@exportFile')->name('archive.invoices.export');
    Route::get('import/view', 'Archive\InvoicesController@importExportView')->name('archive.invoices.import.view');
    //Route::post('import/store', 'Archive\InvoicesController@importFile')->name('archive.invoices.import.store');
    Route::delete('deletefile/{id}', 'Archive\InvoicesController@destroyFile')->name('archive.invoices.deletefile');
    Route::delete('deletefile2/{id}', 'Archive\InvoicesController@destroyFile2')->name('archive.invoices.deletefile2');
    Route::post('delete/multi', 'Archive\InvoicesController@deletemulti')->name('archive.invoices.deletemulti');
    Route::get('mass-maker', 'Archive\InvoicesController@SelectAllGroup')->name('archive.invoices.mass.maker');
    Route::get('mass-mail/{account}', 'Archive\InvoicesController@massMail')->name('archive.invoices.mass.mail');
    Route::get('preview/{account}/{invno}/{date}/{printer}', 'Archive\InvoicesController@invoice')->name('archive.invoices.preview');
    Route::get('export/pdf/{account}/{invno}/{date}', 'Archive\InvoicesController@exportDetailPDF')->name('archive.invoices.pdfdetails');
    Route::get('massmail', 'Archive\InvoicesController@massMail')->name('archive.invoices.massmail');
    Route::post('auto-search', 'Archive\InvoicesController@invoiceAuto')->name('archive.invoice.auto');
    Route::post('send-invoice', 'Archive\InvoicesController@sendInvoiceToMail')->name('archive.invoices.sendmail');
    Route::get('export/csv/{account}/{invno}/{date}', 'Archive\InvoicesController@exportDetailExcel')->name('archive.invoices.exceldetails');

});
