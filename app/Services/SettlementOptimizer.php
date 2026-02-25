<?php

namespace App\Services;

use App\Models\Colocation;

class SettlementOptimizer
{
    public function __construct(
        private BalanceCalculator $calculator
    ) {}
    /**
     * Suggest the minimum number of transactions to settle debts.
     *
     * @param Colocation $colocation
     * @return array
     */
    public function optimize(Colocation $colocation): array
    {
        // $calculator = new BalanceCalculator();
        $balances = $this->calculator->calculate($colocation);

        $debtors = [];
        $creditors = [];

        foreach ($balances as $userId => $data) {
            $balance = round($data['balance'], 2);
            if ($balance < 0) {
                $debtors[] = ['id' => $userId, 'name' => $data['user']->name, 'amount' => abs($balance)];
            } elseif ($balance > 0) {
                $creditors[] = ['id' => $userId, 'name' => $data['user']->name, 'amount' => $balance];
            }
        }

        // Sort both by amount descending to be match
        usort($debtors, fn($a, $b) => $b['amount'] <=> $a['amount']);
        usort($creditors, fn($a, $b) => $b['amount'] <=> $a['amount']);

        $settlements = [];
        $i = 0; // current debtor index
        $j = 0; // current creditor index

        while ($i < count($debtors) && $j < count($creditors)) {
            $debtor = &$debtors[$i];
            $creditor = &$creditors[$j];

            $amountToSettle = min($debtor['amount'], $creditor['amount']);

            if ($amountToSettle > 0) {
                $settlements[] = [
                    'from' => $debtor['name'],
                    'from_id' => $debtor['id'],
                    'to' => $creditor['name'],
                    'to_id' => $creditor['id'],
                    'amount' => $amountToSettle,
                ];

                $debtor['amount'] -= $amountToSettle;
                $creditor['amount'] -= $amountToSettle;
            }

            if (round($debtor['amount'], 2) <= 0) {
                $i++;
            }
            if (round($creditor['amount'], 2) <= 0) {
                $j++;
            }
        }

        return $settlements;
    }
}
