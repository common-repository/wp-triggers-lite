<?php 
$trigger_id = ($_GET["trigger_id"]) ? $_GET["trigger_id"] : "" ;
$pagetitle = ($trigger_id) ? "Edit Trigger Box" : "Add New Trigger Box" ;
if( $trigger_id ){
	$trigger = wptggBackEnd::get_trigger(array("ID" => $trigger_id)) ;
	$infos = json_decode($trigger->box_info) ;
	
}
?>
<div class="wrap plugin-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php echo __($pagetitle) ;?></h2>		
	<form id="trigger_frm" method="post">
		<div id="add-trigger-content">		
			<div class="mymessage"></div>			
			<div>
				<label style="font-size:16px;">Trigger Box Name</label><br/>
				<input type="text" id="triggerbox_name" name="triggerbox_name" class="trigger-txt" value="<?php echo $trigger->box_name;?>">
			</div>
			<div style="margin-top:20px;">				
				<div id="trigger_sets">
					<?php 
					if( $infos ){
						$i = 1 ;
						foreach ($infos as $v) {
							echo wptggBackEnd::get_trigger_one_set($i, $v);
							$i++ ;
						}
					}else{
						 echo wptggBackEnd::get_trigger_one_set();
					}
					?>							
				</div>				
				<div style="margin-top:20px;">					
					
					<span style="float:right;margin-top:5px;">&nbsp;</span>
				</div>				
			</div>
			<div class="clear"></div>
			<div>			
				<fieldset style="border:0px;">
					<legend><?php echo  __("Trigger Not Found");?></legend>
					<div style="margin-top:10px;">
						<div class="mymessage"></div>
						<div class="txtarea_div">								
							<label>If the visitor types in ANYTHING else, then display this...</label><br>
							<textarea id="no_found_txt" name="no_found_txt" rows="5" cols="" style="width:100%;"><?php echo $trigger->no_found;?></textarea><br>
							<label>insert html here</label>										
						</div>																				
					</div>													
				</fieldset>						
			</div>
			<?php 
			$chk = "checked" ;
			if( $trigger->show_chk == "unchecked" )$chk = "" ;
			?>
			<div style="margin-top:15px;">
				<input type="checkbox" name="hide_trigger_chk" id="hide_trigger_chk" value="checked" <?php echo $chk;?> /><label style="margin-left:5px;">Hide this Trigger Box after a visitor searches for a Trigger</label>
			</div>	
			<div style="margin-top:30px;">
				<input type="submit" id="trigger_box_save" class="button-primary trigger_button" value="Save Changes"  />
			</div>			
		</div>	
		<input type="hidden" id="hi_trigger_info" name="hi_trigger_info" />	
		<input type="hidden" id="hi_trigger_id" name="hi_trigger_id" value="<?php echo $trigger_id;?>" />				
	</form>	
</div>