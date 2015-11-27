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
                $post->text = $request->get('text');
                $post->latitude = $request->get('latitude');
                $post->longitude = $request->get('longitude');
                //$post->is_anonymous = $request->get('anonymous');
                $post->save();
                return response()->json(['error' => $error, 'error_msg' => $error_msg]);
            } else {
                $error = 1;
                $error_msg = "Your session has expired. Please Login again!";
                return response()->json(['error' => $error, 'error_msg' => $error_msg]);
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
        $arr_posts = array();
        $posts = Post::where('latitude', $latitude)->where('longitude', $longitude)->get();
        foreach ($posts as $post) {
            $user_id = $post->user_id;
            $user = User::where('id', $user_id)->first();
            $post->username = $user->username;
            array_push($arr_posts, $post);
        }
        if (count($arr_posts) == 0) {
            $error = 1;
            $error_msg = "No posts.";
            return response()->json(['error' => $error, 'error_msg' => $error_msg]);
        } else {
            $error = 0;
            $error_msg = "";
            return response()->json(['error' => $error, 'posts' => $arr_posts]);
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


    /**
     * Show upload form for test 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show_upload(Request $request)
    {
        return view('upload_image');
    }


    public function upload_image(Request $request)
    {
        /*if (empty($request->get('clientid')) || $request->file('upload');$_FILES['upload']['error'] !== 0 || $_FILES['upload']['size'] > 5000000000) {
            exit;
        }*/
        $error_msg = "";
        $client_id = "68a375e185f3ffb";

        if ($request->file('image') == null)
            $error_msg = "null"; 

        /*if ($input_image->getMimeType() != 'image') {
            die('Invalid image type');
        }*/

        $image = file_get_contents($request->file('image')->getRealPath());

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Authorization: Client-ID $client_id" ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'image' => base64_encode($image) ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $reply = curl_exec($ch);

        curl_close($ch);

        $reply = json_decode($reply);

        return response()->json(['reply' => $reply, 'error_msg' => $error_msg]);

    }

    public function show_upload_2(Request $request)
    {

        $client_id = "68a375e185f3ffb";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image/hfrUrec');
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Authorization: Client-ID $client_id" ));
        //curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'image' => base64_encode($image) ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $reply = curl_exec($ch);

        curl_close($ch);

        $reply = json_decode($reply);
        

        return response()->json(['reply' => stripslashes($reply->data->link)]);
    }

}
