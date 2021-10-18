<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    public function register(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        //check field are not empty
        if (empty($email) || empty($password) || empty($name)) {
            return response()->json(['status'=>'error', 'message'=>'You must enter email, name and password']);
        }
        //check email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['status'=>'error', 'message'=>'email must be valid']);
        }

        if (strlen($password) < 6) {
            return response()->json(['status'=>'error', 'message'=>'password must be greater than 5 characters']);
        }

        // check if user existrs
        if (User::where('email', '=', $email)->exists()) {
            return response()->json(['status'=>'error', 'message'=>'user already exists']);
        }

        try {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = app('hash')->make($password);
            if ($user->save()) {
                return $this->login($request);
                //return response()->json(['status'=>'success','message'=>'user created successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
       * Get the token array structure.
       *
       * @param  string $token
       *
       * @return \Illuminate\Http\JsonResponse
       */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
