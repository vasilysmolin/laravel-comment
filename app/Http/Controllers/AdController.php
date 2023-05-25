<?php

namespace App\Http\Controllers;

use App\Models\Ad;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::with('images')->paginate();
        $ads->getCollection()->transform(function($ad) {
            $ad->images = $ad->setDisk(config('app.filesystem_driver'))->getImages();
            $ad->image = $ad->setDisk(config('app.filesystem_driver'))->getFirstImage();
        });
        return response()->json($ads);
    }

    public function show(string $slug)
    {
        $ad = Ad::where('slug', $slug)
            ->with('images')
            ->first();

        $ad->images = $ad->setDisk(config('app.filesystem_driver'))->getImages();
        $ad->image = $ad->setDisk(config('app.filesystem_driver'))->getFirstImage();

        return response()->json($ad);
    }
}
