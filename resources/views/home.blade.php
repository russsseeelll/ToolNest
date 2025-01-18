@extends('layouts.app')

@section('title', 'UofG: CoSE IT Tools')

@section('content')
    <main class="container mx-auto py-12 px-6 flex flex-wrap lg:flex-nowrap">
        <!-- Main Content: Tools Grid -->
        <div class="w-full lg:w-3/4 lg:mr-6">
            <!-- Customisation Modal Trigger -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-[#003865]">Your Tools</h1>
                <button id="openModalBtn" class="flex items-center space-x-2 text-[#003865] hover:text-[#002a52]">
                    <i class="fas fa-cogs text-xl"></i>
                    <span>Customise View</span>
                </button>
            </div>

            <!-- Check if there are tools -->
            @if($tools->isEmpty())
                <p class="text-center text-gray-500">No tools yet.</p>
            @else
                <!-- Tool Grid -->
                <div id="toolsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    @foreach($tools as $tool)
                        @if($tool->visible)
                            <a href="{{ $tool->url }}" class="block border border-gray-300 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-shadow transform hover:scale-105 duration-200" style="order: {{ $tool->order }}">
                                <img src="{{ $tool->image ? asset('storage/' . $tool->image) : 'https://via.placeholder.com/400x300.png?text=' . $tool->name }}" alt="{{ $tool->name }}" class="w-full h-48 object-cover">
                                <div class="p-4" style="background-color: {{ $tool->colour }}; color: white;">
                                    <h2 class="text-lg font-semibold truncate">{{ $tool->name }}</h2>
                                </div>
                            </a>
                        @endif
                    @endforeach
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
                            <div class="bg-gray-100 p-4 rounded-lg border border-gray-300 shadow-sm hover:shadow-lg transition-shadow duration-200">
                                <a href="{{ $news->url }}" target="_blank" class="block">
                                    <h3 class="font-semibold text-lg mb-2">{{ $news->title }}</h3>
                                    <p class="text-sm text-gray-700 mb-2 truncate">{{ $news->description }}</p>
                                </a>
                                <div class="mt-2 text-xs text-gray-600 flex justify-between">
                                    <span><strong>Source:</strong> {{ $news->source_name }}</span>
                                    <span><strong>Published:</strong> {{
                                        Carbon\Carbon::parse($news->published_at)->format('d/m/Y, h:i A') }}</span>
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

    <!-- TailwindCSS Modal -->
    <div id="customiseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-2xl transform transition-all duration-300 scale-95">
            <h2 class="text-3xl font-extrabold mb-6 text-[#003865] text-center">Customise View</h2>
            <form id="toolPreferencesForm" action="{{ route('tools.preferences.save') }}" method="POST" class="space-y-6">
                @csrf
                <ul id="toolPreferencesList" class="space-y-4 max-h-96 overflow-y-auto border-t border-gray-200 pt-4">
                    @foreach($allTools as $tool)
                    <li class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200" data-id="{{ $tool->id }}">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-grip-vertical text-gray-400 cursor-pointer hover:text-gray-600"></i>
                            <input type="checkbox" name="tools[{{ $tool->id }}][visible]" value="1" {{ $tool->visible ? 'checked' : '' }} class="h-5 w-5 text-[#385a4f] focus:ring-[#385a4f] rounded">
                            <input type="hidden" name="tools[{{ $tool->id }}][order]" value="{{ $tool->order }}">
                            <span class="tool-name text-gray-800 font-medium">{{ $tool->name }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="closeModalBtn" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium hover:bg-gray-400 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="bg-[#385a4f] text-white px-5 py-2 rounded-lg font-medium hover:bg-[#2c483d] transition duration-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Required JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toolPreferencesList = document.getElementById('toolPreferencesList');
            const openModalBtn = document.getElementById('openModalBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const modal = document.getElementById('customiseModal');

            // Enable drag-and-drop sorting
            new Sortable(toolPreferencesList, {
                animation: 150,
                handle: '.fa-grip-vertical',
                onEnd: function () {
                    // Update order inputs on drag-and-drop
                    Array.from(toolPreferencesList.children).forEach((li, index) => {
                        li.querySelector('input[name$="[order]"]').value = index + 1; // Update the order input
                    });
                },
            });

            // Open modal
            openModalBtn.addEventListener('click', () => {
                modal.classList.remove('hidden');
            });

            // Close modal
            closeModalBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        });
    </script>

@endsection
