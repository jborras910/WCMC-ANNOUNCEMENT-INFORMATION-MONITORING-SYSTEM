<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

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


//user and guest
Route::get('/', [Controller::class, 'welcome'])->name('welcome');

Route::get('/login', [Controller::class, 'login'])->name('login');
Route::get('/register', [Controller::class, 'register'])->name('register');
Route::post('/login', [Controller::class, 'loginPost'])->name('login.post');
Route::post('/registration', [Controller::class, 'registrationPost'])->name('registration.post');




Route::get('/logout', [Controller::class, 'logout'])->name('logout');
Route::get('/dashboard', [Controller::class, 'dashboard'])->name('admin.dashboard');


//user crud
Route::get('/users', [Controller::class, 'users'])->name('admin.users');
Route::get('/addUser', [Controller::class, 'addUser'])->name('admin.addUser');
Route::post('/addUser', [Controller::class, 'addUserPost'])->name('admin.addUserPost');
Route::delete('/deleteUser/{user}/destroy', [Controller::class, 'destroyUser'])->name('deleteUser.destroy');
Route::get('/editUser/{user}/edit', [Controller::class, 'editUser'])->name('user.edit');
Route::put('/updateUser/{user}/updateUser', [Controller::class, 'updateUser'])->name('admin.updateUserPost');


//activity logs
Route::get('/activity', [Controller::class, 'activity'])->name('admin.activity');
Route::get('/viewInterface', [Controller::class, 'viewInterface'])->name('admin.viewInterface');

//slide crud
Route::get('/addSlide', [Controller::class, 'addSlide'])->name('admin.addSlide');
Route::post('/addVideoslide', [Controller::class, 'addVideoslide'])->name('addVideoslide.post');
Route::get('/slide/{slide}/edit', [Controller::class, 'editSlide'])->name('slide.edit');
Route::put('/slide/{slide}/updateVideo', [Controller::class, 'updateVideo'])->name('slide.updateVideo');
Route::delete('/deleteSlide/{slide}/destroy', [Controller::class, 'destroy'])->name('deleteSlide.destroy');




// Route::post('/addSlide', [Controller::class, 'addSlidePost'])->name('addSlide.post');
// Route::post('/addDocumnetslide', [Controller::class, 'addDocumentslide'])->name('addDocumentslide.post');
// Route::put('/slide/{slide}/update', [Controller::class, 'updateSlide'])->name('slide.update');
