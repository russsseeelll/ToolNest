<?php

namespace App\Http\Controllers;

use App\Models\Group;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('groups.index', compact('groups'));
    }

    public function getAllGroups()
    {
        return Group::all();
    }
}
