<?php 

require "kvp_db.php";  //low-level calls to mongodb insert/update/upsert functions

/////////functions to create, get, add, update objects

//new_concept
//Description: adds a new concept to the database
//Returns: array representing new object, including the record id from the db
//Remarks: this function should be called by a wrapper function for the given framework (ct_db_wrappers.php)
function new_concept($title, $desc, $features, $pic_url ="") {
	//get a new uid
	$id = new MongoId();
	$date = date('m/d/Y h:i:s a', time());
	$author = get_currentuserinfo();
	
	//update the feature table
	global $c_feature;
	$feature_list = array();
	foreach ($features as $feature) {
		//($collection, $key_name, $key, $value)
		//update type where title = $feature->name
		
		//!!!!!!!!!!!!!!!
		//we are first adding the features to the feature collection (value independent!)
		//then we are going to add the values into the concept document!
		
		
		$feature_id = update_value_db($c_feature,'title', $feature->name, 'data_type', $feature->type);
		//update value
		update_value_db($c_feature,'_id', $feature_id, 'data_type', $feature->type);
		
		
		
		array_push($feature_list, $feature_id);
	}
	
	$ret = array(
		'_id' => $id,
		'features' => $feature_list,
		'title' => $title,
		'desc' => $desc,
		'default_pic' => $pic_url,
		'author' => $author
	);
	
	return $ret;
}

function get_concept($key) {
	return get_single_db($key);
}

function add_concept($concept) {
	
	insert_db($concept->uid, $concept);
}

function update_concept($concept_key, $prop_key, $value) {
	update_value_db($c_concept, $concept_key, $prop_key, $value);
}


///FEATURE API
/*
 * Features are like properties. some are required (necessary)
 * so all concepts will have these common features (id, title, author, etc)
 * which will be traditional properties
 * a feature is always optional and goes into the feature collection
 * a feature is described as an association of a concept, and that association is fuzzy (0<association<1)
 * so, features are tacked onto a concept in the following format:
 * concept_key -> array({feature_key:asdf1234, AV = 0.5}, {feature_key:dkdk1234, AV = 0.1}) //where AV stands for Associative Value
 * and there is a category collection that has a list of features and their weights:
 * category_key -> array({feature_key:asdf1234, Weight=0.2}, etc.)
 * the concept then gets the categories connected to it via another collection:
 * concept_key -> array (category_key:a32333, p = 0.8) where p is the probability it falls in that category (0<p<1)
 * 
 */

//////////////////////////////
//functions for features
/////////////////////////////

//returns key of new feature (in a json object with key = feature key, and value = other information in an array 
function new_feature( $feature_name, $author_id, $desc = '') {
	//the feature can be a proprety.  for instance, "location" is a feature but also a property
	//so you add the feature, "location", with a data type and all that, save the key in the concept
	//then, the concept will also add it as a property, since the feature is marked as is_property: true
	//return feature key?
	
}

function add_concept_feature($concept, $feature){
	//you can think of a feature as "has_xxx"
	
}



function add_cat_feature($cat, $feature) {
}

function get_features($con_key) {
}

//returns true if concept has a particular feature
function has_feature($con_key, $feature_key) {

}

function feature_is_property($feature_key) {
}

function get_property($concept, $prop_name) {
}


////////////////////////////////
//category functions
/////////////////////////////// 

//creates a new category object based a list of feature ids
function new_cat($name, $features) {
	$uid = create_key();
	$date = date('m/d/Y h:i:s a', time());
	
	$ret = array(
		'uid' => $uid,
		'features' => features,
	);
	
	return $ret;
}

function add_cat($key, $cat) {
	
}

function add_concept_cat($concept, $cat_key) {
}

function get_cat_propreties($cat_key) {
	//get the list of properties
	//check if there is a field: queried_parents, if true, don't query them
	//otherwise, create a list in memory of all parent categories by _id key
	//inside, put a list of properties
	//extract a unique list of properties
	//replace the property list in the category
	//return list
}

function is_cat($con_key, $cat_key) {
	$properties = get_cat_propreties($cat_key);
	//get the mean of the AVs
	
	return false;
}

function assign_concept_cat($con_key, $cat_key) {
	if(is_cat($con_key, $cat_key)) {
	}
}

function load_cats($con_key) {
	
}
//inheritance function
function add_cat_parent($cat, $parent) {
	
}



function find_concept_cats($concept) {

}

///actions
/*
 * actions behave like features and is defined like one, but has
 * an extra definition that marks it as an 'action'.  this allows the concept
 * to be used in an OOP fashion, and allows for the data to do something productive,
 * giving the data context.
 */
?>