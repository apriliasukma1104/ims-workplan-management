<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Models\Members;
use App\Models\Projects;
use App\Models\Tasks;

class ProjectsController extends Controller
{
    // public function PageListProjects(Request $request)
    // {
    //     $params=[
    //         'page'=>$request->input('page'),
    //         'search'=>$request->input('search')
    //     ];
    //     $title = 'Projects';
    //     $projects = Projects::select('id', 'name', 'project_type', 'team_leader', 'start_date', 'end_date', 'status')->get();
    //     return Inertia::render('Projects/ListProjects', [
    //         'title' => $title,
    //         'projects' => $projects, // Mengirim data proyek ke halaman Vue.js
    //     ]);
    // }

    public function PageListProjects(Request $request)
    {
        $search = $request->input('search');
        $title = 'Projects';
        $projectsQuery = Projects::with('teamLeader')
            ->select('id', 'name', 'project_type', 'team_leader', 'start_date', 'end_date', 'status');
        if ($search) {
            $projectsQuery->where('name', 'like', '%' . $search . '%');
        }
        $projects = $projectsQuery->paginate(10);
        return Inertia::render('Projects/ListProjects', [
            'title' => $title,
            'projects' => $projects, 
        ]);
    }

    public function PageAddProject()
    {
        $title = 'Projects';
        $members = Members::all(); 
        return Inertia::render('Projects/AddProject', [
            'title' =>  $title,
            'members' => $members 
        ]);
    }

    public function StoreProjects(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'project_type' => 'required|string|in:RKAP,KPI,NPM',
            'team_leader' => 'required|exists:members,id', 
            'team_members' => 'required|array',
            'team_members.*' => 'exists:members,id', 
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:to do,doing,done',
            'description' => 'required|string|max:255',
        ])->validate();
        $validatedData['team_members'] = json_encode($validatedData['team_members']);
        $project = Projects::create($validatedData);
        $teamMembers = json_decode($validatedData['team_members']);
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
        $project = Projects::find($request->input('id'));
        $project->update($request->all());
        return redirect()->route('projects.list_projects')->with('message', 'Data Updated Successfully');
    }

    public function DeleteProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:projects,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $project = Projects::findOrFail($request->id);
            $project->delete();
            return response()->json(['message' => 'Data Deleted Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to Delete Data'], 500);
        }
    }

    public function ViewProject(Request $request)
    {
        $project = Projects::with(['teamMembers', 'teamLeader'])->findOrFail($request->id);
        $task = Tasks::select('id', 'id_project', 'task', 'description', 'status')
            ->where('id_project', $request->id) ->get();

        return Inertia::render('Projects/ViewProject', [
            'formData' => $project,
            'tasks' => $task,
        ]);
    }

    public function StoreTasks(Request $request)
    {
        $validatedData = $request->validate([
            'task' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required|in:to do,doing,done',
            'id_project' => 'required|integer',
        ]);
        Tasks::create($validatedData);
    }

    public function ListTasks(Request $request){
        $task = Tasks::where('id_project', $request->id_project) ->get();
        return response()->json(['message' => 'Data Saved Successfully', "data" => $task]);
    }

    public function UpdateTask(Request $request)
    {
        $task = Tasks::findOrFail($request->id); 
        $task->update($request->all());
    }

    public function DeleteTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tasks,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $task = Tasks::findOrFail($request->id);
            $task->delete();
            return response()->json(['message' => 'Data Deleted Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to Delete Data'], 500);
        }
    }
}
