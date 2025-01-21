<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\Group;
use App\Models\Colour;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function manage(Request $request, $userId = null)
    {
        $search = $request->input('search', ''); // Default to an empty search query

        // Filter users based on search query
        $users = User::with('groups')
            ->when($search, function ($query) use ($search) {
                $query->where('fullname', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(10); // Add pagination with 10 users per page

        $groups = Group::pluck('groupname');
        $tools = Tool::all();
        $colours = Colour::all();

        $user = null;
        $userGroups = '';

        if ($userId) {
            $user = User::with('groups')->find($userId);
            if ($user) {
                $userGroups = $user->groups->pluck('groupname')->implode(', ');
            }
        }

        session()->flash('activeTab', 'user-manager');

        return view('manage', compact('users', 'user', 'userGroups', 'groups', 'tools', 'colours', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username|max:255',
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'admin' => 'required|boolean',
            'groups' => 'nullable|string',
        ]);

        $user = User::create([
            'username' => $request->username,
            'fullname' => $request->fullname,
            'email' => $request->email,
            'admin' => $request->admin,
        ]);

        if ($request->has('groups')) {
            $groupIds = $this->getGroupIdsFromNames($request->groups);
            $user->groups()->sync($groupIds);
        }

        return redirect()->route('manage', ['user' => $user->id])->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}|max:255",
            'admin' => 'required|boolean',
            'groups' => 'nullable|string',
        ]);

        $user->update([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'admin' => $request->admin,
        ]);

        if ($request->has('groups')) {
            $groupIds = $this->getGroupIdsFromNames($request->groups);
            $user->groups()->sync($groupIds);
        }

        return redirect()->route('manage', ['user' => $user->id])->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('manage')
                ->withErrors(['You cannot delete your own account.']);
        }

        $user->groups()->detach();
        $user->delete();

        return redirect()->route('manage')->with('success', 'User deleted successfully.');
    }

    private function getGroupIdsFromNames($groupNames)
    {
        $groupNamesArray = array_map('trim', explode(',', $groupNames));
        return Group::whereIn('groupname', $groupNamesArray)->pluck('id')->toArray();
    }
}
