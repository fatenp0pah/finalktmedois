<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class OfficerController extends Controller
{
    public function auditLog(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderBy('Timestamp', 'desc');

        // Filter by username/email
        if ($request->filled('username')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('UserEmail', 'like', '%' . $request->username . '%')
                    ->orWhere('Username', 'like', '%' . $request->username . '%');
            });
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('Action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('Timestamp', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('Timestamp', '<=', $request->date_to);
        }

        $logs = $query->get();

        return view('officer.audit-log', compact('logs'));
    }
}
