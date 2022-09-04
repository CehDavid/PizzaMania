<?php

function isError ($errors) {
    return count(array_reduce(array_values($errors), 'array_merge', []));
}

function getErrorMessages($schema, $errors){
    $validatorNameToMessage = [
        "required" => fn () => "A mező megadása kötelező.",
        "email"=> fn() => "Mező értéknek érvényes email címnek kell lennie. '" . ($value ?? " ") . "' lett megadva.",
        "minLength" => fn ($value, $param) => "A mezőnek legalább $param"." karakterből kell áljon. Csak ". strlen($value) . " karakter lett megadva.",
        "alreadeyExistsEmail"=> fn()=>"Az e-mail cím már regisztrálva van",
        'alreadeyExistsUser'=>fn()=> "A felhasználónév már foglalt",
        "doesntMacht"=> fn()=> "A jelszavak nem egyeznek",
        "isNumber"=>fn() => "A telefonszám csak számokat tartalmazhat", 
     ];

    $ret = [];

    foreach($errors as $fieldName => $errorsForField) {

        foreach ($errorsForField as $err) {
            $toMessageFunction = $validatorNameToMessage[$err['validatorName']];
            $schemaForField = $schema[$fieldName];
            $ret[$fieldName][] = $toMessageFunction($err['value'], $schemaForField[$err['validatorName']]['params']);
        }
    }
    return $ret;
}

function required(){
    
    return [
        "validatorName" => "required",
        "validatorFn" => function ($input) {
            return (bool)$input;
        },
        "params" => null
    ];
}
function emailFormat(){
      return [
        "validatorName" => "email",
        "validatorFn" => fn ($input) => filter_var($input, FILTER_VALIDATE_EMAIL),
        "params" => null
    ];
}
function minLegth($length){
    
    return [
        "validatorName" => "minLength",
        "validatorFn" => fn ($input) => strlen($input) >= $length,
        "params" => $length
    ];
}
function alreadyExistsEmail(){

    return [
        'validatorName'=>'alreadeyExistsEmail',
        'validatorFn'=> function($input) {
            $pdo = getConnection();
            $stmt = $pdo->prepare('SELECT * FROM `users` WHERE email=?');
            $stmt->execute([$input]);
            $user= $stmt->fetch();
            return (!(bool)$user);
        } ,
        'params'=> null
    ];
}
function alreadyExistsUser(){

    return [
        'validatorName'=>'alreadeyExistsUser',
        'validatorFn'=> function($input) {
            $pdo = getConnection();
            $stmt = $pdo->prepare('SELECT * FROM `users` WHERE user=?');
            $stmt->execute([$input]);
            $user= $stmt->fetch();
            return (!(bool)$user);
        } ,
        'params'=> null
    ];
}
function doesntMacht(){
    return [
        "validatorName" => "doesntMacht",
        "validatorFn" => fn ($input) => $input===$_POST['password'] ?? '',
        "params" => null
    ];
}
function isNumber(){
    return [
        "validatorName" => "isNumber",
        "validatorFn" => fn ($input) => is_numeric($input),
        "params" => null
    ];
}

function toSchema($items) {

    $ret = [];
    foreach ($items as $key => $value) {
        $ret[$key] = array_reduce($value, fn ($acc, $item) => array_merge($acc, [$item['validatorName'] => $item]), []);
    }
    return $ret;
}

function validate($schema,$body){
    
    $fieldNames = array_keys($schema);
    
   $ret = array_reduce(
        $fieldNames, 
        fn ($collector, $fieldName) => array_merge($collector, [$fieldName => []]),
        []

    );
    
    foreach ($fieldNames as $fieldName) {
        $validators = $schema[$fieldName];
        foreach ($validators as $validator) {
            $validatorFn = $validator['validatorFn'];
            $isFieldValid = $validatorFn($body[$fieldName] ?? null);
            if (!$isFieldValid) {
                $ret[$fieldName][] = [
                    'validatorName' => $validator['validatorName'],
                    'value' => $body[$fieldName] ?? null
                ];
            }
        }
    }

    return $ret;
}

?>