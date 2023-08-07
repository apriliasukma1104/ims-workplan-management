<?php

use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
};

use App\Http\Controllers\{
    DashboardController,
    MembersController,
    ProjectsController,

    MasterController,
    PostsController,
    DcrController,
    CekController,
};
use App\Models\Members;
use App\Models\Projects;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

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

Route::get('login', [LoginController::class, 'showLoginForm'])->name('showLoginForm')->middleware('guest');
Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('showRegisterForm')->middleware('guest');

Route::post('login', [LoginController::class, 'authenticate'])->name('login');
Route::post('register', [RegisterController::class, 'register'])->name('register');

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::post('/', [DashboardController::class, 'indexPost'])->name('indexPost');
    Route::get('/getDataDashboard', [DashboardController::class, 'getDataDashboard'])->name('data');
});

Route::prefix('members')->name('members.')->group(function () {
    Route::get('/add_members', [MembersController::class, 'PageAddMembers'])->name('add_members');
    Route::get('/list_members', [MembersController::class, 'PageListMembers'])->name('list_members');
    Route::get('/manage_member', [MembersController::class, 'PageManageMember'])->name('manage_member');
    Route::post('/update_manage_member', [MembersController::class, 'UpdateManageMember'])->name('update_manage_member');

    Route::post('/add/store_members', [MembersController::class, 'StoreMembers'])->name('store_members');
    Route::get('/list/edit_members', [MembersController::class, 'EditMembers'])->name('edit_members');
    Route::post('/list/update_members', [MembersController::class, 'UpdateMembers'])->name('update_members');
    Route::post('/list/delete_members', [MembersController::class, 'DeleteMembers'])->name('delete_members');
});

Route::get('/images/{filename}', function ($filename) {
    $path = storage_path('app/public/uploads/' . $filename);
    
    if (file_exists($path)) {
        return Response::file($path);
    } else {
        abort(404);
    }
});

Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/add_projects', [ProjectsController::class, 'PageAddProjects'])->name('add_projects');
    Route::get('/list_projects', [ProjectsController::class, 'PageListProjects'])->name('list_projects');
   
    Route::post('/add/store_projects', [ProjectsController::class, 'StoreProjects'])->name('store_projects');
    Route::get('/list/edit_projects', [ProjectsController::class, 'EditProjects'])->name('edit_projects');
    Route::post('/list/update_projects', [ProjectsController::class, 'UpdateProjects'])->name('update_projects');
    Route::post('/list/team_leader_name', [ProjectsController::class, 'GetTeamLeaderName'])->name('team_leader_name');
    Route::post('/list/delete_projects', [ProjectsController::class, 'DeleteProjects'])->name('delete_projects');
    Route::get('/list/view_projects', [ProjectsController::class, 'ViewProjects'])->name('view_projects');
    
    Route::post('/list/view_projects/store_tasks', [ProjectsController::class, 'StoreTasks'])->name('store_tasks');
    Route::get('/list/view_projects/list_tasks', [ProjectsController::class, 'ListTasks'])->name('list_tasks');
});

Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TasksController::class, 'PageTasks'])->name('page_tasks');
});

Route::resource('post', PostsController::class);

Route::redirect('/', 'dashboard');



// Route::prefix('dcr')->name('dcr.')->group(function () {
//     Route::get('/', [DcrController::class, 'index'])->name('index');
// });

// Route::prefix('master')->name('master.')->group(function () {
//     Route::get('/lokasi', [MasterController::class, 'PageMaster'])->name('PageMaster');
//     Route::get('/get-lokasi', [MasterController::class, 'getLokasi'])->name('getLokasi');
//     Route::post('/simpan-lokasi', [MasterController::class, 'simpanLokasi'])->name('simpanLokasi');
//     Route::post('/delete-lokasi', [MasterController::class, 'hapusLokasi'])->name('hapusLokasi');

//     Route::get('/gedung', [MasterController::class, 'PageGedung'])->name('PageGedung');
//     Route::get('/get-gedung', [MasterController::class, 'getGedung'])->name('getGedung');
//     Route::post('/simpan-gedung', [MasterController::class, 'simpanGedung'])->name('simpanGedung');
//     Route::post('/delete-gedung', [MasterController::class, 'hapusGedung'])->name('hapusGedung');

//     Route::get('/proyek', [MasterController::class, 'PageProyek'])->name('PageProyek');
//     Route::get('/get-proyek', [MasterController::class, 'PageProyek'])->name('getProyek');
//     Route::post('/simpan-proyek', [MasterController::class, 'simpanProyek'])->name('simpanProyek');
//     Route::post('/delete-proyek', [MasterController::class, 'hapusProyek'])->name('hapusProyek');

//     Route::get('/user', [MasterController::class, 'PageUser'])->name('PageUser');
//      Route::post('/simpan-user', [MasterController::class, 'simpanUser'])->name('simpanUser');
//     Route::post('/delete-user', [MasterController::class, 'hapusUser'])->name('hapusUser');
// });

// Route::prefix('cek')->name('cek.')->group(function () {
//     Route::get('cek-by-lpbg', [CekController::class, 'CekLpbg'])->name('ceklpbg');
//     Route::get('cek-by-sttp', [CekController::class, 'CekSttp'])->name('ceksttp');
// });
