<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    // manage groups page
    public function managePage(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('groups');
    }

    // create group
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:groups']
        ]);

        Group::create([
            'name' => $request->input('name')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Group Created Successfully'
        ]);
    }

    // load groups
    public function load(Request $request)
    {
        return Group::withCount(['subGroups'])->filter($request)->paginate(20);
    }

    // update group
    public function update(Request $request, Group $group): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique('groups')->ignore($group->id)]
        ]);

        $group->update([
            'name' => $request->input('name')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Group Updated Successfully'
        ]);
    }
}
