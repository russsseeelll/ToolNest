<!-- Left Pane: Existing Users List -->
<div class="w-full lg:w-1/4 lg:mr-6">
    <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-6 h-full max-h-[40rem] flex flex-col">
        <h2 class="text-2xl font-semibold mb-4 text-[#003865]">Existing Users</h2>
        <ul class="space-y-2 flex-grow overflow-y-auto">
            @foreach($users as $userItem)
                <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-300 shadow-sm">
                    <span>{{ $userItem->fullname }}</span>
                    <div class="flex space-x-2">
                        <a href="{{ route('manage.user', $userItem->id) }}" class="bg-[#003865] hover:bg-[#002a52] text-white px-4 py-2 rounded-lg transition">Edit</a>
                        <form action="{{ route('users.destroy', $userItem->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="bg-[#7d2239] hover:bg-[#5c1a2b] text-white px-4 py-2 rounded-lg transition" type="submit">Delete</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
        <!-- Add New User Button -->
        <div class="mt-4 sticky bottom-0 bg-white pt-2">
            <a href="{{ route('manage', ['tab' => 'user-manager']) }}" class="block bg-[#385a4f] hover:bg-[#2c483d] text-white p-3 rounded-lg shadow-md text-center transition">Add New User</a>
        </div>
    </div>
</div>

<!-- Right Pane: Editing/Adding Form -->
<div class="w-full lg:w-3/4 bg-white border border-gray-300 rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-semibold mb-4 text-[#003865]">{{ isset($user) ? 'Edit User' : 'Add User' }}</h2>
    <form id="user-form" action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <!-- GUID -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="user-guid">GUID</label>
            <input type="text" id="user-guid" name="guid" value="{{ old('guid', $user->guid ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" placeholder="Enter user GUID" required>
        </div>

        <!-- Full Name -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="user-fullname">Full Name</label>
            <input type="text" id="user-fullname" name="fullname" value="{{ old('fullname', $user->fullname ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" placeholder="Enter full name" required>
        </div>

        <!-- Groups -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="user-groups">Group(s)</label>
            <input type="text" id="user-groups" name="groups" value="{{ old('groups', $userGroups ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" placeholder="Enter group names separated by commas" autocomplete="off" required>
            <ul id="user-group-suggestions" class="border border-gray-300 rounded-lg bg-white shadow-lg mt-1 p-2 hidden"></ul>
        </div>

        <!-- Admin Checkbox -->
        <div class="mb-4">
            <input type="hidden" name="admin" value="0">
            <input type="checkbox" id="user-admin" name="admin" value="1" {{ old('admin', isset($user) && $user->admin ? 'checked' : '') }} class="mr-2">
            <label for="user-admin" class="text-gray-700 text-sm font-bold">Admin</label>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-2">
            @if(isset($user))
                <a href="{{ route('manage') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancel</a>
            @endif
            <button type="submit" class="bg-[#003865] text-white px-4 py-2 rounded-lg">{{ isset($user) ? 'Update User' : 'Save User' }}</button>
        </div>
    </form>
</div>
