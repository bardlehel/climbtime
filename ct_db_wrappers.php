<?php 
//Name: ct_db_wrappers.php
//Author: Dorian White (Lehel Kovach)
//Date: 10/13
//Description: this script's purpose is to wrap the ct_api.php api for the climbtime wordpress plugin

require "ct_api.php";


function ct_add_concept($title, $desc, $features) {
	global $c_concept;
	$c = new_concept($title, $desc, $features);
	save_db($c_concept, '_id', $c->_id, $c);	
}
?>