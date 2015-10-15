<?php
$method = isset($method)?$method:$_SERVER['REQUEST_METHOD'];
$request = isset($request)?$request:(isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'');
$get = isset($get)?$get:$_GET;
$post = isset($post)?$post:'php://input';
$request = explode('/', trim($request,'/'));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Page is called via a POST method";
    if(!isset($_POST['name']))
    {
        echo("The name parameter is not posted in this form...");
    }
    else
    {
        echo 'Hello Constituent' . htmlspecialchars($_POST["name"]) . '!';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "Page is called via a GET method";
    if(!empty($_GET["name"]))
    {
        echo("The name parameter is not posted in this form...");
    }
    else
    {
        echo 'Searching the Constituent' . htmlspecialchars($_POST["name"]) . '!';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    echo "Page is called via a PUT method";
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    echo "Page is called via a DELETE method";
}
?>