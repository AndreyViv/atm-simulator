<?php 

class Person {
    public $firsName;
    public function __construct(string $firsName, string $lastName, bool $admin = false) {
        $this->firstName = $firsName;
        $this->lastName = $lastName;
        $this->admin = $admin;
    }
}