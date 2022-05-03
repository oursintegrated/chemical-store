<?php

namespace App\Http\Controllers;

use App\Services\APIService;
use Illuminate\Http\Request;

class APIController extends Controller
{
    protected $_APIService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(APIService $APIService)
    {
        $this->_APIService = $APIService;
    }

    public function getToken(){
        $result = $this->_APIService->requestJWTToken(1, session('user.pass'));

        var_dump($result);
    }

    public function getAllUser(){
        $method = "POST";
        $uri = "/example/auth/login";
        $ipAddress = "127.0.0.1";
        $contentType = 'multipart/form-data';
        $body = [
            'type' => 'body',
            'boundary' => true,
            'data' => [
                'username' => 'admin',
                'password' => 'admin'
            ]
        ];
        $requiredToken = false;

        $result = $this->_APIService->requestApi($method, $uri, $ipAddress, $contentType, $body, $requiredToken);

        var_dump($result);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        //
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
