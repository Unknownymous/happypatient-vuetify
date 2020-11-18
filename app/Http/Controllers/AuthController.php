<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
use Hash;
use Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function init()
    {   
        $user = Auth::user();
        return response()->json(['user' => $user], 200);   
    }

    public function login(Request $request)
    {   
        $rules = [
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',
        ];

        $valid_fields = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $valid_fields, $rules);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        if(!Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')], true))
        {   
            return response()->json(['error' => 'Invalid credentials'], 200);
        }
        
        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response()->json([
            'user' => Auth::user(), 
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
        ], 200);

        // $accessToken->expires_at = Carbon::now()->addWeeks(1);
        // $accessToken->save();
        
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

        $accessToken = $user->createToken('authToken')->accessToken;
        // Auth::login($user);

        return response()->json(['success' => 'You are now registered', 'user' => $user, 'access_token' => $accessToken], 200);

    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        
        return response()->json(['success' => 'You have been successfully logged out'], 200);
    }
}
