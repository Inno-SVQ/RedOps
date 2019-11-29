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

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/', 'HomeController@index')->name('home')->middleware('auth');

/* Audits */
Route::get('/audits', 'AuditController@index')->name('audits')->middleware('auth');
Route::get('/audits/new', 'AuditController@create')->name('createAudit')->middleware('auth');
Route::post('/audits/new', 'AuditController@createExecute')->middleware('auth');
Route::get('/audit/{id}', 'AuditController@auditDetail')->name('auditDetail')->middleware('auth');
Route::delete('/audit/{id}', 'AuditController@delete')->name('auditDelete')->middleware('auth');


/* Jobs */
Route::get('/audit/{id}/jobs', 'JobsController@index')->name('jobs')->middleware('auth');
Route::get('/ajax/audit/{id}/jobs/poll', 'Api\JobsController@ajaxPoll')->name('ajax/jobs/poll')->middleware('auth');
Route::get('/ajax/audits/jobs/poll', 'Api\JobsController@ajaxPollAll')->name('ajax/jobs/poll/all')->middleware('auth');

/* Enumeration */
Route::get('/audit/{id}/enumeration', 'EnumerationController@index')->name('enumeration')->middleware('auth');
Route::get('/audit/{id}/enumeration/companies', 'EnumerationController@companies')->name('companies')->middleware('auth');
Route::post('/audit/{id}/enumeration/companies', 'EnumerationController@addCompany')->name('companies/add')->middleware('auth');
Route::post('/audit/{id}/enumeration/companies/delete', 'EnumerationController@deleteCompanies')->name('companies/delete')->middleware('auth');
Route::get('/ajax/audit/{id}/enumeration/companies', 'EnumerationController@companiesAjax')->name('ajax/enumeration/companies')->middleware('auth');
Route::post('/ajax/audit/{id}/enumeration/companies/finddomains', 'EnumerationController@findDomains')->name('ajax/enumeration/companies/findDomains')->middleware('auth');

/* Domains */
Route::get('/audit/{id}/enumeration/domains', 'EnumerationController@domains')->name('domains')->middleware('auth');
Route::post('/audit/{id}/enumeration/domains', 'EnumerationController@addDomain')->name('domains/add')->middleware('auth');
Route::post('/audit/{id}/enumeration/domains/delete', 'EnumerationController@deleteDomains')->name('domains/delete')->middleware('auth');
Route::get('/ajax/audit/{id}/enumeration/domains', 'EnumerationController@domainsAjax')->name('ajax/enumeration/domains')->middleware('auth');
Route::post('/ajax/audit/{id}/enumeration/companies/findSubdomains', 'EnumerationController@findSubdomains')->name('ajax/enumeration/companies/findSubdomains')->middleware('auth');

/* Services */
Route::get('/audit/{id}/enumeration/services', 'ServicesController@services')->name('services')->middleware('auth');
Route::post('/audit/{id}/enumeration/services/delete', 'ServicesController@deleteServices')->name('services/delete')->middleware('auth');
Route::post('/audit/{id}/enumeration/services', 'ServicesController@addService')->name('services/add')->middleware('auth');
Route::get('/ajax/audit/{id}/enumeration/services', 'ServicesController@servicesAjax')->name('ajax/enumeration/services')->middleware('auth');
Route::post('/ajax/audit/{id}/enumeration/companies/findServices', 'ServicesController@findServices')->name('ajax/enumeration/companies/findServices')->middleware('auth');

/* Credentials */
Route::get('/audit/{id}/enumeration/credentials', 'CredentialsController@index')->name('credentials')->middleware('auth');
Route::post('/audit/{id}/enumeration/credentials/delete', 'CredentialsController@deleteCredentials')->name('credentials/delete')->middleware('auth');
Route::post('/audit/{id}/enumeration/credentials/add', 'CredentialsController@addCredential')->name('credentials/add')->middleware('auth');
Route::get('/ajax/audit/{id}/enumeration/credentials', 'CredentialsController@credentialsAjax')->name('ajax/enumeration/credentials')->middleware('auth');
Route::post('/ajax/audit/{id}/enumeration/findCredentials', 'CredentialsController@findCredentials')->name('ajax/enumeration/findCredentials')->middleware('auth');

/* Web Services detail */
Route::get('/audit/{id}/enumeration/services/{serviceid}', 'WebServicesController@index')->name('servicedetail')->middleware('auth');
Route::post('/ajax/audit/{id}/enumeration/services/webtechnologies', 'WebServicesController@webtechnologies')->name('ajax/enumeration/services/webtechnologies')->middleware('auth');
Route::post('/ajax/audit/{id}/enumeration/services/fuzz', 'WebServicesController@fuzz')->name('ajax/enumeration/services/fuzz')->middleware('auth');
Route::get('/ajax/audit/{id}/enumeration/services/{serviceid}/directories', 'WebServicesController@directories')->name('ajax/enumeration/services/directories')->middleware('auth');
Route::post('/ajax/audit/{id}/enumeration/services/screenshot', 'WebServicesController@screenshot')->name('ajax/enumeration/services/screenshot')->middleware('auth');


Route::middleware(['middleware' => 'auth'])->group(function () {
    Route::post('broadcasting/auth', ['uses' => 'BroadcastController@authenticate']);
});

