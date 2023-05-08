<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // manage groups page
    public function managePage(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('expenses');
    }

    // load expenses
    public function load(Request $request)
    {
        return Expense::filter($request)->paginate(20);
    }

    // create expense
    public function create(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string']
        ]);

        Expense::create([
            'title' => $request->input('title'),
            'date' => $request->input('date'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Expense Created Successfully'
        ]);
    }

    // update expense
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string']
        ]);

        $expense->update([
            'title' => $request->input('title'),
            'date' => $request->input('date'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Expense Updated Successfully'
        ]);
    }
}
