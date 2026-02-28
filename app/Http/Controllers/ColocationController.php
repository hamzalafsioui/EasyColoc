<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Membership;
use App\Models\User;
use App\Http\Requests\StoreColocationRequest;
use App\Http\Requests\UpdateColocationRequest;
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
    public function store(StoreColocationRequest $request)
    {
        if (!auth()->user()->is_admin && auth()->user()->hasActiveColocation()) {
            return redirect()->back()
                ->with('error', 'You already have an active colocation. You must leave or cancel it before creating a new one.');
        }

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

        // Create default category
        $colocation->categories()->create([
            'name' => 'General',
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
    public function update(UpdateColocationRequest $request, Colocation $colocation)
    {
        $membership = $colocation->memberships()->where('user_id', auth()->id())->first();

        if (!$membership || $membership->role !== 'owner') {
            abort(403);
        }

        if ($request->status === 'cancelled') {
            $balances = $colocation->calculateBalances();
            $nonZeroBalances = array_filter($balances, fn($b) => round($b['balance'], 2) != 0);

            if (!empty($nonZeroBalances)) {
                return redirect()->back()
                    ->with('error', 'Cannot cancel colocation until all balances are settled (0.00).');
            }
        }

        $colocation->update($request->only('name', 'description', 'status'));

        if ($colocation->status === 'cancelled') {
            // Update reputation (+1) for all members if cancelled via update
            foreach ($colocation->memberships()->whereNull('left_at')->get() as $m) {
                $m->user->updateReputation(1);
            }
        }
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

    /**
     * Cancel the colocation.
     */
    public function cancel(Colocation $colocation)
    {
        $membership = $colocation->memberships()->where('user_id', auth()->id())->first();

        if (!$membership || $membership->role !== 'owner') {
            abort(403);
        }

        $balances = $colocation->calculateBalances();
        $nonZeroBalances = array_filter($balances, fn($b) => round($b['balance'], 2) != 0);

        if (!empty($nonZeroBalances)) {
            return redirect()->back()
                ->with('error', 'Cannot cancel colocation until all balances are settled (0.00).');
        }

        // Update reputation (+1) for all members before cancelling
        // Since balances are 0, they all get +1
        foreach ($colocation->memberships()->whereNull('left_at')->get() as $m) {
            $m->user->updateReputation(1);
        }

        $colocation->update(['status' => 'cancelled']);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation has been cancelled and reputation updated. -)');
    }

    public function balances(Colocation $colocation)
    {
        if (!$colocation->users()->where('user_id', auth()->id())->exists()) {
            abort(403);
        }

        $balances = $colocation->calculateBalances();
        $settlements = $colocation->getSettlements();

        return view('colocations.balances', compact('colocation', 'balances', 'settlements'));
    }
}
