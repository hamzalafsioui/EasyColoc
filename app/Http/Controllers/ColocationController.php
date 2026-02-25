<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ColocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colocations = auth()->user()->colocations;
        return view('colocations.index', compact('colocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('colocations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->hasActiveColocation()) {
            return redirect()->back()
                ->with('error', 'You already have an active colocation. You must leave or cancel it before creating a new one.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $colocation = Colocation::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'active',
        ]);

        // Automatically add the creator as owner
        $colocation->memberships()->create([
            'user_id' => auth()->id(),
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation created successfully. -)');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Colocation $colocation)
    {
        if (!$colocation->users()->where('user_id', auth()->id())->exists()) {
            abort(403);
        }

        $month = $request->get('month', now()->format('Y-m'));
        $expenses = $colocation->expenses();

        if ($request->has('month')) {
            $expenses = $expenses->where('date', 'like', "{$month}%");
        }

        $expenses = $expenses->get();

        $balances = $colocation->calculateBalances();
        $settlements = $colocation->getSettlements();

        return view('colocations.show', compact('colocation', 'balances', 'settlements', 'expenses', 'month'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Colocation $colocation)
    {
        $membership = $colocation->memberships()->where('user_id', auth()->id())->first();

        if (!$membership || $membership->role !== 'owner') {
            abort(403);
        }

        return view('colocations.edit', compact('colocation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Colocation $colocation)
    {
        $membership = $colocation->memberships()->where('user_id', auth()->id())->first();

        if (!$membership || $membership->role !== 'owner') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,cancelled',
        ]);

        $colocation->update($request->only('name', 'description', 'status'));

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation updated successfully. -)');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Colocation $colocation)
    {
        $membership = $colocation->memberships()->where('user_id', auth()->id())->first();

        if (!$membership || $membership->role !== 'owner') {
            abort(403);
        }

        $colocation->delete();

        return redirect()->route('colocations.index')
            ->with('success', 'Colocation deleted successfully. -)');
    }

   
}
