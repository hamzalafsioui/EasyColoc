<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\User;

class BalanceCalculator
{
    /**
     * Calculate the balance for each member in the colocation.
     *
     * @param Colocation $colocation
     * @return array
     */
    public function calculate(Colocation $colocation): array
    {
        $members = $colocation->users()->get();
        $numMembers = $members->count();

        if ($numMembers === 0) {
            return [];
        }

        $totalExpenses = $colocation->expenses()->sum('amount');
        $sharePerMember = $totalExpenses / $numMembers;

        $balances = [];

        foreach ($members as $member) {
            $amountPaid = $colocation->expenses()->where('paid_by', $member->id)->sum('amount');

            $totalSent = $colocation->payments()
                ->where('from_user_id', $member->id)
                ->sum('amount');

            $totalReceived = $colocation->payments()
                ->where('to_user_id', $member->id)
                ->sum('amount');

            $balance = ($amountPaid - $sharePerMember) + ($totalSent - $totalReceived);

            $balances[$member->id] = [
                'user' => $member,
                'amount_paid' => $amountPaid,
                'total_sent' => $totalSent,
                'total_received' => $totalReceived,
                'share' => $sharePerMember,
                'balance' => $balance,
            ];
        }

        return $balances;
    }
}
