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

    function getConn(){
        return $this->conn;
    }

    /**
     * Fetching all Constituent Names
     * @param none
     */
    public function getAllConstituentNames() {
        $sql = "SELECT * FROM constituents";
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
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
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

}

?>