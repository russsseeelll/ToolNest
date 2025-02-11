<!-- resources/views/partials/user_manager.blade.php -->
<div class="w-full lg:w-1/4 lg:mr-6">
    <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-6 h-full flex flex-col">
        <h2 class="text-2xl font-semibold mb-4 text-[#003865]">Existing Users</h2>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('manage') }}" class="mb-4">
            <!-- Ensure the user-manager tab remains active -->
            <input type="hidden" name="tab" value="user-manager">
            <div class="flex">
                <input
                    type="text"
                    name="search"
                    placeholder="Search users..."
                    value="{{ request('search', $search ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none"
                />
                <button
                    type="submit"
                    class="bg-[#003865] hover:bg-[#002a52] text-white px-4 py-2 rounded-r-lg"
                >
                    Search
                </button>
            </div>
        </form>

        <!-- User List -->
        <ul class="space-y-4 flex-1 overflow-y-auto">
            @foreach($users as $userItem)
                <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-300 shadow-sm relative">
                    <!-- User Info -->
                    <div>
                        <p class="font-semibold">{{ $userItem->fullname }}</p>
                        <p class="text-sm text-gray-500">{{ $userItem->email }}</p>
                    </div>

                    <!-- Actions Dropdown -->
                    <div class="relative">
                        <button
                            id="dropdown-icon-{{ $userItem->id }}"
                            class="text-[#003865] hover:text-[#002a52] focus:outline-none"
                            onclick="toggleDropdown({{ $userItem->id }})"
                        >
                            <i class="fas fa-cog text-2xl"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdown-menu-{{ $userItem->id }}"
                             class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg z-50">
                            <a
                                href="{{ route('manage.user', array_merge(request()->all(), ['user' => $userItem->id, 'tab' => 'user-manager'])) }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition"
                            >
                                <i class="fa-solid fa-pen-to-square mr-2"></i> Edit
                            </a>
                            <button
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition w-full text-left"
                                onclick="openModal('delete-modal-{{ $userItem->id }}')"
                            >
                                <i class="fa-solid fa-trash mr-2"></i> Delete
                            </button>
                            <button
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition w-full text-left"
                                onclick="openModal('reset-modal-{{ $userItem->id }}')"
                            >
                                <i class="fa-solid fa-envelope mr-2"></i> Send Reset
                            </button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{-- Force merge tab=user-manager into the query string --}}
            {{ $users->appends(array_merge(request()->all(), ['tab' => 'user-manager']))->links() }}
        </div>

        <!-- Add New User Button -->
        <div class="mt-6">
            <a href="{{ route('manage', array_merge(request()->all(), ['tab' => 'user-manager'])) }}" class="block bg-[#385a4f] hover:bg-[#2c483d] text-white p-3 rounded-lg shadow-md text-center transition">
                Add New User
            </a>
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

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="user-email">Email</label>
            <input type="email" id="user-email" name="email" value="{{ old('email', $user->email ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" placeholder="Enter email address" required>
        </div>

        <!-- Username -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="user-username">Username</label>
            <input type="text" id="user-username" name="username" value="{{ old('username', $user->username ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" placeholder="Enter username" required>
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
                <a href="{{ route('manage', array_merge(request()->all(), ['tab' => 'user-manager'])) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancel</a>
            @endif
            <button type="submit" class="bg-[#003865] text-white px-4 py-2 rounded-lg">{{ isset($user) ? 'Update User' : 'Save User' }}</button>
        </div>
    </form>
</div>

<!-- Modals for Each User -->
@foreach($users as $userItem)
    <!-- Delete Confirmation Modal -->
    <div id="delete-modal-{{ $userItem->id }}"
         class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-lg font-semibold text-[#003865] mb-4">Confirm Delete</h2>
            <p>Are you sure you want to delete <span class="font-semibold">{{ $userItem->fullname }}</span>?</p>
            <div class="flex justify-end mt-4 space-x-2">
                <button class="bg-gray-500 text-white px-4 py-2 rounded-lg" onclick="closeModal('delete-modal-{{ $userItem->id }}')">Cancel</button>
                <form action="{{ route('users.destroy', $userItem->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-[#7d2239] text-white px-4 py-2 rounded-lg">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div id="reset-modal-{{ $userItem->id }}"
         class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-lg font-semibold text-[#003865] mb-4">Confirm Password Reset</h2>
            <p>Are you sure you want to send a password reset email to <span class="font-semibold">{{ $userItem->email }}</span>?</p>
            <div class="flex justify-end mt-4 space-x-2">
                <button class="bg-gray-500 text-white px-4 py-2 rounded-lg" onclick="closeModal('reset-modal-{{ $userItem->id }}')">Cancel</button>
                <form action="{{ route('users.sendPasswordReset', $userItem->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-[#385a4f] text-white px-4 py-2 rounded-lg">Send Reset</button>
                </form>
            </div>
        </div>
    </div>
@endforeach
