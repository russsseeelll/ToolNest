@extends('layouts.app')

@section('title', 'Management', env('ORG_NAME', 'OrgName') . ' Tools')

@section('content')
    <!-- Flash Messages -->
    @if(session('success'))
        <div id="flash-message" class="container mx-auto mt-4">
            <div class="p-4 rounded-lg shadow-md flex justify-between items-center"
                 style="background-color: rgb(56, 90, 79); color: white;">
                <span>{{ session('success') }}</span>
                <button id="close-flash" class="text-white ml-4 focus:outline-none">&times;</button>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div id="flash-message" class="container mx-auto mt-4">
            <div class="p-4 rounded-lg shadow-md flex justify-between items-center"
                 style="background-color: rgb(125, 34, 87); color: white;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button id="close-flash" class="text-white ml-4 focus:outline-none">&times;</button>
            </div>
        </div>
    @endif

    <!-- Toggle Banner -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <div class="container mx-auto flex justify-start items-center space-x-4">
            <h2 class="text-xl font-semibold text-[#003865]">Manage Tools & Users</h2>

            @if(!isset($tool) && !isset($user))
                <!-- Show active tabs only if not editing -->
                <button id="tool-manager-btn" class="toggle-button bg-gray-300 text-gray-700 px-6 py-2 rounded-lg shadow-md hover:bg-gray-400 hover:text-gray-900 transition">Tool Manager</button>
                <button id="user-manager-btn" class="toggle-button bg-gray-300 text-gray-700 px-6 py-2 rounded-lg shadow-md hover:bg-gray-400 hover:text-gray-900 transition">User Manager</button>
            @else
                <!-- Disable tabs when editing -->
                <button class="toggle-button bg-gray-100 text-gray-500 px-6 py-2 rounded-lg shadow-md cursor-not-allowed">Tool Manager</button>
                <button class="toggle-button bg-gray-100 text-gray-500 px-6 py-2 rounded-lg shadow-md cursor-not-allowed">User Manager</button>
            @endif
        </div>
    </div>

    <!-- Tool Manager Section -->
    <div id="tool-manager-section" class="flex w-full mt-6 {{ isset($user) ? 'hidden' : '' }}">
        @include('partials.tool_manager')
    </div>

    <!-- User Manager Section -->
    <div id="user-manager-section" class="flex w-full mt-6 {{ isset($tool) ? 'hidden' : '' }}">
        @include('partials.user_manager', ['search' => $search ?? ''])
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let groups = @json($groups);

            function setActiveTab(tab) {
                if (tab === 'user-manager') {
                    $('#user-manager-section').removeClass('hidden');
                    $('#tool-manager-section').addClass('hidden');
                    $('#user-manager-btn').addClass('bg-gray-400 text-gray-900').removeClass('bg-gray-300 text-gray-700');
                    $('#tool-manager-btn').addClass('bg-gray-300 text-gray-700').removeClass('bg-gray-400 text-gray-900');
                } else {
                    $('#tool-manager-section').removeClass('hidden');
                    $('#user-manager-section').addClass('hidden');
                    $('#tool-manager-btn').addClass('bg-gray-400 text-gray-900').removeClass('bg-gray-300 text-gray-700');
                    $('#user-manager-btn').addClass('bg-gray-300 text-gray-700').removeClass('bg-gray-400 text-gray-900');
                }
            }

            let urlParams = new URLSearchParams(window.location.search);
            let activeTab = urlParams.get('tab') || "{{ session('activeTab', 'tool-manager') }}"; // Default to tool manager
            setActiveTab(activeTab);

            $('#tool-manager-btn').on('click', function() {
                setActiveTab('tool-manager');
            });

            $('#user-manager-btn').on('click', function() {
                setActiveTab('user-manager');
            });

            bindGroupAutocomplete('#tool-groups', '#tool-group-suggestions', groups);
            bindGroupAutocomplete('#user-groups', '#user-group-suggestions', groups);

            function bindGroupAutocomplete(inputSelector, suggestionSelector, groups) {
                $(inputSelector).on('input', function () {
                    const inputField = $(this);
                    const suggestionBox = $(suggestionSelector);

                    // Only run autocomplete if the input is not disabled
                    if (!inputField.prop('disabled')) {
                        handleGroupInput(inputField, suggestionBox, groups);
                    }
                });
            }

            function handleGroupInput(inputField, suggestionBox, groups) {
                let query = inputField.val().toLowerCase().split(',').pop().trim();
                suggestionBox.empty().hide();

                if (query.length > 0) {
                    let filteredGroups = groups.filter(group => group.toLowerCase().includes(query));
                    if (filteredGroups.length > 0) {
                        filteredGroups.forEach(group => {
                            $('<li>')
                                .text(group)
                                .addClass('cursor-pointer p-2 hover:bg-gray-200')
                                .appendTo(suggestionBox)
                                .on('click', function() {
                                    let currentGroups = inputField.val().split(',').map(g => g.trim());
                                    currentGroups.pop();
                                    currentGroups.push(group);
                                    inputField.val(currentGroups.join(', ') + ', ');
                                    suggestionBox.empty().hide();
                                });
                        });
                        suggestionBox.show();
                    }
                }
            }

            $('.cancel-btn').on('click', function(e) {
                e.preventDefault();
                setActiveTab('tool-manager');
            });

            // Flash message close button
            $('#close-flash').on('click', function() {
                $('#flash-message').fadeOut();
            });

            setTimeout(function() {
                $('#flash-message').fadeOut();
            }, 5000);
        });

        // All group logic
        $(document).ready(function() {
            const allGroupsCheckbox = $('#allGroups');
            const groupsInput = $('#tool-groups');

            if (allGroupsCheckbox.is(':checked')) {
                groupsInput.prop('disabled', true)
                    .css({
                        'background-color': '#d3d3d3',
                        'opacity': '0.7',
                        'cursor': 'not-allowed'
                    })
                    .val('');
            }

            allGroupsCheckbox.on('change', function() {
                const isChecked = $(this).is(':checked');
                if (isChecked) {
                    groupsInput.prop('disabled', true)
                        .css({
                            'background-color': '#d3d3d3',
                            'opacity': '0.7',
                            'cursor': 'not-allowed'
                        })
                        .val('');
                } else {
                    groupsInput.prop('disabled', false)
                        .css({
                            'background-color': '',
                            'opacity': '',
                            'cursor': ''
                        });
                }
            });
        });

        // NEW DROPDOWN LOGIC: Detach dropdowns to avoid clipping issues.
        const originalDropdownParents = {};

        function toggleDropdown(userId) {
            const dropdownMenu = document.getElementById(`dropdown-menu-${userId}`);
            const isHidden = dropdownMenu.classList.contains('hidden');

            if (isHidden) {
                // Store the original parent if not already stored.
                if (!originalDropdownParents[userId]) {
                    originalDropdownParents[userId] = dropdownMenu.parentNode;
                }
                // Open dropdown: remove hidden class.
                dropdownMenu.classList.remove('hidden');
                // Detach dropdown and append it to document.body.
                document.body.appendChild(dropdownMenu);

                // Position dropdown relative to the cog button.
                const button = document.getElementById(`dropdown-icon-${userId}`);
                const rect = button.getBoundingClientRect();
                let top = rect.bottom; // try to position below the button

                // If dropdown overflows at bottom, position above the button.
                if (top + dropdownMenu.offsetHeight > window.innerHeight) {
                    top = rect.top - dropdownMenu.offsetHeight;
                }
                dropdownMenu.style.position = 'fixed';
                dropdownMenu.style.top = top + 'px';
                dropdownMenu.style.left = rect.left + 'px';
                dropdownMenu.style.zIndex = '1000';
            } else {
                // Close dropdown: add hidden class and reset styles.
                dropdownMenu.classList.add('hidden');
                dropdownMenu.style.position = '';
                dropdownMenu.style.top = '';
                dropdownMenu.style.left = '';
                dropdownMenu.style.zIndex = '';

                // Reattach the dropdown back to its original parent.
                if (originalDropdownParents[userId]) {
                    originalDropdownParents[userId].appendChild(dropdownMenu);
                }
            }
        }

        // Close dropdown if clicking outside.
        document.addEventListener('click', function (event) {
            document.querySelectorAll('[id^="dropdown-icon-"]').forEach(function(button) {
                const userId = button.id.split('-').pop();
                const dropdownMenu = document.getElementById(`dropdown-menu-${userId}`);
                if (!button.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    if (!dropdownMenu.classList.contains('hidden')) {
                        toggleDropdown(userId);
                    }
                }
            });
        });

        // Optional: Close any open dropdown on scroll.
        window.addEventListener('scroll', function() {
            document.querySelectorAll('[id^="dropdown-menu-"]').forEach(function(dropdownMenu) {
                if (!dropdownMenu.classList.contains('hidden')) {
                    const userId = dropdownMenu.id.split('-').pop();
                    toggleDropdown(userId);
                }
            });
        });

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
        }
    </script>
@endsection
