<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use GuzzleHttp\Client;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
      $base_url = "http://local.mylumenone.com/";
      $email = $request->email;
      $password = $request->password;

      // check if fields are not empty ..
      if(empty($email) || empty($password)){
        return response()->json(['status'=>'error', 'message'=>'You must enter email and password']);
      }

      $client = new Client();
      try{
        return $client->post(config('service.passport.login_endpoint'),[
          "form_params"=>[
              "client_secret"=>config('service.passport.client_secret'),//"OaLX68fzbYyy4ySlMgaSxsuqLD6b3mnsHnTMAOA3",
              "grant_type"=>"password",
              "client_id"=>config('service.passport.client_id'),
              "username"=>$email,
              "password"=>$password
            ]
          ]
        );
      }catch(\Exception $e){
        return response()->json(['status'=>'error', 'message'=>$e->getMessage()]);
      }
    }

    public function register(Request $request)
    {
      $name = $request->name;
      $email = $request->email;
      $password = $request->password;

      //check field are not empty
      if(empty($email) || empty($password) || empty($name)){
        return response()->json(['status'=>'error', 'message'=>'You must enter email, name and password']);
      }
      //check email is valid
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return response()->json(['status'=>'error', 'message'=>'email must be valid']);
      }

      if(strlen($password) < 6){
        return response()->json(['status'=>'error', 'message'=>'password must be greater than 5 characters']);
      }

      // check if user existrs
      if(User::where('email','=',$email)->exists()){
        return response()->json(['status'=>'error', 'message'=>'user already exists']);
      }

      try{
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = app('hash')->make($password);
        if($user->save()){
          return $this->login($request);
          //return response()->json(['status'=>'success','message'=>'user created successfully']);
        }
      }catch(\Exception $e){
        return response()->json(['status'=>'error','message'=>$e->getMessage()]);
      }
    }

    public function logout(Request $request)
    {
      try{
        auth()->user()->tokens()->each(function($token){
          $token->delete();
        });
        return response()->json(['status'=>'success','message'=>'user '.auth()->user()->name.' logged out successfully']);
      }catch(\Exception $e){
        return response()->json(['status'=>'error','message'=>$e->getMessage()]);
      }
    }
}
