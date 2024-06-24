<?php

use App\Http\Controllers\Auth\{
    LoginController,
};

use App\Http\Controllers\{
    DashboardController,
    ChartController,
    MembersController,
    WorkplansController,
    QueueController,
    HistoryController
};
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

Route::get('login', [LoginController::class, 'showLoginForm'])->name('showLoginForm')->middleware('guest');
Route::post('login', [LoginController::class, 'authenticate'])->name('login');

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/progress_projects', [DashboardController::class, 'progressProjects'])->name('progress_projects');
});

Route::redirect('/', 'dashboard');

// Route data dari ChartController
Route::get('/chart_data_dept_1', [ChartController::class, 'showChartDept_1']);
Route::get('/chart_data_dept_2', [ChartController::class, 'showChartDept_2']);
Route::get('/chart_data_1', [ChartController::class, 'showChart_1']);
Route::get('/chart_data_2', [ChartController::class, 'showChart_2']);

Route::prefix('members')->name('members.')->group(function () {
    Route::get('/add_member', [MembersController::class, 'PageAddMember'])->name('add_member');
    Route::get('/list_members', [MembersController::class, 'PageListMembers'])->name('list_members');

    Route::post('/add/store_members', [MembersController::class, 'StoreMembers'])->name('store_members');
    Route::get('/list/edit_member', [MembersController::class, 'EditMember'])->name('edit_member');
    Route::post('/list/update_member', [MembersController::class, 'UpdateMember'])->name('update_member');
    Route::post('/list/update_status_member', [MembersController::class, 'UpdateStatusMember'])->name('update_status_member');
    Route::post('/list/delete_member', [MembersController::class, 'DeleteMember'])->name('delete_member');
});

Route::prefix('workplans')->name('workplans.')->group(function () {
    Route::get('/add_workplan', [WorkplansController::class, 'PageAddWorkplan'])->name('add_workplan');
    Route::get('/list_workplans', [WorkplansController::class, 'PageListWorkplans'])->name('list_workplans');
    Route::post('/launch_task', [WorkplansController::class, 'LaunchTask'])->name('launch_task');
   
    Route::post('/add/store_workplans', [WorkplansController::class, 'StoreWorkplans'])->name('store_workplans');
    Route::get('/list/edit_workplan', [WorkplansController::class, 'EditWorkplan'])->name('edit_workplan');
    Route::post('/list/update_workplan', [WorkplansController::class, 'UpdateWorkplan'])->name('update_workplan');
    Route::post('/list/delete_workplan', [WorkplansController::class, 'DeleteWorkplan'])->name('delete_workplan');
    Route::get('/list/view_workplan', [WorkplansController::class, 'ViewWorkplan'])->name('view_workplan');
    Route::post('/list/update_status_submitted', [WorkplansController::class, 'UpdateStatusSubmitted'])->name('update_status_submitted');
    Route::post('/list/update_status_completed', [WorkplansController::class, 'UpdateStatusCompleted'])->name('update_status_completed');

    // Task Plans
    Route::post('/list/view_workplan/store_task_plans', [WorkplansController::class, 'StoreTaskPlans'])->name('store_task_plans');
    Route::get('/list/view_workplan/list_task_plans', [WorkplansController::class, 'ListTaskPlans'])->name('list_task_plans');
    Route::post('/list/view_workplan/update_task_plan', [WorkplansController::class, 'UpdateTaskPlan'])->name('update_task_plan');
    // Weight
    Route::post('/list/view_workplan/store_weight_task_plans', [WorkplansController::class, 'StoreWeightTaskPlans'])->name('store_weight_task_plans');
    Route::get('/list/view_workplan/list_weight_task_plans', [WorkplansController::class, 'ListWeightTaskPlans'])->name('list_weight_task_plans');

    // Task Realizations
    Route::post('/list/view_workplan/store_task_realizations', [WorkplansController::class, 'StoreTaskRealizations'])->name('store_task_realizations');
    Route::get('/list/view_workplan/list_task_realizations', [WorkplansController::class, 'ListTaskRealizations'])->name('list_task_realizations');
    Route::post('/list/view_workplan/update_task_realization', [WorkplansController::class, 'UpdateTaskRealization'])->name('update_task_realization');
    Route::get('/list/view_workplan/filter_tp', [WorkplansController::class, 'FilterTP'])->name('filter_tp');
});

Route::prefix('queue')->name('queue.')->group(function () {
    Route::get('/', [QueueController::class, 'PageQueue'])->name('page_queue');
    Route::get('/edit_queue', [QueueController::class, 'EditQueue'])->name('edit_queue');
    Route::post('/update_queue', [QueueController::class, 'UpdateQueue'])->name('update_queue');
});

Route::prefix('history')->name('history.')->group(function () {
    Route::get('/', [HistoryController::class, 'PageHistory'])->name('page_history');
    Route::post('/approve', [HistoryController::class, 'Approve'])->name('approve');
    Route::post('/not_approve', [HistoryController::class, 'NotApprove'])->name('not_approve');
});