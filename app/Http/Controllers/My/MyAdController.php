<?php

namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ad\AdStoreRequest;
use App\Http\Requests\Ad\AdUpdateRequest;
use App\Models\Ad;
use Illuminate\Http\Response;

class MyAdController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();
        $ads = Ad::with('images')
            ->where('user_id', $user->getKey())
            ->paginate();
        return response()->json($ads);
    }

    public function show(string $slug)
    {
        $user = auth('api')->user();
        $ad = Ad::where('slug', $slug)
            ->where('user_id', $user->getKey())
            ->with('images')
            ->first();
        abort_unless($ad,404);
        return response()->json($ad);
    }

    public function store(AdStoreRequest $request)
    {
        $formData = $request->all();
        $user = auth('api')->user();
        $formData['user_id'] = $user->getKey();
        $ad = new Ad();
        $ad->fill($formData);
        $ad->save();
        if ($request->images) {
            $ad
                ->setImages($request->images)
                ->setDisk(config('app.filesystem_driver'))
                ->syncImages();
        }
        return response()->json([], Response::HTTP_CREATED, ['Location' => "/ads/$ad->id"]);
    }

    public function update(AdUpdateRequest $request, $id)
    {
        $formData = $request->all();
        $ad = Ad::find($id);
        $ad->fill($formData);
        $ad->update();
        if ($request->images) {
            $ad
                ->setImages($request->images)
                ->setDisk(config('app.filesystem_driver'))
                ->syncImages();
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function destroy(AdUpdateRequest $request, $id)
    {
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
