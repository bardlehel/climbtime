<?php 

$connection = null;
$c_concept = null;
$c_cat = null;
$c_feature = null;
$mongohq_username = "lehelkovach@gmail.com";
$mongohq_pw = "Kovach789";



function init_db() {
	global $connection;
	global $c_concept;
	global $c_cat;
	global $c_feature;
	global $mongohq_username;
	global $mongohq_pw;
	
	MongoLog::setLevel(MongoLog::ALL);
	MongoLog::setModule(MongoLog::ALL);
	
	$connection = new MongoClient("mongodb://lehel:Kovach789@paulo.mongohq.com:10011/climbtime"); // connect to a remote host at a given port
	$db = $connection->climbtime;
	$c_concept = $db->concepts;
	$c_cat = $db->categories;
	$c_feature = $db->features;		
}

$key_list = array();
$value_list = array();

//not efficient!
function save_db($collection, $key, $document, $timeout = 0) {
	global $key_list;
	global $value_list;
	
	if(!($ret = get_transient($key)) && $ret != $document)
	$collection->save($document);
	
	set_transient($key, $document, 0);
	array_push($key_list, $key);
	array_push($value_list, $document);
}

function update_value_db($collection, $where_array, $set_array) {
	global $value_list;
	
	$data = array('$set' => $set_array);

	$result = $collection->db->command( array(
	    'findAndModify' => 'collection',
	    'query' => $where_array,
	    'update' => $data,
	    'new' => true,        # To get back the document after the upsert
	    'upsert' => true,
	    'fields' => array( '_id' => 1 )   # Only return _id field
	) );
	$id = $result['value']['_id'];
	
	set_transient($id, $result['value'], 0);
	//update value list
}

function get_single_db($collection, $key) {
	
	if(!($result = get_transient($key))) {
		$result = $collection->findOne(array('key' => $key));
		set_transient($key, $result);
	}
		
	return $result;
}

//gets 
function get_first_category_by_name($collection, $name) {
	
}

$db_cursor = null;

//this won't work without mongodb, so do not use!
/*
function get_where_db($where) {
	global $use_mongodb;
	global $collection;
	
	if($use_mongodb) {
		$result = $collection->find(array('key' => $key))
	} else {
		$result = get_transient($key, $value, 0);
	}
	
	return $result;
}
*/
?>