<?php 
$triggers = wptggBackEnd::get_trigger() ;
//echo "<pre>";
//print_r($triggers) ;
//echo "</pre>" ;
//----------------
$count_posts = count($triggers);				
$pagenum=($_GET["paged"]) ? $_GET["paged"] : 1;	
$per_page=($per_page) ? $per_page : 15;
?>
<div class="wrap plugin-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php echo __("WP Triggers") ;?><a href="admin.php?page=add-trigger" class="add-new-h2"><?php echo __("Add New"); ?></a></h2>		
	<div>
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php wptggBackEnd::get_page_link($count_posts,$pagenum, $per_page);?>
			</div>
		</div>
		<table class="wp-list-table widefat fixed posts" cellspacing="0" border=0>
			<thead>
				<?php wptggBackEnd::get_table_header();?>
			</thead>	
			<tfoot>
				<?php wptggBackEnd::get_table_header();?>
			</tfoot>	
			<tbody>
				<?php 
				if( $triggers ){
					$count = 0;
					$start = ($pagenum - 1) * $per_page;
					$end = $start + $per_page;
					foreach ($triggers as $trigger) {
						if ( $count >= $end )
							break;
						if ( $count >= $start )
						{
							$nnum++ ;
							echo "<tr class='alternate author-self status-publish format-default iedit'>";
							echo "<th scope='row' class='check-column'><input type='checkbox' name='linkcheck[]' value='1'></th>";
							echo "<td><strong>{$nnum}</strong></td>" ;
							echo "<td>
									<a href='admin.php?page=wp-trigger&trigger_id={$trigger->ID}'><strong>{$trigger->box_name}</strong></a>
									<div class='row-actions'>						
										<span><a href='admin.php?page=wp-trigger&trigger_id={$trigger->ID}' class='menu_edit_link'>Edit</a></span>&nbsp;|&nbsp; 
										<span><a href='admin.php?page=wp-trigger&delete={$trigger->ID}' class='menu_delete_link'>Delete</a></span>						
									</div>
								 </td>" ;
							echo "<td>[wptrigger id={$trigger->ID}]</td>" ;
							echo "<td>{$trigger->create_datetime}</td>" ;
							echo "</tr>" ;
						}
						$count++ ;
					}
					
				}else{
					$msg = "<lavel style='height:30px;'>You don't have any Trigger Boxes yet! Let's go </label> 
							<a href='admin.php?page=add-trigger'>add one</a> !" ;												
					echo "<tr>" ;
					echo "<td colspan='5' style='padding:20px;'>$msg</td>" ;
					echo "</tr>" ;
				}
				?>
				<tr>
				
				</tr>
			</tbody>
		</table>
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php wptggBackEnd::get_page_link($count_posts,$pagenum, $per_page);?>
			</div>
		</div><div id="wpt_update"><h2>Thank you for trying WP Triggers Lite!</h2><br><a href="http://www.wptriggers.com/upgrade" target="_blank"><img class="alignright" alt="CLICK TO UPGRADE TO WPTRIGGERS PRO" src="http://www.wptriggers.com/wp-content/uploads/button-upgrade.png" /></a>Did you know that there is a Pro version that has a ton of interactive features that your visitors will love?  Here is just some of the awesome features in the Pro version:<ul>	<li><strong>Shortcode Support</strong> (display shortcode from other plugins)</li>	<li><strong>Multiple Trigger Sets</strong> (create different messages/actions within the same trigger box)</li>	<li><strong>Email Address Trigger</strong> (email addresses can be used as a valid trigger to build your mailing list)</li>	<li><strong>Submit Button</strong> (include a standard or image submit button with each trigger box)</li>	<li><strong>URL Redirect</strong> (redirect users to a specific URL when they type in a trigger)</li>	<li><strong>Trigger History</strong> (see the triggers that people submit along with the date and time)</li></ul>With those features, you can make some awesome things and you get it all for under $20 bucks.  It's a sweet deal.  Click the button and upgrade today!<br><br>Not sure what you can do with those PRO features?  <a href="http://www.wptriggers.com/ideas/" target="_blank">Click here to see some amazing ideas</a></div>
</div>
