<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\UserListController;
use Illuminate\Support\Facades\Auth;


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
    return view('auth.login');
});

Auth::routes();
Route::get('/', 'DashboardController@index');

// routes/web.php

Route::get('/auth/changepassword', [ChangePasswordController::class, 'showChangePasswordForm'])->name('auth.change');
Route::post('/auth/changepassword', [ChangePasswordController::class, 'changePassword'])->name('auth.changepassword');

Route::get('/auth/userlist', [UserListController::class, 'showUserList'])->name('auth.userlist');

Route::get('/admin/dashboard', 'DashboardController@index')->name('dashboard.index');

Route::get('/admin/gatein/containers', 'GateinController@select2Containers')->name('gatein.select2');

Route::put('/admin/annimport/{no_container}', 'AnnimportController@update')->name('annimport.update');

//---MASTERDATA---//
Route::get('/admin/datamaster/datakapal', 'KapalController@index')->name('kapal.index');
Route::post('/admin/datamaster/datakapal', 'KapalController@store')->name('kapal.store');
Route::delete('/admin/datamaster/datakapal/{nama_kapal}', 'KapalController@destroy')->name('kapal.destroy');

Route::get('/admin/datamaster/datagudang', 'GudangController@index')->name('gudang.index');
Route::post('/admin/datamaster/datagudang', 'GudangController@store')->name('gudang.store');
Route::delete('/admin/datamaster/datagudang/{nama_gudang}', 'GudangController@destroy')->name('gudang.destroy');

Route::get('/admin/datamaster/datacontainer', 'ContainerController@index')->name('container.index');
Route::post('/admin/datamaster/datacontainer', 'ContainerController@store')->name('container.store');
Route::delete('/admin/datamaster/datacontainer/{nama}', 'ContainerController@destroy')->name('container.destroy');

Route::get('/admin/datamaster/datayard', 'YardController@index')->name('yard.index');
Route::post('/admin/datamaster/datayard', 'YardController@store')->name('yard.store');

Route::get('/admin/datamaster/datacustomer', 'CustomerController@index')->name('customer.index');
Route::post('/admin/datamaster/datacustomer', 'CustomerController@store')->name('customer.store');
Route::delete('/admin/datamaster/datacustomer/{nama_customer}', 'CustomerController@destroy')->name('customer.destroy');

Route::get('/admin/datamaster/datatarif', 'TarifController@index')->name('tarif.index');
Route::post('/admin/datamaster/datatarif', 'TarifController@store')->name('tarif.store');
Route::delete('/admin/datamaster/datatarif/{nama_jasa}', 'TarifController@destroy')->name('tarif.destroy');

Route::get('/admin/datamaster/datacedex', 'CedexController@index')->name('cedex.index');
Route::post('/admin/datamaster/datacedex', 'CedexController@store')->name('cedex.store');
Route::post('/admin/datamaster/datacedex/import-excel', 'CedexController@importExcel')->name('cedex.importExcel');

Route::get('/admin/eor/editeordetail/{eor_code}', 'EstimateofrepairController@editeordetail')->name('eor.editeordetail');
Route::put('/admin/eor/updateeordetail/{eor_code}', 'EstimateofrepairController@updateeordetail')->name('eor.updateeordetail');

// Movement In/Out
Route::get('/admin/movementinout', 'MovementinoutController@index')->name('movementinout.index');
Route::get('/admin/movementinout/show/{kode_survey}', 'MovementinoutController@show')->name('movementinout.show');

// Monitoring Yard
Route::get('/admin/monitoringyard', 'MonitoringyardController@index')->name('monitoringyard.index');
Route::get('/admin/monitoringyard/tierDetails', 'MonitoringYardController@tierDetails')->name('monitoringyard.tierDetails');


