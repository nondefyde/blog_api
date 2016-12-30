<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Post;
use Carbon\Carbon;
use JWTAuth;
use Validator;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => [
            'update', 'store', 'destroy'
        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        if($posts) {
            foreach ($posts as $post) {
                $post->post_link = [
                    'href' => 'api/v1/post/' . $post->slug,
                    'method' => 'GET'
                ];
            }
            $response = $this->createResponse(false,$posts,'List of Posts',null);
            return response()->json($response, 200);
        }
        $response = $this->createResponse(true,null,'An error occurred',null);
        return response()->json($response, 501);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->setTrim($request);

        $user = JWTAuth::parseToken()->authenticate();

        if ($user->user_type != 1) {
            $response = $this->createResponse(true,null,'Unauthorized to create a post',null);
            return response()->json($response, 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:190',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            $response = $this->createResponse(true,null,"There are problems with your input",$validator->errors()->all());
            return response()->json($response,400);
        }

        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $user->id
        ]);
        if ($post) {
            $post_link = [
                'href' => 'api/v1/post/' . $post->slug,
                'method' => 'GET'
            ];
            $response = $this->createResponse(false,$post_link,'Post created',null);
            return response()->json($response, 201);
        }
        $response = $this->createResponse(true,null,'Failed to save post',null);
        return response()->json($response, 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post = Post::with('owner')->where('slug', $slug)->first();
        if($post) {
            $post->comment_link = [
                'href' => '#',
                'method' => 'GET'
            ];
            $response = $this->createResponse(false,$post,'Post details',null);
            return response()->json($response, 200);
        }
        $response = $this->createResponse(true,null,'Post Not Found',null);
        return response()->json($response, 404);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $this->setTrim($request);

        $user = JWTAuth::parseToken()->authenticate();

        if ($user->user_type != 1) {
            $response = $this->createResponse(true,null,'Unauthorized to update post',null);
            return response()->json($response, 401);
        }

        $post = Post::where('slug', $slug)->first();
        if(!$post){
            $response = $this->createResponse(true,null,'Post Not Found',null);
            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:190',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            $response = $this->createResponse(true,null,"There are problems with your input",$validator->errors()->all());
            return response()->json($response,400);
        }

        $post_updated = $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);
        if ($post_updated) {
            $post = Post::where('id', $user->id)->first();
            $post_link = [
                'href' => 'api/v1/post/' . $post->slug,
                'method' => 'GET'
            ];
            $response = $this->createResponse(false,$post_link,'Post updated',null);
            return response()->json($response, 201);
        }

        $response = $this->createResponse(true,null,'Failed to update post',null);
        return response()->json($response, 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($user->user_type != 1) {
            $response = $this->createResponse(true,null,'Unauthorized to delete post',null);
            return response()->json($response, 401);
        }

        $post = Post::where('slug', $slug)->first();
        if(!$post){
            $response = $this->createResponse(true,null,'Post Not Found',null);
            return response()->json($response, 404);
        }

        $post_deleted = $post->delete();
        if ($post_deleted) {
            $post_create_link = [
                'href' => 'api/v1/post',
                'method' => 'POST',
                'params' => 'title, description'
            ];
            $response = $this->createResponse(false,$post_create_link,'Post deleted',null);
            return response()->json($response, 201);
        }

        $response = $this->createResponse(true,null,'Failed to delete post',null);
        return response()->json($response, 404);
    }
}
