<?php  
class connMySQLClass{
    // SET ATTRIBUTE CONFIG DB
    private $servername = "localhost"; // SERVER NAME
    // private $username = "u256627252_oni"; // USERNAME
    // private $password = "/L3vVa@1d"; // PASSWORD
    // private $dbname = "u256627252_oni"; // DATABASE NAME

    private $username = "root"; // USERNAME
    private $password = ""; // PASSWORD
    private $dbname = "oni"; // DATABASE NAME

    // METHOD CONNECT TO DB
    protected function dbConn(){
        try{
            // SET CONNECTION
            $connect = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
            return $connect;
        }catch (mysqli_sql_exception $e) {
            return die("Error: " . $e->getMessage());
        }
        
    }
    
    // METHOD CHECK TABLE
    protected function checkTable(?string $tableName = null){
        // QUERY TO CHECK TABLE
        $sql = "SHOW TABLES LIKE '" . $tableName . "'";
        // EXECUTE THE QUERY
        $exe = $this->dbConn()->query($sql);
        // GET NUMS ROW TABLE
        $result = $exe->num_rows;
        $this->dbConn()->close(); // CLOSE THE CONNECTION
        return $result;
    }
}



?>