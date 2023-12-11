<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExcuseController;
use App\Http\Controllers\AdvisorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\DeprivationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [UserController::class, 'home'])->middleware('auth');

Route::get('/signin', [UserController::class, 'signin'])->name('login')->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');
Route::post('/users/authenticate', [UserController::class, 'authenticate']);

Route::get('/students/home', [StudentController::class, 'index'])->middleware('auth');

Route::post('/Excuse/submit-excuse',[ExcuseController::class, 'store'])->middleware('auth');

Route::get('/export-deprivations', [DeprivationController::class, 'export'])->name('export.deprivations')->middleware('auth');


Route::get('/advisors/home', [AdvisorController::class, 'index'])->middleware('auth');
Route::post('/excuses/update-excuse',[ExcuseController::class, 'update'])->middleware('auth');
Route::get('/export-excuses', [ExcuseController::class, 'export'])->name('export.excuses')->middleware('auth');

// Route::get('/committees/approved', [CommitteeController::class, 'getApprovedExcuses'])->middleware('auth');
// Route::get('/committees/rejected', [CommitteeController::class, 'getRejectedExcuses'])->middleware('auth');
// Route::get('/committees/export-approved', [CommitteeController::class, 'exportApproved'])->name('export.excuses.approved')->middleware('auth');
// Route::get('/committees/export-rejected', [CommitteeController::class, 'exportRejected'])->name('export.excuses.rejected')->middleware('auth');

Route::get('/committees/home/{stat}', [CommitteeController::class, 'index'])->middleware('auth');
Route::get('/committees/export/{stat}', [CommitteeController::class, 'export'])->name('export.excuses.stat')->middleware('auth');
Route::post('/committees/update-excuse',[CommitteeController::class, 'updateExcuse'])->middleware('auth');





Route::get('/download/{filename}', [FileController::class, 'download'])->name('download');
Route::get('/file/download/{model}/{folder}/{id}', [FileController::class, 'downloadPrivate'])->name('file.download');
// ->middleware('auth')

Route::post('/language-switch', [LanguageController::class, 'switchLang'])->name('language.switch');


