<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tasks;

class TasksController extends Controller
{
    // public function PageTasks(Request $request)
    // {
    //     $title = 'Tasks';
    //     return Inertia::render('Tasks/PageTasks', [
    //         'title' => $title,
    //     ]);
    // }

    public function PageTasks(Request $request)
    {
        $title = 'Tasks';

        $tasks = Tasks::with('project') 
            ->leftJoin('projects', 'tasks.id_project', '=', 'projects.id')
            ->select(
                'tasks.id',
                'tasks.task',
                'tasks.description',
                'tasks.status',
                'projects.name',
                'projects.start_date',
                'projects.end_date',
                'projects.status as project_status'
            )
            ->paginate(10);

        return Inertia::render('Tasks/PageTasks', [
            'title' => $title,
            'tasks' => $tasks,
        ]);
    }
}
