<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DepartmentController;

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


route::get('test/page', function (){
    dd(phpinfo());
});

//user and guest
Route::get('/', [Controller::class, 'welcome'])->name('welcome');
Route::get('/welcome-queue', [Controller::class, 'welcomeQueue'])->name('welcomeQueue');
Route::get('/slides/current', [Controller::class, 'currentSlides'])->name('slides.current');

// per-department public display screens (e.g. Marketing's own looping TV)
Route::get('/display/{department}', [Controller::class, 'departmentWelcome'])->name('display.welcome');
Route::get('/display/{department}/current', [Controller::class, 'departmentCurrentSlides'])->name('display.current');


Route::get('/TableData', [Controller::class, 'dataTable'])->name('dataTable');



Route::get('/login', [Controller::class, 'login'])->name('login');
Route::get('/register', [Controller::class, 'register'])->name('register');
Route::post('/login', [Controller::class, 'loginPost'])->name('login.post');
Route::post('/registration', [Controller::class, 'registrationPost'])->name('registration.post');

Route::group(['middleware' => 'auth'], function(){
    Route::get('/logout', [Controller::class, 'logout'])->name('logout');
    Route::get('/dashboard', [Controller::class, 'dashboard'])->name('admin.dashboard');
    //user crud — master_admin only
    Route::group(['middleware' => 'can:manage-users'], function(){
        Route::get('/users', [Controller::class, 'users'])->name('admin.users');
        Route::get('/addUser', [Controller::class, 'addUser'])->name('admin.addUser');
        Route::post('/addUser', [Controller::class, 'addUserPost'])->name('admin.addUserPost');
        Route::delete('/deleteUser/{user}/destroy', [Controller::class, 'destroyUser'])->name('deleteUser.destroy');
        Route::get('/editUser/{user}/edit', [Controller::class, 'editUser'])->name('user.edit');
        Route::put('/updateUser/{user}/updateUser', [Controller::class, 'updateUser'])->name('admin.updateUserPost');
    });

    // roles & permissions management — master_admin only
    Route::group(['middleware' => 'can:manage-roles'], function(){
        Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles');
        Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroyRole'])->name('admin.roles.destroy');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroyPermission'])->name('admin.permissions.destroy');
    });

    // departments management — master_admin only
    Route::group(['middleware' => 'can:manage-departments'], function(){
        Route::get('/departments', [DepartmentController::class, 'index'])->name('admin.departments');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('admin.departments.store');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('admin.departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroyDepartment'])->name('admin.departments.destroy');
    });

    //activity logs
    Route::get('/activity', [Controller::class, 'activity'])->name('admin.activity');
    Route::get('/viewInterface', [Controller::class, 'viewInterface'])->name('admin.viewInterface');

    //slide crud — anyone who can author/manage slides
    Route::group(['middleware' => 'can:manage-slides'], function(){
        Route::get('/addSlide', [Controller::class, 'addSlide'])->name('admin.addSlide');
        Route::post('/addVideoslide', [Controller::class, 'addVideoslide'])->name('addVideoslide.post');
        Route::get('/slide/{slide}/edit', [Controller::class, 'editSlide'])->name('slide.edit');
        Route::put('/slide/{slide}/updateVideo', [Controller::class, 'updateVideo'])->name('slide.updateVideo');
        Route::delete('/deleteSlide/{slide}/destroy', [Controller::class, 'destroy'])->name('deleteSlide.destroy');
        Route::post('/slides/reorder', [Controller::class, 'reorderSlides'])->name('slides.reorder');
    });

    Route::get('/filter', [Controller::class, 'filter'])->name('filter');

    //slide review queue — master_admin and admin
    Route::group(['middleware' => 'can:review-slides'], function(){
        Route::put('/pending/{slide}/publishFile', [Controller::class, 'publishFile'])->name('slide.publishFile');
        Route::put('/pending/{slide}/reject', [Controller::class, 'rejectFile'])->name('slide.reject');
        Route::get('/pending', [Controller::class, 'pendingSlides'])->name('pendingSlides');
    });
});

Route::fallback(function () {


    return view('error');
});





// Route::post('/addSlide', [Controller::class, 'addSlidePost'])->name('addSlide.post');
// Route::post('/addDocumnetslide', [Controller::class, 'addDocumentslide'])->name('addDocumentslide.post');
// Route::put('/slide/{slide}/update', [Controller::class, 'updateSlide'])->name('slide.update');
