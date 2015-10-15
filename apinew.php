<?php

require_once 'dbtest.php';

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/', function() {
    echo("Running");
});

/**
 * Listing all tasks of particual user
 * method GET
 * url /tasks
 */
$app->get('/names', function() {
    $response = array();
    $db = new DbHandler();

    // fetching all user tasks
    $result = $db->getAllConstituentNames();

    $response["error"] = false;
    $response["names"] = array();

    // looping through result and preparing tasks array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $task["id"];
        $tmp["name"] = $task["name"];
        array_push($response["names"], $tmp);
    }
    echoRespnse(200, $response);
});

/**
 * Listing all tasks of particual user
 * method GET
 * url /tasks
 */
$app->get('/names/:name', function($name) {
    $response = array();
    $db = new DbHandler();

    // fetching all user tasks
    $result = $db->checkAConstituentName($name);

    $response["error"] = false;
    $response["names"] = array();

    // looping through result and preparing tasks array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $task["id"];
        $tmp["name"] = $task["name"];
        array_push($response["names"], $tmp);
    }
    echoRespnse(200, $response);
});


/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Array $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}
header('Access-Control-Allow-Origin: *');
$app->run();

?>