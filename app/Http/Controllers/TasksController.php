<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tasks;

class TasksController extends Controller
{
    public function PageTasks(Request $request)
    {
        $title = 'Tasks';
        return Inertia::render('Tasks/PageTasks', [
            'title' => $title,
        ]);
    }
}
