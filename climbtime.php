<?php 

/*
Plugin Name: Climbtime Concept Catalog
Version: 1.0.1
Author: Dorian White (Lehel Kovach)
License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

require "ct_db_wrappers.php";

init_db();


add_action( 'init', 'climbtime_cat_load' ); //xxx

function climbtime_cat_load() {
	global $current_user;
    get_currentuserinfo();
    $author = $current_user->ID;
	     
	wp_register_style( 'ctstyle', plugins_url('ctstyle.css', __FILE__) );
    
}
///////////////////////utility functions



//////////////////////////////////////////




add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );

function theme_name_scripts() {
	wp_enqueue_style( 'ctstyle', get_stylesheet_uri() );
	wp_register_style('jqueryuistyle', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
	wp_register_style('autostyle', '/wp-content/plugins/climbtime/scripts/jquery.autocomplete.css');
	wp_enqueue_style('jqueryuistyle');
	wp_enqueue_style('autostyle');
	//wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
	wp_deregister_script( 'jquery' ); 
    //wp_register_script( 'jquery', 'http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js', false, '' ); 
    wp_register_script( 'jquery', 'http://code.jquery.com/jquery-latest.min.js', false, '' );
	wp_register_script( 'googlemaps', 'http://maps.googleapis.com/maps/api/js?sensor=false', false, '' );
	wp_register_script( 'addconcept', plugins_url( 'scripts/add_concept.js', __FILE__ ), false, '' );
	
    wp_enqueue_script('jquery');
    wp_enqueue_script('googlemaps');
    wp_enqueue_script('addconcept');
    
    $myvars = array( 
    'ajaxurl' => admin_url( 'admin-ajax.php' )//,
    //'somevar1' => $somevar_from_somewhere,
    //'somevar2' => $somevar_from_elsewhere
	);
	
	wp_localize_script( 'addconcept', 'MyAjax', $myvars );

}



//WP shortcode for insertion of concept entry form


add_shortcode('new_concept', 'ct_new_concept'); //(use ajax)
function ct_new_concept($atts) {
	//add a form that goes to a submission action php page that redirects back
	//fill in the fields before drawing them if its a view/edit page

	$ret .= "<input type='hidden' name='return_url' value='" . $_SERVER["REQUEST_URI"] . " />";
	$ret = "<div class='ctaddform'>";
	$ret = "<div class='ctleftcolumn'>";
	$ret .= "<label class='ctlabel' for='title'>Title</label>";
	$ret .= "<input class='ctinput' type='text' id='title' name='title'  />";
	$ret .= "<br/>";
	$ret .= "<label class='ctlabel' for='desc' >Description</label>";
	$ret .= "<textarea class='ctinput'  id='desc' name='desc'></textarea>";
	
	
	$ret .= "<br/>";
	$ret .= "<br/>";

	
	//dyanmically get all the fields for the features
	$ret .= get_feature_list_ctl();
	$ret .= "<br/>";
	$ret .= "<br/>";
	$ret .= "<br/>";
	$ret .= "<br/>";
		$ret .= "<br/>";
	$ret .= "<br/>";
	$ret .= "<br/>";
	$ret .= "<br/>";
$ret .= "<br/>";
	$ret .= "<br/>";
	$ret .= "</div>";
	$ret .= "<div class='ctrightcolumn'>";
	$ret .= "<button class='ctsubmit' id='addconcept'>Add</button>";
	$ret .= "</div>";
	$ret .= "</div>";
	
	
	
	return $ret;
}

function get_feature_list_ctl() {
	//create the javascript to dynamically insert a new feature element
	$ret = "<script type='text/javascript'>
	
	
	</script>";
	//start the section
	$ret .= "<div class='ctfeatureset'>";
	$ret .= "<fieldset class='ctfields'>";
	$ret .= "<legend>Features:</legend>";
	//create a list
	$ret .= "<ul id='features_list'>";
	//add header as first row
	$ret .= "<li class='ctfeature'>";
	$ret .= "<div class='ctaddfeaturelabels'>";
  	$ret .= "<div class='typelabel'>Type of...</div>";
  	$ret .= "<div class='titlelabel'>Feature name</div>";
  	$ret .= "<div class='valuelabel'>Feature's value</div>";
  	$ret .= "<div class='removelabel'>X</div>";
  	$ret .= "</div>";
	$ret .="</ul>";
	$ret .="<button class='ctbutton' id='add_feature'>+</button>";
	$ret .= "</fieldset>";
	$ret .= "</div>";
	$ret .= "<div id='geobox_bg'><button id='mapdone'>Done</button><div id='geobox'></div></div>";
	
	return $ret;
}



add_action('wp_ajax_addconcept', 'addconcept_callback');
add_action('wp_ajax_nopriv_addconcept', 'addconcept_callback' );

function addconcept_callback() {
	global $wpdb; // this is how you get access to the database
	
	ob_clean();
	
	$title = $_POST['title'];
	$desc = $_POST['desc'];
	$features = $_POST['_featureList'];
	
	//add values to new concept in mongodb
	ct_add_concept($title, $desc, $features);
	$json = array('ct_return_code' => 'success');
	echo json_encode($features);
	die();
	
    //wp_send_json( $json ); //$features
    
}


add_shortcode('view_edit_concept', 'ct_view_edit_concept');  //use ajax
function ct_view_edit_concept($atts) {
	//add a form that goes to a submission action php page that redirects back
	//fill in the fields before drawing them if its a view/edit page
}

//helper HTML functions  (use Wordpress ajax functions if need be)
function render_concept_widget($concept_key) {

}


function create_concept_auto_fill() {
}

function create_feature_auto_fill() {
	//create the javascript/jquery function
}








?>
