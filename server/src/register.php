<?php

function registerSchema(){

    $registerSchema = [
        'user'=>[required(), minLegth(4), alreadyExistsUser()],
        'password'=>[required(), minLegth(6)],
        'repeatedPass'=>[required(),doesntMacht()],
        'email'=> [required(), emailFormat(), alreadyExistsEmail()],
        'phone'=>[required(),isNumber(),minLegth(11)],
        'lastName'=>[required()],
        'firstName'=>[required()]
    ];

    return toSchema($registerSchema);

}

function addressSchema(){

    $addressSchema = [
        'address'=>[required()],
    ];

    return toSchema($addressSchema);

}

function registrationHandler(){

    $errors = validate(registerSchema(), $_POST);

    if (isError($errors)) {
        $encodedErrors = base64_encode(json_encode($errors));
        header('Location:/register?errors='.$encodedErrors . '&values='.base64_encode(json_encode($_POST)));
        return;
    }

    $pdo = getConnection();
    $statment = $pdo->prepare("
    INSERT INTO `users`(`user`, `password`, `email`,
     `phone`, `firstName`, `lastName`) 
    VALUES (?,?,?,?,?,?);"
    );
    $statment->execute([
    $_POST['user'],
    password_hash($_POST["password"], PASSWORD_DEFAULT),
    $_POST['email'],
    $_POST['phone'],
    $_POST['firstName'],
    $_POST['lastName'] 
    ]);

    header('Location:/register?info=registrationSuccessful');
}

function addNewAddressHandler(){

    $errors = validate(addressSchema(), $_POST);

    if (isError($errors)) {
         $encodedErrors = base64_encode(json_encode($errors));
        header('Location:/new-address?errors='.$encodedErrors . '&values='.base64_encode(json_encode($_POST)));
        return;
    }
    
    $pdo = getConnection();
    $statment = $pdo->prepare(
        "INSERT INTO `addresses`(`address`, `info`, `userId`) 
        VALUES (?,?,?)"
    );
  
    $statment->execute([
        $_POST['address'],
        $_POST['info'],
        $_SESSION['userId']
    ]);
    header('Location:/order');
}

?>