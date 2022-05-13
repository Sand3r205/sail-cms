<?php
    include __DIR__ . '/../includes/UserDatabase.class.php';

    $input = json_decode(file_get_contents('php://input'));

    if($input->username == null || $input->password == null) {
        echo json_encode(false);
        return;
    }

    $userDB = new UserDatabase();
    $result = $userDB->authenticate($input->username, $input->password);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(false);
    }
?>