<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Projects;
use App\Models\History;

class HistoryController extends Controller
{
    public function PageHistory(Request $request)
    {
        $user = Auth::user();

        $projectsQuery = Projects::with('teamLeader', 'teamMembers')
            ->where('status', '!=', 'Created')
            ->where('status', '!=', 'Submitted')
            ->leftJoin('history', 'projects.id', '=', 'history.id_projects')
            ->select('projects.*', 'history.validation', 'history.note', 
                    DB::raw('DATE_FORMAT(history.created_at, "%d-%m-%Y") as _created_at'), 
                    DB::raw('DATE_FORMAT(projects.date_submitted, "%d-%m-%Y") as _date_submitted'), 
                    DB::raw('CAST(budget AS DECIMAL(0)) as _budget'));        

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
        
        return Inertia::render('History/PageHistory', [
            'projects' => $projects,
            'auth' => $user 
        ]);
    }

    public function Approve(Request $request)
    {
        $projects = Projects::findOrFail($request->id); 
        $history = new History([
            'validation' => $request->validation
        ]);

        $projects->historyAsIdProjects()->save($history);

        $currentYear = date('Y');

        if ($projects->status === 'Submitted' && $projects->year < $currentYear) {
            $projects->status = 'Overdue';
            $projects->save(); 
        } else if ($projects->status === 'Submitted' && $projects->year >= $currentYear) {
            $projects->status = 'On progress';
            $projects->save(); 
        }

        return Inertia::render('History/PageHistory', [
            'projects' => $projects,
            'history' => $history 
        ]);
    }

    public function NotApprove(Request $request)
    {
        $projects = Projects::findOrFail($request->id); 
        $history = new History([
            'note' => $request->note,
            'validation' => $request->validation
        ]);

        $projects->historyAsIdProjects()->save($history);

        if ($projects->status === 'Submitted') {
            $projects->status = 'Rejected';
            $projects->save(); 
        }

        return Inertia::render('History/PageHistory', [
            'projects' => $projects,
            'history' => $history 
        ]);
    }
}