Route::middleware(['auth', 'role:administrator'])->group(function () {
    // Work Order
    Route::get('/admin/workorder', 'WorkorderController@index')->name('workorder.index');
    Route::get('/admin/workorder/create', 'WorkorderController@create')->name('workorder.create');
    Route::post('/admin/workorder', 'WorkorderController@store')->name('workorder.store');
    Route::post('/admin/workorder-detail', 'WorkorderController@storedetail')->name('workorder.storedetail');
    Route::get('/admin/workorder/{id}', 'WorkorderController@show')->name('workorder.show');
    Route::get('/admin/workorderdetail/{id}', 'WorkorderController@showdetail')->name('workorder.showdetail');
    Route::get('/admin/workorder/cetak_wo/{nomor_wo}', 'WorkorderController@cetak_wo')->name('workorder.cetak_wo');

    // Ann Import
    Route::get('/admin/annimport', 'AnnimportController@index')->name('annimport.index');
    Route::post('/admin/annimport', 'AnnimportController@store')->name('annimport.store');
    Route::post('/admin/annimport/import-excel', 'AnnimportController@importExcel')->name('annimport.importExcel');
    Route::post('/admin/annimport/{no_container}/update-status', 'AnnimportController@updateStatus')->name('annimport.updateStatus');
    Route::delete('/admin/annimport/{no_container}', 'AnnimportController@destroy')->name('annimport.destroy');

    // Movement In/Out
    Route::get('/admin/movementinout', 'MovementinoutController@index')->name('movementinout.index');
    Route::get('/admin/movementinout/show/{kode_survey}', 'MovementinoutController@show')->name('movementinout.show');

    // Survey In
    Route::get('/admin/surveyin', 'SurveyInController@index')->name('surveyin.index');
    Route::get('/admin/surveyin/create', 'SurveyInController@create')->name('surveyin.create');
    Route::post('/admin/surveyin', 'SurveyInController@store_detail')->name('surveyin.store_detail');
    Route::get('/admin/surveyin/cetak_eir/{kode_survey}', 'SurveyInController@cetak_eir')->name('surveyin.cetak_eir');

    // Survey Out
    Route::get('/admin/surveyout', 'SurveyOutController@index')->name('surveyout.index');
    Route::get('/admin/surveyout/create', 'SurveyOutController@create')->name('surveyout.create');
    Route::post('/admin/surveyout', 'SurveyOutController@store')->name('surveyout.store');
    Route::get('/admin/surveyout/cetak_eiro/{kode_surveyout}', 'SurveyOutController@cetak_eiro')->name('surveyout.cetak_eiro');

    // Estimate Of Repair
    Route::get('/admin/eor', 'EstimateofrepairController@index')->name('eor.index');
    Route::get('/admin/eor/create', 'EstimateofrepairController@create')->name('eor.create');
    Route::get('/admin/eor/edit/{eor_code}', 'EstimateofrepairController@edit')->name('eor.edit');
    Route::post('/admin/eor/edit{eor_code}', 'EstimateofrepairController@start')->name('eor.start');
    Route::post('/admin/eor/edit/{eor_code}', 'EstimateofrepairController@complete')->name('eor.complete');
    Route::post('/admin/eor/update/{eor_code}', 'EstimateofrepairController@update')->name('eor.update');
    Route::get('/admin/eor/cetak_eor/{eor_code}', 'EstimateofrepairController@cetak_eor')->name('eor.cetak_eor');

    // Billing
    Route::get('/admin/billing', 'BillingController@index')->name('billing.index');
    Route::get('/admin/billing/searchWorkOrder/{nomor_wo}', 'BillingController@searchWorkOrder')->name('billing.searchWorkOrder');
    Route::post('/admin/billing/savePayment', 'BillingController@savePayment')->name('billing.savePayment');

    // Payment
    Route::get('/admin/payment', 'PaymentController@index')->name('payment.index');
    Route::get('/admin/payment/cetak_inv/{nomor_wo}', 'PaymentController@cetak_inv')->name('payment.cetak_inv');

    // Gatein
    Route::get('/admin/gatein', 'GateinController@index')->name('gatein.index');
    Route::post('/admin/gatein', 'GateinController@store')->name('gatein.store');
    Route::put('/admin/gatein/{id}', 'GateinController@update')->name('gatein.update');
    Route::delete('/admin/gatein/{id}', 'GateinController@destroy')->name('gatein.destroy');

    // Gateout
    Route::get('/admin/gateout', 'GateoutController@index')->name('gateout.index');
    Route::post('/admin/gateout', 'GateoutController@store')->name('gateout.store');

    // Report In
    Route::get('/admin/reportin', 'ReportinController@index')->name('reportin.index');
    Route::get('/admin/reportin/export', 'ReportinController@exportSurveyInPerDay')->name('reportin.export');
    Route::post('/admin/reportin/filter', 'ReportinController@filter')->name('reportin.filter');

    // Report By Customer
    Route::get('/admin/reportcustomer', 'ReportcustomerController@index')->name('reportcustomer.index');
});

Route::middleware(['auth', 'role:surveyorin'])->group(function () {
    // Gatein
    Route::get('/admin/gatein', 'GateinController@index')->name('gatein.index');
    Route::post('/admin/gatein', 'GateinController@store')->name('gatein.store');

    // Survey In
    Route::get('/admin/surveyin', 'SurveyInController@index')->name('surveyin.index');
    Route::get('/admin/surveyin/create', 'SurveyInController@create')->name('surveyin.create');
    Route::post('/admin/surveyin', 'SurveyInController@store_detail')->name('surveyin.store_detail');
    Route::get('/admin/surveyin/cetak_eir/{kode_survey}', 'SurveyInController@cetak_eir')->name('surveyin.cetak_eir');
});

