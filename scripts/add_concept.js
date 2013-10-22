//File Name: add_concept.js
//Author: Dorian White (Lehel Kovach)
//Date: 10/2013
//Description: this file contains javascript bridge code between the html/wordpress and the php api (ajax)
//specifically, the one wordpress hook for when the add concept button is pressed and concept data
//is sent via ajax through this function to the php code which interacts with the mongodb



//name: add_concept_action
//description: wordpress hook for when user submits a new concept via web form
function add_concept_action() {

	//var feature_list = new Array();
	//use xpath to select all the feature boxes
	var featureTypes = jQuery("select[id*='feature_type']");
	var featureNames = jQuery("input[id*='feature_name']");
	var featureValues = jQuery("input[id*='feature_value']"); //input[@id="feature_value*"]
	
	var featureList = new Array();
	var feature;
	
	for(var i = 0; i != featureValues.length; i++) {
		feature = {
				type: featureTypes[i].value,
				name: featureNames[i].value,
				value: featureValues[i].value
		};
		featureList.push(feature);
	
	}
	
	var data = {
		action: 'addconcept',
		title: jQuery('#title').text(),
		desc: jQuery('#desc').text(),
		_featureList: featureList
	};
	
	//data = JSON.stringify(data);

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(MyAjax.ajaxurl, data, function(response) {
		alert('Got this from the server: ' + response);
	});
}
	
	var x = 0;

	
	jQuery(document).ready(function() {
		jQuery('#geobox_bg').hide();
		jQuery('#addconcept').click(function() { add_concept_action() });
				
  		jQuery('#add_feature').click(function() {	
  			
  			jQuery('#features_list').append('<li id=\'feature' + x + '\'>' +
  				'<div  class=\'ctfeaturerow\'>' +
  				'<select class=\'ctnewtype\' id=\'feature_type' + x + '\' name=\'feature_type' + x + '\'>' +
  				'<option selected=\'selected\' value=\'Just a feature\'>Just a feature</option>' +
  				'<option value=\'True/False\'>True/False</option>' +
  				'<option value=\'Number\'>Number</option>' +
  				'<option value=\'Text\'>Text</option>' +
  				'<option value=\'Date/Time\'>Date/Time</option>' +
  				'<option value=\'Time duration\'>Time duration</option>' +
  				'<option value=\'Location\'>Location</option>' +
  				'<option value=\'URL\'>URL</option></select>' +
  				'<input class=\'ctnewfeaturetitle\' id=\'feature_name' + x +  '\' name=\'feature[' + x + ']\' />' +
  				'<input class=\'ctnewvalue\' name=\'feature_value' + x + '\' id=\'feature_value' + x + '\' />' +
  				'<button id=\'remove' + x + '\'>-</button></div></li>' );	
  				
			
			
			jQuery('#remove' + x).click(function() {			
				var currentId = $(this).attr('id');
				var y = currentId.substring(6);
				jQuery('#feature' + y).remove();
			});
			
			//needed globals
			var initialized = false;
			var currentPlace = null;
			var markersArray = [];
			
			function clearOverlays() {
				  for (var i = 0; i < markersArray.length; i++ ) {
				    markersArray[i].setMap(null);
				  }
				  markersArray = [];
				}
			
			
			jQuery("#feature_type" + x).change(function() {			
				var numSuffix = jQuery(this).attr('id').substring(12);
				jQuery('#feature_value' + numSuffix).unbind();
				jQuery('#feature_value' + numSuffix).click(function() {
				//get the text of the selected value
					var numSuffix = jQuery(this).attr('id').substring(13);
					var selectVal = jQuery('#feature_type' + numSuffix + ' option:selected').val();
					var value_id = jQuery(this).attr('id');
				    
					var position = jQuery(this).position();   
					$('#geobox_bg').css("position", "absolute");
				    $('#geobox_bg').css("left", position.left + 100);
				    $('#geobox_bg').css("top", position.top);
				    
					if(selectVal == 'Location') {
						if(!initialized) {
							intialized = true;
							
							var mapOptions = {
								    center: new google.maps.LatLng(40.8688, 111.2195),
								    zoom: 13,
								    mapTypeId: google.maps.MapTypeId.ROADMAP
								  };
							var map = new google.maps.Map(document.getElementById('geobox'), mapOptions);
							  var defaultBounds = new google.maps.LatLngBounds(
							      new google.maps.LatLng(30, 90),
							      new google.maps.LatLng(50, 110));
							  map.fitBounds(defaultBounds);

							  var input = (document.getElementById(value_id));
							  var searchBox = new google.maps.places.SearchBox(input);
							  
							  
							  //add removeListeners! for both places and map changed.

							  google.maps.event.addListener(searchBox, 'places_changed', function() {
								 
								  
							    var places = searchBox.getPlaces();
							    
							    for (var i = 0, marker; marker = markersArray[i]; i++) {
							    	marker.setMap(null);
							    }
							    
							    

							    markersArray = [];
							    var bounds = new google.maps.LatLngBounds();
							    
							    for (var i = 0, place; place = places[i]; i++) {
							    
							    	var image = {
							        url: place.icon,
							        size: new google.maps.Size(71, 71),
							        origin: new google.maps.Point(0, 0),
							        anchor: new google.maps.Point(17, 34),
							        scaledSize: new google.maps.Size(25, 25)
							      };

							    
							      var marker = new google.maps.Marker({
							        map: map,
							        icon: image,
							        title: place.name,
							        position: place.geometry.location
							      });

							      
							      markersArray.push(marker);
							      google.maps.event.addListener(marker,"click",function(){});
							   
							      bounds.extend(place.geometry.location);
							      map.fitBounds(bounds);
							      
							      if(i == 0) {
							    	  alert(place.geometry.location + " asdf");
							    	 
							      }
							    }

							    
							  });

							  google.maps.event.addListener(map, 'bounds_changed', function() {
							    var bounds = map.getBounds();
							    searchBox.setBounds(bounds);
							  });
							  
							  google.maps.event.addListener(map, 'click', function(e) {
								  clearOverlays();
								  
								  var marker = new google.maps.Marker({
								        map: map,
								        position: e.latLng
								      });

								  markersArray.push(marker);
								  google.maps.event.addListener(marker,"click",function(){});
							  });
							
						}
												
						jQuery('#geobox_bg').delay(400).fadeIn(300);
						
						jQuery(this).clear();
						jQuery(this).change(function() {
							
						});
						
					}
					else {
						jQuery('#geobox_bg').hide();
					}
				});
				
				jQuery('#mapdone').click(function() {
					jQuery('#geobox_bg').delay(100).fadeOut(500);
				});
				
				
			//xpath query for all the selectboxes
			
				//get the name and get the number suffix
					
				
				//RESAVE THIS!!!!
				
				
			});
			
			x++;
		});
		
	});
	
	
	

        
        



