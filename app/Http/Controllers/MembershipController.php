<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\User;
use App\Services\AdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MembershipController extends Controller
{
    private AdjustmentService $adjustmentService;

    public function __construct(AdjustmentService $adjustmentService)
    {
        $this->adjustmentService = $adjustmentService;
    }

    /**
     * Leave the colocation.
     */
    public function leave(Colocation $colocation)
    {
        $membership = $colocation->memberships()->where('user_id', auth()->id())->whereNull('left_at')->first();

        if (!$membership || $membership->role === 'owner') {
            abort(403, 'Owners cannot leave. They must transfer ownership or cancel the colocation. (-_-)');
        }

        $balances = $colocation->calculateBalances();
        $userBalance = $balances[auth()->id()]['balance'] ?? 0;

        // Apply reputation logic
        auth()->user()->updateReputation($userBalance >= 0 ? 1 : -1);

        $membership->update(['left_at' => now()]);

        return redirect()->route('colocations.index')
            ->with('success', 'You have left the colocation. -)');
    }

    /**
     * Remove a member from the colocation.
     */
    public function remove(Colocation $colocation, User $user)
    {
        $ownerMembership = $colocation->memberships()
            ->where('user_id', auth()->id())
            ->whereNull('left_at')
            ->first();

        if (!$ownerMembership || $ownerMembership->role !== 'owner') {
            abort(403);
        }

        if ($colocation->status !== 'active') {
            abort(403);
        }

        $memberMembership = $colocation->memberships()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        if (!$memberMembership || $memberMembership->role === 'owner') {
            abort(403);
        }

        DB::transaction(function () use ($colocation, $user, $memberMembership) {
            $balances = $colocation->calculateBalances();
            $userBalance = $balances[$user->id]['balance'] ?? 0;

            $user->updateReputation($userBalance >= 0 ? 1 : -1);

            if ($userBalance < 0) {
                $this->adjustmentService->imputeDebtToOwner(
                    $colocation,
                    auth()->user(),
                    $user,
                    abs($userBalance)
                );
            }

            $memberMembership->update(['left_at' => now()]);
        });

        return redirect()->route('colocations.show', $colocation)
            ->with('success', "Member {$user->name} has been removed successfully.");
    }
}
