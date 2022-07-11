<?php
include 'includes/config.php';
include 'includes/headers.php';
session_start();
// -----------
// DECONNEXION
// -----------
if ( isset($_GET['logout']) ) :
    unset($_SESSION['user']);
    unset($_SESSION['token']);
    unset($_SESSION['expiration']);

    $response['response'] = "déconnexion";
    $response['time'] = date('Y-m-d,H:i:s');
    $response['code'] = 200;
    echo json_encode($response);
    exit;
endif;

// ---------
// CONNEXION
// ---------
$json = file_get_contents('php://input');
$arrayPOST = json_decode($json, true);

// SI CHAMPS VIDE
if ( !isset($arrayPOST['login']) OR !isset($arrayPOST['password'])) :
    $response['message'] = "Il manque le login et/ou le mot de passe";
    $response['code'] = 500;
    $response['ok'] = false;
else:
    // CONNEXION AVEC PSEUDO
    $sql = sprintf("SELECT * FROM users WHERE pseudo = '%s' AND password = '%s'",
        $arrayPOST['login'],
        $arrayPOST['password']
    );
    $result = $connect->query($sql);
    echo $connect->error;
    $nb_rows =  $result->num_rows;

    // SI PAS, CONNEXION AVEC EMAIL
    if($nb_rows == 0) :
        $sql = sprintf("SELECT * FROM users WHERE email = '%s' AND password = '%s'",
            $arrayPOST['login'],
            $arrayPOST['password']
        );
        $result = $connect->query($sql);
        echo $connect->error;
        $nb_rows =  $result->num_rows;
    endif;

    // Si aucune correspondances
    if($nb_rows == 0) :
        $response['message'] = 'Login et/ou mot de passe incorrecte(s)';
        $response['code'] = 403;
        $response['ok'] = false;

    // Si correspondances
    else :
        $row = $result->fetch_all(MYSQLI_ASSOC);
        $row = $row[0];
        $_SESSION['user'] = $row['id'];
        $_SESSION['token'] = md5($row['email'].time());
        $_SESSION['expiration'] = time() + 10 * 60; // 10 minutes
        $response['response'] = "Connecté";
        $response['token'] = $_SESSION['token'];
        $response['ok'] = true;

    endif;
endif;

$response['code'] = ( isset($response['code']) ) ? $response['code'] : 200;

echo json_encode($response);
exit;