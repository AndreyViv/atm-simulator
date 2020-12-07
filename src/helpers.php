<?php


function sendJSON($obj) {
    header('Content-type: application/json');
    echo json_encode($obj);
}

function errorResponse(string $messege) {
    return array(
        'status' => 'error',
        'messege' => $messege
    );
}

function successResponse(array $data) {
    return array(
        'status' => 'success',
        'data' => $data
    );
}