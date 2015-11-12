<?php
/**
 * Created by Sudhir.
 * User: PSSE
 * Date: 10/15/2015
 * Time: 8:43 PM
 */

/**
 * Database configuration
 */
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'wrldc_schedule');

define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXISTED', 2);

class DbConnect {

    private $conn;

    function __construct() {
    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        // Connecting to mysql database
        $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // returing connection resource
        return $this->conn;
    }

}

class DbHandler {

    private $conn;

    function __construct() {
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
        //$this->echoAllConstituentNames();
    }

    /**
     * Fetching all Constituent Names
     * @param none
     */
    public function getAllConstituentNames() {
        $sql = "SELECT * FROM constituents ORDER BY constituents.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    /**
     * Echoing all Constituent Names
     * @param none
     */
    public function echoAllConstituentNames() {
        $result = $this->getAllConstituentNames();
        $response = array();
        $response["names"] = array();
        while ($task = $result->fetch_assoc()) {
            $tmp = array();
            $tmp["id"] = $task["id"];
            $tmp["name"] = $task["name"];
            array_push($response["names"], $tmp);
        }
        //array_push($response["names"], $this->deleteAConstituentName("adf"));
        echo json_encode($response);
    }

    /**
     * Creating a Constituent Name
     * @param String $namestr of Constituent
     */
    public function createAConstituentName($namestr) {
        $sql = "INSERT INTO constituents(name) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $namestr);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            // Constituent name creation failed
            $stmt->close();
            return NULL;
        } else {
            // Constituent name created
            $stmt->close();
            return $this->conn->insert_id;
        }

    }

    /**
     * Checking a particular Constituent Name
     * @param none
     */
    public function checkAConstituentName($namestr) {
        $sql = "SELECT * FROM constituents WHERE name=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $namestr);
        $stmt->execute();
        $names = $stmt->get_result();
        $stmt->close();
        return $names;
    }

    /**
     * Get a particular Constituent Name by id
     * @param none
     */
    public function getAConstituentNameById($id) {
        $sql = "SELECT name FROM constituents WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks->fetch_assoc()["name"];
    }

    /**
     * Fetching all Constituent Names
     * @param none
     */
    public function updateAConstituentName($namestr,$updatenamestr) {
        $sql = "UPDATE constituents SET name=? WHERE name=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $updatenamestr,$namestr);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows;
    }

    /**
     * Delete a Constituent Name
     * @param String $namestr of Constituent
     */
    public function deleteAConstituentName($namestr) {
        $sql = "DELETE FROM `constituents` WHERE name=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $namestr);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows;
    }

    /**
     * Fetching all Generator Names
     * @param none
     */
    public function getAllGeneratorNames() {
        $sql = "SELECT id, name FROM generators ORDER BY generators.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    /**
     * Creating a Generator Name
     * @param String $namestr of Constituent
     */
    public function createAGeneratorName($name,$ramp,$dc,$onbar) {
        $sql = "INSERT INTO generators(name,ramp,dc,onbar) VALUES (?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $name,$ramp,$dc,$onbar);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            // Constituent name creation failed
            $stmt->close();
            return NULL;
        } else {
            // Constituent name created
            $stmt->close();
            return $this->conn->insert_id;
        }

    }

    /**
     * Fetching a particular Generator Name data
     * @param none
     */
    public function fetchAGeneratorName($namestr) {
        $sql = "SELECT * FROM generators WHERE name=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $namestr);
        $stmt->execute();
        $names = $stmt->get_result();
        $stmt->close();
        return $names;
    }

    /**
     * Delete a Generator Name
     * @param String $namestr of Generator
     */
    public function deleteAGeneratorName($namestr) {
        $sql = "DELETE FROM `generators` WHERE name=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $namestr);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows;
    }

