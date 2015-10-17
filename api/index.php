<?php

require_once '../dbtest.php';

require '../Slim/Slim.php';

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
 * Verifying required params posted or not
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
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Creating new USER in db
 * method POST
 * params - name
 * url - /names/
 */
$app->post('/names', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('name'));
    $response = array();
    $name = $app->request->post('name');

    $db = new DbHandler();

    // creating new name
    $name_id = $db->createAConstituentName($name);

    if ($name_id != NULL) {
        $response["error"] = false;
        $response["message"] = "Task created successfully";
        $response["name_id"] = $name_id;
        echoRespnse(201, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "Failed to create task. Please try again";
        echoRespnse(200, $response);
    }
});


/**
 * Checking a particuar name
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
 * Updating existing name
 * method PUT
 * params name
 * url - /names/:id
 */
$app->put('/names/:id', function($id) use($app) {
    echoRespnse(200, $id);
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

$app->delete('/names/:name','deleteName');
function deleteName($name) {
    $response = array();
    $db = new DbHandler();

    // fetching all user tasks
    $result = $db->deleteAConstituentName($name);
    $response["error"] = false;
    $response["message"] = 'Deleted the name';
    echoRespnse(200, $response);
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: Origin, HTTP_X_REQUESTED_WITH, Content-Type, Accept, Authorization");
$app->run();

?>