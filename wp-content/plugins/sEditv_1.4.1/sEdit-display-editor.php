<?php 


function sEdit_url_input_display_func(){
	?>
	<div class="wrap">
	<?php 
 	screen_icon('themes');  //USE THIS OR LINE ABOVE 	
	?>
	<?php
	echo "<h2>sEditor</h2>";
	extract($_GET);

	if(!isset($attachmentID)){
		?>
		<div id="message" class="error">
			<h4>Image is not selected!</h4>
		</div>
		<p>Please go to your Media library and select image to edit by pressing "Edit with sEditor"</p>
		<?php
		exit;
	}

//get attachment id based on url
	function get_attachment_id_from_url($url) {
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$url'";
		return $wpdb->get_var($query);
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

//if url is not posted and attachment id is:
	if($attachmentID!=''){

//	$sEdit_list_image_sizes=get_intermediate_image_sizes();
	}

	//get all availabale sizes
	$sEdit_list_image_sizes=get_intermediate_image_sizes();

	if(isset($selectedURL) && $selectedURL!=''){
	//$attachmentID=get_attachment_id_from_url($URL);
		$URL=$selectedURL;
	}else{
		foreach ($sEdit_list_image_sizes as $value) {
		$list=wp_get_attachment_image_src( $attachmentID,  $value);
		//echo $value." -> ".$list[0]." -> ".$list[1]." -> ".$list[2]."<br>";
		$URL=$list[0];
		}
	}/*if image url is posted if not use first inage fron list*/



	

	?>
	<hr>
	<div id="sEdit_display_image_sizes">
		<h3>All available sizes</h3>
		<p>(click on image to edit)</p>
		<ul >
			<?php
			foreach ($sEdit_list_image_sizes as $value) {

		 		// make list of all available images
				$list=wp_get_attachment_image_src( $attachmentID,  $value);
		 		//echo $list[0]."<br>";

		 		//if image exist show link to it
				if(file_exists(getRelativePath(admin_url('admin.php'),$list[0]))){

					//mark selected image
					if(isset($URL) && $URL==$list[0]){
						?>
						<li class="selectedSourceImage">
							<?php		
						}else{
							?>
							<li>
								<?php
							}
							?>

							<center>
								<?php echo $value; ?>
							</center>
							<a href="admin.php?page=sEdit_plugin_editor_ID&attachmentID=<?php echo $attachmentID; ?>&selectedURL=<?php echo $list[0]; ?>">
								<img src="<?php echo $list[0]; ?>?<?php echo rand(); ?>" width="100" />
							</a>
							<br>
							<center>
								<?php 
										//read image width and height
								list($width, $height) = getimagesize($list[0]);
								echo $width."x".$height;

								?>
							</center>
						</li>
						<?php
					}/*if image exist show link to it*/

				}/* for each*/
				?>	
			</ul>
		</div><!--sEdit_display_image_sizes -->
		<div style="clear:both;"></div>
		<?php



		/*++++++++++++++++++++++++++++++++++++ settings  +++++++++++++++++++++++++++++++++++++++++++++++*/
			//effects folder
		$settingsValue['effectsFolder']=dirname( __FILE__).DIRECTORY_SEPARATOR.'effects';

			//temp folder for temp images
		$settingsValue['tempFolder']=plugins_url( 'sEditor/temp');

			//font used in writing text over image. If you want to use your own font, please copy font.ttf file to this folder and replace value bellow
		$settingsValue['font']=plugins_url( 'sEditor/fonts/arial.ttf');

			// simple effects
		$simpleEffects=array(
			array('value'=>'Pixelate','name'=>'Pixelate'),
			array('value'=>'Pixelate-out','name'=>'Pixelate outside'),
			array('value'=>'Negative','name'=>'Negative'),
			array('value'=>'Greyscale','name'=>'Greyscale'),
			array('value'=>'Greyscale-out','name'=>'Greyscale outside'),
			array('value'=>'Blur','name'=>'Blur'),
			array('value'=>'Blur-out','name'=>'Blur outside'),
			array('value'=>'Brightness','name'=>'Brightness'),
			array('value'=>'Contrast','name'=>'Contrast'),
			array('value'=>'Colorize','name'=>'Colorize'),
			array('value'=>'Emboss','name'=>'Emboss'),
			array('value'=>'Text','name'=>'Text'),
			array('value'=>'Crop','name'=>'Crop')
			);

			// compiled effects   comment if you dont want to use them
		$compiledEffects=array(

			array('value'=>'Rosie','img'=>plugin_dir_url(__FILE__).'/web_images/compiledEffects/Rosie.jpg'),
			array('value'=>'Patty','img'=>plugin_dir_url(__FILE__).'/web_images/compiledEffects/Patty.jpg'),
			array('value'=>'Juliette','img'=>plugin_dir_url(__FILE__).'/web_images/compiledEffects/Juliette.jpg'),
			array('value'=>'Sonny','img'=>plugin_dir_url(__FILE__).'/web_images/compiledEffects/Sonny.jpg'),
			array('value'=>'Oddie','img'=>plugin_dir_url(__FILE__).'/web_images/compiledEffects/Oddie.jpg'),
			array('value'=>'FilmFrame','img'=>plugin_dir_url(__FILE__).'/web_images/compiledEffects/FilmFrame.jpg')
			);
		/*++++++++++++++++++++++++++++++++++++ settings  +++++++++++++++++++++++++++++++++++++++++++++++*/		

		if(isset($URL)){
			$srcImage=$URL;
			$extensionCheck = pathinfo($srcImage);
			$extension=strtolower($extensionCheck['extension']);
			$extension=str_replace(" ","",$extension);

			if( $extension !='jpg' && $extension!='jpeg'){
				die('Select image size to edit!');
			}
		}
		
		
		?>
		<div id="sEditorMainFrame">

			<div id="whiteLoading">
				<img id="preLoader" src="<?php echo plugin_dir_url(__FILE__); ?>/web_images/ajax-loader.gif" />
			</div><!-- white loader -->

			<!-- +++++++++++++++++ header +++++++++++++++++  -->
			<div id="sEditorheader">
				<div id="sEditorTopMenuHolder">
					<ul id="sEditorheaderNavigation">

						<li id="sEditorEffectsButton"><p>Effects</p>
							<ul>
								<div id="sEditorTopNavigationsubMeni">

									<!-- background image holder -->
									<div id="subMenuTop"></div>
									<div id="SubMenuBottom"></div>
									<!-- background image holder -->
									<?php 
									/*list effects from array*/
									foreach ($simpleEffects as  $row) {
										?>
										<div class='TopSubMenuListDivs' sEffect="<?php echo $row['value']; ?>" sEffectCategory="simplesEffects"><?php echo $row['name']; ?></div>
										<?php
                                   // echo '<li id="'.$row['value'].'"><a href="#">'.$row['name'].'</a></li>';
									}
									?>
								</div><!-- subMen1 -->
							</ul>
						</li>

						<li id="sEditorSelectAllButton"><p>Select All</p></li>

						<li id="sEditorDeselectButton" ><p>Deselect</p></li>

						<li id="sEditorSelectionCircleButton"><p>Round Selector</p></li>

						<li id="sEditorSelectionBlockButton"><p>Block Selector</p></li>


						<li id="sEditorColorPalleteButton" ><p>Color On/Off</p></li>

						<li id="sEditorRevertButton" ><p>Revert</p></li>
						<!--<li id="sEditorCropButton" ><p>Crop</p></li>-->

						
						<li id="sEditorSaveButton" ><p>Save</p>
							<ul>
								<div id="sEditorTopNavigationsubMeni">

									<!-- background image holder -->
									<div id="subMenuTop"></div>
									<div id="SubMenuBottom"></div>
									<!-- background image holder -->
									<div >
										<div class='sEitorSaveOptions' id="saveOverwrite"><p>Overwrite original</p></div>
										<div class='sEitorSaveOptions'  id="saveNew"><p>Save as new image</p></div>
									</div>

									<div id="sEditorSave"> New name:<br>
										<input type="text" size="18" class="sEditorSelectionInfo" id="newImageName" name="newImageName" value="New image name"/>
									</div>
									<div id="sEditorSave-CancelButtonHolder">
										<div id="sEditorSaveBtn">Save</div>
										<div id="sEditorCancelBtn">Cancel</div>
									</div><!-- sEditorSave-CancelButtonHolder -->
								</div><!-- subMen1 -->
							</ul>

						</li>
						<li id="sEditorApplyButton" ><p>Apply effect</p></li>
						<!-- color picker -->
						<li id="sEditorColorChoser" ><p>Selected color</p><div id="colorSelector" ><div ></div></div></li>
						<!-- /color picker -->


						<ul><!-- sEditorheaderNavigation -->

						</div><!-- /sEditorTopMenuHolder -->

						<div id="sEditorSliderHolder">
							<div id="slider"><p id="currentSliderStatusDescription">Status</p>
								<div></div>
							</div>
						</div><!-- /sEditorSliderHolder -->

						<div id="sEditorTextBox">
							<textarea>Insert text here...</textarea>
						</div><!-- /sEditorTextBox -->

						<div id="sEditorCurrentEffect">
						</div><!-- /sEditorCurrentEffect -->

						<!-- position selection display -->
						<div id="sEditorSelectionInfo">
							<form onsubmit="return false;">
								<label>X1 <input type="text" size="1" class="sEditorSelectionInfo" id="sEditorXpos" name="sEditorXpos" /></label>
								<label>Y1 <input type="text" size="1" class="sEditorSelectionInfo" id="sEditorYpos" name="sEditorYpos" /></label>
								<label>X2 <input type="text" size="1" class="sEditorSelectionInfo" id="sEditorX2pos" name="sEditorX2pos" /></label>
								<label>Y2 <input type="text" size="1" class="sEditorSelectionInfo" id="sEditorY2pos" name="sEditorY2pos" /></label>
								<label>W <input type="text" size="1" class="sEditorSelectionInfo" id="sEditorWidthpos" name="sEditorWidthpos" /></label>
								<label>H <input type="text" size="1" class="sEditorSelectionInfo" id="sEditorHeightpos" name="sEditorHeightpos" /></label>
							</form>
						</div><!-- /sEditorSelectionInfo -->

					</div><!-- /sEditorheader -->

					<!-- +++++++++++++++++ /header +++++++++++++++++  -->

					<!-- +++++++++++++++++ img Holder +++++++++++++++++  -->

					<div id="sEditorImageHolder">
						<center>
							<!-- image editor -->

							<?php 
							//create relative image path
							if(isset($srcImage)){
								$sEdit_rel_image_path=getRelativePath(admin_url(),$srcImage);
							}
							 
							 //echo "<br>PAth: ".admin_url();
							//$sEdit_rel_image_path=getRelativePath("http://localhost/PROJECTS/sEditor/sEditorWP/wordpress/wp-admin/admin.php", $srcImage);


							?>

							<img id="cropbox" src="<?php echo $srcImage; ?>?<?php echo rand(); ?>" relSrc="<?php echo $sEdit_rel_image_path; ?>?<?php echo rand(); ?>" style="display:none" />
							<!-- /image editor -->
						</center>

					</div><!-- /sEditorImageHolder -->

					<!-- +++++++++++++++++ /img Holder +++++++++++++++++  -->

					<!-- +++++++++++++++++ footer +++++++++++++++++  -->

					<div id="sEditorBottomMenu">

						<ul>
							<?php

					// get current number of compiled efects if some are commented out by user
							$noCompiledEffects=count($compiledEffects);

					//if there are no compiled effects dont show Default Effects button
							if($noCompiledEffects>1)
								{ 	?>
							<li id="sEditorDefaultEffects" class='sEditorBottomMenu'>Default Effects</li>
							<?php 
						}
						?>

						<?php
						/*  list menu from folder names*/

						$folders = scandir($settingsValue['effectsFolder'], 1);

						/*remove dots form $folders*/
						unset($folders[count($folders)-1]);
						unset($folders[count($folders)-1]);
						/*loop trough folders and take names of the folders*/
						foreach ($folders as $folderNames) {
                             //echo '<li id="'.$folderNames.'" class="sEditorBottomMenu">'.$folderNames.'</li>';
							?>
							<li id="<?php echo $folderNames; ?>" class='sEditorBottomMenu'><?php echo $folderNames; ?></li>
							<?php
						} /* for each $folder*/

						?>

					</ul>

				</div><!-- /sEditorBottomMenu-->

				<div id="effectsListHolder">
					<div class="sEditorEffectsSampleImages" id="sEditorDefaultEffectsList">

						<?php 
						/*list compiled effects from array*/
						foreach ($compiledEffects as  $row) {
							?>
							<div class='imageEffectSampleImageHodlder' sEffect="<?php echo $row['value']; ?>" seffectcategory="compiledsEffects"><img src="<?php echo $row['img']; ?>" /><p><?php echo $row['value']; ?></p></div>
							<?php
                   // echo '<li id="'.$row['value'].'"><a href="#">'.$row['name'].'</a></li>';
						}
						?>

					</div><!-- /effectsList -->

					<?php 
					/*loop trough folders and show images from each folder*/
					foreach ($folders as $folderNames2) {

						?>
						<div class="sEditorEffectsSampleImages" id="<?php echo $folderNames2; ?>List">
							<?php 

                            //list images from each folder
							/*search .png files in each folder andd create effect list form png names*/
							$files = glob( $settingsValue['effectsFolder'].'/'.$folderNames2 . '/*.png');
							/*if there is images in folder list them*/
							if(count($files>0)){
								?>

								<?php
								/*loop trough each folder and outpu image names*/
								
								foreach ($files as $name) {
									$path=explode('/',$name);
									$countPath=count($path);
									$name=explode('.',$path[$countPath-1]);
									//echo '<li id="'.$name[0].'" idf="'.$folderNames.'"><a href="#">'.$name[0].'</a></li>';
									//echo $name[0]."|";
									?>
									<div class="imageEffectSampleImageHodlder" sEffect="<?php echo  $name[0]; ?>"  sEffectCategory="<?php echo $folderNames2; ?>"><img src="<?php echo plugin_dir_url(__FILE__).'/effects/'.$folderNames2.'/'.$name[0] . '.png'; ?>"  /><p> <?php echo $name[0]; ?></p></div>
									<?php
								}/*for each image loop*/
								?>

								<?php	
							}/*if count $files is bigger then zero*/

							?>
						</div><!-- /effectsList -->

						<?php 
					} /* for each $folder*/

					?>


				</div><!-- /effectsListHolder-->
				<!-- +++++++++++++++++ /footer +++++++++++++++++  -->

			</div><!-- /sEditorMainFrame -->
	</div><!-- wrap -->
			<?php 

		}/* // sEdit_editor_display_func*/

