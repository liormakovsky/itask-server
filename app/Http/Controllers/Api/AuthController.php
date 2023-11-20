<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;

class AuthController extends BaseController
{
    public function signin(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $request->session()->regenerate();
            $authUser = Auth::user(); 
            $success['id'] = $authUser->id;
            $success['token'] =  $authUser->createToken($authUser->email.'_Token')->plainTextToken; 
            $success['name'] = $authUser->name;
            $success['email'] = $authUser->email;
            $success['role'] = $authUser->role;
  
            return $this->sendResponse($success, 'User signed in');
        } 
        else{ 
            return $this->sendError('Wrong name or password', ['error'=>'Unauthorized'],403);
        } 
    }

    public function signup(Request $request)
    {
   
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'role' => 'required',
            'password' => 'required',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors(),403);       
        }

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        if(!$user){
            return $this->sendError('Failed to Create User', [],500);    
        }

        $user->createToken($user->email.'_Token');

        if(Auth::attempt(['email' => $user->email, 'password' => $request->password])){ 
        $request->session()->regenerate();
        
        $success['id']= $user->id;
        $success['name']= $user->name;
        $success['email'] = $user->email;
        $success['role'] = $user->role;
        $success['token'] =  $user->createToken($user->email.'_Token')->plainTextToken;
 

        return $this->sendResponse($success, 'User created successfully.');
        }else{
            return $this->sendError('Failed to Create User', [],500); 
        }
    }
    
    public function updateUser(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError("Error", $validator->errors(),403);       
        }

        $input = $request->all();
        $updatedUser = [
           'name'=>$input['name'],
           'role'=>$input['role'],
        ];

        $user = User::where('email', $input['email'])->first();

        if ($user) {
            // Assuming $updateUser is an array containing fields to update
            $user->update($updatedUser);

            // Return the updated user or any other response
            return response()->json(['user' => $user, 'message' => 'User updated successfully']);
        } else {
            // Handle the case where the user with the given ID is not found
            return response()->json(['message' => 'User not found'], 404);
        }

        return $this->sendResponse($user, 'User updated');

    }

    // public function logout(Request $request)
    // {
    //     auth()->user()->tokens()->delete();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return $this->sendResponse('success', 'User logout successfully.');
    // }


}