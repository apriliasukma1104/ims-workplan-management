<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Projects;

class ReportsController extends Controller
{
    public function PageReports(Request $request)
    {
        $projectsQuery = Projects::with('tasks')
            ->select('id', 'name', 'start_date', 'end_date', 'status');
        
        $search = $request->input('search');
        if ($search) {
            $projectsQuery->where('name', 'like', '%' . $search . '%');
        }

        $reportsQuery = $projectsQuery->paginate(3);
        $totalData = $projectsQuery->count();

        $reports = [];

        foreach ($reportsQuery as $project) {
            $completedTasks = $project->tasks->where('status', 'done')->count();
            $totalTasks = $project->tasks->count();
            $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

            $start_date = new \DateTime($project->start_date);
            $end_date = new \DateTime($project->end_date);
            $end_date->setTime(23, 59, 0);
            $work_duration = $start_date->diff($end_date)->days;

            $now = now();
            if ($now > $end_date && ($completedTasks < $totalTasks || $totalTasks === 0)) {
                $status = 'over due';
            } elseif ($totalTasks === 0) {
                $status = 'pending';
            } elseif ($completedTasks === 0) {
                $status = 'started';
            } elseif ($completedTasks < $totalTasks && $progress >= 25) {
                $status = 'on-progress';
            } elseif ($project->status === 'review') {
                $status = 'review';
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
            return response()->json(['reports' => ['data' => $reports, 'total' => $totalData]]);
        }

        return Inertia::render('Reports/PageReports', [
            'reports' => ['data' => $reports, 'total' => $totalData] 
        ]);
    }
}
