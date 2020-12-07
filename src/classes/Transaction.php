<?php 

class Transaction {
    private $success = false;
    
    public function __construct(int $amount) {
        $this->amount = $amount;
        $this->notes = [];
    }

    public function isSucceed() {
        return $this->success;
    }

    public function addNotes(int $denom, int $count) {
        $amount = $denom * $count;
        
        $this->notes[$denom] = $count;
        $this->amount -= $amount;
        $this->successAmount += $amount;

        if ($this->amount == 0) {
            $this->success = true;
        }
    }

    public function totalNotesCount() {
        $count = 0;

        foreach($this->notes as $k => $v) {
            $count += $v;
        }

        return $count;
    }
}