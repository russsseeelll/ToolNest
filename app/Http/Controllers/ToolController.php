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
use Illuminate\Pagination\LengthAwarePaginator;

class ToolController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = auth()->user();
        $userGroupIds = $user->groups->pluck('id')->toArray();

        // Get all tools for the user (filter by groups and search)
        $toolsQuery = Tool::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        })
            ->where(function ($query) use ($userGroupIds) {
                $query->where('allGroups', true)
                    ->orWhereHas('groups', function ($groupQuery) use ($userGroupIds) {
                        $groupQuery->whereIn('groups.id', $userGroupIds);
                    });
            });

        // Fetch all tools for sorting and preference purposes
        $allTools = $toolsQuery->get();
        $preferences = collect(json_decode($user->tool_preferences, true) ?? []);

        // Apply preferences to all tools
        $allTools->transform(function ($tool) use ($preferences) {
            $pref = $preferences->firstWhere('id', $tool->id);
            $tool->visible = $pref['visible'] ?? true;
            $tool->order = $pref['order'] ?? $tool->id;
            return $tool;
        });

        // Only include visible tools
        $visibleTools = $allTools->filter(fn($tool) => $tool->visible)->sortBy('order');

        // Manually paginate visible tools
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 8;
        $paginatedTools = new LengthAwarePaginator(
            $visibleTools->forPage($page, $perPage),
            $visibleTools->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Fetch tech news
        $techNews = News::inRandomOrder()->limit(5)->get();

        return view('home', [
            'tools' => $paginatedTools, // Paginated visible tools
            'allTools' => $allTools->sortBy('order'), // All tools for preferences
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
            'info' => 'nullable|string|max:2000',
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
            'info' => $request->info,
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
            'info' => 'nullable|string|max:2000',
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
            'info' => $request->info,
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

        $updatedPreferences = [];
        foreach ($preferences as $toolId => $data) {
            $updatedPreferences[] = [
                'id' => (int) $toolId,
                'visible' => isset($data['visible']),
                'order' => (int) $data['order'],
            ];
        }

        $user->update(['tool_preferences' => json_encode($updatedPreferences)]);

        return redirect()->back()->with('success', 'Preferences updated successfully.');
    }

}
