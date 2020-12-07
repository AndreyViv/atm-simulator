<?php

include_once 'src/classes/Atm.php';

$notes = array (
    100 => 1000,
    500 => 1000,
    20 => 40,
    10 => 200,
    5 => 1000
);

$atm = new Atm($notes, true);
$atm->run();

