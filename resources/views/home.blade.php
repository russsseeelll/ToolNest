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

        <!-- Sidebar: Latest Ivanti Tickets -->
        <aside class="w-full lg:w-1/4 mt-12 lg:mt-0">
            <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-6 ticket-box">
                <h2 class="text-2xl font-semibold mb-4 text-[#003865]">Latest Ivanti Tickets</h2>
                <div class="space-y-2 h-full overflow-y-auto">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-300 shadow-sm">
                        <p><span class="font-semibold">Ticket Ref:</span> 100578</p>
                        <p><span class="font-semibold">Name:</span> Michael Scott</p>
                        <p><span class="font-semibold">Subject:</span> Printer setup</p>
                        <p><span class="font-semibold">Timestamp:</span> 01/09/24, 01:00pm</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-300 shadow-sm">
                        <p><span class="font-semibold">Ticket Ref:</span> 100577</p>
                        <p><span class="font-semibold">Name:</span> Alice Brown</p>
                        <p><span class="font-semibold">Subject:</span> VPN not connecting</p>
                        <p><span class="font-semibold">Timestamp:</span> 01/09/24, 12:30pm</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-300 shadow-sm">
                        <p><span class="font-semibold">Ticket Ref:</span> 100576</p>
                        <p><span class="font-semibold">Name:</span> Jane Smith</p>
                        <p><span class="font-semibold">Subject:</span> Software installation request</p>
                        <p><span class="font-semibold">Timestamp:</span> 01/09/24, 11:45am</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-300 shadow-sm">
                        <p><span class="font-semibold">Ticket Ref:</span> 100575</p>
                        <p><span class="font-semibold">Name:</span> John Doe</p>
                        <p><span class="font-semibold">Subject:</span> Access to shared drive</p>
                        <p><span class="font-semibold">Timestamp:</span> 01/09/24, 11:00am</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-300 shadow-sm">
                        <p><span class="font-semibold">Ticket Ref:</span> 100574</p>
                        <p><span class="font-semibold">Name:</span> Sarah Phillips</p>
                        <p><span class="font-semibold">Subject:</span> Password reset</p>
                        <p><span class="font-semibold">Timestamp:</span> 01/09/24, 10:20am</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-300 shadow-sm">
                        <p><span class="font-semibold">Ticket Ref:</span> 100573</p>
                        <p><span class="font-semibold">Name:</span> Russell McInnes</p>
                        <p><span class="font-semibold">Subject:</span> I need a new monitor</p>
                        <p><span class="font-semibold">Timestamp:</span> 01/09/24, 09:15am</p>
                    </div>

                </div>
            </div>
        </aside>
    </main>
@endsection
