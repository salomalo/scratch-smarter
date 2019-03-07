<?php
global $wpdb, $pwccrm_table_name;

$pwccrm_table_name = "pwccrm_opt_in_information";
$table_name = $wpdb->prefix . $pwccrm_table_name;

if($_POST['pwccrm_deleting_opt_in_id']>0)
{
	$sql_delete_from_opt_in_list = "DELETE FROM ".$table_name." WHERE id = ".$_POST['pwccrm_deleting_opt_in_id'];
	mysql_query($sql_delete_from_opt_in_list);
}
?>
<style>
.listing_of_data{
	background-color:#F2FBFE;
}
.listing_of_data:hover
{
	background-color:#DCF2FF;
}


.mouse_over_effect_only{
	text-decoration:none;
	color:#09F; 
	cursor:pointer;
}
.mouse_over_effect_only:hover{
	
	text-decoration:underline;
	color:#09F; 
	cursor:pointer;
}

.general_text{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}

</style>
<script type="text/javascript">
function js_pwccrm_delete_opt_in_information(opt_in_id)
{
	var x = confirm("Do you want to delete this Opt-In Information?");
	if(x)
	{
		document.getElementById('pwccrm_deleting_opt_in_id').value = opt_in_id;
		document.pwccrm_delete_existing_record.submit();
	}
}
</script>
<div style="width:700px; padding:20px; border:1px solid #CCCCCC;">

<div style="color:#666; font-family:Arial, Helvetica, sans-serif; font-size:32px; padding-bottom:20px;">
Manage Opt-In Form
</div>

<table width="100%" cellpadding="3" cellspacing="3" border="0">
<tr>
    <th width="32%" style="background-color:#C5E9FF;" height="28" valign="middle">Name
    </th>
    <th width="56%" style="background-color:#C5E9FF;" height="28" valign="middle">Code To Paste
    </th>
    <th width="12%" style="background-color:#C5E9FF;" height="28" valign="middle">Manage
    </th>
</tr>
<?php

$sql_find_opt_in_data = "SELECT `id`, `name`, `link_for_wp`, `active_or_not` FROM ".$table_name." ORDER BY `name` ASC";
$result_sql_find_opt_in_data = mysql_query($sql_find_opt_in_data);
while($result_set_sql_find_opt_in_data = mysql_fetch_array($result_sql_find_opt_in_data))
{
?>
<tr class="listing_of_data">
    <td height="43" valign="middle" class="general_text" align="left" style="padding:3px;"><?php echo $result_set_sql_find_opt_in_data['name'];?>
    </td>
    <td height="43" valign="middle" class="general_text" align="center" style="padding:3px;"><?php echo $result_set_sql_find_opt_in_data['link_for_wp'];?>
    </td>
    <td height="43" valign="middle" class="general_text" align="center" style="padding:3px;"> 
    <a href="admin.php?page=optin&action=edit&opt_in_id=<?php echo $result_set_sql_find_opt_in_data['id'];?>"><img src="../wp-content/plugins/pwccrm_opt_in_plugin/image/edit.png" border="0" /></a>
    &nbsp;&nbsp;
    <span class="general_text mouse_over_effect_only" title="Delete Opt-In Form" onclick="js_pwccrm_delete_opt_in_information('<?php echo $result_set_sql_find_opt_in_data['id'];?>')"><img src="../wp-content/plugins/pwccrm_opt_in_plugin/image/delete_icon.png" border="0" /></span>
    </td>
</tr>
<?php
}
?>
</table>
</div>
<form name="pwccrm_delete_existing_record" id="pwccrm_delete_existing_record" method="post" action="">
<input type="hidden" name="pwccrm_deleting_opt_in_id" id="pwccrm_deleting_opt_in_id" value="0" />
</form>