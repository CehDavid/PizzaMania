<?php

function loginHandler(){
    
    $pdo = getConnection();
    $statement = $pdo->prepare("SELECT * FROM users WHERE user = ?");
    $statement->execute([$_POST["user"]]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if(!$user) {
        header('Location:'. getPath($_SERVER['HTTP_REFERER']) .'?info=invalidCredentials');
        return;
    }

    $isVerified = password_verify($_POST['psw'], $user["password"]);

    if(!$isVerified){
        header('Location:'. getPath($_SERVER['HTTP_REFERER']) .'?info=invalidCredentials');
        return;
    }
   
    $_SESSION['userId'] = $user['id'];
    $_SESSION['user']=$user['user']; 
    header('Location:/'); 
}

function isLoggedIn():bool {

    if(!isset($_COOKIE[session_name()])){
        return false;
    }
    if (!isset($_SESSION['userId'])) {
        return false;
    }

    return true;

}

?>