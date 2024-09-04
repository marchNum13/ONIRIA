<?php
// CLASS TABLE paket
class settingsPaketTableClass extends connMySQLClass{
    
    // SET ATTRIBUTE TABLE NAME
    private $table_name = "settings_ads_paket";
    
    // CREATE DEFAULT TABLE
    public function __construct(){
        // IF TABLE DOESN'T EXISTS, CREATE TABLE!`
        if($this->checkTable($this->table_name) == 0){
            // SET QUERY
            $sql = "CREATE TABLE $this->table_name (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                settings_nama_paket VARCHAR(10) NOT NULL UNIQUE,
                settings_harga_paket DOUBLE NOT NULL,
                settings_reward_tugas DOUBLE NOT NULL,
                settings_jumlah_tugas DOUBLE NOT NULL
            )";
            // EXECUTE THE QUERY TO CREATE TABLE
            $create = $this->dbConn()->query($sql);
            // CLOSE THE CONNECTION
            $this->dbConn()->close();
            if($create){
                $dataArray = array(
                    "nama_paket" => [
                        "Magang",
                        "Paket 1",
                        "Paket 2",
                        "Paket 3",
                        "Paket 4",
                        "Paket 5",
                        "Paket 6",
                        "Paket 7"
                    ],
                    "harga_paket" => [
                        "0",
                        "270000",
                        "1100000",
                        "2500000",
                        "5500000",
                        "7500000",
                        "13500000",
                        "21000000"
                    ],
                    "reward_tugas" => [
                        "1250",
                        "270",
                        "640",
                        "765",
                        "852",
                        "950",
                        "1125",
                        "1470"
                    ],
                    "jumlah_tugas" => [
                        "5",
                        "5",
                        "10",
                        "20",
                        "40",
                        "50",
                        "80",
                        "100"
                    ]
                );
                foreach($dataArray['nama_paket'] as $key => $paket){
                    $harga = $dataArray['harga_paket'][$key];
                    $reward = $dataArray['reward_tugas'][$key];
                    $jumlah = $dataArray['jumlah_tugas'][$key];

                    $insert = $this->insertPaket(
                        fields: "
                                settings_nama_paket,
                                settings_harga_paket,
                                settings_reward_tugas,
                                settings_jumlah_tugas
                            ",
                        value: "
                                '$paket',
                                '$harga',
                                '$reward',
                                '$jumlah'
                            "
                    );
                }
            }
        }
    }

    // insert data paket
    public function insertPaket(string $fields, string $value){
        // query
        $sql = "INSERT INTO $this->table_name ($fields) VALUE($value)";
        // EXECUTE THE QUERY TO CREATE TABLE
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }

    // get data paket
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

    // update data paket
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