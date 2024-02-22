<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Members;
use App\Models\Projects;
use App\Models\TaskPlans;
use App\Models\TaskRealizations;
use App\Models\WeightTaskPlans;

class WorkplansController extends Controller
{
    public function PageListWorkplans(Request $request)
    {
        $user = Auth::user();

        $projectsQuery = Projects::with('teamLeader', 'teamMembers')
            ->select('id', 'name', 'description', 'project_type', 'team_leader', 'team_members', 'year', 'projects_status');

        if ($user->role === 'Kabag' || $user->role === 'Staf') {
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
                $columns = ['name', 'project_type', 'team_leader', 'year'];
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('teamLeader', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // Setting ascending pada projects
        $projects = $projectsQuery->orderBy('year', 'desc')->paginate(10);
        
        if ($request->ajax()){
            return response()->json(['data'=>$projects]);
        }
        
        return Inertia::render('Workplans/ListWorkplans', [
            'projects' => $projects,
            'auth' => $user 
        ]);
    }

    public function PageAddWorkplan()
    {
        $members = Members::all(); 
        return Inertia::render('Workplans/AddWorkplan', [
            'members' => $members 
        ]);
    }

    public function StoreWorkplans(Request $request)
    {
        $projectsQuery = $request->all();
        $projectsQuery['team_members'] = json_encode($projectsQuery['team_members']);
        $project = Projects::create($projectsQuery);
        $teamMembers = json_decode($projectsQuery['team_members']);
        foreach ($teamMembers as $memberId) {
            $project->teamMembers()->attach($memberId);
        }
        return redirect()->route('workplans.list_workplans')->with('message', 'Data Created Successfully!');
    }

    public function EditWorkplan(Request $request)
    {
        $project = Projects::findOrFail($request->id);
        $members = Members::all(); 
        return Inertia::render('Workplans/EditWorkplan', [
            'formData' => $project,
            'members' => $members 
        ]);
    }

    public function UpdateWorkplan(Request $request)
    {
        $project = Projects::findOrFail($request->id); 
        $project->update($request->all());

        $members = $project->teamMembers;
        $project->teamMembers()->sync($request->input('team_members'));

        return redirect()->route('workplans.list_workplans')
            ->with('project', $project)
            ->with('members', $members);
    }

    public function UpdateStatusProject(Request $request)
    {
        $project = Projects::find($request->input('id'));
        $project->projects_status=$request->input('projects_status');
        $project->save();
    }

    public function ViewWorkplan(Request $request)
    {
        $project = Projects::with(['teamMembers', 'teamLeader'])->findOrFail($request->id);

        $task_plan = TaskPlans::with('weight_task_plans', 'task_realizations')
            ->select('id', 'id_projects', 'type', 'task', 'description', 'year', 'tot_weight_plan')
            ->where('id_projects', $request->id) 
            ->orderBy('task', 'asc')
            ->get();

        $weight_task_plan = WeightTaskPlans::select('id', 'id_projects', 'id_task_plans', 'month_plan', 'weight_plan')
            ->where('id_projects', $request->id) 
            ->get();

        // $task_realizations = TaskRealizations::select('task_realizations.id', 'task_realizations.id_task_plans', 'task_plans.task AS task_name', 'task_realizations.name_member', 'task_realizations.type', 'task_realizations.month', 'task_realizations.weight')
        //     ->leftJoin('task_plans', 'task_plans.id', '=', 'task_realizations.id_task_plans')
        //     ->where('task_realizations.id_projects', $request->id)
        //     ->orderBy('task_name', 'asc')
        //     ->get();

        // $task = DB::table('vw_task')
        //     ->where('id_project', $request->id) 
        //     ->get();

        // $task= DB::table('vw_weight_monthly')
        //     ->where('id_projects', $request->id) 
        //     ->get();

        return Inertia::render('Workplans/ViewWorkplan', [
            'formData' => $project,
            'task_plans' => $task_plan,
            'weight_task_plans' => $weight_task_plan,
            // 'task_realizations' => $task_realizations,
            // 'task'=>$task,
            // 'weight_monthly'=>$weight_monthly
        ]);
    }

    // public function StoreTasks(Request $request)
    // {
    //     $task = $request->all();

    //     // set month
    //     $unixtime = strtotime($task["month"]);
    //     $task["month"] = date('m', $unixtime); 
    //     $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    //     $task["month"] = $months[$task["month"] - 0]; 

    //     dd($task);
    //     exit;
    //     Tasks::create($task);
    // }

    public function StoreTaskPlans(Request $request)
    {
        $task_plan = $request->all();
        TaskPlans::create($task_plan);
    }

    public function ListTaskPlans(Request $request){
        $task_plans = TaskPlans::where('id_projects', $request->id_projects) ->get();
        return response()->json(["data" => $task_plans]);
    }

    public function UpdateTaskPlan(Request $request)
    {
        $task_plan = TaskPlans::findOrFail($request->id); 
        $task_plan->update($request->all());
    }

    public function StoreWeightTaskPlans(Request $request)
    {
        $weight_task_plan = $request->all();
        WeightTaskPlans::create($weight_task_plan);
    }

    public function ListWeightTaskPlans(Request $request){
        $weight_task_plans = WeightTaskPlans::where('id_projects', $request->id_projects) ->get();
        return response()->json(["data" => $weight_task_plans]);
    }

    public function UpdateWeightTaskPlan(Request $request)
    {
        $weight_task_plan = WeightTaskPlans::findOrFail($request->id); 
        $weight_task_plan->update($request->all());
    }

    public function StoreTaskRealizations(Request $request)
    {
        $task_realization = $request->all();
        TaskRealizations::create($task_realization);
    }

    public function ListTaskRealizations(Request $request){
        $task_realizations = TaskRealizations::select('task_realizations.id', 'task_realizations.id_task_plan', 'task_plans.task AS task_name', 'task_realizations.name_member', 'task_realizations.type', 'task_realizations.month', 'task_realizations.weight')
        ->leftJoin('task_plans', 'task_plans.id', '=', 'task_realizations.id_task_plan')
        ->where('task_realizations.id_projects', $request->id_projects)
        ->get();
        return response()->json(["data" => $task_realizations]);
    }

    public function UpdateTaskRealization(Request $request)
    {
        $task_realization = TaskRealizations::findOrFail($request->id); 
        $task_realization->update($request->all());
    }

}
