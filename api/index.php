<?php

require_once '../dbtest.php';

require '../Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/','getHome');
$app->get('/names','getAllNames');
$app->post('/names','createAName');
$app->get('/names/:name','getAName');
$app->put('/names/:id', 'updateAName');
$app->delete('/names/:name','deleteAName');


/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Array $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

/**
 * Verifying required params posted or not
 * @param Array $required_fields Fields to check
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}

/**
 * Checking if the API is responding
 */
function getHome() {
    $response = array();
    $response["error"] = false;
    $response["message"] = 'API Responding!!!';
    echoResponse(200, $response);
}

/**
 * Listing all names of the user database
 * method GET
 * url /names
 */
function getAllNames() {
    $response = array();
    $db = new DbHandler();
    // fetching all names
    $result = $db->getAllConstituentNames();
    $response["error"] = false;
    $response["names"] = array();
    // looping through result and preparing names array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $task["id"];
        $tmp["name"] = $task["name"];
        array_push($response["names"], $tmp);
    }
    echoResponse(200, $response);
}

/**
 * Creating new user in db
 * method POST
 * params - name
 * url - /names/
 */
function createAName(){
    $app = \Slim\Slim::getInstance();
    // check for required params
    verifyRequiredParams(array('name'));
    $response = array();
    $name = $app->request->post('name');
    $db = new DbHandler();
    // creating new name
    $name_id = $db->createAConstituentName($name);
    if ($name_id != NULL) {
        $response["error"] = false;
        $response["message"] = "Name created successfully";
        $response["name_id"] = $name_id;
        echoResponse(201, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "Failed to create name. Please try again";
        echoResponse(200, $response);
    }
}

/**
 * Checking a particular name
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function getAName($name) {
    $response = array();
    $db = new DbHandler();
    // fetching all users with a particular name
    $result = $db->checkAConstituentName($name);
    $response["error"] = false;
    $response["names"] = array();
    // looping through result and preparing names array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"] = $task["id"];
        $tmp["name"] = $task["name"];
        array_push($response["names"], $tmp);
    }
    echoResponse(200, $response);
}

/**
 * Updating an existing name
 * method PUT
 * @param Number $id id of the name in Databse
 * params name,updatename
 * url - /names/:id
 */
function updateAName($id) {
    $app = \Slim\Slim::getInstance();
    // check for required params
    verifyRequiredParams(array('name','updatename'));
    $response = array();
    parse_str($app->request()->getBody(), $request_params);
    $name = $request_params['name'];
    $updatename = $request_params['updatename'];
    $db = new DbHandler();
    // updating name
    $num_rows = $db->updateAConstituentName($name,$updatename);
    if ($num_rows != NULL) {
        $response["error"] = false;
        $response["message"] = "Name updated successfully";
        $response["num_rows"] = $num_rows;
        echoResponse(201, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "Failed to update name. Please try again";
        echoResponse(200, $response);
    }
}

/**
 * Deleting an existing name
 * method DELETE
 * @param String $name Name of the user to delete from database
 * params none
 * url - /names/:id
 */
function deleteAName($name) {
    $response = array();
    $db = new DbHandler();
    $num_rows = $db->deleteAConstituentName($name);
    $response["error"] = false;
    $response["message"] = 'Deleted the name';
    $response["num_rows"] = $num_rows;
    echoResponse(200, $response);
}

/*
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: Origin, HTTP_X_REQUESTED_WITH, Content-Type, Accept, Authorization");
*/
$app->run();
?>