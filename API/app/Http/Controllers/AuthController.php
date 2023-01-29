<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
class AuthController extends Controller
{
    //

    public function index(){
        $users=User::all();
        return response([
            'users'=>$users,
        ],200);
    }

    public function register(Request $request)
    {

        $atts=$request->validate([
            'name'=>'required',
            'email'=>'required',
            'password'=>'required'
        ]);
        // create  user
        $user=User::create([
            'name'=>$atts['name'],
            'email'=>$atts['email'],
            'password'=>bcrypt($atts['password'])
        ]);
        // return user and token
        return response([
            'user'=>$user,
            'token'=>$user->createToken("API_TOKEN")->plainTextToken,
           
        ]);
    }

    public function login(Request $request){

        $atts=$request->validate([
            'email'=>'required' ,
            'password'=>'required|min:7',

        ]);

        // attemps login
        if(!Auth::attempt($atts)){
            return response([
                'message'=>"Invalid Credentials"
            ],400);
        }
        $user=User::where('email',$request->email)->first();
        // return user and token

        return response([
            'user'=>auth()->user(),
            'token'=>$user->createToken()->plainTextToken
        ],200);
    }

    public function logout(User $user){

        $user->tokens()->delete();
        return response([
            'message'=>'Logout successly'
        ],200);
    }


    private function setUser(Request $request, User $users, $fileNameToStore)
    {
        $users->prenom = $request->input('prenom');
        $users->nom = $request->input('nom');
        $users->email = $request->input('email');
        $users->role = $request->input('role');
        if ($request->input('password') != NULL) {
            $users->password = bcrypt($request->input('password'));
        }
        if ($request->hasFile('photo')) {
            $users->photo = $fileNameToStore;
        }
        $users->save();




    }


    private function validateRequest(Request $request, $id)
    {
        $this->validate($request, [
            'prenom'   =>  'required|min:3',
            'nom'    =>  'required|min:3',
            'role'    =>  'required',
            'password'     =>  '' . ($id ? 'nullable|min:7' : 'required|min:7'),
            'email'        =>  'required|email|unique:users,email,' . ($id ?: '') . '|min:7',
            'photo'      =>  'required'
        ]);
    }



}
