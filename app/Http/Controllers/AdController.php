<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::with('images')->paginate();
        return response()->json($ads);
    }

    public function show(string $slug)
    {
        $ad = Ad::where('slug', $slug)
            ->with('images')
            ->first();
        return response()->json($ad);
    }
}
