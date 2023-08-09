<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function PageReports(Request $request)
    {
        $title = 'Reports';
        return Inertia::render('Reports/PageReports', [
            'title' => $title,
        ]);
    }
}
