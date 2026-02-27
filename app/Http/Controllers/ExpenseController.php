<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Colocation $colocation)
    {
        if (!$colocation->users()->where('user_id', auth()->id())->exists()) {
            abort(403);
        }

        $month = $request->get('month', now()->format('Y-m'));
        $categoryId = $request->get('category_id');

        $query = $colocation->expenses()->with(['payer', 'category']);

        if ($request->has('month') && $month) {
            $query->where('date', 'like', "{$month}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $expenses = $query->latest('date')->paginate(20)->withQueryString();
        $categories = $colocation->categories;

        return view('expenses.index', compact('colocation', 'expenses', 'categories', 'month', 'categoryId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Colocation $colocation)
    {
        if (!$colocation->users()->where('user_id', auth()->id())->exists()) {
            abort(403);
        }

        $categories = $colocation->categories;
        $members = $colocation->memberships()->whereNull('left_at')->with('user')->get()->pluck('user');

        return view('expenses.create', compact('colocation', 'categories', 'members'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(StoreExpenseRequest $request, Colocation $colocation)
    {
        $validated = $request->validated();
        $paidBy = $validated['paid_by'] ?? auth()->id();

        $expense = $colocation->expenses()->create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'paid_by' => $paidBy,
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Expense added successfully. -)');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(UpdateExpenseRequest $request, Colocation $colocation, Expense $expense)
    {
        $validated = $request->validated();
        $paidBy = $validated['paid_by'] ?? $expense->paid_by;

        $expense->update([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'paid_by' => $paidBy,
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Colocation $colocation, Expense $expense)
    {
        $membership = $colocation->memberships()->where('user_id', auth()->id())->whereNull('left_at')->first();

        if (!$membership || ($membership->role !== 'owner' && $expense->paid_by !== auth()->id())) {
            abort(403, 'You are not authorized to delete this expense. (-_-)');
        }

        if ($expense->colocation_id !== $colocation->id) {
            abort(404);
        }

        $expense->delete();

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Expense deleted successfully. -)');
    }
}
