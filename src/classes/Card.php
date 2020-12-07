<?php 

class Card {
    private $amount;
    private $pin;
    
    public static $holder;
    public static $number;


    public function __construct(string $number, string $pin, Person $holder, float $amount = 0.00) {
        $this->number = $number;
        $this->pin = $pin;
        $this->holder = $holder;
        $this->amount = $amount;
    }

    public function checkPin($pin) {
        return $this->pin == $pin;
    }

    public function changePin($newPin) {
        $this->pin = $newPin;
    }

    public function getPin() {
        return $this->pin;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getHolder() {
        return $this->holder;
    }

    public function topUpCard(int $amount) {
        $this->amount += $amount;
    }

    public function cashOutCard(int $amount) {
        $this->amount -= $amount;
    }
}