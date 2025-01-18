<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\Group;
use App\Models\Colour;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\News;

class ToolController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = auth()->user();
        $userGroupIds = $user->groups->pluck('id')->toArray();

        // Fetch all tools accessible to the user
        $tools = Tool::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        })
            ->where(function ($query) use ($userGroupIds) {
                $query->where('allGroups', true)
                    ->orWhereHas('groups', function ($groupQuery) use ($userGroupIds) {
                        $groupQuery->whereIn('groups.id', $userGroupIds);
                    });
            })
            ->get();

        // Decode the preferences JSON string if necessary
        $preferences = is_string($user->tool_preferences)
            ? collect(json_decode($user->tool_preferences, true)) // Decode JSON string
            : collect($user->tool_preferences); // Already an array

        // Merge preferences into tools
        $tools = $tools->map(function ($tool) use ($preferences) {
            $pref = $preferences->firstWhere('id', $tool->id);

            // Apply preferences
            $tool->visible = $pref['visible'] ?? true; // Default to true if not set
            $tool->order = $pref['order'] ?? $tool->id; // Default to tool ID if no order is set
            return $tool;
        });

        // Tools for the main grid: Only visible ones
        $visibleTools = $tools->filter(function ($tool) {
            return $tool->visible === true; // Explicitly check for visible: true
        })->sortBy('order'); // Sort visible tools by order

        // Sort all tools for the modal
        $allTools = $tools->sortBy('order'); // Ensure allTools is sorted by order

        $techNews = News::inRandomOrder()->limit(5)->get();

        return view('home', [
            'tools' => $visibleTools, // Tools for the grid
            'allTools' => $allTools, // Sorted tools for customisation modal
            'search' => $search,
            'techNews' => $techNews,
        ]);
    }
    public function manage(Request $request, Tool $tool = null)
    {
        $tools = Tool::with('groups')->get();
        $users = User::with('groups')->get();
        $groups = Group::pluck('groupname');
        $colours = Colour::all();

        $toolGroups = '';
        if ($tool) {
            $toolGroups = $tool->groups->pluck('groupname')->implode(', ');
        }

        session()->flash('activeTab', 'tool-manager');

        return view('manage', compact('tools', 'tool', 'toolGroups', 'users', 'groups', 'colours'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'colour' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'groups' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->processImageWithIntervention($request->file('image'));
        }

        $tool = Tool::create([
            'name' => $request->name,
            'url' => $request->url,
            'colour' => $request->colour,
            'image' => $imagePath,
            'allGroups' => $request->boolean('allGroups'),
        ]);

        if (!$request->boolean('allGroups') && $request->has('groups')) {
            $groupIds = $this->getGroupIdsFromNames($request->groups);
            $tool->groups()->sync($groupIds);
        }


        return redirect()->route('manage', ['tool' => $tool->id])->with('success', 'Tool created successfully.');
    }

    public function update(Request $request, Tool $tool)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'colour' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'groups' => 'nullable|string',
            'allGroups' => 'nullable|boolean',
        ]);


        $imagePath = $tool->image;
        if ($request->hasFile('image')) {
            $imagePath = $this->processImageWithIntervention($request->file('image'));
        }

        $tool->update([
            'name' => $request->name,
            'url' => $request->url,
            'colour' => $request->colour,
            'image' => $imagePath,
            'allGroups' => $request->boolean('allGroups'),
        ]);

        if (!$request->boolean('allGroups')) {
            $groupIds = $this->getGroupIdsFromNames($request->groups);
            $tool->groups()->sync($groupIds);
        } else {
            $tool->groups()->detach();
        }


        return redirect()->route('manage', ['tool' => $tool->id])->with('success', 'Tool updated successfully.');
    }

    private function processImageWithIntervention($image)
    {

        $manager = new ImageManager(new Driver());

        $img = $manager->read($image->getPathname());

        $img->scale(width: 500);
        $img->scale(height: 500);

        $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
        $imagePath = storage_path('app/public/tools/' . $imageName);
        $img->toPng()->save($imagePath);

        return 'tools/' . $imageName;
    }

    public function destroy(Tool $tool)
    {

        $tool->groups()->detach();
        $tool->delete();

        return redirect()->route('manage')->with('success', 'Tool deleted successfully.');
    }

    private function getGroupIdsFromNames($groupNames)
    {
        $groupNamesArray = array_map('trim', explode(',', $groupNames));
        return Group::whereIn('groupname', $groupNamesArray)->pluck('id')->toArray();
    }

    public function getUserPreferences()
    {
        $user = auth()->user();
        return response()->json($user->tool_preferences);
    }

    public function saveUserPreferences(Request $request)
    {
        $user = auth()->user();
        $preferences = $request->input('tools', []);

        // Map preferences to the correct format
        $updatedPreferences = [];
        foreach ($preferences as $toolId => $data) {
            $updatedPreferences[] = [
                'id' => (int) $toolId, // Ensure the tool ID is an integer
                'visible' => isset($data['visible']), // Checkbox handling
                'order' => (int) $data['order'], // Save order as an integer
            ];
        }

        // Save preferences as JSON
        $user->update(['tool_preferences' => json_encode($updatedPreferences)]);

        return redirect()->back()->with('success', 'Preferences updated successfully.');
    }




}
