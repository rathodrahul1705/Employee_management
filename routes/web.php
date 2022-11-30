<?php

use Illuminate\Support\Facades\Route;

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
    return redirect()->route('login');
});



Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');

Route::namespace('Admin')->prefix('admin')->name('admin.')->middleware(['auth','can:admin-access'])->group(function () {
    Route::get('/', 'AdminController@index')->name('index');
    Route::get('/reset-password', 'AdminController@reset_password')->name('reset-password');
    Route::put('/update-password', 'AdminController@update_password')->name('update-password');

    // Routes for employees //
    Route::get('/employees/list-employees', 'EmployeeController@index')->name('employees.index');
    Route::get('/employees/add-employee', 'EmployeeController@create')->name('employees.create');
    Route::post('/employees', 'EmployeeController@store')->name('employees.store');
    Route::get('/employees/profile/{employee_id}', 'EmployeeController@employeeProfile')->name('employees.profile');
    Route::get('/employees/edit/{employee_id}', 'EmployeeController@employeeEdit')->name('employees.edit');
    Route::delete('/employees/{employee_id}', 'EmployeeController@destroy')->name('employees.delete');
    Route::POST('/employees/update{employee_id}', 'EmployeeController@update')->name('employees.update');

    //Import Export Employee
    Route::POST('/employees/file_import', 'EmployeeController@fileInport')->name('employees.file_import');
    Route::get('/employees/file_export', 'EmployeeController@fileExport')->name('employees.file_export');
    Route::get('/employees/mail', 'EmployeeController@mail')->name('employees.mail');

    // Routes for employees //
});

Route::namespace('Employee')->prefix('employee')->name('employee.')->middleware(['auth','can:employee-access'])->group(function () {
    Route::get('/', 'AdminController@index')->name('index');
    Route::get('/reset-password', 'AdminController@reset_password')->name('reset-password');
    Route::put('/update-password', 'AdminController@update_password')->name('update-password');

    // Routes for employees //
    Route::get('/employees/list-employees', 'EmployeeController@index')->name('employees.index');
    Route::get('/employees/add-employee', 'EmployeeController@create')->name('employees.create');
    Route::post('/employees', 'EmployeeController@store')->name('employees.store');
    Route::get('/employees/profile/{employee_id}', 'EmployeeController@employeeProfile')->name('employees.profile');
    Route::get('/employees/edit/{employee_id}', 'EmployeeController@employeeEdit')->name('employees.edit');
    Route::delete('/employees/{employee_id}', 'EmployeeController@destroy')->name('employees.delete');
    Route::POST('/employees/update{employee_id}', 'EmployeeController@update')->name('employees.update');

    //Import Export Employee
    Route::POST('/employees/file_import', 'EmployeeController@fileInport')->name('employees.file_import');
    Route::get('/employees/file_export', 'EmployeeController@fileExport')->name('employees.file_export');
    Route::get('/employees/mail', 'EmployeeController@mail')->name('employees.mail');
});