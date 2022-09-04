<?php

function manageCart(){
        
    if(isset($_SESSION['cart'])){

        $count = count($_SESSION['cart']);              
        $_SESSION['cart'][$count]= array('id'=>$_POST['id'], 'name'=>$_POST['name'],'price'=>$_POST['price']);
    }
    else{
        $_SESSION['cart'][0]= array('id'=>$_POST['id'], 'name'=>$_POST['name'],'price'=>$_POST['price']);
    }
    header('Location:'.$_SERVER['HTTP_REFERER']);
}

function removeFromCart(){
    
    
    foreach($_SESSION['cart'] as $key=>$value){

        if($value['name']==$_POST['name']){
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart']=array_values($_SESSION['cart']);
        }
    }
    header('Location: '. $_SERVER['HTTP_REFERER']);
}
?>