<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActivityLog;
use DB;
use Auth;

class ActivityLogController extends Controller
{   
    public function index()
    {
        $activity_logs = DB::table('activity_logs')
                           ->leftJoin('users', 'activity_logs.userid', '=', 'users.id')
                           ->select('activity_logs.object_id', DB::raw("DATE_FORMAT(activity_logs.created_at, '%m/%d/%Y - %H:%i')  as created_at"), 'activity_logs.table_name', 'activity_logs.description', 'activity_logs.action', 'users.username')
                           ->get();
        
        return response()->json(['activity_logs' => $activity_logs], 200);
    }
    
}