Route::middleware(['auth', 'role:surveyorout'])->group(function () {
    // Survey Out
    Route::get('/admin/surveyout', 'SurveyOutController@index')->name('surveyout.index');
    Route::get('/admin/surveyout/create', 'SurveyOutController@create')->name('surveyout.create');
    Route::post('/admin/surveyout', 'SurveyOutController@store')->name('surveyout.store');
    Route::get('/admin/surveyout/cetak_eiro/{kode_surveyout}', 'SurveyOutController@cetak_eiro')->name('surveyout.cetak_eiro');

    // Gateout
    Route::get('/admin/gateout', 'GateoutController@index')->name('gateout.index');
    Route::post('/admin/gateout', 'GateoutController@store')->name('gateout.store');
});

Route::middleware(['auth', 'role:adminops'])->group(function () {
    // Work Order
    Route::get('/admin/workorder', 'WorkorderController@index')->name('workorder.index');
    Route::get('/admin/workorder/create', 'WorkorderController@create')->name('workorder.create');
    Route::post('/admin/workorder', 'WorkorderController@store')->name('workorder.store');
    Route::post('/admin/workorder-detail', 'WorkorderController@storedetail')->name('workorder.storedetail');
    Route::get('/admin/workorder/{id}', 'WorkorderController@show')->name('workorder.show');
    Route::get('/admin/workorderdetail/{id}', 'WorkorderController@showdetail')->name('workorder.showdetail');
    Route::get('/admin/workorder/cetak_wo/{nomor_wo}', 'WorkorderController@cetak_wo')->name('workorder.cetak_wo');


    // Ann Import
    Route::get('/admin/annimport', 'AnnimportController@index')->name('annimport.index');
    Route::post('/admin/annimport', 'AnnimportController@store')->name('annimport.store');
    Route::post('/admin/annimport/import-excel', 'AnnimportController@importExcel')->name('annimport.importExcel');
    Route::post('/admin/annimport/{no_container}/update-status', 'AnnimportController@updateStatus')->name('annimport.updateStatus');
    Route::delete('/admin/annimport/{no_container}', 'AnnimportController@destroy')->name('annimport.destroy');

    // Movement In/Out
    Route::get('/admin/movementinout', 'MovementinoutController@index')->name('movementinout.index');
    Route::get('/admin/movementinout/show/{kode_survey}', 'MovementinoutController@show')->name('movementinout.show');

    // Report In
    Route::get('/admin/reportin', 'ReportinController@index')->name('reportin.index');
    Route::get('/admin/reportin/export', 'ReportinController@exportSurveyInPerDay')->name('reportin.export');
});

Route::middleware(['auth', 'role:ops'])->group(function () {
    // Estimate Of Repair
    Route::get('/admin/eor', 'EstimateofrepairController@index')->name('eor.index');
    Route::get('/admin/eor/create', 'EstimateofrepairController@create')->name('eor.create');
    Route::get('/admin/eor/edit/{eor_code}', 'EstimateofrepairController@edit')->name('eor.edit');
    Route::post('/admin/eor/edit{eor_code}', 'EstimateofrepairController@start')->name('eor.start');
    Route::post('/admin/eor/edit/{eor_code}', 'EstimateofrepairController@complete')->name('eor.complete');
    Route::post('/admin/eor/update/{eor_code}', 'EstimateofrepairController@update')->name('eor.update');
    Route::get('/admin/eor/cetak_eor/{eor_code}', 'EstimateofrepairController@cetak_eor')->name('eor.cetak_eor');
});

Route::middleware(['auth', 'role:chasier'])->group(function () {
    // Billing
    Route::get('/admin/billing', 'BillingController@index')->name('billing.index');
    Route::get('/admin/billing/searchWorkOrder/{nomor_wo}', 'BillingController@searchWorkOrder')->name('billing.searchWorkOrder');
    Route::post('/admin/billing/savePayment', 'BillingController@savePayment')->name('billing.savePayment');

    // Payment
    Route::get('/admin/payment', 'PaymentController@index')->name('payment.index');
    Route::get('/admin/payment/cetak_inv/{nomor_wo}', 'PaymentController@cetak_inv')->name('payment.cetak_inv');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    // Movement In/Out
    Route::get('/admin/movementinout', 'MovementinoutController@index')->name('movementinout.index');
    Route::get('/admin/movementinout/show/{kode_survey}', 'MovementinoutController@show')->name('movementinout.show');

    // Report In
    Route::get('/admin/reportin', 'ReportinController@index')->name('reportin.index');
    Route::get('/admin/reportin/export', 'ReportinController@exportSurveyInPerDay')->name('reportin.export');
});
// Work Order
Route::get('/admin/workorder', 'WorkorderController@index')->name('workorder.index');
Route::get('/admin/workorder/create', 'WorkorderController@create')->name('workorder.create');
Route::post('/admin/workorder', 'WorkorderController@store')->name('workorder.store');
Route::post('/admin/workorder-detail', 'WorkorderController@storedetail')->name('workorder.storedetail');
Route::get('/admin/workorder/{id}', 'WorkorderController@show')->name('workorder.show');
Route::get('/admin/workorderdetail/{id}', 'WorkorderController@showdetail')->name('workorder.showdetail');
Route::get('/admin/workorder/cetak_wo/{nomor_wo}', 'WorkorderController@cetak_wo')->name('workorder.cetak_wo');


