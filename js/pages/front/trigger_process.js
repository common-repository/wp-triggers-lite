jQuery(document).ready(function() {	
	jQuery(".wptgg_pass_key").live("keyup", function(event){
		if(event.keyCode == 13){
			if(!jQuery(".wptgg_pass_key").val())return;
			var obj = this ;
			var data = {
					action: "get_display_trigger" ,
					wptgg_id: jQuery(this).parent().children(".wptgg_id").val(),
					passkey: jQuery(this).val()
				   };
			jQuery(this).parent().children("span").addClass("wptgg_loading") ;
			jQuery.post(wptgg_ajaxurl, data, function(response) {
				jQuery(obj).parent().children("span").removeClass("wptgg_loading") ;
				if(response){
					jQuery(".wptgg_action").removeClass("wptgg_loading") ;
					var response_arr = response.split("@#@") ;
					if( response_arr[0] == "checked" ){
						jQuery(obj).parent(".wptrigger_content").html(response_arr[1]) ;
					}else{
						jQuery(obj).parent(".wptrigger_content").children(".wptrigger_append").html(response_arr[1]) ;
					}					
				}			
			});
		}
	}) ;	
});