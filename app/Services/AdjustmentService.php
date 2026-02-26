<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\Payment;
use App\Models\User;

class AdjustmentService
{
    /**
     * Impute a member debt to the owner.
     *
     * @param Colocation $colocation
     * @param User $owner
     * @param User $member
     * @param float $debtAmount
     * @return Payment
     */
    public function imputeDebtToOwner(Colocation $colocation, User $owner, User $member, float $debtAmount)
    {
        return Payment::create([
            'colocation_id' => $colocation->id,
            'from_user_id' => $owner->id, // from owner
            'to_user_id' => $member->id, // to member
            'amount' => $debtAmount,
            'paid_at' => now(),
        ]);
    }
}
