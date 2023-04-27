<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentIndexRequest;
use App\Http\Requests\CommentStoreRequest;
use App\Models\Comment;
use Illuminate\Http\Response;

class CommentController extends Controller
{

    public function index(CommentIndexRequest $request)
    {
        $comments = Comment::where('article_id', $request->article_id)
            ->with('childrenComments', 'user')
            ->get();
        return response()->json(['comments' => $comments]);
    }

    public function store(CommentStoreRequest $request)
    {
        $data = $request->all();
        $comment = new Comment();
        $comment->fill($data);
        $comment->save();

        return response()->json([],Response::HTTP_CREATED);
    }

}
