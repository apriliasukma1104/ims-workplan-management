<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->leftJoin('history', 'projects.id', '=', 'history.id_projects')
            ->select('projects.*', 'history.validation', 'history.note', DB::raw('CAST(budget AS DECIMAL(0)) as _budget'));        

        if ($user->role === 'Kabag') {
            $projectsQuery->where(function ($query) use ($user) {
                $query->where('team_leader', $user->id)
                    ->orWhereHas('teamMembers', function ($query) use ($user) {
                        $query->where('id', $user->id)
                              ->orWhere('sub_department', $user->sub_department);
                    });
            });
        } else if ($user->role === 'Staf') {
            $projectsQuery->where('status', '!=', 'Created')
                          ->where('status', '!=', 'Submitted')
                          ->where(function ($query) use ($user) {
                                $query->where('team_leader', $user->id)
                                      ->orWhereHas('teamMembers', function ($query) use ($user) {
                                            $query->where('id', $user->id);
                                      });
                          });
        }     

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
        
        return Inertia::render('Workplans/ListWorkplans', [
            'projects' => $projects,
            'auth' => $user 
        ]);
    }

    public function PageAddWorkplan()
    {
        $userSubDepartment = Auth::user()->sub_department; 

        $pic = Members::whereIn('role', ['Staf', 'Kabag'])
        ->where('members_status', 1)
        ->where('sub_department', $userSubDepartment)
        ->get();

        $members = Members::whereIn('role', ['Staf', 'Kabag'])
        ->where('members_status', 1)
        ->get();

        return Inertia::render('Workplans/AddWorkplan', [
            'pic' => $pic,
            'members' => $members 
        ]);
    }

    public function StoreWorkplans(Request $request)
    {
        $projectsQuery = $request->all();

        $unit = [
            'Infrastructure & Support' => "INS",
            'ERP & EIM' => "ERP",
            'Apps Development & Governance' => "ADG"
        ];

        $userSubmit = Auth::user();
        $kodeUnit = $unit[$userSubmit->sub_department];
        $year = $request->year;
        $cekUrutan = Projects::selectRaw("IF(YEAR(created_at) = $year, COALESCE(MAX(sequence), 0) + 1, 1) AS urut")
            ->where('code_unit', $kodeUnit)
            ->whereYear('created_at', $year)
            ->first();

        $code_workplans = $kodeUnit . '' . sprintf('%03d', $cekUrutan->urut) . '' . $year;

        $projectsQuery['code_unit'] = $kodeUnit;
        $projectsQuery['sequence'] = $cekUrutan->urut;
        $projectsQuery['code_workplans'] = $code_workplans;

        // Setting team_members (id)
        $teamMembersId = [];
        foreach ($projectsQuery['team_members'] as $member) {
            $teamMembersId[] = $member['id'];
        }
        $projectsQuery['team_members'] = json_encode($teamMembersId);
        $project = Projects::create($projectsQuery);
        foreach ($teamMembersId as $memberId) {
            $project->teamMembers()->attach($memberId);
        }

        return redirect()->route('workplans.list_workplans')->with('message', 'Data Created Successfully!');
    }

    public function EditWorkplan(Request $request)
    {
        $project = Projects::findOrFail($request->id);
        $userSubDepartment = Auth::user()->sub_department; 

        $pic = Members::whereIn('role', ['Staf', 'Kabag'])
        ->where('members_status', 1)
        ->where('sub_department', $userSubDepartment)
        ->get();
        
        $members = Members::whereIn('role', ['Staf', 'Kabag'])
        ->where('members_status', 1)
        ->get();

        $id_team_members = json_decode($project->team_members);
        $team_members = Members::whereIn('id', $id_team_members)->get();
      
        return Inertia::render('Workplans/EditWorkplan', [
            'formData' => $project,
            'members' => $members,
            'pic' => $pic,
            'team_members'=>$team_members 
        ]);
    }

    public function UpdateWorkplan(Request $request)
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

        return redirect()->route('workplans.list_workplans')
            ->with('project', $project)
            ->with('members', $members);
    }

    public function DeleteWorkplan(Request $request)
    {
        $project = Projects::findOrFail($request->id);
        $project->delete();
    }

    public function UpdateStatusSubmitted(Request $request) 
    {
        $project = Projects::find($request->input('id'));
        if ($project->status === 'Created') {
            $project->status = 'Submitted'; 
            $project->date_submitted = now();
            $project->save(); 
        }
    }

    public function ViewWorkplan(Request $request)
    {
        // Tampilan Work Plan
        $project = Projects::with(['teamMembers', 'teamLeader'])
            ->select('projects.*', DB::raw('CAST(budget AS DECIMAL(0)) as _budget'))
            ->findOrFail($request->id);

        // Setting Bulan untuk Task Plans & Task Realizations
        $months = [
            1 => "January",
            2 => "February",
            3 => "March",
            4 => "April",
            5 => "May",
            6 => "June",
            7 => "July",
            8 => "August",
            9 => "September",
            10 => "October",
            11 => "November",
            12 => "December"
        ];

        // Tampilan Task Plans
        $task_plan = TaskPlans::with('weight_task_plans')
            ->select('task_plans.*')
            ->where('id_projects', $request->id) 
            ->orderBy('start_month', 'asc')
            ->get();
        foreach ($task_plan as $plan) {
            $plan->start_month = $months[$plan->start_month];
            $plan->end_month = $months[$plan->end_month];
        }
        $totWeightTP = TaskPlans::where('id_projects', $request->id)->sum('tot_weight_plan');
        $totalWeightTaskPlan = $totWeightTP;

        // Tampilan Task Realizations
        $task_realization = TaskRealizations::select('task_realizations.*', 'task_plans.task AS task_name')
            ->leftJoin('task_plans', 'task_plans.id', '=', 'task_realizations.id_task_plans')
            ->where('task_realizations.id_projects', $request->id)
            ->get();    
        foreach ($task_realization as $realization) {
            $realization->month = $months[$realization->month];
        }

        $filter_tp = DB::table('vw_filter_tp')
            ->where('id_projects', $request->id) 
            ->get();

        $task = DB::table('vw_task')
            ->where('id_projects', $request->id) 
            ->get();

        $vw_weight_monthly = DB::table('vw_weight_monthly')
            ->where('id_projects', $request->id) 
            ->get();

        $vw_weight_cumulative = DB::table('vw_weight_cumulative')
            ->where('id_projects', $request->id) 
            ->get();

        $vw_weight_gap = DB::table('vw_weight_gap')
            ->where('id_projects', $request->id) 
            ->get();

        return Inertia::render('Workplans/ViewWorkplan', [
            'formData' => $project,
            'task_plans' => $task_plan,
            'totalWeightTP' =>  $totWeightTP,
            'calculateWeight' => $totalWeightTaskPlan,
            'task_realizations' => $task_realization,
            'filter_tp'=> $filter_tp,
            'task'=>$task,
            'vw_weight_monthly' => $vw_weight_monthly,
            'vw_weight_cumulative' => $vw_weight_cumulative,
            'vw_weight_gap' => $vw_weight_gap
        ]);
    }

    public function StoreTaskPlans(Request $request)
    {
        $task_plan = $request->all();

        // Ambil nilai end_month dan start_month dari $task_plan
        $end_month = $task_plan['end_month'];
        $start_month = $task_plan['start_month'];
        // Setting duration
        $duration = $end_month - $start_month + 1;
        $task_plan['duration'] = $duration;

        $cek = TaskPlans::where('id_projects', $request->id_projects)->sum('tot_weight_plan');
        
        if ($cek + $request->tot_weight_plan > 100) {
            return response()->json(['error' => "Maximum 100."], 422);
        }

        $savedTaskPlan = TaskPlans::create($task_plan);        
        return response()->json($savedTaskPlan, 201);
    }

    public function ListTaskPlans(Request $request){
        $task_plans = TaskPlans::with('weight_task_plans')
            ->select('task_plans.*')
            ->where('id_projects', $request->id_projects) 
            ->orderBy('start_month', 'asc')
            ->get();

        $months = [
            1 => "January",
            2 => "February",
            3 => "March",
            4 => "April",
            5 => "May",
            6 => "June",
            7 => "July",
            8 => "August",
            9 => "September",
            10 => "October",
            11 => "November",
            12 => "December"
        ];
    
        // Setting start_month dan end_month dengan nama bulan
        foreach ($task_plans as $task_plan) {
            $task_plan->start_month = $months[$task_plan->start_month];
            $task_plan->end_month = $months[$task_plan->end_month];
        }

        return response()->json(["data" => $task_plans]);
    }

    public function UpdateTaskPlan(Request $request)
    {
        $task_plan = TaskPlans::findOrFail($request->id); 
        $params = $request->all();
        unset($params['weight_task_plans']);

        // Ambil nilai end_month dan start_month dari $task_plan
        $end_month = $task_plan['end_month'];
        $start_month = $task_plan['start_month'];

        // Setting duration
        $duration = $end_month - $start_month + 1;
        $params['duration'] = $duration;

        // Lakukan pengecekan total weight
        $cek = TaskPlans::where('id_projects', $task_plan->id_projects)->where('id', '!=', $task_plan->id)->sum('tot_weight_plan');

        if ($cek + $request->tot_weight_plan > 100) {
            return response()->json(['error' => "Maximum 100."], 422);
        }

        $task_plan->update($params);
        return response()->json($task_plan, 200);
    }

    public function StoreWeightTaskPlans(Request $request)
    {   
        $weight_task_plan = $request->all();
        $cek = WeightTaskPlans::where('id_task_plans', $request->id_task_plans)->count(); 
        if($cek > 0){
           WeightTaskPlans::where('id_task_plans', $request->id_task_plans)->delete();  
        }

        foreach($weight_task_plan['data'] as $key => $value){
            $savedWeightTaskPlan = WeightTaskPlans::create($value);
        }

        return response()->json($savedWeightTaskPlan, 201);
    }

    public function ListWeightTaskPlans(Request $request){
        $weight_task_plans = WeightTaskPlans::where('id_projects', $request->id_projects) ->get()->toArray();
        return response()->json(["data" => $weight_task_plans]);
    } 

    public function LaunchTask(Request $request){
        Projects::where('id',$request->id)->update(['launched'=>1]);
    }
    
    public function StoreTaskRealizations(Request $request)
    {
        $task_realization = $request->all();
        $task_realization = $request->except('task_name');

        $cek = TaskRealizations::where('id_task_plans', $request->id_task_plans)->count();
        if($cek > 0){
            $tot_weight_plan = TaskPlans::where('id', $request->id_task_plans)->value('tot_weight_plan');
            $tot_weight_realization = TaskRealizations::where('id_task_plans', $request->id_task_plans)->sum('weight');
            $available_task =  $tot_weight_plan - $tot_weight_realization;
            if ($request->weight > $available_task) {
                return response()->json(['error' => "Current weight:  $tot_weight_realization/$tot_weight_plan"], 422);
            }
        } else {
            $tot_weight_plan = TaskPlans::where('id', $request->id_task_plans)->value('tot_weight_plan');
            if ($request->weight > $tot_weight_plan) {
                return response()->json(['error' => "Maximum $tot_weight_plan"], 422);
            }
        }

        $savedTaskRealization = TaskRealizations::create($task_realization);        
        return response()->json($savedTaskRealization, 201);
    }

    public function  ListTaskRealizations(Request $request){
        $task_realizations = TaskRealizations::select('task_realizations.*', 'task_plans.task AS task_name')
            ->leftJoin('task_plans', 'task_plans.id', '=', 'task_realizations.id_task_plans')
            ->where('task_realizations.id_projects', $request->id_projects)
            ->get();

        $months = [
            1 => "January",
            2 => "February",
            3 => "March",
            4 => "April",
            5 => "May",
            6 => "June",
            7 => "July",
            8 => "August",
            9 => "September",
            10 => "October",
            11 => "November",
            12 => "December"
        ];

        foreach ($task_realizations as $task_realization) {
            $task_realization->month = $months[$task_realization->month];
        }
    
        return response()->json(["data" => $task_realizations]);
    }

    public function UpdateTaskRealization(Request $request)
    {
        $task_realization = TaskRealizations::findOrFail($request->id);
        $data = $request->all();
        unset($data['task_name']);

        $tot_weight_plan = TaskPlans::where('id', $request->id_task_plans)->value('tot_weight_plan');
        $realization = TaskRealizations::where('id_task_plans', $request->id_task_plans)->count();
        if($realization > 1)
        {
            $tot_weight_realization =  TaskRealizations::where('id_task_plans', $request->id_task_plans)->sum('weight'); 
            $realized_task = TaskRealizations::where('id_task_plans', $request->id_task_plans)->where('id', '!=', $request->id)->sum('weight');
            $available_task = $tot_weight_plan - $realized_task;
            if ($request->weight > $available_task ) {
                return response()->json(['error' => "Current weight: $tot_weight_realization/$tot_weight_plan"], 422);
            }
            $task_realization->update($data);   
        } else {
            if ($request->weight > $tot_weight_plan) {
                return response()->json(['error' => "Maximum $tot_weight_plan"], 422);
            }
            $task_realization->update($data);
        }
    }

    public function FilterTP(Request $request)
    {
        $filter_tp = DB::table('vw_filter_tp')
            ->where('id_projects', $request->id_project) 
            ->get();

        return response()->json(['data'=>$filter_tp]);
    }

    public function UpdateStatusCompleted(Request $request) 
    {
        $project = Projects::find($request->input('id'));

        $currentYear = date('Y');

        if ($project->status === 'On progress') {
            if ($project->year < $currentYear) {
                $project->status = 'Overdue';
                $project->date_overdue = now();
                $project->save(); 
            } else if ($project->year >= $currentYear) {
                $tasks = DB::table('vw_task')
                            ->where('id_projects', $project->id)
                            ->where('type', 'Realization')
                            ->sum('tot_weight_realization');

                $allTasksCompleted = $tasks == 100 ?? true;

                if ($allTasksCompleted) {
                    $project->status = 'Completed'; 
                    $project->date_completed = now();
                    $project->save(); 
                }
            }
        }
    }
}