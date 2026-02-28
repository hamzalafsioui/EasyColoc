<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Http\Requests\StorePaymentRequest;

class PaymentController extends Controller
{
    /**
     * Store a newly created payment in storage.
     */
    public function store(StorePaymentRequest $request, Colocation $colocation)
    {
        $validated = $request->validated();

        $colocation->payments()->create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $validated['to_user_id'],
            'amount' => $validated['amount'],
            'paid_at' => now(),
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Payment marked successfully. -)');
    }
}
