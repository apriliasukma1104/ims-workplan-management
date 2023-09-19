<?php

use App\Http\Controllers\Auth\{
    LoginController,
};

use App\Http\Controllers\{
    DashboardController,
    MembersController,
    ProjectsController,
    TasksController,
    ReportsController,
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
Route::post('login', [LoginController::class, 'authenticate'])->name('login');

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::post('/', [DashboardController::class, 'indexPost'])->name('indexPost');
    Route::get('/getDataDashboard', [DashboardController::class, 'getDataDashboard'])->name('data');
});

Route::redirect('/', 'dashboard');

Route::prefix('members')->name('members.')->group(function () {
    Route::get('/add_member', [MembersController::class, 'PageAddMember'])->name('add_member');
    Route::get('/list_members', [MembersController::class, 'PageListMembers'])->name('list_members');
    Route::get('/manage_member', [MembersController::class, 'PageManageMember'])->name('manage_member');
    Route::post('/update_manage_member', [MembersController::class, 'UpdateManageMember'])->name('update_manage_member');

    Route::post('/add/store_members', [MembersController::class, 'StoreMembers'])->name('store_members');
    Route::get('/list/edit_member', [MembersController::class, 'EditMember'])->name('edit_member');
    Route::post('/list/update_member', [MembersController::class, 'UpdateMember'])->name('update_member');
    Route::post('/list/delete_member', [MembersController::class, 'DeleteMember'])->name('delete_member');
});

// Route::get('/images/{filename}', function ($filename) {
//     $path = storage_path('app/public/uploads/' . $filename);
    
//     // if (file_exists($path)) {
//     //     return Response::file($path);
//     // } else {
//     //     abort(404);
//     // }
// }); 

Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/add_project', [ProjectsController::class, 'PageAddProject'])->name('add_project');
    Route::get('/list_projects', [ProjectsController::class, 'PageListProjects'])->name('list_projects');
   
    Route::post('/add/store_projects', [ProjectsController::class, 'StoreProjects'])->name('store_projects');
    Route::get('/list/edit_project', [ProjectsController::class, 'EditProject'])->name('edit_project');
    Route::post('/list/update_project', [ProjectsController::class, 'UpdateProject'])->name('update_project');
    Route::post('/list/update_validation', [ProjectsController::class, 'UpdateValidation'])->name('update_validation');
    Route::post('/list/team_leader_name', [ProjectsController::class, 'GetTeamLeaderName'])->name('team_leader_name');
    Route::post('/list/delete_project', [ProjectsController::class, 'DeleteProject'])->name('delete_project');

    Route::get('/list/view_project', [ProjectsController::class, 'ViewProject'])->name('view_project');
    Route::post('/list/view_project/store_tasks', [ProjectsController::class, 'StoreTasks'])->name('store_tasks');
    Route::get('/list/view_project/list_tasks', [ProjectsController::class, 'ListTasks'])->name('list_tasks');
    Route::post('/list/view_project/update_task', [ProjectsController::class, 'UpdateTask'])->name('update_task');
    Route::post('/list/view_project/delete_task', [ProjectsController::class, 'DeleteTask'])->name('delete_task');
});

Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TasksController::class, 'PageTasks'])->name('page_tasks');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportsController::class, 'PageReports'])->name('page_reports');
});