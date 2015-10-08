<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use App\User;
use App\Session;
use App\Post;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = User::find($request->get('username'));
        if($user == null) {
            $error = 1;
            $error_msg = "Login are incorrect. Please try again!";
            return response()->json(['error' => $error, 'error_msg' => $error_msg]);
        }
        else {
            if ( $user->password == sha1($request->get('password'))) {
                $error = 0;
                $session = new Session();
                $session->token = md5(microtime().$_SERVER['REMOTE_ADDR']);
                $session->user_id = $user->id;
                $session->is_expired = 0;
                $session->save(); 
                //return view('session')->with($session->toArray());
                return response()->json(['error' => $error, 'token' => $session->token]);
            } else {
                //return redirect('/api/user/login');
                $error = 1;
                $error_msg = "Login are incorrect. Please try again!";
                return response()->json(['error' => $error, 'error_msg' => $error_msg]);
            }
        }

        //return view('index')->with($user->toArray());
    }

    public function logout(Request $request)
    {
        $error = 0;
        $error_msg = "Logout completed";
        $session = Session::where('token', $request->get('token'))->first();
        $session->is_expired = 1;
        $session->save();
        return response()->json(['error' => $error, 'error_msg' => $error_msg]);
    }

    public function getLogout(Request $request)
    {
        return view('logout');
    }


    public function getLogin(Request $request)
    {
        return view('login');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(RegisterFormRequest $request)
    {
        $user_chk = User::find($request->get('username'));
        if($user_chk != null) {
            $error = 1;
            $error_msg = "User already existed.";
            return response()->json(['error' => $error, 'error_msg' => $error_msg]);
        } else {
            $error = 0;
            $error_msg = "Registration completed.";
            $user = new User();
            $user->username = $request->get('username');
            $user->password = sha1($request->get('password'));
            $user->first_name = $request->get('first_name');
            $user->last_name = $request->get('last_name');
            $user->email = $request->get('email');
            $user->save();
            return response()->json(['error' => $error, 'error_msg' => $error_msg]);
            //return redirect('/api/user/register');
        }
    }

    public function pullLocation($latitude = null, $longitude =null ){
        $arr_posts = array();
        $curr = pow(floatval($latitude),2) + pow(floatval($longitude),2) ;
        $today = date('y-m-d');
        $today_posts = Post::SELECT('latitude','longitude')->whereRaw('date(created_at) = :today' , ['today' => $today] )->distinct()->get();
        foreach ($today_posts as $post) {
            $lat = $post->latitude ;
            $long = $post->longitude ;
            $position = pow(floatval($lat),2) + pow(floatval($long),2);
            if( abs(floatval($curr) -floatval($position)) <= 1.283671  ){
                array_push($arr_posts, $post);
                //echo json_encode(['latitude'=>$lat , 'longitude'=> $long]);
            }
        }
        return response()->json(['posts' => $arr_posts]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
