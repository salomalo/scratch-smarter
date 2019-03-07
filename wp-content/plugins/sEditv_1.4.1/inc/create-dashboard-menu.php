<?php 

function sEdit_plugin_options_func() {

	add_theme_page(
		'sEditor', 				// The title to be displayed in the browser window for this page.
		'sEditor',					// The text to be displayed for this menu item
		'administrator',					// Which type of users can see this menu item
		'sEdit_plugin_options_ID',				// The unique ID - that is, the slug - for this menu item
		'sEdit_plugin_display_func'			// The name of the function to call when rendering this menu's page
	);

	add_menu_page(
		'sEditor Settings',					// The value used to populate the browser's title bar when the menu page is active
		'sEditor Settings',					// The text of the menu in the administrator's sidebar
		'administrator',					// What roles are able to access the menu
		'sEdit_plugin_options_ID',				// The ID used to bind submenu items to this menu 
		'sEdit_plugin_settings_func'				// The callback function used to render this menu
	);
 
 	add_submenu_page(
		'sEdit_plugin_options_ID',	 
		'sEditor',					
		'sEditor', 
		'administrator', 
		'sEdit_plugin_editor_ID',
		'sEdit_url_input_display_func'
		);
 

 	//register settings group
	register_setting('sEditor-main-settings', 'sEditor-main-settings','sEditor_settings_validate');

	//add section -> Basic filter settings
	add_settings_section('sEditor-png-filter-settings', 'Add/remove custom effect', 'custom_png_filter_section_func', 'sEdit_custom_png_effects_options_ID');

}/* /sEdit_plugin_options_func  */

//atach to hook
add_action( 'admin_menu', 'sEdit_plugin_options_func');

function basic_filter_settings_section_func(){
	echo "basic";
}

function custom_png_filter_section_func(){

	$effectsFolderPath=plugin_dir_path(dirname(__FILE__)).'effects';
	  
	if (isset($_POST['folderName'])) {
		if ($_FILES["sEditorUploadFile"]["error"] > 0)
		  {
		  echo "Error: " . $_FILES["sEditorUploadFile"]["error"] . "<br>";
		  }
		else
		  {
		  // echo "Upload: " . $_FILES["sEditorUploadFile"]["name"] . "<br>";
		  echo "Type: " . $_FILES["sEditorUploadFile"]["type"] . "<br>";
		  // echo "Size: " . ($_FILES["sEditorUploadFile"]["size"] / 1024) . " kB<br>";
		  // echo "Stored in: " . $_FILES["sEditorUploadFile"]["tmp_name"];
		  	$urlEffectsPlugin=plugin_dir_path(dirname(__FILE__));
		  	$urlEffectsPlugin=str_replace('/', "\\", $urlEffectsPlugin);
		  // echo "<br>plugin url:".$urlEffectsPlugin."effects"."\\".$_POST['folderName'];
		  if( $_FILES["sEditorUploadFile"]["type"] =='image/png' || $_FILES["sEditorUploadFile"]["type"] =='image/x-png')
		  {	
		  	//get relative path of effects folder based on admin path
		  	$effectsFolderRelativePath=getRelativePath(admin_url(),plugin_dir_url(dirname(__FILE__)));
		  	//echo "<br>Rel path: ".$effectsFolderRelativePath."effects/".$_POST['folderName']."/".$_FILES["sEditorUploadFile"]["name"];
		  	 move_uploaded_file($_FILES["sEditorUploadFile"]["tmp_name"], $effectsFolderRelativePath."effects/".$_POST['folderName']."/".$_FILES["sEditorUploadFile"]["name"]);
		  	 ?>
		  	 <div id="message" class="updated">PNG Effects is added. </div>
		  	 <?php
		  }else{
		  	?>
		  	<div class="error">
		  		<h3>Error</h3>
		  		<p>File is not uploaded, effect image must be in png format</p>
		  	</div><!-- erreoe -->
		  	<?php
		  }

		  }/* if file is uploadd*/
	}/* IF ISSET POST*/

	?>	

		<form id="form-sEdit-plugin-options" action="<?php echo get_admin_url(); ?>admin.php?page=sEdit_plugin_options_ID" method="post" enctype="multipart/form-data">
			
				<?php 
						
						//echo "path".$effectsFolderPath;
						$folders = scandir($effectsFolderPath, 1);
						//print_r($folders);
						/*remove dots form $folders*/
						unset($folders[count($folders)-1]);
						unset($folders[count($folders)-1]);
				 ?>
			<label for="folderName">Add png effect to this folder</label><br>
			<select name="folderName" id="fodlerName">
			  <?php
						/*  list menu from folder names*/
						
						/*loop trough folders and take names of the folders*/
						foreach ($folders as $folderNames) {
                             //echo '<li id="'.$folderNames.'" class="sEditorBottomMenu">'.$folderNames.'</li>';
							?>
							<option value="<?php echo $folderNames; ?>"><?php echo $folderNames; ?></option>
						<?php
						} /* for each $folder*/

						?>
			</select><br>
			<input type="file" name="sEditorUploadFile" ><br>
			<button type="submit" class="button-secondary">Submit</button>
		</form>
		
		<h3> List of curent folders and png effects</h3>
		
		<ul id="effectListFolders">
			<?php
			foreach ($folders as $folderNames) {
				?>
               <li id="<?php echo $folderNames ?>" class="sEditorBottomMenu folderEffectsList sEditorEffectFolders"><?php echo $folderNames ?>
               		<div class="sEditDeleteFolder sEditDeleteFolderButton">X
               			<div class="sEditDeleteEffectTootlip">Delete this set of effects</div>
               		</div>
               </li>
               <?php
			} /* for each $folder*/
			?>
			<li id="addNewSetOfEffects" class="sEditorBottomMenu folderEffectsList">Add new
               		<div class="sEditDeleteFolder sEditAddFolderButton">+
               			<div class="sEditDeleteEffectTootlip">Add new folder/set of effects</div>
               		</div>
               </li>
			
		</ul>
		
		<div id="actionEffectLayer"></div><!-- actionEffectLayer -->
		
	<?php
}

function sEdit_plugin_settings_func(){
	?>
		<!-- 'wrap','submit','icon32','button-primary' and 'button-secondary' are classes
		for a good WP Admin Panel viewing and are predefined by WP CSS -->
	
		<div class="wrap">

			 
			<!-- <div id="icon-themes" class="icon32"><br /></div>-->
			<?php 
		 	screen_icon('options-general');  //USE THIS OR LINE ABOVE 
			
			?>
			<h2><?php _e( 'Seditor Plugin Options','sEditor-Plugin'); ?></h2>
			
			
			<!-- PNG filter settings -->
			<br>
				<div class="sidebar-name">
					<div class="sidebar-name-arrow"><br></div>
						<h3>Custom PNG Filter Settings</h3>
					</div><!-- sidebar-name-arrow -->
				
				<div id="sEditor-custom-png-filter-settings" class="adminToggleSection">

					<?php
						settings_fields('sEditor-png-filter-settings');
						do_settings_sections('sEdit_custom_png_effects_options_ID');
					?>
				</div><!-- sEditor-header-settings-section -->
			
		</div><!-- /wrap -->
 	<?php
	
}