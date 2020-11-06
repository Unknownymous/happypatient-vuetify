<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
use Hash;

class AuthController extends Controller
{
    public function init()
    {
        $user = Auth::user();

        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        if(Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')], true))
        {
            return response()->json(Auth::user(), 200);
        }
        else
        {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        
    }

    public function register(Request $request)
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
            return response()->json($validator->errors(), 401);
        }

        $user = new User();
        $user->name = $request->get('name');
        $user->description = $request->get('description');
        $user->license = $request->get('license');
        $user->email = $request->get('email');
        $user->username = $request->get('username');
        $user->password = Hash::make($request->get('password'));
        $user->save();

        Auth::login($user);

        return response()->json(['success' => 'You are now registered'], 200);

    }

    public function logout()
    {
        Auth::logout();
    }
}
