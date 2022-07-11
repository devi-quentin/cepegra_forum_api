<?php
include 'includes/config.php';
include 'includes/headers.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // TOUS LES TICKETS
    if(!isset($_GET['id'])) {
        $sql = "SELECT tickets.*, users.pseudo as author FROM tickets INNER JOIN users ON tickets.id_user = users.id";
        $result = $connect->query($sql);
        $response = $result->fetch_all(MYSQLI_ASSOC);
        $responseJSON['message'] = 'all tickets';
        $responseJSON['data'] = $response;
        $responseJSON['nb_hits'] = $result->num_rows;
    }
    // UN TICKET
    else {
        $sql = sprintf("SELECT tickets.*, users.pseudo as author FROM tickets INNER JOIN users ON tickets.id_user = users.id WHERE tickets.id = '%d'", $_GET['id']);
        $result = $connect->query($sql);
        $response = $result->fetch_all(MYSQLI_ASSOC);
        $responseJSON['message'] = 'One ticket';
        $responseJSON['data'] = $response;
    }
}

echo json_encode($responseJSON);