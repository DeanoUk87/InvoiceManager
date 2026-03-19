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
Route::prefix('admin/archive-invoices')->group(function () {
    Route::get('', 'Admin\InvoicesArchiveController@index')->name('archive-invoices.index');
    Route::get('getdata/{fromdate}/{todate}/{customer}/{invoice}/{printer}', 'Admin\InvoicesArchiveController@getdata')->name('archive-invoices.search');

    Route::get('details/{id}', 'Admin\InvoicesArchiveController@details')->name('archive-invoices.details');
    Route::delete('delete/{id}', 'Admin\InvoicesArchiveController@destroy')->name('invoices.delete');
    Route::get('export/pdf/{fromdate}/{todate}/{customer}/{invoice}', 'Admin\InvoicesArchiveController@exportPDF')->name('archive-invoices.pdf');
    Route::get('export/{type}/{fromdate}/{todate}/{customer}/{invoice}', 'Admin\InvoicesArchiveController@exportFile')->name('archive-invoices.export');
    Route::get('import/view', 'Admin\InvoicesArchiveController@importExportView')->name('archive-invoices.import.view');
    Route::delete('deletefile/{id}', 'Admin\InvoicesArchiveController@destroyFile')->name('archive-invoices.deletefile');
    Route::delete('deletefile2/{id}', 'Admin\InvoicesArchiveController@destroyFile2')->name('archive-invoices.deletefile2');
    Route::post('delete/multi', 'Admin\InvoicesArchiveController@deletemulti')->name('archive-invoices.deletemulti');

    Route::get('mass-maker', 'Admin\InvoicesArchiveController@SelectAllGroup')->name('archive-invoices.mass.maker');
    Route::get('mass-mail/{account}', 'Admin\InvoicesArchiveController@massMail')->name('archive-invoices.mass.mail');
    Route::get('preview/{account}/{invno}/{date}/{printer}', 'Admin\InvoicesArchiveController@invoice')->name('archive-invoices.preview');
    Route::get('export/pdf/{account}/{invno}/{date}', 'Admin\InvoicesArchiveController@exportDetailPDF')->name('archive-invoices.pdfdetails');
    Route::get('export/csv/{account}/{invno}/{date}', 'Admin\InvoicesArchiveController@exportDetailExcel')->name('archive-invoices.exceldetails');
    Route::get('massmail', 'Admin\InvoicesArchiveController@massMail')->name('archive-invoices.massmail');
    Route::post('auto-search', 'Admin\InvoicesArchiveController@invoiceAuto')->name('archive-invoice.auto');
    Route::post('send-invoice', 'Admin\InvoicesArchiveController@sendInvoiceToMail')->name('archive-invoices.sendmail');
    Route::get('send-preview', 'Admin\InvoicesArchiveController@sendPreview')->name('archive-send.preview');
});
