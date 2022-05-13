<?php
    include __DIR__ . '/../includes/UserDatabase.class.php';

    $input = json_decode(file_get_contents('php://input'));

    if($input->token == null) {
        echo json_encode(false);
        return;
    }

    $userDB = new UserDatabase();
    $userDB->logoutByToken($input->token);

    echo json_encode(true);
?>