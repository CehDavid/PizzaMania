<?php

require './router.php';
require './auth.php';
require './cart.php';
require './validator.php';
require './register.php';

session_start();  

$method = $_SERVER["REQUEST_METHOD"];
$parsed = parse_url($_SERVER["REQUEST_URI"]);
$path = $parsed["path"];

$routes = [
    ['GET','/','productListHandler'],
    ['GET', '/about-us','infoHandler'],
    ['GET','/register','registerHandler'],
    ['GET','/order','orderHandler'],
    ['GET','/new-address','newAddressHandler'],
    ['GET','/logout','logoutHandler'],
    ['POST', '/login', 'loginHandler'],
    ['POST','/add-to-cart','manageCart'],
    ['POST','/remove-from-cart','removeFromCart'],
    ['POST','/registration','registrationHandler'],
    ['POST','/add-new-address','addNewAddressHandler'],
    ['POST','/submit-order','submitOrderHandler']
];

$dispatch = registerRoutes($routes);
$matchedRoute = $dispatch($method, $path);
$handlerFunction = $matchedRoute['handler'];
$handlerFunction($matchedRoute['vars']);

function productListHandler(){

    $pdo = getConnection();
    $stmt = $pdo->prepare('SELECT * FROM `productTypes`');
    $stmt->execute();
    $productTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
    foreach($productTypes as $index => $type){
        
        $stmt = $pdo->prepare('SELECT * FROM `products` WHERE isActive=1 AND typeId=?');
        $stmt->execute([$type['id']]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $productTypes[$index]['products']=$products;

    }
    echo render("wrapper.phtml",[
        'content'=> render('productList.phtml',[
            'productTypes'=>$productTypes
        ]),
        'profil'=>render('profil.phtml',[
            'isAuthorized' => isLoggedIn(),
            'user' => $_SESSION['user'] ?? '',
            'info'=> $_GET['info'] ?? ''
        ]),
        'cart' => render('cart.phtml',[
        ]),
        
    ]);
    
}
function infoHandler(){

    $timeToOpen = strtotime('10:00:00');
    $timeToClose = strtotime('22:00:00');

    $weAre='close';
    
    if(time()>$timeToOpen && time()<$timeToClose){
        $weAre='open';
    }
 
    echo render('wrapper.phtml',[
        'content' =>render('info.phtml',[
            'weAre'=>$weAre
        ]),
        'profil'=>render('profil.phtml',[
            'isAuthorized' => isLoggedIn(),
            'user' => $_SESSION['user'] ?? '',
            'info'=> $_GET['info'] ?? ''
        ]),
        'cart' => render('cart.phtml',[
        ]),

    ]);
}
function registerHandler(){

    if(!isLoggedIn()){

        $errors = json_decode(base64_decode($_GET['errors'] ?? ""), true);
        $errorMessages = getErrorMessages(registerSchema(), $errors ?? []);

        echo render('wrapper.phtml', [
            'content'=> render('registration.phtml',[
                'info'=> $_GET['info'] ?? '',
                "errorMessages" => $errorMessages,
                'values' => json_decode(base64_decode($_GET['values'] ?? ''), true),
            ]),
            'profil'=>render('profil.phtml',[
                'isAuthorized' => false,
                'user' => $_SESSION['user'] ?? '',
                'info'=> $_GET['info'] ?? ''
            ]),
            'cart' => render('cart.phtml',[
            ])
        ]);
    } 
}
function notFoundHandler(){
    echo render('wrapper.phtml', [
        'content'=> render('404.phtml', [

        ]),
        'profil'=>render('profil.phtml',[
            'isAuthorized' => isLoggedIn(),
            'user' => $_SESSION['user'] ?? '',
            'info'=> $_GET['info'] ?? ''
        ]),
        'cart' => render('cart.phtml',[

        ]),
        
    ]);
}
function render($filePath, $params = []): string{
    
    ob_start();
    require __DIR__ . "/views/" . $filePath;   
    return ob_get_clean();
}
function getConnection(){

    return new PDO(
         'mysql:host=' . $_SERVER['DB_HOST'] . ';dbname=' . $_SERVER['DB_NAME'],
         $_SERVER['DB_USER'],
         $_SERVER['DB_PASSWORD']
     );
 
}
function orderHandler(){

    if(isLoggedIn()){

        $pdo = getConnection();
        $statement = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $statement->execute([$_SESSION['userId']]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        
        $statement = $pdo->prepare("SELECT * FROM addresses WHERE userId = ?");
        $statement->execute([$_SESSION['userId']]);
        $addresses = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        echo render('wrapper.phtml',[
            'content'=> render('order.phtml',[
                'user'=> $user,
                'addresses'=> $addresses,
                'info'=> $_GET['info'] ?? ''
            ]),
            'profil'=> render('profil.phtml',[
                'isAuthorized' => true,
                'user' => $_SESSION['user'] ?? '',
                'info'=> $_GET['info'] ?? ''
            ]),
            'cart'=> '',
            
        ]);
    }
    else{
        echo render('wrapper.phtml',[
            'content'=> render('alert.phtml',[
            ]),
            'profil'=> render('profil.phtml',[
                'isAuthorized' => false,
                'user' => $_SESSION['user'] ?? '',
                'info'=> $_GET['info'] ?? ''   
            ]),
            'cart'=> render('cart.phtml',[

            ]),
        ]);
    }
   
}
function newAddressHandler(){

    $errors = json_decode(base64_decode($_GET['errors'] ?? ""), true);
    $errorMessages = getErrorMessages(addressSchema(), $errors ?? []);
    
    echo render('wrapper.phtml',[
        
        'content'=> render('newAddressForm.phtml',[
            "errorMessages" => $errorMessages,
            'values' => json_decode(base64_decode($_GET['values'] ?? ''), true),
        ]),
        'profil'=> render('profil.phtml',[
            'isAuthorized' => true,
            'user' => $_SESSION['user'] ?? '',
            'info'=> $_GET['info'] ?? ''
        ]),
        'cart'=>  render('cart.phtml',[

        ]),
        
    ]);
}
function logoutHandler(){
    
    $params = session_get_cookie_params();

    setcookie(session_name(),  '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
    
    session_destroy();

    header('Location:/');
}
function getPath($url){
    
    $path = parse_url($url);

    return $path['path'];
}
function submitOrderHandler(){

    if(!isset($_POST['addressId'])){
        
        header('Location:'. getPath($_SERVER['HTTP_REFERER']) .'?info=noAddress');
    }
    if(!isset($_POST['paymentOptions'])){
        
        header('Location:'. getPath($_SERVER['HTTP_REFERER']) .'?info=noPaymentOption');
    }

    if(count($_SESSION['cart'])<1){

        header('Location:'. getPath($_SERVER['HTTP_REFERER']) .'?info=emtyCart');
    }

    $orderId = uniqid();

    $pdo = getConnection();
    $statment = $pdo->prepare("
    INSERT INTO `orders`(`id`, `addressId`, `comment`,
     `payment`, `date`, `status`) 
    VALUES (?,?,?,?,?,?);"
    );
    $statment->execute([
        $orderId,
        $_POST['addressId'],
        $_POST['comment'],
        $_POST['paymentOptions'],
        date("Y-m-d h:i"),
        'Elküldve'
    ]);

    foreach ($_SESSION['cart'] as $product){
        $statment = $pdo->prepare("
            INSERT INTO `orderDetails`(`orderId`, `productId`) VALUES (?,?);"
        );
        $statment->execute([$orderId, $product['id']]);
    }

    unset($_SESSION['cart']);    
    
    echo render('wrapper.phtml',[
        'content' =>render('feedback.phtml',[
        ]),
        'profil'=>render('profil.phtml',[
            'isAuthorized' => isLoggedIn(),
            'user' => $_SESSION['user'] ?? '',
            'info'=> $_GET['info'] ?? ''
        ]),
        'cart' => render('cart.phtml',[
        ])

    ]);

}
?>