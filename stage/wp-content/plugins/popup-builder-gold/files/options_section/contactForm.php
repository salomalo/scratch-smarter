<?php
$popupId = @$id;
if(!isset($id)) {
	$popupId = 0;
}
?>
<div id="special-options">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="postbox-container-2" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox popup-builder-special-postbox">
					<div class="handlediv js-special-title" title="Click to toggle"><br></div>
					<h3 class="hndle ui-sortable-handle js-special-title">
						<span>
						<?php
							global $POPUP_TITLES;
							$popupTypeTitle = $POPUP_TITLES[$popupType];
							echo $popupTypeTitle." <span>options</span>";
						?>
						</span>
					</h3>
					<div class="special-options-content">
						<div class="sg-text-align">
							<h1 >Live Preview</h1>
							<input type="text" class="js-contact-text-inputs js-contact-name" value="" placeholder="<?php echo esc_html(@$sgContactNameLabel);?>">
							<input type="text" class="js-contact-text-inputs js-contact-subject" value="" placeholder="<?php echo esc_html(@$sgContactSubjectLabel);?>">
							<input type="text" class="js-contact-text-inputs js-contact-email" value="" placeholder="<?php echo esc_html(@$sgContactEmailLabel);?>">
							<textarea placeholder="<?php echo @$sgContactMessageLabel?>" class="js-contact-message js-contact-text-area"></textarea><br>
							<input type="button" value="Submit" class="js-contact-submit-btn"><br>
							<hr>
						</div>
						<span class="liquid-width"><b>General options</b></span><br>
						<span class="liquid-width">Show form before content:</span><input type="checkbox" name="show-form-to-top" <?php echo $sgShowFormToTop; ?>>
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">If checked, the Contact form will be shown above your popup content.</span><br>

						<span class="liquid-width"><b>Name</b></span><input class="js-checkbox-acordion" data-contact-rel="js-contact-name" type="checkbox" name="contact-name-status" <?php echo $sgContactNameStatus; ?>>
						<div class="sub-options-content">
							<span class="liquid-width">Placeholder:</span><input class="input-width-static sg-contact-fileds" data-contact-rel="js-contact-name" type="text" name="contact-name" value="<?php echo esc_attr(@$sgContactNameLabel); ?>"/>
							<span class="liquid-width">Required:</span><input type="checkbox" name="contact-name-required" <?php echo $sgContactNameRequired; ?>>
							<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">This field will be required.</span>
						</div><br>

						<span class="liquid-width"><b>Subject</b></span><input class="js-checkbox-acordion" data-contact-rel="js-contact-subject" type="checkbox" name="contact-subject-status" <?php echo $sgContactSubjectStatus; ?>>
						<div class="sub-options-content">
							<span class="liquid-width">Placeholder:</span><input class="input-width-static sg-contact-fileds" data-contact-rel="js-contact-subject" type="text" name="contact-subject" value="<?php echo esc_attr(@$sgContactSubjectLabel); ?>"/><br>
							<span class="liquid-width">Required:</span><input type="checkbox" name="contact-subject-required" <?php echo $sgContactSubjectRequired; ?>>
							<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">This field will be required.</span>
						</div><br>

						<span class="liquid-width">Email placeholder:</span>
						<input type="text" class="input-width-static sg-contact-fileds" data-contact-rel="js-contact-email" name="contact-email" value="<?php echo esc_attr(@$sgContactEmailLabel); ?>"><br>

						<span class="liquid-width">Message placeholder:</span>
						<input class="input-width-static sg-contact-fileds" data-contact-rel="js-contact-message" type="text" name="contact-message" value="<?php echo esc_attr(@$sgContactMessageLabel); ?>"/>

						<span class="liquid-width">Receiver email:</span>
						<input class="input-width-static sg-contact-fileds" type="email" required name="contact-receive-email" value="<?php echo esc_attr(@$sgContactResiveEmail); ?>"/>

						<span class="liquid-width">Send error message:</span>
						<input class="input-width-static sg-contact-fileds" type="text" name="contact-fail-message" value="<?php echo esc_attr(@$sgContactFailMessage); ?>"/>

						<span class="liquid-width">Required field message:</span>
						<input class="input-width-static" type="text" name="contact-validation-message" value="<?php echo esc_attr(@$sgContactValidationMessage);?>"><br>

						<span class="liquid-width">Invalid email field message:</span>
						<input class="input-width-static" type="text" name="contact-validate-email" value="<?php echo esc_attr(@$sgContactValidateEmail);?>"><br>

						<span class="liquid-width"><b>Input styles</b></span><br>
						<!--
							Text inputs options
						-->
						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" name="contact-inputs-width" data-contact-rel="js-contact-text-inputs" value="<?php echo esc_attr(@$sgContactInputsWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" name="contact-inputs-height" data-contact-rel="js-contact-text-inputs" value="<?php echo esc_attr(@$sgContactInputsHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Border width:</span>
						<input type="text" class="input-width-static" name="contact-inputs-border-width" data-contact-rel="js-contact-text-inputs" value="<?php echo esc_attr(@$sgContactInputsBorderWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example:"5px".</span><br>

						<span class="liquid-width">Background color:</span>
						<div class="color-picker"><input class="sg-contact-btn-color" id="sgOverlayColor" data-contact-rel="js-contact-text-inputs" data-contact-area-rel="js-contact-text-area" type="text" name="contact-text-input-bgcolor" value="<?php echo esc_attr(@$sgContactTextInputBgcolor); ?>"></div><br>

						<span class="liquid-width">Border color:</span>
						<div class="color-picker"><input class="sg-contact-btn-border-color" id="sgOverlayColor" data-contact-rel="js-contact-text-inputs" data-contact-area-rel="js-contact-text-area" type="text" name="contact-text-bordercolor" value="<?php echo esc_attr(@$sgContactTextBordercolor); ?>"></div><br>

						<span class="liquid-width">Text color:</span>
						<div class="color-picker"><input class="sg-contact-btn-text-color" id="sgOverlayColor" data-contact-rel="js-contact-text-inputs" data-contact-area-rel="js-contact-text-area" type="text" name="contact-inputs-color" value="<?php echo esc_attr(@$sgContactInputsColor); ?>"></div><br>

						<span class="liquid-width">Placeholder color:</span>
						<div class="color-picker"><input class="sg-contact-placeholder-color" id="sgOverlayColor" data-contact-rel="js-contact-text-inputs" data-contact-area-rel="js-contact-text-area" type="text" name="contact-placeholder-color" value="<?php echo esc_attr(@$sgContactPlaceholderColor); ?>"></div><br>


						<!--
							Textarea button options
						-->
						<span class="liquid-width"><b>Text area style</b></span><br>

						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" name="contact-area-width" data-contact-rel="js-contact-text-area" value="<?php echo esc_attr(@$sgContactAreaWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" name="contact-area-height" data-contact-rel="js-contact-text-area" value="<?php echo esc_attr(@$sgContactAreaHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Resize</span>
						<?php echo sgCreateSelect($sgTextAreaResizeOptions,'sg-contact-resize',@$sgContactResize)?><br>

						<span class="liquid-width"><b>Submit button style</b></span><br>
						<!--
							Submit button options
						-->

						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" data-contact-rel="js-contact-submit-btn" name="contact-btn-width" value="<?php echo esc_attr(@$sgContactBtnWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" data-contact-rel="js-contact-submit-btn" name="contact-btn-height" value="<?php echo esc_attr(@$sgContactBtnHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Title:</span>
						<input type="text" class="input-width-static" data-contact-rel="js-contact-submit-btn" name="contact-btn-title" value="<?php echo esc_attr(@$sgContactBtnTitle); ?>"><br>

						<span class="liquid-width">Title (in progress):</span>
						<input type="text" class="input-width-static" data-contact-rel="js-contact-submit-btn" name="contact-btn-progress-title" value="<?php echo esc_attr(@$sgContactBtnProgressTitle); ?>"><br>

						<span class="liquid-width">Background color:</span>
						<div class="color-picker"><input class="sg-contact-btn-color" id="sgOverlayColor" data-contact-rel="js-contact-submit-btn" type="text" name="contact-button-bgcolor" value="<?php echo esc_attr(@$sgContactButtonBgcolor); ?>"></div><br>

						<span class="liquid-width">Text color:</span>
						<div class="color-picker"><input class="sg-contact-btn-text-color" id="sgOverlayColor" data-contact-rel="js-contact-submit-btn" type="text" name="contact-button-color" value="<?php echo esc_attr(@$sgContactButtonColor); ?>"></div><br>

						<div class="sg-radio-option-behavior">
							<span class="liquid-width"><b>After successful form submission</b></span><br>
							<?php createRadiobuttons($popupDefaultData['contactSuccessBehavior'], 'contact-success-behavior', true, esc_html(@$sgContactSuccessBehavior), "liquid-width");?>
							<div class="js-accordion-showMessage js-radio-accordion sg-accordion-content">
								<span class="liquid-width">Success message</span>
								<input class="input-width-static" type="text" name="contact-success-message" value="<?php echo esc_attr(@$sgContactSuccessMessage);?>"><br><br>
							</div>
							<div class="js-accordion-redirectToUrl js-radio-accordion sg-accordion-content">
								<span class="liquid-width">Redirect URL</span>
								<input class="input-width-static" type="text" name="contact-success-redirect-url" value="<?php echo $sgContactSuccessRedirectUrl;?>">
								<span class="liquid-width">Redirect to new tab</span>
								<input type="checkbox" name="contact-success-redirect-new-tab" <?php echo $sgContactSuccessRedirectNewTab; ?>>
							</div>
							<div class="js-accordion-openPopup js-radio-accordion sg-accordion-content">
								<span class="liquid-width">Select popup</span>
								<?php $popupsData =  SGFunctions::getPopupsDataList(array('id' => @$id));
								echo sgCreateSelect($popupsData,'contact-success-popups-list',esc_html(@$sgContactSuccessPopupsList));
								?>
							</div>
						</div>
						<span class="liquid-width">Don't show to the already contacted user</span>
						<input class="input-width-static js-checkbox-contact-success-frequency-click" type="checkbox" name="dont-show-content-to-contacted-user" <?php echo $sgDontShowContentToContactedUser;?>>
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">
						    If this option is enabled you can set the frequency of showing the popup to the same users again after they have contacted you.
						</span><br>
						<div class="sg-hide sg-full-width js-checkbox-contact-success-frequency-wrraper">
							<span class="liquid-width">show after</span>
							<input type="number" name="contact-success-frequency-days" value="<?php echo esc_html($sgContactSuccessFrequencyDays);?>">
							<span class="span-percent">day(s)</span>
						</div>
						<?php

							@$contactParams = array(
								'inputsWidth' => $sgContactInputsWidth,
								'buttnsWidth' => $sgContactBtnWidth,
								'inputsHeight' => $sgContactInputsHeight,
								'buttonHeight' => $sgContactBtnHeight,
								'procesingTitle' => $sgContactBtnProgressTitle,
								'placeholderColor' => $sgContactPlaceholderColor,
								'btnTextColor' => $sgContactButtonColor,
								'btnBackgroundColor' => $sgContactButtonBgcolor,
								'inputsBackgroundColor' => $sgContactTextInputBgcolor,
								'inputsColor' => $sgContactInputsColor,
								'inputsBorderColor' => $sgContactTextBordercolor,
								'contactInputsBorderWidth' => $sgContactInputsBorderWidth,
								'contactAreaWidth' => $sgContactAreaWidth,
								'contactAreaHeight' => $sgContactAreaHeight,
								'contactResize' => $sgContactResize
							);
						?>
						<?php  wp_localize_script('sg_contactForm', 'contactFrontend'.$popupId, $contactParams);
							echo "<script type=\"text/javascript\">
								jQuery(document).ready(function() {
									sgContactObj = new SgContactForm(".$popupId.");
									sgContactObj.livePreview();
									sgContactObj.buildStyle();
									sgContactObj.fieldsStylesViaJs();
								});

							</script>";
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