// Ann Import
Route::get('/admin/annimport', 'AnnimportController@index')->name('annimport.index');
Route::post('/admin/annimport', 'AnnimportController@store')->name('annimport.store');
Route::post('/admin/annimport/import-excel', 'AnnimportController@importExcel')->name('annimport.importExcel');
Route::post('/admin/annimport/{no_container}/update-status', 'AnnimportController@updateStatus')->name('annimport.updateStatus');
Route::delete('/admin/annimport/{no_container}', 'AnnimportController@destroy')->name('annimport.destroy');

// Survey In
Route::get('/admin/surveyin', 'SurveyInController@index')->name('surveyin.index');
Route::get('/admin/surveyin/create', 'SurveyInController@create')->name('surveyin.create');
Route::post('/admin/surveyin', 'SurveyInController@store_detail')->name('surveyin.store_detail');
Route::get('/admin/surveyin/cetak_eir/{kode_survey}', 'SurveyInController@cetak_eir')->name('surveyin.cetak_eir');

// Survey Out
Route::get('/admin/surveyout', 'SurveyOutController@index')->name('surveyout.index');
Route::get('/admin/surveyout/create', 'SurveyOutController@create')->name('surveyout.create');
Route::post('/admin/surveyout', 'SurveyOutController@store')->name('surveyout.store');
Route::get('/admin/surveyout/cetak_eiro/{kode_surveyout}', 'SurveyOutController@cetak_eiro')->name('surveyout.cetak_eiro');

// Estimate Of Repair
Route::get('/admin/eor', 'EstimateofrepairController@index')->name('eor.index');
Route::get('/admin/eor/create', 'EstimateofrepairController@create')->name('eor.create');
Route::get('/admin/eor/edit/{eor_code}', 'EstimateofrepairController@edit')->name('eor.edit');
Route::post('/admin/eor/edit{eor_code}', 'EstimateofrepairController@start')->name('eor.start');
Route::post('/admin/eor/edit/{eor_code}', 'EstimateofrepairController@complete')->name('eor.complete');
Route::post('/admin/eor/update/{eor_code}', 'EstimateofrepairController@update')->name('eor.update');
Route::get('/admin/eor/cetak_eor/{eor_code}', 'EstimateofrepairController@cetak_eor')->name('eor.cetak_eor');

// Movement In/Out
Route::get('/admin/movementinout', 'MovementinoutController@index')->name('movementinout.index');
Route::get('/admin/movementinout/show/{kode_survey}', 'MovementinoutController@show')->name('movementinout.show');

// Billing
Route::get('/admin/billing', 'BillingController@index')->name('billing.index');
Route::get('/admin/billing/searchWorkOrder/{nomor_wo}', 'BillingController@searchWorkOrder')->name('billing.searchWorkOrder');
Route::post('/admin/billing/savePayment', 'BillingController@savePayment')->name('billing.savePayment');

// Payment
Route::get('/admin/payment', 'PaymentController@index')->name('payment.index');
Route::get('/admin/payment/cetak_inv/{nomor_wo}', 'PaymentController@cetak_inv')->name('payment.cetak_inv');

// Gatein
Route::get('/admin/gatein', 'GateinController@index')->name('gatein.index');
Route::post('/admin/gatein', 'GateinController@store')->name('gatein.store');
Route::get('/admin/gatein/select2-containers', 'GateinController@select2Containers')->name('gatein.select2.containers');


// Gateout
Route::get('/admin/gateout', 'GateoutController@index')->name('gateout.index');
Route::post('/admin/gateout', 'GateoutController@store')->name('gateout.store');
// endpoint Select2 untuk dropdown container
Route::get('/admin/gateout/select2-containers', 'GateoutController@select2Containers')->name('gateout.select2');

// Report In
Route::get('/admin/reportin', 'ReportinController@index')->name('reportin.index');
Route::get('/admin/reportin/export', 'ReportinController@exportSurveyInPerDay')->name('reportin.export');
Route::post('/admin/reportin/filter', 'ReportinController@filter')->name('reportin.filter');

// Report By Customer
Route::get('/admin/reportcustomer', 'ReportcustomerController@index')->name('reportcustomer.index');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
