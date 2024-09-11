<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comments::all();
        return response()->json(['comments' => $comments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function CommentValidate(Request $request)
    {
        return Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'post_id' => 'required|integer',
            'content' => 'required|string|max:500',
            'reply_id' => 'nullable|integer',
        ]);

    }
    public function store(Request $request)
    {
        $validator = $this->CommentValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $comment = Comments::create([
            'user_id' => $request->input('user_id'),
            'post_id' => $request->input('post_id'),
            'content' => $request->input('content'),
            'reply_id' => $request->input('reply_id'),
        ]);
        return response()->json(['comments' => $comment], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comments $comments, $id)
    {
        $user = Comments::find($id);
        if (!$user) {
            return response()->json(['error' => 'Comments not found'], 404);
        }
        return response()->json(['Comments' => $user], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comments $comments)
    {
        $validator = $this->CommentValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $cmm = Comments::find($request->id);
        if (!$cmm) {
            return response()->json(['error' => 'post already exists.'], 400);
        }
        $cmm->update(
            [
                'user_id' => $request->input('user_id'),
                'post_id' => $request->input('post_id'),
                'content' => $request->input('content'),
                'reply_id' => $request->input('reply_id'),
            ]
        );
        return response()->json(['message' => 'Updated success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comments $comments, $id)
    {
        $cmm = Comments::find($id);
        if (!$cmm) {
            return response()->json(['error' => 'post not found'], 404);
        }
        $cmm->delete();
        return response()->json(['message' => 'Deleted successfully']);

    }
    public function getPost(Request $req)
    {
        $id = $req->id;

        $post = Post::with([
            'poster',
            'comments' => function ($query) {
                $query->select('id', 'comments_user_id', 'content', 'reply_id', 'post_id');
            },
            'comments.user:id,name',
        ])->select('id', 'post_user_id', 'content', 'title')
            ->find($id);

        if (!$post) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $comments = $post->comments;
        $post->user_name = $post->poster->name;
        unset($post->poster, $post->comments);
        $post->all_comments = $this->getReplyComments($comments);
        return response()->json(['post' => $post, 'comments' => $post->all_comments]);
    }

    public function getReplyComments($comments)
    {
        $commentQueue = [];
        $commentMap = [];
        $nestedComments = [];

        foreach ($comments as $comment) {
            $comment->user_name = $comment->user->name;
            unset($comment->user);
            $comment->reply_id = [];
            $commentMap[] = $comment;
            if ($comment->reply_id === null) {
                $commentQueue[] = $comment;
            }
        }

        $removeReplayIds = [];
        $i = 0;
        $current = null;
        do {
            if (!isset($commentQueue[$i])) {
                break;
            }

            $current = $commentQueue[$i];
            $replies = [];
            foreach ($commentMap as $comment) {
                $comment->user_name = $current->user->name;
                unset($comment->user);
                if ($comment->reply_id === $current->id) {
                    $replies[] = $comment;
                    $commentQueue[] = $comment;
                    $removeReplayIds[] = $comment->id;
                }
            }
            $current->reply_id = $replies;
            if (!in_array($current->id, $removeReplayIds)) {
                $nestedComments[] = $current;
            }
            $i++;
        } while ($current);
        return $nestedComments;
    }

}
