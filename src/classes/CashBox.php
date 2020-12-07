<?php 

class CashBox {
    public function __construct(int $denom, int $count) {
        $this->denom = $denom;
        $this->count = $count;
    }

    public function getAmount() {
        return $this->denom * $this->count;
    }

    public function addNotes(int $count) {
        $this->count += $count;
    }

    public function removeNotes(int $count) {
        $this->count -= $count;
    }

    public function checkNotes(Transaction $transaction) {
        $count = floor($transaction->amount / $this->denom);
        
        if ($count > 0 && $count <= $this->count) {
            $transaction->addNotes($this->denom, $count);
        }
    }
}