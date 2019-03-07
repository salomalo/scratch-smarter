<!--  Hot Tip: Please do not DELETE or ALTER the HIDDEN field values of the form or the customer will not be added to the CRM.  -->
<link href="http://localhost/CRMPWC/fields_function.css" rel="stylesheet" type="text/css"> 
  <div id="content" style="width:300px; margin:0 0 0 0;">
  
  <style>
	.pwccrm_submit_button{
		background-color:#2D9C0B;
	}
	.pwccrm_submit_button:hover{
		background-color:#32179C;
	}
	</style>
  
    
          <form name="form_add_customer" action="http://localhost/CRMPWC/crm_add_new_customer_from_mail.php" method="post" target="_self">
        <input name="merchant_id" type="hidden" value="1">
    <input name="funnel_id" type="hidden" value="">
    <input name="sp_id" type="hidden" value="">
    <input name="tag_id" type="hidden" value="">
    <input name="opt_id" type="hidden" value="33">
    <input name="pwccrm_same_or_landing_page" id="pwccrm_same_or_landing_page" type="hidden" value="0">
    <input name="pwccrm_mandatory_fields" id="pwccrm_mandatory_fields" type="hidden" value="1|2|4">
    
    <input name="go_to_location" id="go_to_location" type="hidden" value="http://localhost/CRMPWC/customer_management/Dashboard_customer_listing.php">
    <input name="pwccrm_bot_checking" id="pwccrm_bot_checking" type="text" value="" style="display:none;">
    <input name="pwccrm_return_path_33" id="pwccrm_return_path_33" type="hidden" value="">
    
        
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody><tr>
        
    <td colspan="3" align="center" style="padding:5px;">
    <div style="padding-bottom: 10px; display: none; " id="closePopUp"> 
  <span style="font-family:Arial, Helvetica, sans-serif; font-size:12px; float:right;"><a href="#" onclick="javascript:hide_div1('DivPopUp');return false;">Close</a></span>
  </div>
    <table cellpadding="0" cellspacing="0" border="0" style="  width:500px;  border:0px dashed #FFFFFF;  background-color:#FFB433;">
        <tbody><tr>
    <td width="100%" valign="top">
    <table width="100%" cellspacing="2" cellpadding="0">

       <tbody><tr>
        <td colspan="2" align="left" class="general_text" style="font-size:16px;padding-top:10px;">
            
        </td>
    </tr>
    <tr>
        <td colspan="2" align="left" class="general_text" style="font-size:16px;padding-top:10px;">
        
    <ul id="phoneticlong" style="list-style-type:none; padding:0; margin:0;">
        <li itemid="1" style="list-style-type: none; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; position: relative; " id="1">
       <div id="bug_cate" style="cursor:default;">
       
           <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody><tr>
        <td width="40%" class="general_text" align="right" style="padding-right:3px; color:#000000; font-size:12px;" valign="middle">		
        			<strong>First Name        <span id="mandatory_fields_1" style="color: rgb(255, 0, 0); float: right; ">*</span>:</strong>
                </td>
        <td width="60%" valign="top">
                <input style="width:250px; height:23px;" type="text" placeholder="First Name" name="txt_1" id="txt_1" value="">
                  </td>
        </tr> 
       </tbody></table>
       
              </div>
    </li>
       
        <li itemid="2" style="list-style-type: none; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; position: relative; " id="2">
       <div id="bug_cate" style="cursor:default;">
       
           <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody><tr>
        <td width="40%" class="general_text" align="right" style="padding-right:3px; color:#000000; font-size:12px;" valign="middle">		
        			<strong>Last Name        <span id="mandatory_fields_2" style="color: rgb(255, 0, 0); float: right; ">*</span>:</strong>
                </td>
        <td width="60%" valign="top">
                <input style="width:250px; height:23px;" type="text" placeholder="Last Name" name="txt_2" id="txt_2" value="">
                  </td>
        </tr> 
       </tbody></table>
       
              </div>
    </li>
       
        <li itemid="4" style="list-style-type: none; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; position: relative; " id="4">
       <div id="bug_cate" style="cursor:default;">
       
           <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody><tr>
        <td width="40%" class="general_text" align="right" style="padding-right:3px; color:#000000; font-size:12px;" valign="middle">		
        			<strong>Email        <span id="mandatory_fields_4" style="color: rgb(255, 0, 0); float: right; ">*</span>:</strong>
                </td>
        <td width="60%" valign="top">
                <input style="width:250px; height:23px;" type="text" placeholder="Email" name="txt_4" id="txt_4" value="">
                  </td>
        </tr> 
       </tbody></table>
       
              </div>
    </li>
       
        </ul>
  <input type="hidden" id="total_country" value="0">
  <input type="hidden" id="total_country_phone_code" value="0">
  		</td>
    </tr>
</tbody></table>
	</td>
    </tr>
    <tr>
    <td><div align="center"> 
        <input type="submit" value="Submit" class="pwccrm_submit_button" style="cursor:pointer;   color:#FFFFFF;">
        
    </div>
    </td>
    </tr>
        	
    </tbody></table>
    </td>
    
        </tr>
    
        </tbody></table>
 	</form>
    <input type="text" id="total_mandatory_field_after_checking" value="" style="display:none;">
    </div>
   
 
          
 
<script type='text/javascript'>document.getElementById('pwccrm_return_path_33').value = document.location.href;</script>


















