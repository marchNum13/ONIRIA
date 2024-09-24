<?php
// CLASS TABLE USER
class depositTableClass extends connMySQLClass{
    
    // SET ATTRIBUTE TABLE NAME
    private $table_name = "trasaction_deposit_user";
    
    // CREATE DEFAULT TABLE
    public function __construct(){
        // IF TABLE DOESN'T EXISTS, CREATE TABLE!`
        if($this->checkTable($this->table_name) == 0){
            // SET QUERY
            $sql = "CREATE TABLE $this->table_name (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                deposit_id VARCHAR(14) NOT NULL UNIQUE,
                deposit_user_id VARCHAR(7) NOT NULL,
                deposit_nominal DOUBLE NOT NULL,
                deposit_bank_admin TEXT NOT NULL,
                deposit_bank_user TEXT NOT NULL,
                deposit_bukti TEXT NOT NULL,
                deposit_status ENUM('Pending','Success','Ditolak') NOT NULL DEFAULT 'Pending',
                deposit_date TEXT NOT NULL
            )";
            // EXECUTE THE QUERY TO CREATE TABLE
            $this->dbConn()->query($sql);
            // CLOSE THE CONNECTION
            $this->dbConn()->close();
        }
    }

    // insert data
    public function insertDeposit(string $fields, string $value){
        // query
        $sql = "INSERT INTO $this->table_name ($fields) VALUE($value)";
        // EXECUTE THE QUERY TO CREATE TABLE
        $exe = $this->dbConn()->query($sql);
        // CLOSE THE CONNECTION
        $this->dbConn()->close();
        return $exe;
    }

    // get data
    public function selectDeposit(string $fields, string $key){
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

    public function selectAllTr($start, $end, $user){
        $sql = "SELECT 
                profit_nominal AS nominal, 
                'reward basic' AS keterangan,
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(profit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date
            FROM 
                trasaction_profit_user
            WHERE 
                (
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(profit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$start 00:00:00' AND 
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(profit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$end 23:59:59'
                ) AND profit_user_id = '$user' AND profit_type = 'Basic'
            
            UNION ALL 

            SELECT 
                profit_nominal AS nominal, 
                'reward premium' AS keterangan,
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(profit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date
            FROM 
                trasaction_profit_user
            WHERE 
                (
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(profit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$start 00:00:00' AND 
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(profit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$end 23:59:59'
                ) AND profit_user_id = '$user' AND profit_type = 'Premium'
            
            UNION ALL 
            
            SELECT 
                bonus_nominal AS nominal, 
                'reward provider' AS keterangan ,
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(bonus_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date
            FROM 
                trasaction_bonus_user 
            WHERE 
                (
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(bonus_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$start 00:00:00' AND 
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(bonus_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$end 23:59:59'
                ) AND bonus_user_id = '$user'
            
            UNION ALL 
            
            SELECT 
                bonus_nominal AS nominal, 
                'reward matching' AS keterangan ,
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(bonus_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date
            FROM 
                trasaction_bonus_matching_user 
            WHERE 
                (
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(bonus_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$start 00:00:00' AND 
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(bonus_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$end 23:59:59'
                ) AND bonus_user_id = '$user'
            
            UNION ALL 
            
            SELECT 
                paket_nominal AS nominal, 
                'package premium' AS keterangan,
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date 
            FROM 
                trasaction_paket_user 
            WHERE 
                (
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$start 00:00:00' AND 
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$end 23:59:59'
                ) AND paket_user_id = '$user'
            
            UNION ALL 
            
            SELECT 
                paket_nominal AS nominal, 
                'package membership' AS keterangan,
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date 
            FROM 
                trasaction_paket_non_premium_user 
            WHERE 
                (
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$start 00:00:00' AND 
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(paket_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$end 23:59:59'
                ) AND paket_user_id = '$user' AND paket_name <> 'Free'
            
            UNION ALL 
            
            SELECT 
                withdraw_nominal AS nominal, 
                'withdraw' AS keterangan,
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(withdraw_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date 
            FROM 
                trasaction_withdraw_user 
            WHERE 
                (
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(withdraw_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$start 00:00:00' AND 
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(withdraw_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$end 23:59:59'
                ) AND withdraw_user_id = '$user' AND withdraw_status = 'Success'
            
            UNION ALL 
            
            SELECT 
                deposit_nominal AS nominal, 
                'deposite' AS keterangan,
                DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(deposit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i') AS date 
            FROM 
                trasaction_deposit_user 
            WHERE 
                (
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(deposit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') >= '$start 00:00:00' AND 
                    DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(deposit_date / 1000), '+00:00', '+08:00'), '%Y-%m-%d %H:%i:%s') <= '$end 23:59:59'
                ) AND deposit_user_id = '$user' AND deposit_status = 'Success'
            ORDER BY date DESC";
        
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
    public function updateDeposit(string $dataSet, string $key){
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