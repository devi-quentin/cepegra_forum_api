<?php
include 'includes/config.php';
include 'includes/headers.php';
session_start();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') :
    $json = file_get_contents('php://input');
    $objectPOST = json_decode($json);
    // Repondre à a ticket
    if ($objectPOST->id_ticket != 'NULL') :
        $sql = sprintf("INSERT INTO tickets SET content='%s', id_user=%d, id_ticket=%s",
        strip_tags(addslashes($objectPOST->content)),
        $_SESSION['user'],
        strip_tags(addslashes($objectPOST->id_ticket)),
        );
        $responseJSON['response'] = "Réponse postée avec succès";
    // Ajouter un ticket
    else :
        $sql = sprintf("INSERT INTO tickets SET title='%s', content='%s', id_user=%d",
            strip_tags(addslashes($objectPOST->title)),
            strip_tags(addslashes($objectPOST->content)),
            $_SESSION['user'],
        );
        $responseJSON['response'] = "Ticket posté avec succès";
    endif;
    $connect->query($sql);
    echo $connect->error;
    $responseJSON['new_id'] = $connect->insert_id;
       
endif; //END POST

echo json_encode($responseJSON);