<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePostsRequest;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Posts::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function postValidate(Request $request)
    {
        return Validator::make($request->all(), [
            'title' => 'required|min:5|max:255',
            'content' => 'required|min:10',
            // post_user_id
            'post_user_id' => 'required|exists:users,id',
        ]);

    }
    public function store(Request $request)
    {
        $validator = $this->postValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $post = Posts::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'post_user_id' => $request->input('post_user_id'),
        ]);
        return response()->json(['post' => $post], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Posts $posts)
    {
        return $posts;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostsRequest $request, Posts $posts)
    {
        $validator = $this->postValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $post = Posts::find($request->id);
        if (!$post) {
            return response()->json(['error' => 'post already exists.'], 400);
        }
        $post->update(
            [
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'post_user_id' => $request->input('post_user_id'),
            ]
        );
        return response()->json(['message' => 'Updated success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Posts $posts, $id)
    {
        $post = Posts::find($id);

        if ($post) {
            $post->delete();
            return response()->json(['message' => 'post deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'post not found.'], 404);
        }
    }
}
