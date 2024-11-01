jQuery(document).ready(function() {	
	jQuery("#add_set_button").click(function(){
		var obj = this ;
		var nnum = jQuery(".trigger_one_set").length ;
		nnum++ ;
		var data = {
				action: "get_trigger_set" ,
				nnumber: nnum
			   };
		jQuery(this).parent().children("span").addClass("loading") ;
		jQuery.post(ajaxurl, data, function(response) {
			if(response){
				jQuery(obj).parent().children("span").removeClass("loading") ;
				jQuery("#trigger_sets").append(response) ;
			}			
		}); 
	});
	
	jQuery(".trigger_set_remove").live("click", function(){
		jQuery(this).parent(".trigger_one_set").fadeOut(function(){
			jQuery(this).remove() ;
		}) ;
		return false;
	});
	
	jQuery("#trigger_box_save").click(function(){
		if(!jQuery("#triggerbox_name").val()){
			var messaging = jQuery("#add-trigger-content").children(".mymessage") ;
			setting_message_show("Please insert trigger box name.", "err_message", messaging);
			return false;
		}
		
		if(!wptgg_chk_data())return false;
		var save_sata = get_trigger_data() ;
		jQuery("#hi_trigger_info").val(save_sata) ;
		jQuery("#trigger_frm").submit();
	});
	
	wptgg_chk_data = function(){
		var chk = true ;
		jQuery(".one_set_content").each(function(){
			var messaging = jQuery(this).parent().children(".mymessage") ;
			var type_txt = jQuery(this).find(".type_txt").val();
			var display_txt = jQuery(this).find(".display_txt").val();
			if( !(type_txt && display_txt)){
				setting_message_show("Oops, you forgot something!", "err_message", messaging);
				chk = false ;
				return false;
			}
		});
		return chk ;
	}
	
	get_trigger_data = function(){
		var saveData = {} ;
		var i = 1 ;
		jQuery(".one_set_content").each(function(){
			var oneData = {} ;
			var type_txt = jQuery(this).find(".type_txt").val();
			var temp = type_txt.split("\n") ;
			oneData["type_txt"] = temp ;
			oneData["display_txt"] = jQuery(this).find(".display_txt").val() ;
			saveData["data_" + i] = oneData ;
			i++ ; 
		});
		return jQuery.toJSON(saveData) ;
	}
});