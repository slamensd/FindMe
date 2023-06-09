<?php
include 'dbconn.php';

$data = array("name","email","type","score");
$data_length = count($data);
$data_val = array();
$dataIsset = 0;

for($i = 0; $i < $data_length; $i++){
	if(isset($_POST[$data[$i]])){
		$dataIsset++;
	}
}

$enableLevel = isset($_POST['enableLevel']) == 1 ? $_POST['enableLevel'] : '';
$format = isset($_POST['format']) == 10 ? $_POST['format'] : '';

if($testMode){
	echo '{"status":true, "test":true}';
	exit;
}

if($dataIsset == $data_length){
	for($i = 0; $i < $data_length; $i++){
		$data_val[$i] = isset($_POST[$data[$i]]) == 1 ? $_POST[$data[$i]] : '';
	}
	
	$dateformat='';
	if($format != ''){
		if($format == 'daily'){
			$dateformat = 'AND DATE(date) = CURDATE()';
		}else if($format == 'weekly'){
			$dateformat = 'AND YEARWEEK(date)= YEARWEEK(CURDATE())';
		}else if($format == 'monthly'){
			$dateformat = 'AND Year(date)=Year(CURDATE()) AND Month(date)= Month(CURDATE())';
		}
	}

	if($enableLevel){
		$result = mysqli_query($conn, "SELECT * FROM $table WHERE email='$data_val[1]' AND type='$data_val[2]' $dateformat");
	}else{
		$result = mysqli_query($conn, "SELECT * FROM $table WHERE email='$data_val[1]' $dateformat");	
	}
	
	if($result){
		if (mysqli_num_rows($result) != 0)
		{
			//exist user (in date range)
			$oldScore = 0;
			for($i = 0; $i < mysqli_num_rows($result); $i++)
			{
				 $row = mysqli_fetch_assoc($result);
				 if($oldScore < $row['score']){
					 $oldScore = $row['score'];
				 }
			}
			
			if($oldScore < $data_val[3]){
				$result = mysqli_query($conn, "UPDATE $table SET score='$data_val[3]' WHERE email='$data_val[1]' AND type='$data_val[2]'");
			}
		}else{
			//new user, or exist user (out of date range)
			if($enableLevel){
				$prevresult = mysqli_query($conn, "SELECT * FROM $table WHERE email='$data_val[1]' AND type='$data_val[2]'");
			}else{
				$prevresult = mysqli_query($conn, "SELECT * FROM $table WHERE email='$data_val[1]'");	
			}
			
			if($prevresult){
				if (mysqli_num_rows($prevresult) != 0){
					//exist user
					$result = mysqli_query($conn, "UPDATE $table SET score='$data_val[3]', date=NOW() WHERE email='$data_val[1]' AND type='$data_val[2]'");
				}else{
					//new user
					$table_col = '';
					$table_val = '';

					for($i = 0; $i < $data_length; $i++){
						$comma = ',';
						if($i == ($data_length-1)){
							$comma = '';	
						}
						$table_col .= $data[$i].$comma;
						$table_val .= "'".$data_val[$i]."'".$comma;
					}
					$result = mysqli_query($conn, "INSERT INTO ".$table." (".$table_col.") VALUES (".$table_val.")");
				}
			}else{
				echo '{"status":false, "error":0}';
			}
		}
		echo '{"status":true}';
	}else{
		echo '{"status":false, "error":0}';	
	}
}else{
	echo '{"status":false}';	
}

// Close connection
mysqli_close($conn);
?>