<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SubGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubGroupController extends Controller
{
    // manage groups page
    public function managePage(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('sub-groups');
    }

    // create sub-group
    public function create(Request $request, Group $group)
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:sub_groups'],
            'ministry' => ['required', 'string', 'unique:sub_groups']
        ]);

        $group->subGroups()->create([
            'name' => $request->input('name'),
            'ministry' => $request->input('ministry'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Sub-Group Created Successfully'
        ]);
    }

    // load sub groups
    public function load(Request $request)
    {
        return SubGroup::with(['group'])->filter($request)->paginate(20);
    }

    // update sub-group
    public function update(Request $request, Group $group, SubGroup $subGroup)
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique('sub_groups')->ignore($subGroup->id)],
            'ministry' => ['required', 'string',  Rule::unique('sub_groups')->ignore($subGroup->id)]
        ]);

        $subGroup->update([
            'group_id' => $group->id,
            'name' => $request->input('name'),
            'ministry' => $request->input('ministry'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Sub-Group Updated Successfully'
        ]);
    }
}
