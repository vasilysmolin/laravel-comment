@extends('layouts.app')
@section('content')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{$article->name}}
        </h2>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{$article->text}}
        </div>
        <div id="review-component">
        <review-component
            :reviews="{{$article->comments}}"
            :user="{{$user}}"
            :article="{{$article->getKey()}}">
        </review-component>
        </div>
    </div>
@endsection
