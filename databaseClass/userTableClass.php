<?php
// CLASS TABLE USER
class userTableClass extends connMySQLClass{
    
    // SET ATTRIBUTE TABLE NAME
    private $table_name = "user";
    
    // CREATE DEFAULT TABLE
    public function __construct(){
        // IF TABLE DOESN'T EXISTS, CREATE TABLE!`
        if($this->checkTable($this->table_name) == 0){
            // SET QUERY
            $sql = "CREATE TABLE $this->table_name (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_username VARCHAR(10) NOT NULL UNIQUE,
                user_refferal VARCHAR(7) NOT NULL UNIQUE,
                user_email VARCHAR(250) NOT NULL UNIQUE,
                user_password TEXT NOT NULL,
                user_role ENUM('Admin','Free','Membership') NOT NULL DEFAULT 'Free',
                user_upline VARCHAR(7) NOT NULL DEFAULT 'NONE',
                user_code_verif TEXT NOT NULL DEFAULT '0',
                user_status ENUM('true','false') NOT NULL DEFAULT 'true'
            )";
            // EXECUTE THE QUERY TO CREATE TABLE
            $this->dbConn()->query($sql);
            // CLOSE THE CONNECTION
            $this->dbConn()->close();
        }
    }

    // insert data user
    public function insertUser(string $fields, string $value){
        // query
        $sql = "INSERT INTO $this->table_name ($fields) VALUE($value)";
        // EXECUTE THE QUERY TO CREATE TABLE
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }

    // get data user
    public function selectUser(string $fields, string $key){
        // query
        $sql = "SELECT $fields FROM $this->table_name WHERE $key";
        // EXECUTE QUERY
        $exe = $this->dbConn()->query($sql);
        // SET DATA FROM TABLE
        while($rows = $exe->fetch_assoc()){
            $data[] = $rows;
        }
        // GET DATA TABLE
        $result["data"] = $data;
        // GET NUMS ROW TABLE
        $result["row"] = $exe->num_rows;
         // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $result;
    }
    
    // update data user
    public function updateUser(string $dataSet, string $key){
        // query
        $sql = "UPDATE $this->table_name SET $dataSet WHERE $key";
        // EXECUTE THE QUERY TO CREATE TABLE
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }

    // login check 
    public function loginMember(?string $key, ?string $param){
        $conn = $this->dbConn();
        /* create a prepared statement */
        if($param == "username"){
            $stmt = mysqli_prepare($conn, "SELECT COUNT(user_refferal), user_refferal, user_role, user_password, user_code_verif, user_status FROM $this->table_name WHERE user_username = ? LIMIT 1");
        }elseif($param == "email"){
            $stmt = mysqli_prepare($conn, "SELECT COUNT(user_refferal), user_refferal, user_role, user_password, user_code_verif, user_status FROM $this->table_name WHERE user_email = ? LIMIT 1");
        }
        /* bind parameters for markers */
        mysqli_stmt_bind_param($stmt, "s", $key);
        /* execute query */
        mysqli_stmt_execute($stmt);
        /* bind result variables */
        mysqli_stmt_bind_result($stmt, $num, $id, $role, $pass, $code, $status);
        /* fetch value */
        mysqli_stmt_fetch($stmt);
        // close connection
        $conn->close();
        return [
            "num" => $num,
            "user_id" => $id,
            "role" => $role,
            "pass_user" => $pass,
            "code" => $code,
            "status_user" => $status
        ];
    }

    // select downlines berdasarkan user_refferal = user_upline
    public function selectDownlines($upline) {
        $sql = "SELECT user_refferal, user_username, user_email FROM $this->table_name WHERE user_upline = '$upline'";
         // EXECUTE QUERY
         $exe = $this->dbConn()->query($sql);
         // SET DATA FROM TABLE
         while($rows = $exe->fetch_assoc()){
             $data[] = $rows;
         }
         // GET DATA TABLE
         $result["data"] = $data;
         // GET NUMS ROW TABLE
         $result["row"] = $exe->num_rows;
          // CLOSE THE CONNECTION
         $this->dbConn()->close();
         return $result;
    }
}

    


?>