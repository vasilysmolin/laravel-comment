<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentStoreRequest;
use App\Models\Comment;
use Illuminate\Http\Response;

class CommentController extends Controller
{

    public function store(CommentStoreRequest $request)
    {
        $data = $request->all();
        $customer = new Comment();
        $customer->fill($data);
        $customer->save();

        return response()->json(Response::HTTP_CREATED);
    }

}
