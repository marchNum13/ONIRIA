<?php
// CLASS TABLE USER
class paketTableClass extends connMySQLClass{
    
    // SET ATTRIBUTE TABLE NAME
    private $table_name = "trasaction_paket_user";
    
    // CREATE DEFAULT TABLE
    public function __construct(){
        // IF TABLE DOESN'T EXISTS, CREATE TABLE!`
        if($this->checkTable($this->table_name) == 0){
            // SET QUERY
            $sql = "CREATE TABLE $this->table_name (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                paket_id VARCHAR(14) NOT NULL UNIQUE,
                paket_user_id VARCHAR(7) NOT NULL,
                paket_nominal DOUBLE NOT NULL,
                paket_estimasi ENUM('Trial','Berbayar') NOT NULL,
                paket_name VARCHAR(250) NOT NULL,
                paket_reward_tugas_satu DOUBLE NOT NULL,
                paket_reward_tugas_dua DOUBLE NOT NULL,
                paket_reward_tugas_tiga DOUBLE NOT NULL,
                paket_jumlah_tugas DOUBLE NOT NULL,
                paket_ads_stop_date TEXT NOT NULL,
                paket_ads_limit DOUBLE NOT NULL,
                paket_date TEXT NOT NULL
            )";
            // EXECUTE THE QUERY TO CREATE TABLE
            $this->dbConn()->query($sql);
            // CLOSE THE CONNECTION
            $this->dbConn()->close();
        }
    }

    // insert data
    public function insertPaket(string $fields, string $value){
        // query
        $sql = "INSERT INTO $this->table_name ($fields) VALUE($value)";
        // EXECUTE THE QUERY TO CREATE TABLE
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }

    // get data
    public function selectPaket(string $fields, string $key){
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
    
    // update data
    public function updatePaket(string $dataSet, string $key){
        // query
        $sql = "UPDATE $this->table_name SET $dataSet WHERE $key";
        // EXECUTE THE QUERY TO CREATE TABLE
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }
}

    


?>