<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Tasks;

class TasksController extends Controller
{
    public function PageTasks(Request $request)
    {
        $user = Auth::user();
        $tasksQuery = Tasks::with('project') 
            ->leftJoin('projects', 'tasks.id_project', '=', 'projects.id')
            ->select(
                'tasks.id',
                'tasks.task',
                'tasks.description',
                'tasks.status',
                'projects.name',
                'projects.team_members',
                'projects.start_date',
                'projects.end_date',
                'projects.status as project_status'
        );      
        
        if ($user->role === 'User') {
            $tasksQuery->where(function ($query) use ($user) {
                $query->where('team_leader', $user->id)
                    ->orWhereRaw("JSON_SEARCH(team_members, 'one', ?) IS NOT NULL", [$user->id]);
            });
        }      
        
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
            'tasks' => $tasks,
            'auth' => $user 
        ]);
    }
}
