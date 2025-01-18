<header class="bg-[#003865] text-white py-4">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-3xl md:text-4xl font-bold">CoSE IT Tools</h1>
        <div class="flex items-center space-x-4">
            <div class="text-lg">
                <span>Welcome, <span class="font-semibold">{{ auth()->user()->fullname }}</span>!</span>
            </div>
            @if(auth()->user()->admin)
                @if(request()->routeIs('tools.edit') || request()->routeIs('tools.create') || request()->routeIs('manage'))
                    <a href="{{ route('home') }}" class="text-white bg-[#005C8A] px-4 py-2 rounded-lg hover:bg-[#004f74] transition-colors">Home</a>
                @else
                    <a href="{{ route('manage') }}" class="text-white bg-[#005C8A] px-4 py-2 rounded-lg hover:bg-[#004f74] transition-colors">Manage</a>
                @endif
            @endif
        </div>
    </div>
</header>
