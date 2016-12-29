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
            $response = [
                'error' => false,
                'message' => 'List of all Posts',
                'data' => $posts
            ];
            return response()->json($response, 200);
        }

        $response = [
            'error' => true,
            'message' => 'An error occurred'
        ];

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
            $response = [
                'error' => true,
                'message' => 'Unauthorized to create a post'
            ];
            return response()->json($response, 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:190',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'error'     => true,
                "message"   => "There are problems with your input",
                "messages"  => $validator->errors()->all()
            ];
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
            $response = [
                'message' => 'Post created',
                'data' => $post_link
            ];
            return response()->json($response, 201);
        }

        $response = [
            'error' => true,
            'message' => 'Failed to save post'
        ];

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
            $response = [
                'error' => false,
                'message' => 'Post information',
                'data' => $post
            ];
            return response()->json($response, 200);
        }

        $response = [
            'error' => true,
            'message' => 'Post Not Found'
        ];

        return response()->json($response, 404);
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
