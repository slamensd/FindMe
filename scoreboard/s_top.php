<?php
include 'dbconn.php';

$data = array("name","email","type","score");
$data_length = count($data);

$type = isset($_REQUEST['type']) == 1 ? $_REQUEST['type'] : '';
$reverse = isset($_REQUEST['reverse']) == 1 ? $_REQUEST['reverse'] : '';
$limit = isset($_REQUEST['limit']) == 10 ? $_REQUEST['limit'] : '';
$format = isset($_REQUEST['format']) == 10 ? $_REQUEST['format'] : '';
$order = $reverse == 'true' ? 'ASC' : 'DESC';

$typeQuery = '';
$dateformat = '';

if($type != ''){
	$typeQuery = "WHERE type='$type'";
}

if($format != ''){
	if($format == 'daily'){
		$dateformat = 'DATE(date) = CURDATE()';
	}else if($format == 'weekly'){
		$dateformat = 'YEARWEEK(date)= YEARWEEK(CURDATE())';
	}else if($format == 'monthly'){
		$dateformat = 'Year(date)=Year(CURDATE()) AND Month(date)= Month(CURDATE())';
	}

	if($type != ''){
		$dateformat = 'AND '.$dateformat;
	}else{
		$dateformat = 'WHERE '.$dateformat;
	}
}

$filterUsersQuery = '';
$filterScoresQuery = '';

if($tableFilterID != ''){
	$filterUsersQuery = "email NOT IN (SELECT email FROM $tableFilterUsers WHERE status=1 AND type='$type' AND game='$tableFilterID')";
	if($type == '' && $format == ''){
		$filterUsersQuery = 'WHERE '.$filterUsersQuery;
	}else{
		$filterUsersQuery = 'AND '.$filterUsersQuery;
	}

	$scoreMin = 0;
	$scoreMax = 0;
	$filterScoresResult_length = 0;
	$filterScoresResult = mysqli_query($conn, "SELECT * FROM $tableFilterScores WHERE status=1 AND type='$type' AND game='$tableFilterID'");
	if($filterScoresResult){
		$filterScoresResult_length = mysqli_num_rows($filterScoresResult);
		for($i = 0; $i < $filterScoresResult_length; $i++)
		{
			$row = mysqli_fetch_assoc($filterScoresResult);
			$scoreMin = $row['score_min'];
			$scoreMax = $row['score_max'];
		}
	}

	if($filterScoresResult_length > 0){
		$filterScoresQuery = "AND score BETWEEN $scoreMin AND $scoreMax";
	}
}

$result = mysqli_query($conn, "SELECT * FROM $table $typeQuery $dateformat $filterUsersQuery $filterScoresQuery ORDER by score ".$order.", date ASC LIMIT ".$limit);
$category = mysqli_query($conn, "SELECT * FROM $table GROUP BY type ORDER BY type ASC");

if($result){
	$top_data_category = '';
	if($category){
		$category_length = mysqli_num_rows($category); 
		$categorydata = array("type");
		$categorydata_length = count($categorydata);

		for($i = 0; $i < $category_length; $i++)
		{
			$row = mysqli_fetch_assoc($category);
			$comma = ',';
			if($i == ($category_length-1)){
				$comma = '';	
			}
			
			$table_val = '';
			for($c = 0; $c < $categorydata_length; $c++){
				$commaInner = ',';
				if($c == ($categorydata_length-1)){
					$commaInner = '';	
				}
				$table_val .= '"'.$categorydata[$c].'":"'.$row[$categorydata[$c]].'"'.$commaInner;
			}
			$top_data_category .= '{ '.$table_val.' }'.$comma;
		}
	}

	$result_length = mysqli_num_rows($result); 
	$top_data = '';
	for($i = 0; $i < $result_length; $i++)
	{
		$row = mysqli_fetch_assoc($result);
		$comma = ',';
		if($i == ($result_length-1)){
			$comma = '';	
		}
		
		$table_val = '';
		for($c = 0; $c < $data_length; $c++){
			$commaInner = ',';
			if($c == ($data_length-1)){
				$commaInner = '';	
			}
			$table_val .= '"'.$data[$c].'":"'.$row[$data[$c]].'"'.$commaInner;
		}
		$top_data .= '{ '.$table_val.' }'.$comma;
	}
	
	if($result_length > 0){
		echo '{"status":true, "datas":['.$top_data.'], "category":['.$top_data_category.']}';
	}else{
		echo '{"status":false}';
	}
}else{
	echo '{"status":false, "error":0}';	
}

// Close connection
mysqli_close($conn);
?>