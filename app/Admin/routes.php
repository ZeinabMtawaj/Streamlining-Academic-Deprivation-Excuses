<?php

use Illuminate\Routing\Router;
use App\Admin\Controllers\ImportController;
use App\Http\Middleware\customizeSession;


Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => array_merge(config('admin.route.middleware'), ['customizeSession']),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('academic-year-and-semesters', AcademicYearAndSemesterController::class);
    $router->resource('courses', CourseController::class);
    $router->resource('advisor-student-links', AdvisorStudentLinkController::class);
    $router->resource('deprivations', DeprivationController::class);
    $router->resource('excuses', ExcuseController::class);


    $router->post('/import-data', [ImportController::class, 'importData'])->name('import_route');












});
