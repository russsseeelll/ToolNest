@extends('layouts.app')

@section('title', 'UofG: CoSE IT Tools')

@section('content')
    <main class="container mx-auto py-12 px-6 flex flex-wrap lg:flex-nowrap">
        <!-- Main Content: Tools Grid -->
        <div class="w-full lg:w-3/4 lg:mr-6">
            <!-- Search Bar -->
            <form action="{{ route('home') }}" method="GET" class="flex mb-6">
                <input
                    type="text"
                    name="search"
                    value="{{ request()->input('search') }}"
                    placeholder="Search tools..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none"
                >
                <button class="bg-[#003865] text-white px-4 py-2 rounded-r-lg" type="submit">Search</button>
            </form>

            <!-- Check if there are tools -->
            @if($tools->isEmpty())
                <p class="text-center text-gray-500">No tools yet.</p>
            @else
                <!-- Tool Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-{{ count($tools) > 4 ? '3' : count($tools) }} lg:grid-cols-{{ count($tools) > 4 ? '4' : count($tools) }} gap-8">
                    <!-- Tool Loop -->
                    @foreach($tools as $tool)
                        <a href="{{ $tool->url }}" class="block border border-gray-300 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-shadow transform hover:scale-105 duration-200">
                            <!-- Image to fill the box -->
                            <img src="{{ $tool->image ? asset('storage/' . $tool->image) : 'https://via.placeholder.com/400x300.png?text=' . $tool->name }}" alt="{{ $tool->name }}" class="w-full h-48 object-fill">
                            <div class="p-4" style="background-color: {{ $tool->colour }}; color: white;">
                                <h2 class="text-lg font-semibold truncate">{{ $tool->name }}</h2>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-center">
                    {{ $tools->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

        <!-- Sidebar: Latest Tech News -->
        <aside class="w-full lg:w-1/4 mt-12 lg:mt-0">
            <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-[#003865]">Latest Tech News</h2>
                <div id="news-section" class="space-y-4 max-h-[600px] overflow-y-auto">
                    @if($techNews->isNotEmpty())
                        @foreach($techNews as $news)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-300 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <a href="{{ $news->url }}" target="_blank" class="block hover:text-[#003865]">
                                    <h3 class="font-semibold text-base mb-1 truncate">{{ $news->title }}</h3>
                                    <p class="text-sm text-gray-600 truncate">{{ $news->description }}</p>
                                </a>
                                <div class="mt-2 text-xs text-gray-500">
                                    <p><span class="font-semibold">Source:</span> {{ $news->source_name }}</p>
                                    <p><span class="font-semibold">Published:</span> {{ \Carbon\Carbon::parse($news->published_at)->format('d/m/Y, h:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center text-gray-500">No tech news available at the moment.</p>
                    @endif
                </div>
            </div>
        </aside>
    </main>
@endsection
