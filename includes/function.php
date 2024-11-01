<?php
if( $_POST["hi_trigger_info"] )wptggBackEnd::trigger_save() ;
if( $_GET["page"] == "wp-trigger" && $_GET["delete"] )wptggBackEnd::trigger_delete() ;
/*************************************/
/*      Trigger basic function       */
/*************************************/ 
class wptggAction
{
	function insert_to_table($table, $data)
	{
		global $wpdb;
		$result = $wpdb->insert($table, $data);
		if( $result ){			
			$row = $wpdb->get_row("SELECT max(ID) as maxid FROM $table");		
			if($row)
				return $row->maxid ;
			else
				return false;
		}else{
			return false;
		}
	}
	
	function get_trigger($param = null, $getform = "rows")
	{
		global $wpdb;
		
		$chgdata = array(
						"name" => "box_name",
						"date" => "create_datetime"
					) ;
					
		$orderby = ( $_GET['orderby'] ) ? " ORDER BY {$chgdata[$_GET['orderby']]} {$_GET['order']}" : "  ORDER BY ID" ;
		
		if($param["ID"])
			$result = $wpdb->get_row( "SELECT * FROM " . WPTGG_TABLE . " WHERE ID = {$param["ID"]}" );
		else 
			$result = $wpdb->get_results( "SELECT * FROM " . WPTGG_TABLE . " $orderby" );
					
		return $result;
	}
}

class wptggBackEnd extends wptggAction
{
	static function trigger_save()
	{
		$chked = ($_POST["hide_trigger_chk"]) ? $_POST["hide_trigger_chk"] : "unchecked" ;
		$data = array(
					"box_name" => $_POST["triggerbox_name"],
					"box_info" => stripcslashes($_POST["hi_trigger_info"]),
					"no_found" => stripcslashes($_POST["no_found_txt"]),
					"show_chk" => $chked
					) ;
		
		if( $_POST["hi_trigger_id"] ){
			global $wpdb ;
			$wpdb->update(WPTGG_TABLE, $data, array("ID" => $_POST["hi_trigger_id"])) ;
		}else{
			$data["create_datetime"] = current_time("mysql") ;
			wptggBackEnd::insert_to_table(WPTGG_TABLE, $data) ;
		}
		wp_redirect(admin_url("admin.php?page=wp-trigger")) ;
		exit() ;
	}
	
	static function trigger_delete()
	{
		global $wpdb ;
		$iid = $_GET["delete"] ;
		$wpdb->get_results("DELETE FROM " . WPTGG_TABLE . " WHERE ID = {$iid}") ;
		wp_redirect(admin_url("admin.php?page=wp-trigger")) ;		
	}
	
	static function get_page_link($count_posts,$pagenum,$per_page=15)
	{
		$allpages=ceil($count_posts / $per_page);
		$base= add_query_arg( 'paged', '%#%' );
		$page_links = paginate_links( array(
			'base' => $base,
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $allpages,
			'current' => $pagenum
		));
		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
				number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
				number_format_i18n( $count_posts ),
				$page_links
				);
		echo $page_links_text;
	}
	
	static function get_table_header()
	{
		$order = ( $_GET["order"] == "desc" ) ? "asc" : "desc" ;
		
		if( $_GET["orderby"] == "name" )
			$bn_order_class = $_GET["order"] ;
		else
			$bn_order_class = "" ;
			
		if( $_GET["orderby"] == "date" )
			$cd_order_class = $_GET["order"] ;
		else
			$cd_order_class = "" ;
			
		
		echo "<tr>";
		echo "<th scope='col' class='manage-column check-column' ><input type='checkbox'></th>";
		echo "<th width='50px'>ID</th>";
		echo "<th width='25%' class='sorted $bn_order_class'>
					<a href='admin.php?page=wp-trigger&orderby=name&order=$order'>
						<span>". __("Name") . "</span>
						<span class='sorting-indicator'></span>
					</a>
				</th>";
		echo "<th>ShortCode</th>";
		echo "<th class='sorted $cd_order_class'>
					<a href='admin.php?page=wp-trigger&orderby=date&order=$order'>
						<span>". __("Date Added") . "</span>
						<span class='sorting-indicator'></span>
					</a>
				</th>";
		echo "</tr>";
	}
	
	static function get_trigger_one_set($num=1, $ddata=null)
	{
		if( $ddata->type_txt && is_array($ddata->type_txt)){
			foreach ($ddata->type_txt as $v) {
				$typetxt .= $v . "\n" ;
			}
		}
		$requ = "" ;
		if( $num == 1 )$requ = " (REQUIRED)" ;
		$str = '<fieldset class="trigger_one_set" style="background-color:#fbfbfb;">
					<legend style="font-size:14px;">' .  __("Trigger Set #") . $num . $requ . '</legend>							
					<div class="mymessage"></div>' ;
		if( $num > 1 )$str .= '<a href="#" class="trigger_set_remove">Remove</a>' ;
		$str .='	<div class="one_set_content">					
						<table width="100%">									
							<tr> 
								<td width="50%">
									<div class="txtarea_div">
										<label>If the visitor types in...</label><br>
										<textarea class="type_txt" rows="10" cols="" >' . $typetxt . '</textarea><br>
										<label>One Trigger per line - NOT case sensitive</label>
									</div>
								</td>
								<td>
									<div class="txtarea_div" style="width:95%;float:right;">
										<label>then display this</label><br>
										<textarea rows="10" class="display_txt" cols="">' . $ddata->display_txt . '</textarea>
										<label>Insert html here</label>
									</div>
								</td>
							</tr>
						</table>																									
					</div>													
				</fieldset>';
		return $str;
	}
	
	static function ajax_get_trigger_one_set()
	{
		$str = wptggBackEnd::get_trigger_one_set($_POST["nnumber"]) ;
		echo $str ;
		exit() ;
	}
}

class wptggFrontEnd extends wptggAction
{
	static function get_display_trigger()
	{
		$trigger = wptggFrontEnd::get_trigger(array("ID" => $_POST["wptgg_id"])) ;
		//echo $_POST["wptgg_id"] . "##" . $_POST["passkey"] ;
		$infos = json_decode($trigger->box_info) ;
		if( $infos ){
			foreach ($infos as $info) {
				if( wptggFrontEnd::check_exist_element($info->type_txt, $_POST["passkey"])){
					echo $trigger->show_chk . "@#@" . nl2br($info->display_txt) ;
					exit() ;
				}
			}
			echo $trigger->show_chk . "@#@" . nl2br($trigger->no_found) ;
		}else{
			echo "" ;
		}
		exit() ;
	}
	
	static function check_exist_element($arrs, $elm)
	{
		if(count($arrs)){
			foreach ($arrs as $v) {
				if(!$v)continue;
				if( trim(strtolower($elm)) == trim(strtolower($v)))return true;
			}
			return false;
		}	
	}
}




?>