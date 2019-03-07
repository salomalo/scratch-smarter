<?php


global $wpdb, $pwccrm_table_name;

$pwccrm_table_name = "pwccrm_opt_in_information";
$table_name = $wpdb->prefix . $pwccrm_table_name;

$opt_value_inserted = 0;

if($_POST['pwccrm_dummy_field_for_post']==1)
{
	$opt_in_form_name =  $_POST['opt_in_form_name'];
	$opt_in_form_content =  $_POST['opt_in_form_content'];
	
	$opt_in_form_name = str_replace('"',"&quot;",$opt_in_form_name);
	$opt_in_form_content = str_replace('"',"&quot;",$opt_in_form_content);
	
	$sql_find_if_there = "SELECT * FROM ".$table_name." WHERE name like \"$opt_in_form_name\"";
	$result_sql_find_if_there = mysql_query($sql_find_if_there);
	if(!mysql_num_rows($result_sql_find_if_there))
	{
		$sql_insert_data = "INSERT INTO ".$table_name." (`name` ,`code` ,`active_or_not` )VALUES (\"".$opt_in_form_name."\", \"".$opt_in_form_content."\", '0')";
		if(mysql_query($sql_insert_data))
		{
			$pwccrm_opt_in_id = mysql_insert_id();
			$sql_update_query = "UPDATE ".$table_name." SET `link_for_wp` = \"[PWCCRM_OPT_IN_FORM  foo='".$pwccrm_opt_in_id."']\" WHERE id = ".$pwccrm_opt_in_id;
			mysql_query($sql_update_query);
			$opt_value_inserted = 1;
		}
	}
	else
	{
		$opt_value_inserted = 2;
	}
}
else
{
	
}
?>
<script type="text/javascript">
function js_pwccrm_check_if_info_there_or_not()
{
	if(document.getElementById('opt_in_form_name').value=="")
	{
		alert("Please provide a name for your convenience. Name should be unique.");
	}
	else if(document.getElementById('opt_in_form_content').value=="")
	{
		alert("Please provide the Opt-In HTML code you got from pwccrm.com.");
	}
	else
	{
		document.getElementById('pwccrm_dummy_field_for_post').value = 1;
		document.pwccrm_plugin_information.submit();
	}
}
</script>

<div style="width:500px; padding:20px; border:1px solid #CCCCCC;">
<div style="color:#666; font-family:Arial, Helvetica, sans-serif; font-size:32px;">
Add Opt-In Form
</div>
<div id="pwccrm_opt_in_record_data_notification_for_admin" style="padding-top:10px; display:none;" align="center">
<?php
if($opt_value_inserted==1)
{
	echo "<font color='#006600'>Opt-In Form Record Inserted.</font>";
}
elseif($opt_value_inserted==2)
{
	echo "<font color='#CC0000'>Name Should Be Unique.</font>";
}
?>
<?php if($opt_value_inserted>0){
	echo "<script>document.getElementById('pwccrm_opt_in_record_data_notification_for_admin').style.display='';</script>";
	}?>
<script type="text/javascript">
setTimeout("document.getElementById('pwccrm_opt_in_record_data_notification_for_admin').style.display='none';",4000);
</script>
</div>
<form name="pwccrm_plugin_information" id="pwccrm_plugin_information" method="post" action="">
<input type="hidden" name="pwccrm_dummy_field_for_post" id="pwccrm_dummy_field_for_post" value="0" />
<table width="100%" cellpadding="2" cellspacing="2" border="0">
<tr>
	<td colspan="2" height="10">
    </td>
</tr>
<tr >
	<td  align="left"> Opt-In Form Name:
    </td>
	<td  align="left"> <input type="text" name="opt_in_form_name" id="opt_in_form_name" value="" style="width:370px;" />
    </td>
</tr>
<tr>
	<td colspan="2" height="5">
    </td>
</tr>
<tr>
	<td> Form Content:
    </td>
	<td> 
    </td>
</tr>
<tr>
	<td colspan="2">
    <textarea cols="5" rows="5" name="opt_in_form_content" id="opt_in_form_content" style="width:500px; height:400px; min-width:500px; min-height:400px; max-width:500px; max-height:400px; overflow:auto;"></textarea>
    </td>
</tr>
<tr>
	<td colspan="2" height="5">
    </td>
</tr>
<tr>
	<td colspan="2" height="5" align="right">
    
    <img src="../wp-content/plugins/pwccrm_opt_in_plugin/image/submit.png" border="0" id="pwccrm_submit_icon" onmouseover="document.getElementById('pwccrm_submit_icon').src='../wp-content/plugins/pwccrm_opt_in_plugin/image/submit_m.png'" onmouseout="document.getElementById('pwccrm_submit_icon').src='../wp-content/plugins/pwccrm_opt_in_plugin/image/submit.png'" style="cursor:pointer;" onclick="js_pwccrm_check_if_info_there_or_not();"/>
    
    </td>
</tr>
</table>
</form>
</div>