<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function PageTasks()
    {
        $title = 'Members';
        return Inertia::render('Home/Tasks', [
            'title' =>  $title
        ]);
    }
}
