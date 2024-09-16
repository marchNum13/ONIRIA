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
                settings_reward_tugas_satu DOUBLE NOT NULL DEFAULT 0,
                settings_reward_tugas_dua DOUBLE NOT NULL DEFAULT 0,
                settings_reward_tugas_tiga DOUBLE NOT NULL DEFAULT 0,
                settings_jumlah_tugas DOUBLE NOT NULL
            )";
            // EXECUTE THE QUERY TO CREATE TABLE
            $create = $this->dbConn()->query($sql);
            // CLOSE THE CONNECTION
            $this->dbConn()->close();
            if($create){
                $dataArray = array(
                    "nama_paket" => [
                        "Membership",
                        "Premium 1",
                        "Premium 2"
                    ],
                    "harga_paket" => [
                        "10",
                        "100",
                        "500"
                    ],
                    "reward_tugas_satu" => [
                        "1",
                        "0.2",
                        "0.2"
                    ],
                    "reward_tugas_dua" => [
                        "1",
                        "0.3",
                        "0.3"
                    ],
                    "reward_tugas_tiga" => [
                        "0",
                        "0",
                        "0.4"
                    ],
                    "jumlah_tugas" => [
                        "2",
                        "2",
                        "3"
                    ]
                );
                foreach($dataArray['nama_paket'] as $key => $paket){
                    $harga = $dataArray['harga_paket'][$key];
                    $reward_satu = $dataArray['reward_tugas_satu'][$key];
                    $reward_dua = $dataArray['reward_tugas_dua'][$key];
                    $reward_tiga = $dataArray['reward_tugas_tiga'][$key];
                    $jumlah = $dataArray['jumlah_tugas'][$key];

                    $insert = $this->insertPaket(
                        fields: "
                                settings_nama_paket,
                                settings_harga_paket,
                                settings_reward_tugas_satu,
                                settings_reward_tugas_dua,
                                settings_reward_tugas_tiga,
                                settings_jumlah_tugas
                            ",
                        value: "
                                '$paket',
                                '$harga',
                                '$reward_satu',
                                '$reward_dua',
                                '$reward_tiga',
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