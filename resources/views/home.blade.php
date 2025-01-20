@extends('layouts.app')

@section('title', env('ORG_NAME', 'OrgName') . ' Tools')

@section('content')
    <main class="container mx-auto py-12 px-6 flex flex-wrap lg:flex-nowrap">
        <div
            class="w-full {{ env('NEWS_ENABLED', false) ? 'lg:w-3/4 lg:mr-6' : 'lg:w-full' }}">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                <h1 class="text-2xl font-bold text-[#003865]">Your Tools</h1>

                <div class="flex items-center space-x-4 w-full md:w-auto">
                    <form action="{{ route('home') }}" method="GET" class="flex flex-grow md:flex-grow-0">
                        <input
                            type="text"
                            name="search"
                            value="{{ request()->input('search') }}"
                            placeholder="Search tools..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none"
                        >
                        <button class="bg-[#003865] text-white px-4 py-2 rounded-r-lg" type="submit">Search</button>
                    </form>

                    @if(!request()->filled('search') || trim(request()->input('search')) === '')
                        <button id="openModalBtn" class="text-[#003865] hover:text-[#002a52]">
                            <i class="fas fa-cog text-2xl"></i>
                        </button>
                    @endif
                </div>
            </div>

            @if($tools->isEmpty())
                <p class="text-center text-gray-500">No tools found. Try adjusting your search.</p>
            @else
                <div id="toolsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    @foreach($tools as $tool)
                        @if($tool->visible)
                            <div
                                class="relative block border border-gray-300 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-shadow transform hover:scale-105 duration-200"
                                style="order: {{ $tool->order }}"
                            >
                                <a href="{{ $tool->url }}">
                                    <img src="{{ $tool->image ? asset('storage/' . $tool->image) : 'https://via.placeholder.com/400x300.png?text=' . $tool->name }}" alt="{{ $tool->name }}" class="w-full h-48 object-cover">
                                    <div class="p-4" style="background-color: {{ $tool->colour }}; color: white;">
                                        <h2 class="text-lg font-semibold truncate">{{ $tool->name }}</h2>
                                    </div>
                                </a>
                                @if(!empty($tool->info))
                                    <button
                                        class="absolute top-2 right-2 text-white text-lg focus:outline-none flex items-center justify-center w-8 h-8 rounded-full border border-white/50 hover:border-white/70"
                                        style="background-color: {{ $tool->colour }};"
                                        data-modal-info="{{ $tool->info }}"
                                    >
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-6 flex justify-center">
                    {{ $tools->appends(['search' => $search])->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

        @if(env('NEWS_ENABLED', false))
            <aside class="w-full lg:w-1/4 mt-12 lg:mt-0">
                <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-semibold mb-4 text-[#003865]">Latest News</h2>
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
        @endif
    </main>


    <div id="customiseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-2xl transform transition-all duration-300 scale-95">
            <h2 class="text-3xl font-extrabold mb-6 text-[#003865] text-center">Customise Tools</h2>
            <form id="toolPreferencesForm" action="{{ route('tools.preferences.save') }}" method="POST" class="space-y-6">
                @csrf
                <ul id="toolPreferencesList" class="space-y-4 max-h-96 overflow-y-auto border-t border-gray-200 pt-4">
                    @foreach($allTools as $tool)
                        <li class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200" data-id="{{ $tool->id }}">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-grip-vertical text-gray-400 cursor-pointer hover:text-gray-600"></i>
                                <input
                                    type="checkbox"
                                    name="tools[{{ $tool->id }}][visible]"
                                    value="1"
                                    {{ $tool->visible ? 'checked' : '' }}
                                    class="h-5 w-5 text-[#385a4f] focus:ring-[#385a4f] rounded"
                                >
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

    <div id="toolInfoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-2xl transform transition-all duration-300 scale-95">
            <h2 class="text-2xl font-bold mb-4 text-[#003865]" id="toolInfoTitle">Tool Information</h2>
            <p class="text-gray-700 text-sm" id="toolInfoContent"></p>
            <div class="flex justify-end mt-6">
                <button id="closeToolInfoModal" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium hover:bg-gray-400 transition duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toolPreferencesList = document.getElementById('toolPreferencesList');
            const openModalBtn = document.getElementById('openModalBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const customiseModal = document.getElementById('customiseModal');

            new Sortable(toolPreferencesList, {
                animation: 150,
                handle: '.fa-grip-vertical',
                onEnd: function () {

                    Array.from(toolPreferencesList.children).forEach((li, index) => {
                        li.querySelector('input[name$="[order]"]').value = index + 1;
                    });
                },
            });

            openModalBtn.addEventListener('click', () => {
                customiseModal.classList.remove('hidden');
            });

            closeModalBtn.addEventListener('click', () => {
                customiseModal.classList.add('hidden');
            });

            const infoModal = document.getElementById('toolInfoModal');
            const infoTitle = document.getElementById('toolInfoTitle');
            const infoContent = document.getElementById('toolInfoContent');
            const closeInfoModalBtn = document.getElementById('closeToolInfoModal');

            document.querySelectorAll('[data-modal-info]').forEach(button => {
                button.addEventListener('click', () => {
                    const toolInfo = button.getAttribute('data-modal-info');
                    const toolName = button.closest('.block').querySelector('h2').textContent;

                    infoTitle.textContent = `${toolName} Info`;
                    infoContent.textContent = toolInfo;

                    infoModal.classList.remove('hidden');
                });
            });

            closeInfoModalBtn.addEventListener('click', () => {
                infoModal.classList.add('hidden');
            });

            infoModal.addEventListener('click', (event) => {
                if (event.target === infoModal) {
                    infoModal.classList.add('hidden');
                }
            });
        });
    </script>

@endsection
