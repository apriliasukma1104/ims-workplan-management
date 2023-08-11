<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tasks;

class TasksController extends Controller
{
    public function PageTasks(Request $request)
    {
        $title = 'Tasks';
        return Inertia::render('Tasks/PageTasks', [
            'title' => $title,
        ]);
    }

    // public function PageTasks(Request $request)
    // {
    //     $search = $request->input('search');
        
    //     $tasksQuery = Tasks::with('project')
    //         ->select('tasks.id', 'tasks.task', 'tasks.status', 'projects.name', 'projects.start_date', 'projects.end_date', 'projects.status');

    //     if ($search) {
    //         $tasksQuery->where('tasks.task', 'like', '%' . $search . '%');
    //     }

    //     $tasksList = $tasksQuery->paginate(10);

    //     return Inertia::render('Tasks/PageTasks', [
    //         'tasksList' => $tasksList,
    //     ]);
    // }
}
