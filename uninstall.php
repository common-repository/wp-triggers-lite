<?php 
	global $wpdb;
	
	delete_option("wptgg_info") ;
	
	$table_arr=array($wpdb->prefix . "trigger");

	foreach($table_arr as $table_name)
	{
	   $sql = "DROP TABLE ". $table_name;
		$wpdb->query($sql);
	}
	
?>