<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Projects;
use App\Models\Members;

class QueueController extends Controller
{
    public function PageQueue(Request $request)
    {
        $user = Auth::user();

        $projectsQuery = Projects::with('teamLeader', 'teamMembers')
            ->select('projects.*', DB::raw('CAST(budget AS DECIMAL(0)) as _budget'))
            ->where('status', 'Submitted');        

        $search = $request->input('search');
        if ($search) {
            $projectsQuery->where(function ($query) use ($search) {
                $columns = ['code_workplans', 'name', 'project_type', 'team_leader', 'year'];
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('teamLeader', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $projects = $projectsQuery->orderBy('id', 'desc')->paginate(10);
        
        if ($request->ajax()){
            return response()->json(['data'=>$projects]);
        }
        
        return Inertia::render('Queue/PageQueue', [
            'projects' => $projects,
            'auth' => $user 
        ]);
    }

    public function EditQueue(Request $request)
    {
        $project = Projects::findOrFail($request->id);
        
        $members = Members::whereIn('role', ['Staf', 'Kabag'])
        ->where('members_status', 1)
        ->get();

        $id_team_members = json_decode($project->team_members);
        $team_members = Members::whereIn('id', $id_team_members)->get();

        return Inertia::render('Queue/EditQueue', [
            'formData' => $project,
            'members' => $members,
            'team_members' => $team_members 
        ]);
    }

    public function UpdateQueue(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'project_type' => 'required',
            'team_leader' => 'required',
            'team_members' => 'required',
            'year' => 'required',
            'budget' => 'required',
        ]);
        $project = Projects::findOrFail($request->id); 

        $params = $request->all();
        $params['team_members'] = array_column($params['team_members'],'id');

        $project->update($params);

        $members = $project->teamMembers;
        $project->teamMembers()->sync($params['team_members']);

        return redirect()->route('queue.page_queue')
            ->with('project', $project)
            ->with('members', $members);
    }
}
