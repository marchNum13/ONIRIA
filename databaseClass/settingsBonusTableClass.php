<?php
// CLASS TABLE paket
class settingsBonusTableClass extends connMySQLClass{
    
    // SET ATTRIBUTE TABLE NAME
    private $table_name = "settings_bonus_paket";
    
    // CREATE DEFAULT TABLE
    public function __construct(){
        // IF TABLE DOESN'T EXISTS, CREATE TABLE!`
        if($this->checkTable($this->table_name) == 0){
            // SET QUERY
            $sql = "CREATE TABLE $this->table_name (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                bonus_nama_paket VARCHAR(10) NOT NULL UNIQUE,
                bonus_level_satu DOUBLE NOT NULL,
                bonus_level_dua DOUBLE NOT NULL,
                bonus_level_tiga DOUBLE NOT NULL,
                bonus_level_empat DOUBLE NOT NULL
            )";
            // EXECUTE THE QUERY TO CREATE TABLE
            $create = $this->dbConn()->query($sql);
            // CLOSE THE CONNECTION
            $this->dbConn()->close();
            if($create){
                $dataArray = array(
                    "bonus_nama_paket" => [
                        "Magang",
                        "Paket 1",
                        "Paket 2",
                        "Paket 3",
                        "Paket 4",
                        "Paket 5",
                        "Paket 6",
                        "Paket 7"
                    ],
                    "bonus_level_satu" => [
                        "5",
                        "5",
                        "5",
                        "5",
                        "10",
                        "10",
                        "10",
                        "10"
                    ],
                    "bonus_level_dua" => [
                        "3",
                        "3",
                        "3",
                        "3",
                        "5",
                        "5",
                        "5",
                        "5"
                    ],
                    "bonus_level_tiga" => [
                        "2",
                        "2",
                        "2",
                        "2",
                        "3",
                        "3",
                        "3",
                        "3"
                    ],
                    "bonus_level_empat" => [
                        "0",
                        "0",
                        "0",
                        "0",
                        "2",
                        "2",
                        "2",
                        "2"
                    ]
                );
                foreach($dataArray['bonus_nama_paket'] as $key => $paket){
                    $lvl_satu = $dataArray['bonus_level_satu'][$key];
                    $lvl_dua = $dataArray['bonus_level_dua'][$key];
                    $lvl_tiga = $dataArray['bonus_level_tiga'][$key];
                    $lvl_empat = $dataArray['bonus_level_empat'][$key];

                    $insert = $this->insertBonus(
                        fields: "
                                bonus_nama_paket,
                                bonus_level_satu,
                                bonus_level_dua,
                                bonus_level_tiga,
                                bonus_level_empat
                            ",
                        value: "
                                '$paket',
                                '$lvl_satu',
                                '$lvl_dua',
                                '$lvl_tiga',
                                '$lvl_empat'
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