<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\UserController;

use App\Http\Middleware\AdminMiddleware;

/*
|--------------------------------------------------------------------------
| PUBLIC & AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLoginForm'])
    ->name('login.form');

Route::post('/', [AuthController::class, 'login'])
    ->name('login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/contact', fn () => view('contact'))
    ->name('contact');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER AREA
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    Route::get('/dashboard/search', [DashboardController::class, 'search'])
        ->name('dashboard.search');


    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS (CORE SYSTEM – STRICTLY AUTHENTICATED)
    |--------------------------------------------------------------------------
    | Documents are page-based and audited.
    */
    Route::prefix('documents')->group(function () {

        Route::get('/', [DocumentController::class, 'index'])
            ->name('documents.index');

        Route::get('/create', [DocumentController::class, 'create'])
            ->name('documents.create');

        Route::post('/', [DocumentController::class, 'store'])
            ->name('documents.store');

        Route::get('/{doc}', [DocumentController::class, 'show'])
            ->name('documents.show');

        Route::get('/{doc}/edit', [DocumentController::class, 'edit'])
            ->name('documents.edit');

        Route::put('/{doc}', [DocumentController::class, 'update'])
            ->name('documents.update');

        Route::delete('/{doc}', [DocumentController::class, 'destroy'])
            ->name('documents.destroy');

        Route::post('/{doc}/action', [DocumentController::class, 'performAction'])
            ->name('documents.action');
    });


    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])
            ->name('reports.index');

        Route::get('/export/pdf', [ReportsController::class, 'exportPDF'])
            ->name('reports.export.pdf');

        Route::get('/export/csv', [ReportsController::class, 'exportCSV'])
            ->name('reports.export.csv');
    });
});


/*
|--------------------------------------------------------------------------
| ADMIN AREA (STRICTLY CONTROLLED)
|--------------------------------------------------------------------------
| - Requires authentication
| - Requires AdminMiddleware
| - Modal-based CRUD only
*/
Route::middleware(['auth', AdminMiddleware::class])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DEPARTMENTS
    |--------------------------------------------------------------------------
    */
    Route::prefix('departments')->group(function () {

        Route::get('/', [DepartmentController::class, 'index'])
            ->name('departments.index');

        Route::post('/', [DepartmentController::class, 'store'])
            ->name('departments.store');

        Route::put('/{department}', [DepartmentController::class, 'update'])
            ->name('departments.update');

        Route::delete('/{department}', [DepartmentController::class, 'destroy'])
            ->name('departments.destroy');

        Route::post('/{department}/toggle', [DepartmentController::class, 'toggleStatus'])
            ->name('departments.toggle');

        Route::post('/search', [DepartmentController::class, 'searchNames'])
            ->name('departments.search');
    });


    /*
    |--------------------------------------------------------------------------
    | SECTIONS
    |--------------------------------------------------------------------------
    */
    Route::prefix('sections')->group(function () {

        Route::get('/', [SectionController::class, 'index'])
            ->name('sections.index');

        Route::post('/', [SectionController::class, 'store'])
            ->name('sections.store');

        Route::put('/{section}', [SectionController::class, 'update'])
            ->name('sections.update');

        Route::delete('/{section}', [SectionController::class, 'destroy'])
            ->name('sections.destroy');

        Route::post('/search', [SectionController::class, 'searchNames'])
            ->name('sections.search');
    });


    /*
    |--------------------------------------------------------------------------
    | POSITIONS
    |--------------------------------------------------------------------------
    */
    Route::prefix('positions')->group(function () {

        Route::get('/', [PositionController::class, 'index'])
            ->name('positions.index');

        Route::post('/', [PositionController::class, 'store'])
            ->name('positions.store');

        Route::put('/{position}', [PositionController::class, 'update'])
            ->name('positions.update');

        Route::delete('/{position}', [PositionController::class, 'destroy'])
            ->name('positions.destroy');

        Route::post('/search', [PositionController::class, 'searchNames'])
            ->name('positions.search');
    });


    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->group(function () {

        Route::get('/', [UserController::class, 'index'])
            ->name('users.index');

        Route::post('/', [UserController::class, 'store'])
            ->name('users.store');

        Route::put('/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });
});


/*
|--------------------------------------------------------------------------
| INTERNAL JSON ENDPOINTS (DynamicModal / AJAX)
|--------------------------------------------------------------------------
| - Authenticated
| - Admin-only
| - Explicit whitelist
*/
Route::prefix('api')->middleware(['auth', AdminMiddleware::class])->group(function () {

    Route::get('/sections/{section}', fn (\App\Models\Section $section) => response()->json($section));

    Route::get('/departments', fn () =>
        \App\Models\Department::select(
            'department_id as value',
            'department_name as label'
        )->orderBy('department_name')->get()
    );
});
