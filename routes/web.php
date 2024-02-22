<?php

use App\Http\Controllers\Auth\{
    LoginController,
};

use App\Http\Controllers\{
    DashboardController,
    MembersController,
    WorkplansController
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

    Route::post('/add/store_members', [MembersController::class, 'StoreMembers'])->name('store_members');
    Route::get('/list/edit_member', [MembersController::class, 'EditMember'])->name('edit_member');
    Route::post('/list/update_member', [MembersController::class, 'UpdateMember'])->name('update_member');
    Route::post('/list/update_status_member', [MembersController::class, 'UpdateStatusMember'])->name('update_status_member');
});

Route::prefix('workplans')->name('workplans.')->group(function () {
    Route::get('/add_workplan', [WorkplansController::class, 'PageAddWorkplan'])->name('add_workplan');
    Route::get('/list_workplans', [WorkplansController::class, 'PageListWorkplans'])->name('list_workplans');
   
    Route::post('/add/store_workplans', [WorkplansController::class, 'StoreWorkplans'])->name('store_workplans');
    Route::get('/list/edit_workplan', [WorkplansController::class, 'EditWorkplan'])->name('edit_workplan');
    Route::post('/list/update_workplan', [WorkplansController::class, 'UpdateWorkplan'])->name('update_workplan');
    Route::post('/list/update_status_project', [WorkplansController::class, 'UpdateStatusProject'])->name('update_status_project');
    Route::get('/list/view_workplan', [WorkplansController::class, 'ViewWorkplan'])->name('view_workplan');


    // Task Plans
    Route::post('/list/view_workplan/store_task_plans', [WorkplansController::class, 'StoreTaskPlans'])->name('store_task_plans');
    Route::get('/list/view_workplan/list_task_plans', [WorkplansController::class, 'ListTaskPlans'])->name('list_task_plans');
    Route::post('/list/view_workplan/update_task_plan', [WorkplansController::class, 'UpdateTaskPlan'])->name('update_task_plan');
    // Weight
    Route::post('/list/view_workplan/store_weight_task_plans', [WorkplansController::class, 'StoreWeightTaskPlans'])->name('store_weight_task_plans');
    Route::get('/list/view_workplan/list_weight_task_plans', [WorkplansController::class, 'ListWeightTaskPlans'])->name('list_weight_task_plans');
    Route::post('/list/view_workplan/update_weight_task_plan', [WorkplansController::class, 'UpdateWeightTaskPlan'])->name('update_weight_task_plan');

    // Task Realizations
    Route::post('/list/view_workplan/store_task_realizations', [WorkplansController::class, 'StoreTaskRealizations'])->name('store_task_realizations');
    Route::get('/list/view_workplan/list_task_realizations', [WorkplansController::class, 'ListTaskRealizations'])->name('list_task_realizations');
    Route::post('/list/view_workplan/update_task_realization', [WorkplansController::class, 'UpdateTaskRealization'])->name('update_task_realization');
});

// Route::prefix('tasks')->name('tasks.')->group(function () {
//     Route::get('/', [TasksController::class, 'PageTasks'])->name('page_tasks');
// });