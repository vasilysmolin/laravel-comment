<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Articles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @foreach($articles as $article)
                <div>
                    <a href="{{route('articles.show', [$article->slug])}}">{{$article->name}}</a>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
