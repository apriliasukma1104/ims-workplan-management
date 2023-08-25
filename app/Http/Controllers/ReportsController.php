<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Projects;

class ReportsController extends Controller
{
    // public function PageReports(Request $request)
    // {
    //     $search = $request->input('search');
    //     $title = 'Reports';
    //     $projectsQuery = Projects::with('tasks')
    //         ->select('id', 'name', 'start_date', 'end_date', 'status');
        
    //     if ($search) {
    //         $projectsQuery->where('name', 'like', '%' . $search . '%');
    //     }

    //     $reports = $projectsQuery->paginate(2);

    //     $formattedReports = [];

    //     foreach ($reports as $project) {
    //         $completedTasks = $project->tasks->where('status', 'done')->count();
    //         $totalTasks = $project->tasks->count();
    //         $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

    //         $start_date = new \DateTime($project->start_date);
    //         $end_date = new \DateTime($project->end_date);
    //         $work_duration = $start_date->diff($end_date)->days;

    //         if (now() > $project->end_date && ($completedTasks < $totalTasks || $totalTasks === 0)) {
    //             $status = 'over due';
    //         } elseif ($totalTasks === 0) {
    //             $status = 'pending';
    //         } elseif ($completedTasks === 0) {
    //             $status = 'started';
    //         } elseif ($completedTasks < $totalTasks && $progress >= 25) {
    //             $status = 'on-progress';
    //         } else {
    //             $status = 'done';
    //         }

    //         $formattedReports[] = [
    //             'project_name' => $project->name,
    //             'due' => $project->end_date,
    //             'total_tasks' => $totalTasks,
    //             'completed_task' => $completedTasks,
    //             'work_duration' => $work_duration,
    //             'progress' => $progress,
    //             'status' => $status,
    //         ];
    //     }

    //     if ($request->ajax()){
    //         return response()->json(['data'=> $reports]);
    //     }

    //     return Inertia::render('Reports/PageReports', [
    //         'title' => $title,
    //         'reports' => $formattedReports,
    //     ]);
    // }

    public function PageReports(Request $request)
    {
        $search = $request->input('search');
        $title = 'Reports';
        $projectsQuery = Projects::with('tasks')
            ->select('id', 'name', 'start_date', 'end_date', 'status');
        
        if ($search) {
            $projectsQuery->where('name', 'like', '%' . $search . '%');
        }

        $totalData = $projectsQuery->count();

        $reportsQuery = $projectsQuery->paginate(3);

        $reports = [];

        foreach ($reportsQuery as $project) {
            $completedTasks = $project->tasks->where('status', 'done')->count();
            $totalTasks = $project->tasks->count();
            $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

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

            $reports[] = [
                'project_name' => $project->name,
                'due' => $project->end_date,
                'total_tasks' => $totalTasks,
                'completed_task' => $completedTasks,
                'work_duration' => $work_duration,
                'progress' => $progress,
                'status' => $status,
            ];
        }

        if ($request->ajax()) {
            return response()->json(['data' => ['data' => $reports, 'total' => $totalData]]);
        }

        return Inertia::render('Reports/PageReports', [
            'title' => $title,
            // 'reports' => ['data' => $reports],
            'reports' => ['data' => $reports, 'total' => $totalData] 
        ]);
    }

}
