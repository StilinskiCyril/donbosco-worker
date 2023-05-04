<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    // manage projects page
    public function managePage()
    {
        return view('projects');
    }

    // create project
    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:projects'],
            'description' => ['required', 'string'],
            'target_amount' => ['required', 'numeric'],
            'target_date' => ['required', 'date', 'after:today']
        ]);

        Project::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'target_amount' => $request->input('target_amount'),
            'target_date' => $request->input('target_date')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Project Created Successfully'
        ]);
    }

    // load projects
    public function load(Request $request)
    {
        return Project::filter($request)->paginate(20);
    }

    // update project
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique('projects')->ignore($project->id)],
            'description' => ['required', 'string'],
            'target_amount' => ['required', 'numeric'],
            'target_date' => ['required', 'date', 'after:today']
        ]);

        $project->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'target_amount' => $request->input('target_amount'),
            'target_date' => $request->input('target_date')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Project Updated Successfully'
        ]);
    }
}
