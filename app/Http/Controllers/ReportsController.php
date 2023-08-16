<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Projects;

class ReportsController extends Controller
{
    // public function PageReports(Request $request)
    // {
    //     $title = 'Reports';
    //     return Inertia::render('Reports/PageReports', [
    //         'title' => $title,
    //     ]);
    // }

    // public function PageReports(Request $request)
    // {
    //     $title = 'Tasks';
    //     $reports = Tasks::with('project') 
    //         ->leftJoin('projects', 'tasks.id_project', '=', 'projects.id')
    //         ->select(
    //             'tasks.id',
    //             'tasks.task',
    //             'tasks.description',
    //             'tasks.status',
    //             'projects.name',
    //             'projects.start_date',
    //             'projects.end_date',
    //             'projects.status as project_status'
    //         )
    //         ->paginate(10);
    //     return Inertia::render('Reports/PageReports', [
    //         'title' => $title,
    //         'reports' => $reports,
    //     ]);
    // }

    // public function PageReports(Request $request)
    // {
    //     $search = $request->input('search');
    //     $title = 'Tasks';
    //     $projectsQuery = Projects::with('tasks')
    //         ->select('id', 'name', 'start_date', 'end_date', 'status');
    //     if ($search) {
    //         $projectsQuery->where('name', 'like', '%' . $search . '%');
    //     }
    //     $reports = $projectsQuery->paginate(10);
    //     return Inertia::render('Reports/PageReports', [
    //         'title' => $title,
    //         'reports' => $reports, 
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

        $reports = $projectsQuery->paginate(10);

        $formattedReports = [];

        foreach ($reports as $project) {
            $completedTasks = $project->tasks->where('status', 'done')->count();
            $totalTasks = $project->tasks->count();
            $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

            $start_date = new \DateTime($project->start_date);
            $end_date = new \DateTime($project->end_date);
            $work_duration = $start_date->diff($end_date)->days;

            if ($totalTasks === 0) {
                $status = 'pending';
            } elseif ($completedTasks === 0) {
                $status = 'started';
            } elseif (now() > $project->end_date && $completedTasks < $totalTasks) {
                $status = 'over due';
            } elseif ($completedTasks < $totalTasks && $progress >= 25) {
                $status = 'on-progress';
            } else {
                $status = 'done';
            }

            $formattedReports[] = [
                'project_name' => $project->name,
                'due' => $project->end_date,
                'total_tasks' => $totalTasks,
                'completed_task' => $completedTasks,
                'work_duration' => $work_duration,
                'progress' => $progress,
                'status' => $status,
            ];
        }

        return Inertia::render('Reports/PageReports', [
            'title' => $title,
            'reports' => $formattedReports,
        ]);
    }

}
