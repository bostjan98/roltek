<?php
class Db{
	private $host = "localhost";
	private $user = "root";
	private $password = "";
	private $database = "roltek";
	private $conn;
	
    function __construct() {
        $this->conn = $this->connectDB();
	}	
	
	function connectDB() {
		$conn = mysqli_connect($this->host,$this->user,$this->password,$this->database);
		$conn->set_charset("utf8");
		return $conn;
	}
	
    function runBaseQuery($query) {
	    $result = mysqli_query($this->conn,$query);
	    while($row=mysqli_fetch_assoc($result)) {
	    	$resultset[] = $row;
	    }		
	    if(!empty($resultset))
	    return $resultset;
    }
    
    
    /**
	* 
	* @param undefined $query
	* @param undefined $param_type
	* @param undefined $param_value_array
	* 
	* @return
	*/
    function runQuery($query, $param_type, $param_value_array) {
        
        $sql = $this->conn->prepare($query);
        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
        }
        
        if(!empty($resultset)) {
            return $resultset;
        }
    }
    
    /**
	* 
	* @param undefined $sql
	* @param undefined $param_type
	* @param undefined $param_value_array
	* 
	* @return
	*/
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
    
 
	 /**
	 * 
	 * @param undefined $query
	 * @param undefined $param_type
	 * @param undefined $param_value_array
	 * 
	 * @return
	 */
    function insert($query, $param_type, $param_value_array) {
        $sql = $this->conn->prepare($query);
        $this->bindQueryParams($sql, $param_type, $param_value_array);
        $sql->execute();
        return $sql->insert_id;
    }
    
    /**
	* 
	* @param undefined $query
	* @param undefined $param_type
	* @param undefined $param_value_array
	* 
	* @return
	*/
    function update($query, $param_type, $param_value_array) {
        $sql = $this->conn->prepare($query);
        $this->bindQueryParams($sql, $param_type, $param_value_array);
        return $sql->execute();
    }

    /**
	* 
	* @param undefined $query
	* @param undefined $param_type
	* @param undefined $param_value_array
	* 
	* @return
	*/
    function select($query, $param_type, $param_value_array) {
       
        $sql = $this->conn->prepare($query);
        if (! empty($param_type) && ! empty($param_value_array)) {
            $this->bindQueryParams($sql, $param_type, $param_value_array);
        }
        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
        }

        if (! empty($resultset)) {
            return $resultset;
        }
    }

    function delete($query) {
	    $result = mysqli_query($this->conn,$query);

	    if($result){
	        return true;
        } else {
            return false;
        }
    }
}
?>