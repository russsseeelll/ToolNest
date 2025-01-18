<div class="w-full lg:w-1/4 lg:mr-6">
    <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-6 h-full max-h-[40rem] flex flex-col">
        <h2 class="text-2xl font-semibold mb-4 text-[#003865]">Existing Tools</h2>
        <ul class="space-y-2 flex-grow overflow-y-auto">
            @foreach($tools as $toolItem)
                <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-300 shadow-sm">
                    <span>{{ $toolItem->name }}</span>
                    <div class="flex space-x-2">
                        <a href="{{ route('manage.tool', $toolItem->id) }}" class="bg-[#003865] hover:bg-[#002a52] text-white px-4 py-2 rounded-lg transition">Edit</a>
                        <form action="{{ route('tools.destroy', $toolItem->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="bg-[#7d2239] hover:bg-[#5c1a2b] text-white px-4 py-2 rounded-lg transition" type="submit">Delete</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-4 sticky bottom-0 bg-white pt-2">
            <a href="{{ route('manage') }}" class="block bg-[#385a4f] hover:bg-[#2c483d] text-white p-3 rounded-lg shadow-md text-center transition">Add New Tool</a>
        </div>
    </div>
</div>

<div class="w-full lg:w-3/4 bg-white border border-gray-300 rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-semibold mb-4 text-[#003865]">{{ isset($tool) ? 'Edit Tool' : 'Add Tool' }}</h2>
    <form id="tool-form" action="{{ isset($tool) ? route('tools.update', $tool->id) : route('tools.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($tool))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="tool-name">Tool Name</label>
            <input type="text" id="tool-name" name="name" value="{{ old('name', $tool->name ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" placeholder="Enter tool name" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="tool-url">URL</label>
            <input type="url" id="tool-url" name="url" value="{{ old('url', $tool->url ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" placeholder="Enter tool URL" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="tool-info">Description</label>
            <textarea
                id="tool-info"
                name="info"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none"
                placeholder="Enter a description for the tool. e.g., what it does, who's an admin etc"
            >{{ old('info', $tool->info ?? '') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="tool-color">Color</label>
            <select id="tool-color" name="colour" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none">
                @foreach($colours as $colour)
                    <option value="{{ $colour->hex_code }}" {{ old('colour', $tool->colour ?? '') == $colour->hex_code ? 'selected' : '' }}>
                        {{ $colour->colour }} ({{ $colour->hex_code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="tool-image">Image Upload</label>
            <input type="file" id="tool-image" name="image" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none">
            @if(isset($tool) && $tool->image)
                <p class="mt-2 text-sm text-gray-500">Current Image: <a href="{{ Storage::url($tool->image) }}" target="_blank">{{ $tool->image }}</a></p>
            @endif
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="tool-url">Group(s)</label>
            <input type="text" id="tool-groups" name="groups"
                   value="{{ old('groups', $toolGroups ?? '') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none"
                   placeholder="Enter group names separated by commas"
                   autocomplete="off"
                   required
                {{ old('allGroups', $tool->allGroups ?? 0) ? 'disabled style=background-color:#d3d3d3;opacity:0.7;cursor:not-allowed;' : '' }}>
            <ul id="tool-group-suggestions" class="border border-gray-300 rounded-lg bg-white shadow-lg mt-1 p-2 hidden"></ul>
        </div>

        <div class="mb-4">
            <input type="hidden" name="allGroups" value="0">
            <input
                type="checkbox"
                id="allGroups"
                name="allGroups"
                value="1"
                class="mr-2"
                {{ old('allGroups', $tool->allGroups ?? false) ? 'checked' : '' }}
            >
            <label for="allGroups" class="text-gray-700 text-sm font-bold">All Groups</label>
        </div>

        <div class="flex justify-end space-x-2">
            @if(isset($tool))
                <a href="{{ route('manage') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancel</a>
            @endif
            <button type="submit" class="bg-[#003865] text-white px-4 py-2 rounded-lg">{{ isset($tool) ? 'Update Tool' : 'Save Tool' }}</button>
        </div>
    </form>
</div>
