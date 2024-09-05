<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\Group;
use App\Models\Colour;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ToolController extends Controller
{

    public function index(Request $request)
    {

        $search = $request->input('search');

        $user = auth()->user();

        $userGroupIds = $user->groups->pluck('id')->toArray();

        $tools = Tool::when($search, function($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        })
            ->whereHas('groups', function($query) use ($userGroupIds) {
                $query->whereIn('groups.id', $userGroupIds);
            })
            ->paginate(8);

        $tools->appends(['search' => $search]);

        return view('home', compact('tools', 'search'));
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
        ]);

        if ($request->has('groups')) {
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
        ]);

        if ($request->has('groups')) {
            $groupIds = $this->getGroupIdsFromNames($request->groups);
            $tool->groups()->sync($groupIds);
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
}
