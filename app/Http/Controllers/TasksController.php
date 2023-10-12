<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Tasks;
use App\Models\Projects;

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
        
        // Opsi filter projects -> name
        $projects = Projects::orderBy('name', 'asc')->get(); 

        // Filter projects -> name
        $tasksFilter = $request->query('tasksFilter');
        if ($tasksFilter) {
            $tasksQuery = $tasksQuery->where('projects.name', 'LIKE', "%$tasksFilter%");
        }
        
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

        // Setting ascending pada task
        $tasks = $tasksQuery->orderBy('projects.name', 'asc')->paginate(10);

        if ($request->ajax()){
            return response()->json(['data'=>$tasks]);
        }
            
        return Inertia::render('Tasks/PageTasks', [
            'projects' => $projects,
            'tasks' => $tasks,
            'auth' => $user,
        ]);
    }

    public function UpdateProductivity(Request $request)
    {
        $project = Tasks::findOrFail($request->id); 
        $project->update($request->all());
    }

}
