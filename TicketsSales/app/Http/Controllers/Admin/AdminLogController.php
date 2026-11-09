<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;

class AdminLogController extends Controller
{
    public function index()
    {
        $logs = AdminLog::with('admin')->latest()->paginate(20);
        return view('admin.logs.index', compact('logs'));
    }
}