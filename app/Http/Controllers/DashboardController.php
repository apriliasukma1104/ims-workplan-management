<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Repositories\Eloquent\SoContractRepo;
use App\Models\Projects;

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
        $search = $request->input('search');
        $title = 'Dashboard';
        $projectsQuery = Projects::with('tasks')
            ->select('id', 'name', 'start_date', 'end_date', 'status');
        
        if ($search) {
            $projectsQuery->where('name', 'like', '%' . $search . '%');
        }

        $dashboard = $projectsQuery->get();

        $formattedDashboard = [];
        $totalTaskProjects = 0;
        $totalProject = 0;

        foreach ($dashboard as $project) {
            $completedTasks = $project->tasks->where('status', 'done')->count();
            $totalTasks = $project->tasks->count();
            $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

            $totalTaskProjects += $totalTasks;
            $totalProject = $project->count();

            $start_date = new \DateTime($project->start_date);
            $end_date = new \DateTime($project->end_date);
            $work_duration = $start_date->diff($end_date)->days;

            if (now() > $project->end_date && ($completedTasks < $totalTasks || $totalTasks === 0)) {
                $status = 'over due';
            } elseif ($totalTasks === 0) {
                $status = 'pending';   
            } elseif ($completedTasks === 0) {
                $status = 'started';
            } elseif ($completedTasks < $totalTasks && $progress >= 25) {
                $status = 'on-progress';
            } else {
                $status = 'done';
            }

            $formattedDashboard[] = [
                'project_name' => $project->name,
                'progress' => $progress,
                'status' => $status,
            ];
        }

        return Inertia::render('Home/Index', [
            'title' => $title,
            'dashboard' => $formattedDashboard,
            'total_tasks' => $totalTaskProjects,
            'total_projects' => $totalProject,
        ]);
    }

}
