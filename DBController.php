<?php
class DBController {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "crud_example";
    private $conn;
    
    function __construct() {
        $this->conn = $this->connectDB();
    }   
    
    function connectDB() {
        $conn = mysqli_connect($this->host,$this->user,$this->password,$this->database);
        return $conn;
    }
    
function runBaseQuery($query) {
    $resultset = array();

    $result = $this->conn->query($query);

    if (!$result) {
        echo "خطأ في الاستعلام: " . $this->conn->error;
    }

    if ($result) {
        while($row = $result->fetch_assoc()) {
            $resultset[] = $row;
        }
    }

    return $resultset;
}



    
    function runQuery($query, $param_type, $param_value_array) {
        $resultset = array();
        $sql = $this->conn->prepare($query);
        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
        $result = $sql->get_result();
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
        }
        
        return $resultset;
    }
    
    function bindQueryParams($sql, $param_type, $param_value_array) {
        $param_value_reference[] = & $param_type;
        for($i=0; $i<count($param_value_array); $i++) {
            $param_value_reference[] = & $param_value_array[$i];
        }
        call_user_func_array(array(
            $sql,
            'bind_param'
        ), $param_value_reference);
    }
    
    function insert($query, $param_type, $param_value_array) {
        $sql = $this->conn->prepare($query);
        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
        $insertId = $sql->insert_id;
        return $insertId;
    }
    
    function update($query, $param_type, $param_value_array) {
        $sql = $this->conn->prepare($query);
        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
    }
}
?>