    /**
     * Add a Generator Share
     * @param String $namestr of Generator
     */
    public function addAGeneratorShareData(&$genIDs, &$conIDs, &$percentages) {
        $sql = "INSERT INTO constshares (p_id, g_id, percentage) VALUES ";
        for ($i = 0; $i < sizeof($genIDs); $i++) {
            if ($i > 0) $sql .= ", ";
            $sql .= "(".$conIDs[$i].",".$genIDs[$i].",".$percentages[$i].")";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows;
    }

    /**
     * Get a Generator Share
     * @param String $namestr of Generator
     */
    public function getAGeneratorShareData($genID) {
        try {
            $sql = "SELECT p_id, from_b, to_b, percentage FROM constshares WHERE g_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $genID);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Delete a Generator Share
     * @param String $namestr of Generator
     */
    public function deleteAGeneratorShareData($genID) {
        $sql = "DELETE FROM constshares WHERE g_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $genID);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows;
    }

    /**
     * update a Generator Share
     * @param String $namestr of Generator
     */
    public function updateAGeneratorShareData($genID,&$conIDs,&$frombs,&$tobs,&$percentages) {
        try{
            //$this->conn->beginTransaction();

            //Delete the generator shares
            $sql = "DELETE FROM constshares WHERE g_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $genID);
            $stmt->execute();
            $stmt->close();

            //Save the generator shares
            $sql = "INSERT INTO constshares (g_id, p_id, from_b, to_b, percentage) VALUES ";
            for ($i = 0; $i < sizeof($conIDs); $i++) {
                if ($i > 0) $sql .= ", ";
                $sql .= "(".$genID.",".$conIDs[$i].",".$frombs[$i].",".$tobs[$i].",".$percentages[$i].")";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $num_affected_rows = $stmt->affected_rows;
            $stmt->close();

            //Commit the transaction
            //$this->conn->commit();
            return $num_affected_rows;
            //return 0;
        }catch (Exception $e){
            //$this->conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Get Revision Count
     * @param String $namestr of Generator
     */
    public function getRevisionCount() {
        try {
            $sql = "SELECT MAX(id) AS count FROM revisions";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Get Revision Count
     * @param String $namestr of Generator
     */
    public function getDateRevisionCount($date) {
        try {
            $sql = "SELECT MAX(id) AS count FROM revisions WHERE date=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $date);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Get Revision Count
     * @param String $namestr of Generator
     */
    public function getDateGenRevisionCount($date, $genID) {
        try {
            $sql = "SELECT MAX(id) AS count FROM revisions WHERE date=? AND g_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $date, $genID);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    /**
     * Get a Revision Share
     * @param String $namestr of Generator
     */
    public function getARevisionData($revId) {
        try {
            $sql = "SELECT g_id, p_id, from_b, to_b, cat, val FROM revisions WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $revId);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Get all Revision Numbers relating to a particular generator ID
     * @param String $namestr of Generator
     */
    public function getAGenRevisionNumbersData($genID) {
        try {
            $sql = "SELECT DISTINCT id FROM revisions WHERE g_id=? ORDER BY id ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $genID);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Get all Revision Numbers relating to a particular generator ID
     * @param String $namestr of Generator
     */
    public function getADateGenRevisionNumbersData($date, $genID) {
        try {
            $sql = "SELECT DISTINCT id FROM revisions WHERE g_id=? AND date=? ORDER BY id ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $genID, $date);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    /**
     * Get a Revision Share
     * @param String $namestr of Generator
     */
    public function getAGenRevisionData($genID, $revId) {
        try {
            $sql = "SELECT MAX(id) AS prac FROM revisions WHERE id<=? AND g_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $revId, $genID);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $practicalID = $result->fetch_assoc()['prac'];

            $sql = "SELECT p_id, from_b, to_b, cat, val FROM revisions WHERE id=? AND g_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $practicalID, $genID);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Get a Revision Share
     * @param String $namestr of Generator
     */
    public function getADateGenRevisionData($date, $genID, $revId) {
        try {
            $sql = "SELECT MAX(id) AS prac FROM revisions WHERE id<=? AND g_id=? AND date=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $revId, $genID, $date);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $practicalID = $result->fetch_assoc()['prac'];

            $sql = "SELECT p_id, from_b, to_b, cat, val FROM revisions WHERE id=? AND g_id=? AND date=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $practicalID, $genID, $date);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public  function getADateGenRevisionParams($date, $revId){
        try {
            $sql = "SELECT comment, time FROM revisionglobal WHERE id=? AND date=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $revId, $date);
            $stmt->execute();
            $results = $stmt->get_result();
            $stmt->close();
            return $results;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    /**
     * Delete a Revision
     * @param String $namestr of Generator
     */
    public function deleteARevisionData($revId) {
        try{
            //$this->conn->beginTransaction();
            //Get the maximum revision number
            $sql = "SELECT MAX(id) AS tot FROM revisions";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $totRevs = $result->fetch_assoc()['tot'];

            if($revId == $totRevs) {
                $sql = "DELETE FROM revisions WHERE id=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("s", $revId);
                $stmt->execute();
                $num_affected_rows = $stmt->affected_rows;
                $stmt->close();
                return $num_affected_rows;
            }
            else{
                //A revision not at at the last is requested to be deleted
                return "A revision which is not the last one is requested to be deleted which is not allowed";
            }
            //Commit the transaction
            //$this->conn->commit();
            //return 0;
            //return $sql;
        }catch (Exception $e){
            //$this->conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Delete a Revision
     * @param String $namestr of Generator
     */
    public function deleteADateRevisionData($date, $revId) {
        try{
            //$this->conn->beginTransaction();
            //TODO delete from revisionglobal table also
            //Get the maximum revision number
            $sql = "SELECT MAX(id) AS tot FROM revisions WHERE date=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $date);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $totRevs = $result->fetch_assoc()['tot'];

            if($revId == $totRevs) {
                $sql = "DELETE FROM revisions WHERE id=? AND date=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ss", $revId, $date);
                $stmt->execute();
                $num_affected_rows = $stmt->affected_rows;
                $stmt->close();
                return $num_affected_rows;
            }
            else{
                //A revision not at at the last is requested to be deleted
                return "A revision which is not the last one is requested to be deleted which is not allowed";
            }
            //Commit the transaction
            //$this->conn->commit();
            //return 0;
            //return $sql;
        }catch (Exception $e){
            //$this->conn->rollBack();
            return $e->getMessage();
        }
    }


    /**
     * Update a Revision
     * @param String $namestr of Generator
     */
    public function updateARevisionData($revId,$genID,&$cats,&$conIDs,&$frombs,&$tobs,&$vals) {
        try{
            //$this->conn->beginTransaction();
            /*
            //Get the real revision to delete
            $sql = "SELECT MAX(id) AS prac FROM revisions WHERE id<=? AND g_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $revId, $genID);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $practicalID = $result->fetch_assoc()['prac'];
            */

            //Delete the generator shares of the asked revision
            $sql = "DELETE FROM revisions WHERE id=? AND g_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $revId, $genID);
            $stmt->execute();
            $stmt->close();

            //Save the generator shares
            $sql = "INSERT INTO revisions (id, g_id, p_id, from_b, to_b, cat, val) VALUES ";
            for ($i = 0; $i < sizeof($conIDs); $i++) {//sizeof($conIDs)
                if ($i > 0) $sql .= ", ";
                $sql .= "(".$revId.",".$genID.",".$conIDs[$i].",".$frombs[$i].",".$tobs[$i].",'".$cats[$i]."','".$vals[$i]."')";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $num_affected_rows = $stmt->affected_rows;
            $stmt->close();

            //Commit the transaction
            //$this->conn->commit();
            return $num_affected_rows;
            //return 0;
            //return $sql;
        }catch (Exception $e){
            //$this->conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Update a Revision
     * @param String $namestr of Generator
     */
    public function updateADateRevisionData($date,$revId,$genID,&$cats,&$conIDs,&$frombs,&$tobs,&$vals, $TO, $comm) {
        try{
            //$this->conn->beginTransaction();
            /*
            //Get the real revision to delete
            $sql = "SELECT MAX(id) AS prac FROM revisions WHERE id<=? AND g_id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $revId, $genID);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $practicalID = $result->fetch_assoc()['prac'];
            */

            //Update the revision parameters
            $this->updateRevisionParams($date,$revId,$TO,$comm);
            //Delete the generator shares of the asked revision
            $sql = "DELETE FROM revisions WHERE id=? AND g_id=? AND date=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $revId, $genID, $date);
            $stmt->execute();
            $stmt->close();

            //Save the generator shares
            $sql = "INSERT INTO revisions (date, id, g_id, p_id, from_b, to_b, cat, val) VALUES ";
            for ($i = 0; $i < sizeof($conIDs); $i++) {//sizeof($conIDs)
                if ($i > 0) $sql .= ", ";
                $sql .= "('".$date."',".$revId.",".$genID.",".$conIDs[$i].",".$frombs[$i].",".$tobs[$i].",'".$cats[$i]."','".$vals[$i]."')";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $num_affected_rows = $stmt->affected_rows;
            $stmt->close();

            //Commit the transaction
            //$this->conn->commit();
            return $num_affected_rows;
            //return 0;
            //return $sql;
        }catch (Exception $e){
            //$this->conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Update a Revision
     * @param String $namestr of Generator
     */
    public function createARevisionData($genID) {
        try{
            //$this->conn->beginTransaction();
            //Get the maximum revision number
            $sql = "SELECT MAX(id) AS tot FROM revisions";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $totRevs = $result->fetch_assoc()['tot'];
            //Get the latest revision data of the generator
            try {
                $sql = "SELECT MAX(id) AS prac FROM revisions WHERE id<=? AND g_id=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ss", $totRevs, $genID);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                $practicalID = $result->fetch_assoc()['prac'];
                if($practicalID == NULL){
                    return $totRevs+1;
                }
                $sql = "SELECT p_id, from_b, to_b, cat, val FROM revisions WHERE id=? AND g_id=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ss", $practicalID, $genID);
                $stmt->execute();
                $results = $stmt->get_result();
                $stmt->close();
                $conIDs = array();
                $frombs = array();
                $tobs = array();
                $cats = array();
                $vals = array();
                while ($task = $results->fetch_assoc()) {
                    array_push($conIDs,$task["p_id"]);
                    array_push($frombs,$task["from_b"]);
                    array_push($tobs,$task["to_b"]);
                    array_push($cats,$task["cat"]);
                    array_push($vals,$task["val"]);
                }
            }
            catch(Exception $e){
                return $e->getMessage();
            }

            //Save the generator shares
            $sql = "INSERT INTO revisions (id, g_id, p_id, from_b, to_b, cat, val) VALUES ";
            for ($i = 0; $i < sizeof($conIDs); $i++) {//sizeof($conIDs)
                if ($i > 0) $sql .= ", ";
                $sql .= "(".($totRevs+1).",".$genID.",".$conIDs[$i].",".$frombs[$i].",".$tobs[$i].",'".$cats[$i]."','".$vals[$i]."')";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            //$num_affected_rows = $stmt->affected_rows;
            $stmt->close();
            //Commit the transaction
            //$this->conn->commit();
            return $totRevs+1;
            //return $sql;
        }catch (Exception $e){
            //$this->conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Update a Revision
     * @param String $namestr of Generator
     */
    public function createADateRevisionData($date, $genID, $TO, $comm) {
        try{
            //$this->conn->beginTransaction();
            //Get the maximum revision number
            $sql = "SELECT MAX(id) AS tot FROM revisions WHERE date='".$date."'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $totRevs = $result->fetch_assoc()['tot'];
            if($totRevs == NULL && !is_numeric($totRevs)){
                $this->updateRevisionParams($date, 0, $TO, $comm);
                return 0;
            }
            //Get the latest revision data of the generator
            try {
                $sql = "SELECT MAX(id) AS prac FROM revisions WHERE id<=? AND g_id=? AND date=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("sss", $totRevs, $genID, $date);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                $practicalID = $result->fetch_assoc()['prac'];
                if($practicalID == NULL && !is_numeric($practicalID)){
                    $this->updateRevisionParams($date, $totRevs+1, $TO, $comm);
                    return $totRevs+1;
                }
                $sql = "SELECT p_id, from_b, to_b, cat, val FROM revisions WHERE id=? AND g_id=? AND date=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("sss", $practicalID, $genID, $date);
                $stmt->execute();
                $results = $stmt->get_result();
                $stmt->close();
                $conIDs = array();
                $frombs = array();
                $tobs = array();
                $cats = array();
                $vals = array();
                while ($task = $results->fetch_assoc()) {
                    array_push($conIDs,$task["p_id"]);
                    array_push($frombs,$task["from_b"]);
                    array_push($tobs,$task["to_b"]);
                    array_push($cats,$task["cat"]);
                    array_push($vals,$task["val"]);
                }
            }
            catch(Exception $e){
                return $e->getMessage();
            }

            //Save the generator shares
            $sql = "INSERT INTO revisions (date, id, g_id, p_id, from_b, to_b, cat, val) VALUES ";
            for ($i = 0; $i < sizeof($conIDs); $i++) {//sizeof($conIDs)
                if ($i > 0) $sql .= ", ";
                $sql .= "('".$date."',".($totRevs+1).",".$genID.",".$conIDs[$i].",".$frombs[$i].",".$tobs[$i].",'".$cats[$i]."','".$vals[$i]."')";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            //$num_affected_rows = $stmt->affected_rows;
            $stmt->close();
            //Commit the transaction
            //$this->conn->commit();
            $this->updateRevisionParams($date, $totRevs+1, $TO, $comm);
            return $totRevs+1;
            //return $sql;
        }catch (Exception $e){
            //$this->conn->rollBack();
            return $e->getMessage();
        }
    }

    function updateRevisionParams($date, $revID, $TO, $comm){
        //INSERT INTO `wrldc_schedule`.`revisionglobal` (`id`, `date`, `time`, `comment`, `metadata`) VALUES ('2', '2015-11-12', '12:47:00', 'sudhir', NULL) ON DUPLICATE KEY UPDATE `comment` = VALUES(`comment`), `time` = VALUES(`time`), `metadata` = VALUES(`metadata`)
        try {
            $sql = "INSERT INTO revisionglobal (id, date, time, comment, metadata) VALUES (?, '".$date."','".$TO."' , ?, NULL ) ON DUPLICATE KEY UPDATE comment = VALUES(comment), time = VALUES(time), metadata = VALUES(metadata)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $revID, $comm);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        }
        catch(Exception $e){
            //TODO error handling
            return $e->getMessage();
        }
    }
}

?>
