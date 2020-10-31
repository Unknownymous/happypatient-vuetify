<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use DB;
use Hash;
use Auth;
use App\ActivityLog;

class UserController extends Controller
{
    
    public function index()
    {
        $users = User::all();

        return response()->json(['users' => $users], 200);
    }

  
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {   
        $rules = [
            'name.required' => 'Please enter name',
            'email.email' => 'Please enter a valid email',
            'username.required' => 'Please enter username',
            'username.unique' => 'Username already exists',
            'password.min' => 'Password must be atleast 8 characters',
            'password.same' => 'Password and Confirm Password did not match'
        ];

        $valid_fields = [
            'name' => 'required|string|max:255',
            // 'email' => 'string|email|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|same:confirm_password',
        ];

        if($request->get('email'))
        {
            $valid_fields['email'] = 'email';
        }

        $validator = Validator::make($request->all(), $valid_fields, $rules);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        $user = new User();
        $user->name = $request->get('name');
        $user->description = $request->get('description');
        $user->license = $request->get('license');
        $user->email = $request->get('email');
        $user->username = $request->get('username');
        $user->password = Hash::make($request->get('password'));
        $user->save();

        // $user->assignRole($request->get('roles'));

        //PUSHER - send data/message if user is created
        // event(new EventNotification('create-user', 'users'));


        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $user->id;
        $activity_log->table_name = 'users';
        $activity_log->description = 'Create User';
        $activity_log->action = 'create';
        // $activity_log->userid = auth()->user()->id;
        $activity_log->save();

        return response()->json(['success' => 'Record has successfully added'], 200);
    }

   
    public function show($id)
    {
        //
    }

    
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
