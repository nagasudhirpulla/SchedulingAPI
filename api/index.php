<?php

require_once '../dbtest.php';

require '../Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/','getHome');
$app->get('/names','getAllNames');
$app->post('/names','createAName');
$app->get('/names/:name','getAName');
$app->put('/names', 'updateAName');
$app->delete('/names/:name','deleteAName');
$app->get('/generators','getAllGenerators');
$app->post('/generators','createAGenerator');
$app->get('/generators/:name','getAGenerator');
$app->delete('/generators/:name','deleteAGenerator');
$app->post('/generators/:name','addAGeneratorShareData');
$app->get('/generatorshares/:genID','getAGeneratorShares');
$app->delete('/generatorshares/:genID','deleteAGeneratorShares');
$app->post('/generatorshares/:genID','updateAGeneratorShares');
$app->get('/generatorRevisionNumbers/:genID','getAGenRevisionNumbers');
$app->get('/generatorRevisionNumbers/:date/:genID','getADateGenRevisionNumbers');
$app->get('/revisions/:revID','getARevision');
$app->get('/revisions/:genID/:revID','getAGenRevision');
$app->get('/revisions/:date/:genID/:revID','getADateGenRevision');
$app->put('/revisions/:revID','updateARevision');
$app->put('/revisions/:date/:revID','updateADateRevision');
$app->post('/revisions/:genID','createAGenRev');
$app->post('/revisions/:date/:genID','createADateGenRev');
$app->delete('/revisions/:revID','deleteARevision');
$app->delete('/revisions/:date/:revID','deleteADateRevision');
//$app->get('/revisions/count','getRevisionCount');

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
    $app = \Slim\Slim::getInstance();
    $request_params = json_decode($app->request()->getBody());
    foreach ($required_fields as $field) {
        if (!isset($request_params->$field) || strlen(trim($request_params->$field)) <= 0) {
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
    $name = json_decode($app->request()->getBody())->name;
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
function updateAName() {
    $app = \Slim\Slim::getInstance();
    // check for required params
    verifyRequiredParams(array('name','updatename'));
    $response = array();
    $request_params = json_decode($app->request()->getBody());
    $name = $request_params->name;
    $updatename = $request_params->updatename;
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

function getAGenRevisionNumbers($genID){
    $response = array();
    $db = new DbHandler();
    // fetching all users with a particular name
    $result = $db->getAGenRevisionNumbersData($genID);
    if(gettype($result)=="string") {
        $response["error"] = true;
        $response["message"]=$result;
    }
    else{
        $response["error"] = false;
        $response["revisionNumbers"] = array();
        // looping through result and preparing names array
        while ($task = $result->fetch_assoc()) {
            array_push($response["revisionNumbers"], $task["id"]);
        }
    }
    echoResponse(200, $response);
}

function getADateGenRevisionNumbers($date, $genID){
    $response = array();
    $db = new DbHandler();
    // fetching all users with a particular name
    $result = $db->getADateGenRevisionNumbersData($date, $genID);
    if(gettype($result)=="string") {
        $response["error"] = true;
        $response["message"]=$result;
    }
    else{
        $response["error"] = false;
        $response["revisionNumbers"] = array();
        // looping through result and preparing names array
        while ($task = $result->fetch_assoc()) {
            array_push($response["revisionNumbers"], $task["id"]);
        }
    }
    echoResponse(200, $response);
}

/**
 * Listing all names of the generator database
 * method GET
 * url /names
 */
function getAllGenerators() {
    $response = array();
    $db = new DbHandler();
    // fetching all names
    $result = $db->getAllGeneratorNames();
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
 * Checking a particular name
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function getAGenerator($name) {
    $response = array();
    $db = new DbHandler();
    // fetching all users with a particular name
    $result = $db->fetchAGeneratorName($name);
    $response["error"] = false;
    // looping through result and preparing names array
    while ($task = $result->fetch_assoc()) {
        $response["id"] = $task["id"];
        $response["name"] = $task["name"];
        $response["ramp"] = $task["ramp"];
        $response["dc"] = $task["dc"];
        $response["onbar"] = $task["onbar"];
    }
    echoResponse(200, $response);
}

/**
 * Creating new generator in db
 * method POST
 * params - name
 * url - /generators/
 */
function createAGenerator(){
    $app = \Slim\Slim::getInstance();
    // check for required params
    verifyRequiredParams(array('name','ramp','dc','onbar'));
    $response = array();
    $name = json_decode($app->request()->getBody())->name;
    $ramp = json_decode($app->request()->getBody())->ramp;
    $dc = json_decode($app->request()->getBody())->dc;
    $onbar = json_decode($app->request()->getBody())->onbar;
    $db = new DbHandler();
    // creating new name
    $gen_id = $db->createAGeneratorName($name,$ramp,$dc,$onbar);
    if ($gen_id != NULL) {
        $response["error"] = false;
        $response["message"] = "Generator created successfully";
        $response["name_id"] = $gen_id;
        echoResponse(201, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "Failed to create generator. Please try again";
        echoResponse(200, $response);
    }
}

/**
 * Deleting an existing generator
 * method DELETE
 * @param String $name Name of the generator to delete from database
 * params none
 * url - /generators/:id
 */
function deleteAGenerator($name) {
    $response = array();
    $db = new DbHandler();
    $num_rows = $db->deleteAGeneratorName($name);
    $response["error"] = false;
    $response["message"] = 'Deleted the name';
    $response["num_rows"] = $num_rows;
    echoResponse(200, $response);
}

function addAGeneratorShareData($name){
    $app = \Slim\Slim::getInstance();
    // check for required params
    //verifyRequiredParams(array('genIDs','conIDs','percentages'));
    $response = array();
    $genIDs = json_decode($app->request()->getBody())->genIDs;
    $conIDs = json_decode($app->request()->getBody())->conIDs;
    $percentages = json_decode($app->request()->getBody())->percentages;
    $db = new DbHandler();
    // creating new name
    $num_rows = $db->addAGeneratorShareData($genIDs,$conIDs,$percentages);
    if ($num_rows != NULL) {
        $response["error"] = false;
        $response["message"] = "Shares created successfully";
        $response["num_rows"] = $num_rows;
        echoResponse(201, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "Failed to create shares. Please try again";
        echoResponse(200, $response);
    }
}

/**
 * Get Shares for a particular generator ID
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function getAGeneratorShares($id) {
    $response = array();
    $db = new DbHandler();
    // fetching all users with a particular name
    $result = $db->getAGeneratorShareData($id);
    if(gettype($result)=="string") {
        $response["error"] = true;
        $response["message"]=$result;
    }
    else{
        $response["error"] = false;
        $response["shares"] = array();
        // looping through result and preparing names array
        while ($task = $result->fetch_assoc()) {
            $tmp = array();
            $tmp["p_id"] = $task["p_id"];
            $tmp["from_b"] = $task["from_b"];
            $tmp["to_b"] = $task["to_b"];
            $tmp["percentage"] = $task["percentage"];
            array_push($response["shares"], $tmp);
        }
    }
    echoResponse(200, $response);
}

/**
 * Delete Shares for a particular generator ID
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function deleteAGeneratorShares($id) {
    $response = array();
    $db = new DbHandler();
    // fetching all users with a particular name
    $num_rows = $db->deleteAGeneratorShareData($id);
    $response["error"] = false;
    $response["num_rows"] = $num_rows;
    echoResponse(200, $response);
}

/**
 * Update Shares for a particular generator ID for saving an update in percentages
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function updateAGeneratorShares($id) {
    $app = \Slim\Slim::getInstance();
    $response = array();
    $db = new DbHandler();
    $conIDs = json_decode($app->request()->getBody())->conIDs;
    $frombs = json_decode($app->request()->getBody())->frombs;
    $tobs = json_decode($app->request()->getBody())->tobs;
    $percentages = json_decode($app->request()->getBody())->percentages;
    $num_rows = $db->updateAGeneratorShareData($id,$conIDs,$frombs,$tobs,$percentages);
    //$num_rows = 90;
    if(is_numeric($num_rows)) {
        $response["error"] = false;
        $response["num_rows"] = $num_rows;
    }
    else{
        $response["error"] = true;
    }
    $response["num_rows"] = $num_rows;
    echoResponse(200, $response);

}

/**
 * Get Shares for a particular generator ID
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function getARevision($revId) {
    $response = array();
    $db = new DbHandler();
    if($revId == 'count'&& !is_numeric($revId)){
        $result = $db->getRevisionCount();
        if(gettype($result)=="string") {
            $response["error"] = true;
            $response["message"]=$result;
        }
        else{
            $response["error"] = false;
            $response["count"] = $result->fetch_assoc()['count'];
        }
        echoResponse(200, $response);
    }
    else{
        // fetching all users with a particular name
        $result = $db->getARevisionData($revId);
        if(gettype($result)=="string") {
            $response["error"] = true;
            $response["message"]=$result;
        }
        else{
            $response["error"] = false;
            $response["revData"] = array();
            // looping through result and preparing names array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["g_id"] = $task["g_id"];
                $tmp["p_id"] = $task["p_id"];
                $tmp["from_b"] = $task["from_b"];
                $tmp["to_b"] = $task["to_b"];
                $tmp["cat"] = $task["cat"];
                $tmp["val"] = $task["val"];
                array_push($response["revData"], $tmp);
            }
        }
        echoResponse(200, $response);
    }
}

/**
 * Get revision data of a particular generator ID
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function getAGenRevision($genID, $revId) {
    $response = array();
    $db = new DbHandler();
    if($revId == 'count'&& !is_numeric($revId)){
        //here the date is genID string
        $result = $db->getDateRevisionCount($genID);
        if(gettype($result)=="string") {
            $response["error"] = true;
            $response["message"]=$result;
        }
        else{
            $response["error"] = false;
            $response["count"] = $result->fetch_assoc()['count'];
        }
        echoResponse(200, $response);
    }
    else if($revId == 'latest'&& !is_numeric($revId)){
        $result = $db->getRevisionCount();
        if(gettype($result)=="string") {
            $response["error"] = true;
            $response["message"]= $result;
            echoResponse(200, $response);
        }
        else{
            $count = $result->fetch_assoc()['count'];
            getAGenRevision($genID,$count);
        }
    }
    else if(is_numeric($revId)){
        // fetching all users with a particular name
        $result = $db->getAGenRevisionData($genID, $revId);
        if(gettype($result)=="string") {
            $response["error"] = true;
            $response["message"]=$result;
        }
        else{
            $response["error"] = false;
            $response["revNumber"] = $revId;
            $response["revData"] = array();
            // looping through result and preparing names array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["p_id"] = $task["p_id"];
                $tmp["from_b"] = $task["from_b"];
                $tmp["to_b"] = $task["to_b"];
                $tmp["cat"] = $task["cat"];
                $tmp["val"] = $task["val"];
                array_push($response["revData"], $tmp);
            }
        }
        echoResponse(200, $response);
    }
    else{
        $response["error"] = true;
        $response["message"]="Invalid Revision data request url";
        echoResponse(200, $response);
    }
}

/**
 * Get revision data of a particular generator ID
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function getADateGenRevision($date, $genID, $revId) {
    $response = array();
    $db = new DbHandler();
    if($revId == 'count' && !is_numeric($revId)){
        $result = $db->getDateGenRevisionCount($date, $genID);
        if(gettype($result)=="string") {
            $response["error"] = true;
            $response["message"]=$result;
        }
        else{
            $response["error"] = false;
            $response["count"] = $result->fetch_assoc()['count'];
        }
        echoResponse(200, $response);
    }
    else if($revId == 'latest' && !is_numeric($revId)){
        $result = $db->getDateRevisionCount($date);
        if(gettype($result)=="string") {
            $response["error"] = true;
            $response["message"]= $result;
            echoResponse(200, $response);
        }
        else{
            $count = $result->fetch_assoc()['count'];
            getADateGenRevision($date, $genID, $count);
        }
    }
    else if(is_numeric($revId)||$revId==null){
        // fetching all users with a particular name
        $result = $db->getADateGenRevisionData($date, $genID, $revId);
        $result1 = $db->getADateGenRevisionParams($date, $revId);
        if(gettype($result)=="string") {
            $response["error"] = true;
            $response["message"]=$result;
        }
        else if(gettype($result1)=="string"){
            $response["error"] = true;
            $response["message"]=$result1;
        }
        else{
            $response["error"] = false;
            $response["revNumber"] = $revId;
            $response["date"] = $date;
            $response["revData"] = array();
            // looping through result and preparing names array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["p_id"] = $task["p_id"];
                $tmp["from_b"] = $task["from_b"];
                $tmp["to_b"] = $task["to_b"];
                $tmp["cat"] = $task["cat"];
                $tmp["val"] = $task["val"];
                array_push($response["revData"], $tmp);
            }
            // looping through result and preparing names array
            while ($task = $result1->fetch_assoc()) {
                $response["comment"] = $task["comment"];
                $response["TO"] = $task["time"];
            }
        }
        echoResponse(200, $response);
    }
    else{
        $response["error"] = true;
        $response["message"]="Invalid Revision data request url";
        echoResponse(200, $response);
    }
}

/**
 * Delete Shares for a particular generator ID
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function deleteARevision($revId) {
    $response = array();
    $db = new DbHandler();
    // fetching all users with a particular name
    $num_rows = $db->deleteARevisionData($revId);
    if(is_numeric($num_rows)) {
        $response["error"] = false;
    }
    else{
        $response["error"] = true;
    }
    $response["num_rows"] = $num_rows;
    echoResponse(200, $response);
}

/**
 * Delete Shares for a particular generator ID
 * @param String $name nameString of User in database
 * method GET
 * url /names/name
 */
function deleteADateRevision($date, $revId) {
    $response = array();
    $db = new DbHandler();
    // fetching all users with a particular name
    $num_rows = $db->deleteADateRevisionData($date, $revId);
    if(is_numeric($num_rows)) {
        $response["error"] = false;
    }
    else{
        $response["error"] = true;
    }
    $response["num_rows"] = $num_rows;
    echoResponse(200, $response);
}

/**
 * Update a Revision
 * @param String $name nameString of User in database
 * method POST
 * url /names/name
 */
function updateARevision($revId) {
    $app = \Slim\Slim::getInstance();
    $response = array();
    $db = new DbHandler();
    $genID = json_decode($app->request()->getBody())->genID;
    $cats = json_decode($app->request()->getBody())->cats;
    $conIDs = json_decode($app->request()->getBody())->conIDs;
    $frombs = json_decode($app->request()->getBody())->frombs;
    $tobs = json_decode($app->request()->getBody())->tobs;
    $vals = json_decode($app->request()->getBody())->vals;
    $num_rows = $db->updateARevisionData($revId,$genID,$cats,$conIDs,$frombs,$tobs,$vals);
    //$num_rows = 90;
    if(is_numeric($num_rows)) {
        $response["error"] = false;
    }
    else{
        $response["error"] = true;
    }
    $response["num_rows"] = $num_rows;
    echoResponse(200, $response);

}

/**
 * Update a Revision
 * @param String $name nameString of User in database
 * method POST
 * url /names/name
 */
function updateADateRevision($date,$revId) {
    $app = \Slim\Slim::getInstance();
    $response = array();
    $db = new DbHandler();
    $genID = json_decode($app->request()->getBody())->genID;
    $cats = json_decode($app->request()->getBody())->cats;
    $conIDs = json_decode($app->request()->getBody())->conIDs;
    $frombs = json_decode($app->request()->getBody())->frombs;
    $tobs = json_decode($app->request()->getBody())->tobs;
    $vals = json_decode($app->request()->getBody())->vals;
    $TO = json_decode($app->request()->getBody())->TO;
    $comm = json_decode($app->request()->getBody())->comm;
    $num_rows = $db->updateADateRevisionData($date, $revId,$genID,$cats,$conIDs,$frombs,$tobs,$vals,$TO, $comm);
    //$num_rows = 90;
    if(is_numeric($num_rows)) {
        $response["error"] = false;
    }
    else{
        $response["error"] = true;
    }
    $response["num_rows"] = $num_rows;
    echoResponse(200, $response);
}

function createAGenRev($genID){
    //Find the latest revision data of the generator to copy from
    //$app = \Slim\Slim::getInstance();
    $db = new DbHandler();
    $newRev = $db->createARevisionData($genID);
    if(is_numeric($newRev)) {
        $response["error"] = false;
    }
    else{
        $response["error"] = true;
    }
    $response["new_rev"] = $newRev;
    echoResponse(200, $response);
}
function createADateGenRev($date, $genID){
    //Find the latest revision data of the generator to copy from
    $app = \Slim\Slim::getInstance();
    $TO = json_decode($app->request()->getBody())->TO;
    $comm = json_decode($app->request()->getBody())->comm;
    $db = new DbHandler();
    $newRev = $db->createADateRevisionData($date, $genID, $TO, $comm);
    if(is_numeric($newRev)) {
        $response["error"] = false;
    }
    else{
        $response["error"] = true;
    }
    $response["new_rev"] = $newRev;
    echoResponse(200, $response);
}
/*
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: Origin, HTTP_X_REQUESTED_WITH, Content-Type, Accept, Authorization");
*/
$app->run();
?>
