<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Post;
use App\Session;
use App\Image;

class PostController extends Controller
{

    /**
     * Show the create new post form for test
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('post');
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // check who is token owner.
        $session = Session::where('token', $request->get('token'))->first(); //find user_id from token
        if($session == null) {
            $error = 1;
            $error_msg = "Token error!";
            return response()->json(['error' => $error, 'error_msg' => $error_msg]);
        } else {
            // check user token isn't expired.
            if($session->is_expired == 0){
                $error = 0;
                if ($request->file('image') == null || $request->file('image')->getClientSize() <= 0) {
                    $error = 1;
                    $error_msg = "Image not found! ";
                }
                else {
                    // upload image to imgur.com
                    $result = $this->upload_image($request->file('image'));
                    //echo $result;
                    $result = json_decode($result);

                    // check upload image to imgur.com isn't failed.
                    if ($result->success != false) {

                        // store image information.
                        $image = new Image();
                        $image->imgur_url = $result->data->link;
                        $image->post_id = null;
                        $error_msg = "Upload completed";
                        $image->save();

                        // store the text post.
                        $post = new Post();
                        $post->user_id = $session->user_id;
                        $post->text = $request->get('text');
                        $post->latitude = $request->get('latitude');
                        $post->longitude = $request->get('longitude');
                        $post->is_anonymous = $request->get('anonymous');
                        $post->save();

                        // update post id of image.
                        $image->post_id = $post->id;
                        $image->save();

                        $error_msg = "Shout completed. :)";
                    } else {
                        $error = 1;
                        // show error msg from imgur.com API
                        $error_msg = $result->data->error; 
                    }
                }
                return response()->json(['error' => $error, 'error_msg' => $error_msg, 'is_anonymous' => $post->is_anonymous]);
            } else {
                $error = 1;
                $error_msg = "Your session has expired. Please Login again!";
                return response()->json(['error' => $error, 'error_msg' => $error_msg]);
            }
        }
        
    }

    /**
     * Show posts on timeline
     *
     * @param  int  $latitude 
     * @param  int  $longitude
     * @return \Illuminate\Http\Response
     */
    public function show($latitude=null, $longitude=null)
    {
        $arr_posts = array();
        $posts = Post::where('latitude', $latitude)->where('longitude', $longitude)->get();
        foreach ($posts as $post) {
            $user_id = $post->user_id;
            $user = User::where('id', $user_id)->first();
            if($post->is_anonymous == 1){
                $post->username = "Anonymous";
            }
            else{
                $post->username = $user->username;
            }
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
     * Show the image upload form for test 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show_upload(Request $request)
    {
        return view('upload_image');
    }

    /**
     * function upload image to imgur.com
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $image_request
     * @return string $reply
     */
    private function upload_image($image_request)
    {
        $error_msg = "";
        $client_id = "68a375e185f3ffb";
        $reply = "";

        if ($image_request == null) {
            $error_msg = "null"; 
        }
        $image = file_get_contents($image_request->getRealPath());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Authorization: Client-ID $client_id" ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'image' => base64_encode($image) ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $reply = curl_exec($ch);

        curl_close($ch);

        //$reply = json_decode($reply);

        return $reply;

    }

    /**
     * upload image to imgur.com for test
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload_image_old(Request $request)
    {
        $error_msg = "";
        $client_id = "68a375e185f3ffb";
        $reply = "";

        if ($request->file('image') == null) {
            $error_msg = "null"; 
        }
        /*if ($request->file('image')->getMimeType() != 'image') {
            $error_msg = "Invalid image type";
        */
        //else {
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

        //}

        //return $reply;
        return response()->json(['reply' => $reply, 'error_msg' => $error_msg]);

    }

    /**
     * test image info
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
