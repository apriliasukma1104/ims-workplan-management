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
        $tasksQuery = Tasks::with('project') 
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
            );

        $search = $request->input('search');
        if ($search) {
            $tasksQuery->where(function ($query) use ($search) {
                $columns = ['tasks.task', 'tasks.description', 'tasks.status', 'projects.name', 'projects.start_date', 'projects.end_date', 'projects.status'];
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        $tasks = $tasksQuery->paginate(10);

        if ($request->ajax()){
            return response()->json(['data'=>$tasks]);
        }
            
        return Inertia::render('Tasks/PageTasks', [
            'title' => $title,
            'tasks' => $tasks,
        ]);
    }

}
