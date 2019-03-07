jQuery(document).ready(function($){


	var sEditorAddButtonFunction=function(){



		function extension(fname) {
		  var pos = fname.lastIndexOf(".");
		  var strlen = fname.length;
		  if (pos != -1 && strlen != pos + 1) {
		    var ext = fname.split(".");
		    var len = ext.length;
		    var extension = ext[len - 1].toLowerCase();
		  } else {
		    extension = "No extension found";
		  }
		  return extension;
		}

		/*upload list media*/
		if($('.column-title').length>0){
			$('.column-title').each(function() {
	     
				   var imgType=$(this).children('p').text();
				   imgType=$.trim(imgType);
				   
				   var imgURL=$(this).children().children('a').attr("href");
				     imgURL=$.trim(imgURL);
				   
				//console.log(imgURL.replace('&action=edit',''))
				  current_attachment_ID = new Array();
				  current_attachment_ID=(imgURL.replace('&action=edit','').split("="));
				  
				  if( current_attachment_ID == ''){
 
				  	var post_id =$(this).closest('tr').attr('id');
				  
				  	if(typeof(post_id) != 'undefined'){
				  		current_attachment_ID = post_id.replace('post-','');
 				  	}
				  	 
				  	
				  } 
				  	 
				  	 var wp_version = sEditVars.sEdit_WP_version.split(".");
				  	 
				  	 if(wp_version[0]>=4){
				  	 	imgType=extension(imgType);
				  	 }
				  	
				      if(imgType=='JPG' || imgType=='JPEG' || imgType=='jpg' || imgType=='jpeg')
				      { 
				      	if(wp_version[0]>=4){
				      		$(this).find(".row-actions").append('<span class="edit"> | <a href="admin.php?page=sEdit_plugin_editor_ID&attachmentID='+current_attachment_ID[1]+'" id="sEditBtn'+current_attachment_ID[1]+'" target="_blank">sEdit</a></span>');
				      	}else{
				      		$(this).find(".edit").after('<a href="admin.php?page=sEdit_plugin_editor_ID&attachmentID='+current_attachment_ID[1]+'" class="sEditBtn" id="sEditBtn'+current_attachment_ID[1]+'" target="_blank">Edit with sEditor</a> | ');
				      	}
				   
				      }
				})/* end each .column-title*/
      	   }/* end if column-title exist*/



      	/*add sEdit button on media page*/
      	if(typeof sEditVars!= 'undefined'){

      		if(sEditVars.sEdit_version_check==1){
      			$("#media-single-form>p>input").after('<a href="admin.php?page=sEdit_plugin_editor_ID&attachmentID='+sEditVars.sEdit_attachmentID+'" class="sEditBtn"  target="_blank">Edit with sEditor</a>');
      		}else{

      			$("#save").after('<a href="admin.php?page=sEdit_plugin_editor_ID&attachmentID='+sEditVars.sEdit_attachmentID+'" class="sEditBtn"  target="_blank">Edit with sEditor</a>');

      		}/*version check*/
      	}/*sEditVars!= 'undefined'*/

      	/*add sEdit button WHEN IMAGE IS UPLOADED*/

      	if($(".urlpostER").length>0){

      		$(".urlpost").each(function() {
      		
	      		if(sEditUploadFinishedVars.sEdit_version_check==0){
	      			var current_real_img_path=jQuery(this).prev('.urlfile').attr('title');
	      		}else{
	      			var current_real_img_path=jQuery(this).prev('.urlfile').attr('data-link-url');
	      		}/*version check*/
                  
	      		 //get atribute name from text field-this attr  contains attachment id
                var absolute_image_link=jQuery(this).prev().prev().prev().prev().attr("name");
    
                    current_attachment_ID = new Array();
                    current_attachment_ID=(absolute_image_link.split("["));
                    current_attachment_ID[1]=current_attachment_ID[1].replace("]", "");


                var current_attachment_extension= current_real_img_path.split('.').pop().toLowerCase();
                                
                if(current_attachment_extension=='jpg' || current_attachment_extension=='jpeg'){

                         if(jQuery('#sEditBtn'+current_attachment_ID[1]).length<1) 
                         {
                              jQuery(this).after('<a href="admin.php?page=sEdit_plugin_editor_ID&attachmentID='+current_attachment_ID[1]+'" class="sEditBtn" id="sEditBtn'+current_attachment_ID[1]+'" target="_blank">Edit with sEditor</a>');
                         } 
                }

             }); /*(".urlpost").each*/      
                        
                 
      	}/* $(".urlpost").length>0*/


	}();/*end self executed function*/

});/*documentReady*/

