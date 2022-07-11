<?php
include 'includes/config.php';
include 'includes/headers.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // TOUS LES USERS
    if(!isset($_GET['id'])) {
        $sql = "SELECT * FROM users";
        $result = $connect->query($sql);
        $response = $result->fetch_all(MYSQLI_ASSOC);
        $responseJSON['message'] = 'all users';
        $responseJSON['data'] = $response;
        $responseJSON['nb_hits'] = $result->num_rows;
    }
    // UN USER
    else {
        $sql = sprintf("SELECT * FROM users WHERE id = '%d'",
        $_GET['id']);
        $result = $connect->query($sql);
        $response = $result->fetch_all(MYSQLI_ASSOC);
        $responseJSON['message'] = 'Onse user';
        $responseJSON['data'] = $response;
    }
}

echo json_encode($responseJSON);