<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use App\User;
use App\Session;

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
        if ( $user->password == sha1($request->get('password'))) {
            $session = new Session();
            $session->token = md5(microtime().$_SERVER['REMOTE_ADDR']);
            $session->user_id = $user->id;
            $session->is_expired = 0;
            $session->save(); 
            return view('session')->with($session->toArray());
        } else {
            return redirect('/api/user/login');
        }
        //return view('index')->with($user->toArray());
    }

    public function logout(Request $request)
    {
        $session = Session::where('token', $request->get('token'))->first();
        $session->is_expired = 1;
        $session->save();
        return view('logout');
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
        $user = new User();
        $user->username = $request->get('username');
        $user->password = sha1($request->get('password'));
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->email = $request->get('email');
        $user->save(); 
        return redirect('/api/user/register');
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
