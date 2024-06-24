<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Eloquent\SoContractRepo;
use Illuminate\Support\Facades\DB;
use App\Models\Projects;
use App\Models\Members;

class DashboardController extends Controller
{
    private $SoContractRepo;
    
    public function __construct(SoContractRepo $SoContractRepo)
    {
        $this->middleware("auth");
        $this->SoContractRepo = $SoContractRepo;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $projectsQuery = Projects::with('teamLeader', 'teamMembers')
            ->select('projects.*'); 

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

        $projects = $projectsQuery->get();

        $members = Members::all();
        $totalMembers = $members->count();

        $progress = DB::table('vw_progress_workplans')->get();

        if ($request->ajax()) {
            return response()->json(['projects' => $projects, 'members' => $members, 'progress_projects' =>  $progress]);
        }   

        return Inertia::render('Home/Index', [
            'auth' => $user,
            'projects' => $projects,
            'total_members' => $totalMembers,
            'progress_projects' => $progress
        ]);
    }

    public function progressProjects(Request $request)
    {
        $selectedName = $request->input('code_workplans');
        
        $progress = DB::table('vw_progress_workplans')
            ->leftJoin('projects', 'vw_progress_workplans.id', '=', 'projects.id');
        
        if ($selectedName) {
            $progress->where('vw_progress_workplans.id', $selectedName);
        }

        $data = $progress->first();

        $progressProject = [
            'progress' => $data ? $data->progress : 0,
        ];

        return response()->json($progressProject);
    }
}