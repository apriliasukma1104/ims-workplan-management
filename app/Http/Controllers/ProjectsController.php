<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Models\Projects;
use App\Models\Members;
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
        $projectsQuery = Projects::select('id', 'name', 'project_type', 'team_leader', 'start_date', 'end_date', 'status');
        if ($search) {
            $projectsQuery->where('name', 'like', '%' . $search . '%');
        }
        $projects = $projectsQuery->paginate(5);
        return Inertia::render('Projects/ListProjects', [
            'title' => $title,
            'projects' => $projects, // Mengirim data yang telah difilter ke halaman Vue.js
        ]);
    }

    public function PageAddProjects()
    {
        $title = 'Projects';
        $members = Members::all(); 
        return Inertia::render('Projects/AddProjects', [
            'title' =>  $title,
            'members' => $members 
        ]);
    }

    // public function StoreProjects(Request $request){
    //     // dd($request->all());
    //     $validatedData = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'project_type' => 'required|string|in:RKAP,KPI,NPM',
    //         'team_leader' => 'required|exists:members,id', 
    //         'team_members' => 'required|array',
    //         'team_members.*' => 'exists:members,id', 
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //         'status' => 'required|in:to do,doing,done',
    //         'description' => 'required|string|max:255',
    //     ])->validate();
    //     // Konversi array anggota tim menjadi string JSON
    //     $validatedData['team_members'] = json_encode($validatedData['team_members']);
    //     // Simpan data ke database
    //     $project = Projects::create($validatedData);
    //     // Simpan data anggota tim
    //     $teamMembers = json_decode($validatedData['team_members']);
    //     foreach ($teamMembers as $memberId) {
    //         $project->teamMembers()->attach($memberId);
    //     }
    //     return response()->json(['message' => 'Data created successfully'], 201);
    // }

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
        return redirect()->route('projects.list_projects')->with('message', 'Data created successfully!');
    }

    public function EditProjects(Request $request)
    {
        $projects = Projects::findOrFail($request->id);
        $members = Members::all(); 
        return Inertia::render('Projects/EditProjects', [
            'formData' => $projects,
            'members' => $members 
        ]);
    }

    public function UpdateProjects(Request $request)
    {
        $projects = Projects::find($request->input('id'));
        $projects->update($request->all());
        return redirect()->route('projects.list_projects')->with('message', 'Data Berhasil Diupdate!');
    }

    public function DeleteProjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:projects,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            // Cari data project berdasarkan ID
            $project = Projects::findOrFail($request->id);
            $project->delete();
            return response()->json(['message' => 'Data deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete data'], 500);
        }
    }

    public function ViewProjects(Request $request)
    {
        $projects = Projects::with('teamMembers')->findOrFail($request->id);
        return Inertia::render('Projects/ViewProjects', [
            'formData' => $projects,
        ]);
    }

    public function StoreTasks(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_project' => 'required|integer',
                'task' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'status' => 'required|in:to do,doing,done',
            ]);
            Tasks::create($validatedData);
            return redirect()->route('projects.list_tasks')->with('message', 'Data Created successfully!');
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data Failed to Save!'], 500);
        }
    }

    public function ListTasks(Request $request)
    {
        $tasks = Tasks::select('id', 'id_project', 'task', 'description', 'status')->get();
        return response()->json(['tasks' => $tasks]);
    }
}
