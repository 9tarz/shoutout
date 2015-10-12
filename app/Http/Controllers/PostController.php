<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Post;
use App\Session;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // map page: write as index.blade.php (edit later)

        // $user = User::find($request->get('username'));
        // $session = Session::find($user->id);
        // if($session->is_expired == 0){
        //     return view('map');
        // }
        // else{
        //     return view('login');
        // }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('post');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $session = Session::where('token', $request->get('token'))->first(); //find user_id from token
        if($session == null) {
            $error = 1;
            $error_msg = "Token error!";
            return response()->json(['error' => $error, 'error_msg' => $error_msg]);
        } else {
            if($session->is_expired == 0){
                $error = 0;
                $error_msg = "Shout completed. :)";
                $post = new Post();
                $post->user_id = $session->user_id;
                $post->text = $request->text;
                $post->latitude = $request->latitude;
                $post->longitude = $request->longitude;
                $post->is_anonymous = $request->anonymous;
                $post->save();
                return response()->json(['error' => $error, 'error_msg' => $error_msg]);
            } else {
                $error = 1;
                $error_msg = "Your session has expired. Please Login again!";
                return redirect('/api/user/login');
            }
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($latitude=null, $longitude =null)
    {
        $posts = Post::where('latitude', $latitude)->where('longitude', $longitude)->get();
        if (count($posts) == 0) {
            $error = 1;
            $error_msg = "No posts.";
            return response()->json(['error' => $error, 'error_msg' => $error_msg]);
        } else {
            $error = 0;
            $error_msg = "";
            return response()->json(['error' => $error, 'posts' => $posts]);
        }
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
