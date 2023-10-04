<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Members;
use App\Models\Projects;
use App\Models\Tasks;

class ProjectsController extends Controller
{
    public function PageListProjects(Request $request)
    {
        $user = Auth::user();
        $projectsQuery = Projects::with('teamLeader', 'teamMembers')
            ->select('id', 'name', 'project_type', 'team_leader', 'start_date', 'end_date', 'status', 'team_members');
            
        // Setting Sortable
        $sortField = $request->input('sortField', 'name'); 
        $sortOrder = $request->input('sortOrder', 'asc'); 

        if ($user->role === 'User') {
            $projectsQuery->where(function ($query) use ($user) {
                $query->where('team_leader', $user->id)
                      ->orWhereHas('teamMembers', function ($query) use ($user) {
                          $query->where('id', $user->id);
                      });
            });
        }

        $search = $request->input('search');
        if ($search) {
            $projectsQuery->where(function ($query) use ($search) {
                $columns = ['name', 'project_type', 'start_date', 'end_date', 'status'];
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('teamLeader', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $projectsQuery->orderBy($sortField, $sortOrder);

        $projects = $projectsQuery->paginate(10);
        
        if ($request->ajax()){
            return response()->json(['data'=>$projects]);
        }
        
        return Inertia::render('Projects/ListProjects', [
            'projects' => $projects,
            'auth' => $user 
        ]);
    }

    public function PageAddProject()
    {
        $members = Members::all(); 
        return Inertia::render('Projects/AddProject', [
            'members' => $members 
        ]);
    }

    public function StoreProjects(Request $request)
    {
        $projectsQuery = $request->all();
        $projectsQuery['team_members'] = json_encode($projectsQuery['team_members']);
        $project = Projects::create($projectsQuery);
        $teamMembers = json_decode($projectsQuery['team_members']);
        foreach ($teamMembers as $memberId) {
            $project->teamMembers()->attach($memberId);
        }
        return redirect()->route('projects.list_projects')->with('message', 'Data Created Successfully!');
    }

    public function EditProject(Request $request)
    {
        $project = Projects::findOrFail($request->id);
        $members = Members::all(); 
        return Inertia::render('Projects/EditProject', [
            'formData' => $project,
            'members' => $members 
        ]);
    }

    public function UpdateProject(Request $request)
    {
        $project = Projects::findOrFail($request->id); 
        $project->update($request->all());

        $members = $project->teamMembers;
        $project->teamMembers()->sync($request->input('team_members'));

        return redirect()->route('projects.list_projects')
            ->with('project', $project)
            ->with('members', $members);
    }

    public function DeleteProject(Request $request)
    {
        $project = Projects::findOrFail($request->id);
        $project->delete();
    }

    public function ViewProject(Request $request)
    {
        $project = Projects::with(['teamMembers', 'teamLeader'])->findOrFail($request->id);
        $task = Tasks::select('id', 'id_project', 'name_member', 'task', 'description', 'status')
            ->where('id_project', $request->id) ->get();

        return Inertia::render('Projects/ViewProject', [
            'formData' => $project,
            'tasks' => $task,
        ]);
    }

    public function validation(Request $request)
    {
        $project = Projects::findOrFail($request->id); 
        $project->update($request->all());
    }

    public function StoreTasks(Request $request)
    {
        $task = $request->all();
        Tasks::create($task);
    }

    public function ListTasks(Request $request){
        $tasks = Tasks::where('id_project', $request->id_project) ->get();
        return response()->json(["data" => $tasks]);
    }

    public function UpdateTask(Request $request)
    {
        $task = Tasks::findOrFail($request->id); 
        $task->update($request->all());
    }

    public function DeleteTask(Request $request)
    {
        $task = Tasks::findOrFail($request->id);
        $task->delete();
    }
}