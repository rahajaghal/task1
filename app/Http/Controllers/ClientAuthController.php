<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientAuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:client', ['except' => ['login','register']]);
    }

    public function register(Request $request){
        $validator=Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }
        $user=Client::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
        ));
        return response()->json([
            'message'=>'client successfully registered',
            'admin'=>$user
        ],201);
    }

    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        if (!$token=auth()->guard('client')->attempt($validator->validated())){
            return response()->json(['error'=>'unauthorized'],401);
        }
        return $this->createNewToken($token);
    }

    public function logout()
    {
        Auth::guard('client')->logout();
        return response()->json([
            'message' => 'client Successfully logged out',
        ]);
    }
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL() *60 ,
            'admin'=>auth()->user()
        ]);
    }

}
