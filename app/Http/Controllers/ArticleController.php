<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::get();
        return view('articles.index', compact('articles'));
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)
            ->with('comments.childrenComments')
            ->first();
        $user = Auth::user();
        return view('articles.show', compact('article', 'user'));
    }
}
