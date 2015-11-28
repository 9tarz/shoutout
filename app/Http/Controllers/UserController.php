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
     * Login function
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // find user object by username.
        $user = User::find($request->get('username'));

        // Is user exist in storage.
        if($user == null) {
            $error = 1;
            $error_msg = "Login are incorrect. Please try again!";
            return response()->json(['error' => $error, 'error_msg' => $error_msg]);
        }
        else {
            // Check password 
            if ( $user->password == sha1($request->get('password'))) {
                $error = 0;
                // generate session token 
                $session = new Session();
                $session->token = md5(microtime().$_SERVER['REMOTE_ADDR']);
                $session->user_id = $user->id;
                $session->is_expired = 0;
                $session->save(); 
                //return view('session')->with($session->toArray());
                return response()->json(['error' => $error, 'token' => $session->token]);
            } else {
                $error = 1;
                $error_msg = "Login are incorrect. Please try again!";
                return response()->json(['error' => $error, 'error_msg' => $error_msg]);
            }
        }

        //return view('index')->with($user->toArray());
    }

    /**
     * Logout function
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $error = 0;
        $error_msg = "Logout completed";
        $session = Session::where('token', $request->get('token'))->first();
        $session->is_expired = 1;
        $session->save();
        return response()->json(['error' => $error, 'error_msg' => $error_msg]);
    }

    /**
     * Test logout form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory
     */
    public function getLogout(Request $request)
    {
        return view('logout');
    }

    /**
     * Test login form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory
     */
    public function getLogin(Request $request)
    {
        return view('login');
    }

    /**
     * Test register form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory
     */
    public function create(Request $request)
    {
        return view('register');
    }

    /**
     * Store a newly created user in storage.
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
        }
    }

    /**
     * Show locations around specific location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function pullLocation($latitude = null, $longitude =null ){
        $arr_posts = array();
        $today = date('y-m-d');
        //$today_posts = Post::SELECT('latitude','longitude')->whereRaw('date(created_at) = :today' , ['today' => $today] )->distinct()->get();
        $today_posts = Post::SELECT('latitude','longitude')->get();
        foreach ($today_posts as $post) {
            $lat = $post->latitude ;
            $long = $post->longitude ;
            $x = pow(abs($latitude-$lat),2) ;
            $y = pow(abs($longitude-$long),2) ;
            if( abs(floatval($x) + floatval($y)) <= 0.000088076929 ) {
                array_push($arr_posts, $post);
            }
        }
        if (count($arr_posts) == 0) {
            $error = 1;
            $error_msg ="Location Not Found.";
            return response()->json(['error_msg' => $error_msg,'error' => $error , 'posts' => $arr_posts]);
        } else {
            $error = 0;
            return response()->json(['error' => $error ,'posts' => $arr_posts]);
        }
    }
}
