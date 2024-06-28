<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    /**
     * @OA\Post(
     *    path="/api/login",
     *    summary="User Login",
     *    description="Login User",
     *    tags={"User"},
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *          required={"username", "password"},
     *          @OA\Property(property="username", type="string"),
     *          @OA\Property(property="password", type="string", format="password" ,example="*****")
     *       )
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="Successful Login",
     *       @OA\JsonContent(
     *          @OA\Property(property="token", type="string")
     *       )
     *    ),
     *    @OA\Response(
     *       response=400,
     *       description="Bad request"
     *    )
     * )
     */

    public function login(Request $request) {

        $username  = $request->username;
        $pass = $request->password;
        $check  = Auth::attempt([
            'email' => $username,
            'password' => $pass
        ]);
        if(!$check) {
            $check = Auth::attempt([
                'name' => $username,
                'password' => $pass
            ]);
        }
        if($check) {
            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'token' => 'Bearer ' .$token
            ],200);
        }
        return response()->json([
            'error' => 'Unauthorized'
        ] , 400);
    }
        /**
     * @OA\Post(
     *    path="/api/register",
     *    summary="User Register",
     *    description="Register a new user",
     *    tags={"User"},
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *          required={"name" ,"email", "password"},
     *          @OA\Property(property="name", type="string"),
     *          @OA\Property(property="email", type="string"  ,format="email"),
     *          @OA\Property(property="password", type="string", format="password")
     *       )
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="Successful registration",
     *       @OA\JsonContent(
     *          @OA\Property(property="user", type="object")
     *       )
     *    ),
     *    @OA\Response(
     *       response=400,
     *       description="Bad request",
     *    @OA\JsonContent(
     *          @OA\Property(property="err", type="string")
     *       )
     *    )
     * )
     */
    public function register(Request $request)
    {
       $validator = Validator::make($request->all() ,[
          'name' =>['required','string' ,'max:225','regex:/^[a-zA-Z]+$/' ,'unique:users'],
          'email' => ['required' ,'string' ,'email' ,'max:225' , 'unique:users'],
          'password' => ['required' ,'string' , 'min:8'],
       ]);
       if($validator->fails()) {
        return response()->json([
            'err' => $validator->errors(),
        ],400);
       }
       $user = User::create([
          'name' =>  $request->input('name'),
          'email' =>  $request->input('email'),
          'password' => Hash::make($request->input('password'))
       ]);
       $user->tokens()->delete();
       return response()->json([
            'user' => $user
       ] ,200);
    }

}
