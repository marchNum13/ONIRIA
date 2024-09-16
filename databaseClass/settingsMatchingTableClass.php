<?php
// CLASS TABLE paket
class settingsMatchingTableClass extends connMySQLClass{
    
    // SET ATTRIBUTE TABLE NAME
    private $table_name = "settings_bonus_matching_paket";
    
    // CREATE DEFAULT TABLE
    public function __construct(){
        // IF TABLE DOESN'T EXISTS, CREATE TABLE!`
        if($this->checkTable($this->table_name) == 0){
            // SET QUERY
            $sql = "CREATE TABLE $this->table_name (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                bonus_lvl VARCHAR(10) NOT NULL UNIQUE,
                bonus_persen DOUBLE NOT NULL
            )";
            // EXECUTE THE QUERY TO CREATE TABLE
            $create = $this->dbConn()->query($sql);
            // CLOSE THE CONNECTION
            $this->dbConn()->close();
            if($create){
                $dataArray = array(
                    "bonus_lvl" => [
                        "Level 1",
                        "Level 2",
                        "Level 3",
                        "Level 4",
                        "Level 5",
                        "Level 6",
                        "Level 7",
                        "Level 8",
                        "Level 9",
                        "Level 10",
                        "Level 11",
                        "Level 12",
                        "Level 13",
                        "Level 14",
                        "Level 15",
                        "Level 16",
                        "Level 17",
                        "Level 18",
                        "Level 19",
                        "Level 20"
                    ],
                    "bonus_persen" => [
                        "30",
                        "30",
                        "10",
                        "5",
                        "3",
                        "2",
                        "2",
                        "2",
                        "1",
                        "1",
                        "1",
                        "1",
                        "1",
                        "1",
                        "1",
                        "1",
                        "1",
                        "1",
                        "1",
                        "1"
                    ]
                );
                foreach($dataArray['bonus_lvl'] as $key => $lvl){
                    $persen = $dataArray['bonus_persen'][$key];

                    $insert = $this->insertBonus(
                        fields: "
                                bonus_lvl,
                                bonus_persen
                            ",
                        value: "
                                '$lvl',
                                '$persen'
                            "
                    );
                }
            }
        }
    }

    // insert data
    public function insertBonus(string $fields, string $value){
        // query
        $sql = "INSERT INTO $this->table_name ($fields) VALUE($value)";
        // EXECUTE THE QUERY TO CREATE TABLE
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }

    // get data
    public function selectBonus(string $fields, string $key){
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
    public function updateBonus(string $dataSet, string $key){
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