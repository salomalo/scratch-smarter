jQuery(document).ready(function($){


	var sEditorSettingsFunction=function(){ 
		/* delete effects folder*/
		$(".sEditDeleteFolderButton").live("click",function(){
			
			var sEditFolderToDelete=$(this).parent().attr('id');

			$('<div></div>').appendTo('body')
			.html('<div>You are going to delete folder <strong>'+sEditFolderToDelete+'</strong> with all efects in it!<br><h3>Are you sure?</h3></div>')
			.dialog({
				modal: true, title: 'Delete message', zIndex: 10000, autoOpen: true,
				width: 'auto', resizable: false,
				buttons: {
					Yes: function () {
                                // $(obj).removeAttr('onclick');                                
                                // $(obj).parents('.Parent').remove();
                                $.ajax({
                                	
                                	url : sEditorSettingsVars.sEdit_pluginurl+'inc/effects_settings-delete.php',
                                	data : { folderToDelete : sEditFolderToDelete },
                                	type : 'POST',
                                	dataType : 'json',
                                	
                                	success : function(data) {
                                		if(data=="success"){
                                			$("#"+sEditFolderToDelete).slideUp(function(){
                                				$("#actionEffectLayer").html('Folder deleted successfully!');
                                			});

											//remove element from select option
											$("#fodlerName>option[value='"+sEditFolderToDelete+"']").hide();
											
										}
										
									},
									
									error : function(jqXHR, status, error) {
										alert('Disculpe, existió un problema: '+xhr.status+" "+error+". No se podrá continuar");
									},
									
								}); //ajax

$(this).dialog("close");
},
No: function () {
	$(this).dialog("close");
}
},
close: function (event, ui) {
	$(this).remove();
}
});






})/*sEditDeleteFolderButton click*/


	//add efect folder
	$(".sEditAddFolderButton,#addNewSetOfEffects").click(function(){
		
		$("#actionEffectLayer").slideUp(function(){

			$("#actionEffectLayer").html('');
			$("#actionEffectLayer").html(
				'<p>Enter new folder / effect set name: (please use one word)</p>'+
				'<input type="text" id="newFolderName" name="newFolderName" />'+
				'<button id="newFolderNameButton" class="button-primary">OK</button>'
				);
			$("#actionEffectLayer").slideDown(function(){

	  			//when new folder name is entered
	  			$("#newFolderNameButton").click(function(){
				  		//get input value
				  		var newFolderNameValue=$("#newFolderName").val();
				  		$("#actionEffectLayer").slideUp(function(){
				  			$("#actionEffectLayer").html('working...');
				  			$("#actionEffectLayer").slideDown();

				  			$.ajax({
				  				
				  				url : sEditorSettingsVars.sEdit_pluginurl+'inc/effects_settings-addfolder.php',
				  				data : { 
				  					folderToAdd : 	newFolderNameValue,
				  					sEditDir: 		sEditorSettingsVars.sEdit_pluginurl, 
				  				},
				  				type : 'POST',
				  				dataType : 'json',
				  				
				  				success : function(data) {
				  					
				  					if(data=="success"){
				  						
											//prepend new folder
											$("#effectListFolders").prepend(
												'<li id="'+newFolderNameValue+'" class="sEditorBottomMenu folderEffectsList sEditorEffectFolders">'+
												newFolderNameValue+
												'<div class="sEditDeleteFolder sEditDeleteFolderButton">'+
												'X'+
												'<div class="sEditDeleteEffectTootlip">Delete this set of effects</div>'+
												'</div>'+
												'</li>'
												);

											//add new element to select list
											$("#fodlerName").prepend(
												'<option value="'+newFolderNameValue+'">'+newFolderNameValue+'</option>'
												);

											//clear info div
											$("#actionEffectLayer").html('Folder/set is created!');

										}else if(data=="exists"){
											$("#actionEffectLayer").html('This directory already exists!');
										}else{
											$("#actionEffectLayer").html('There was an error during creation of new folder!');
										}
									}, //success
									
									error : function(jqXHR, status, error) {
										alert('Problem during folder creation : '+xhr.status+" "+error);
									},
									
								});

});
})/* when input OK button is presed*/

});/*actioneffect layer slideup add input field and slide down*/
});/* slide up actionEffectLayer*/

})

	//show effects from specific folder
	$(".sEditorEffectFolders").live('click', function(){

		//get current folder name from id
		var sEditFolderToDisplay=$(this).attr('id');
		
		$("#actionEffectLayer").slideUp(function(){

			$("#actionEffectLayer").html('');
			
			$.ajax({
				
				url : sEditorSettingsVars.sEdit_pluginurl+'inc/effects_settings-display.php',
				data : { sEditFolderToSearchIn: sEditFolderToDisplay },
				type : 'POST',
				dataType : 'json',
				
				success : function(data) {
					
					var results=eval(data);
					$("#actionEffectLayer").html('<ul></ul>')
					$.each(results, function(i) {

						    //sEditorSettingsVars.sEdit_pluginurl /results[i]
						    $("#actionEffectLayer ul").append(

						    	'<li id="'+i+'" class="sEditorBottomMenu folderEffectsList sEditorEffectImages" img-src="'+results[i]+'"  img-folder="'+sEditFolderToDisplay+'">'+
						    	'<img src="'+sEditorSettingsVars.sEdit_pluginurl+'effects/'+sEditFolderToDisplay+'/'+results[i]+'" style="max-width:100px; max-height:100px;" />'+
						    	'<div class="sEditDeleteFolder sEditDeleteImageButton">X'+
						    	'<div class="sEditDeleteEffectTootlip">Delete this image effect</div>'+
						    	'</div>'+
						    	'</li>'

						    	);
						});

					$("#actionEffectLayer").slideDown();


					
				},
				
				error : function(jqXHR, status, error) {
					alert('Disculpe, existió un problema: '+xhr.status+" "+error+". No se podrá continuar");
				},
				
				}); //ajax


})/*slide up actio layer*/

	}); //show effects from specific folder

$(".sEditorEffectImages").live('click', function(){

	var imageIDNumber=$(this).attr('id');
	var imageFolder=$(this).attr('img-folder');
	var imageName=$(this).attr('img-src');
	$('<div></div>').appendTo('body')
	.html('<div>You are going to delete image <strong>'+imageName+'</strong>  from <strong>'+imageFolder+'</strong> folder!<br><h3>Are you sure?</h3></div>')
	.dialog({
		modal: true, title: 'Delete message', zIndex: 10000, autoOpen: true,
		width: 'auto', resizable: false,
		buttons: {
			Yes: function () {
                                // $(obj).removeAttr('onclick');                                
                                // $(obj).parents('.Parent').remove();
                                $.ajax({
                                	
                                	url : sEditorSettingsVars.sEdit_pluginurl+'inc/effects_settings-deleteImage.php',
                                	data : { 
                                		folderToDelete : imageFolder,
                                		imageToDelete : imageName
                                	},
                                	type : 'POST',
                                	dataType : 'json',
                                	
                                	success : function(data) {
                                		if(data=="success"){
                                			$("#actionEffectLayer ul #"+imageIDNumber).slideUp();
                                		}else{
                                		}
                                		
                                	},
                                	
                                	error : function(jqXHR, status, error) {
                                		alert('Disculpe, existió un problema: '+xhr.status+" "+error+". No se podrá continuar");
                                	},
                                	
								}); //ajax

$(this).dialog("close");
},
No: function () {
	$(this).dialog("close");
}
},
close: function (event, ui) {
	$(this).remove();
}
});


})



//$(".sidebar-name").next().css('display','none');
$(".sidebar-name").click(function(){
	
	$(this).next().slideToggle('slow',function(){
		
			});//toggle
	
		})//click function


}();/*end self executed function*/

});/*documentReady*/