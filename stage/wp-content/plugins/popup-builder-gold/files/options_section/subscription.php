<?php
if(!isset($id)) {
	$id = '';
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
							<input type="text" class="js-subs-text-inputs js-subs-email-name" value="" placeholder="<?php echo esc_attr(@$sgSubscriptionEmail);?>">
							<input type="text" class="js-subs-text-inputs js-subs-first-name" value="" placeholder="<?php echo esc_attr(@$sgSubsFirstName);?>">
							<input type="text" class="js-subs-text-inputs js-subs-last-name" value="" placeholder="<?php echo esc_attr(@$sgSubsLastName); ?>">
							<input type="button" value="Submit" class="js-subs-submit-btn"><br>
							<hr>
						</div>
						<span class="liquid-width"><b>General options</b></span><br>

						<span class="liquid-width">Email placeholder:</span>
						<input type="text" class="input-width-static sg-subs-fileds" data-subs-rel="js-subs-email-name" name="subscription-email" value="<?php echo esc_attr(@$sgSubscriptionEmail); ?>"><br>

						<span class="liquid-width"><b>First name</b></span>
						<input class="js-checkbox-acordion" data-subs-rel="js-subs-first-name" type="checkbox" name="subs-first-name-status" <?php echo $sgSubsFirstNameStatus;?>>
						<div class="socials-content js-email-options-content">
							<span class="liquid-width">Placeholder:</span>
							<input class="input-width-static sg-subs-fileds" data-subs-rel="js-subs-first-name" type="text" name="subs-first-name" value="<?php echo esc_attr(@$sgSubsFirstName); ?>"/><br>
							<span class="liquid-width">Required field:</span>
							<input type="checkbox" name="subs-first-name-required" <?php echo $sgSubsFirstNameRequired; ?>>
						</div><br>

						<span class="liquid-width"><b>Last name</b></span>
						<input class="js-checkbox-acordion" type="checkbox" data-subs-rel="js-subs-last-name" name="subs-last-name-status" <?php echo $sgSubsLastNameStatus;?>>
						<div class="socials-content js-email-options-content">
							<span class="liquid-width">Placeholder:</span>
							<input class="input-width-static sg-subs-fileds" data-subs-rel="js-subs-last-name" type="text" name="subs-last-name" value="<?php echo esc_attr(@$sgSubsLastName); ?>"/>
							<span class="liquid-width">Required field:</span>
							<input type="checkbox" name="subs-last-name-required" <?php echo $sgSubsLastNameRequired; ?>>
						</div><br>

						<span class="liquid-width">Required field message:</span>
						<input class="input-width-static" type="text" name="subs-validation-message" value="<?php echo esc_attr(@$sgSubsValidateMessage);?>"><br>

						<span class="liquid-width"><b>Input styles</b></span><br>
						<!--
							Text inputs options
						-->
						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" name="subs-text-width" value="<?php echo esc_attr(@$sgSubsTextWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" name="subs-text-height" value="<?php echo esc_attr(@$sgSubsTextHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Border width:</span>
						<input type="text" class="input-width-static" name="subs-text-border-width" data-subs-rel="js-subs-text-inputs" value="<?php echo esc_attr(@$sgSubsTextBorderWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example:"5px".</span><br>

						<span class="liquid-width">Background color:</span>
						<div class="color-picker"><input class="sg-subs-btn-color" id="sgOverlayColor" data-subs-rel="js-subs-text-inputs" type="text" name="subs-text-input-bgColor" value="<?php echo esc_attr(@$sgSubsTextInputBgColor); ?>"></div><br>

						<span class="liquid-width">Border color:</span>
						<div class="color-picker"><input class="sg-subs-btn-border-color" id="sgOverlayColor" data-subs-rel="js-subs-text-inputs" type="text" name="subs-text-borderColor" value="<?php echo esc_attr(@$sgSubsTextBorderColor); ?>"></div><br>

						<span class="liquid-width">Text color:</span>
						<div class="color-picker"><input class="sg-subs-btn-text-color" id="sgOverlayColor" data-subs-rel="js-subs-text-inputs" type="text" name="subs-inputs-color" value="<?php echo esc_attr(@$sgSubsInputsColor); ?>"></div><br>

						<span class="liquid-width">Placeholder color:</span>
						<div class="color-picker"><input class="sg-subs-placeholder-color" id="sgOverlayColor" data-subs-rel="js-subs-text-inputs" type="text" name="subs-placeholder-color" value="<?php echo esc_attr(@$sgSubsPlaceholderColor); ?>"></div><br>

						<span class="liquid-width"><b>Submit button styles</b></span><br>
						<!--
							Submit button options
						-->

						<span class="liquid-width">Width:</span>
						<input type="text" class="input-width-static" data-subs-rel="js-subs-submit-btn" name="subs-btn-width" value="<?php echo esc_attr(@$sgSubsBtnWidth); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Height:</span>
						<input type="text" class="input-width-static" name="subs-btn-height" value="<?php echo esc_attr(@$sgSubsBtnHeight); ?>">
						<span class="dashicons dashicons-info escKeyImg same-image-style"></span><span class="infoEscKey samefontStyle">Set a fixed width. Example: "100%", "100px", or 100.</span><br>

						<span class="liquid-width">Title:</span>
						<input type="text" class="input-width-static" data-subs-rel="js-subs-submit-btn" name="subs-btn-title" value="<?php echo esc_attr(@$sgSubsBtnTitle); ?>"><br>

						<span class="liquid-width">Title (in progress):</span>
						<input type="text" class="input-width-static" data-subs-rel="js-subs-submit-btn" name="subs-btn-progress-title" value="<?php echo esc_attr(@$sgSubsBtnProgressTitle); ?>"><br>

						<span class="liquid-width">Background color:</span>
						<div class="color-picker"><input class="sg-subs-btn-color" id="sgOverlayColor" data-subs-rel="js-subs-submit-btn" type="text" name="subs-button-bgColor" value="<?php echo esc_attr(@$sgSubsButtonBgColor); ?>"></div><br>

						<span class="liquid-width">Text color:</span>
						<div class="color-picker"><input class="sg-subs-btn-text-color" id="sgOverlayColor" data-subs-rel="js-subs-submit-btn" type="text" name="subs-button-color" value="<?php echo esc_attr(@$sgSubsButtonColor); ?>"></div><br>

                        <span class="liquid-width"><b>After successful subscription</b></span><br>
                        <?php createRadiobuttons($subsSuccessBehavior, 'subs-success-behavior', true, esc_html($sgSubsSuccessBehavior), "liquid-width");?>
                        <div class="js-subs-success-message-content sg-accordion-content">
                            <span class="liquid-width">Success message</span>
                            <input class="input-width-static" type="text" name="subs-success-message" value="<?php echo esc_attr(@$sgSuccessMessage);?>"><br>
                        </div>
                        <div class="js-subs-success-redirect-content sg-accordion-content">
                            <span class="liquid-width">Redirect URL</span>
                            <input class="input-width-static" type="text" name="subs-success-redirect-url" value="<?php echo $sgSubsSuccessRedirectUrl;?>">
                            <span class="liquid-width">Redirect to new tab</span>
	                        <input type="checkbox" name="subs-success-redirect-new-tab" <?php echo $sgSubsSuccessRedirectNewTab; ?>>
                        </div>
                        <div class="js-subs-success-popups-list-content sg-accordion-content">
                            <span class="liquid-width">Select popup</span>
                            <?php
                                $popupsData =  SGFunctions::getPopupsDataList(array('id'=>$id));
								echo sgCreateSelect($popupsData,'subs-success-popups-list',esc_html($sgSubsSuccessPopupsList));
                            ?>
                        </div>

                        <?php
							$subscriptionParams = array(
								"ajaxurl" => admin_url( 'admin-ajax.php'),
								"textInputsWidth" => $sgSubsTextWidth,
								"sgSubsBtnWidth" => $sgSubsBtnWidth,
								"sgSubsBtnHeight" => $sgSubsBtnHeight,
								"sgSubsTextHeight" => $sgSubsTextHeight,
								"textInputsBgColor" => $sgSubsTextInputBgColor,
								"submitButtonBgColor" => $sgSubsButtonBgColor,
								"sgSubsTextBorderColor" => $sgSubsTextBorderColor,
								"sgSubsInputsColor" => $sgSubsInputsColor,
								"subsButtonColor" => $sgSubsButtonColor,
								"sgSubsBtnTitle" => $sgSubsBtnTitle,
								"sgSubsBtnProgressTitle" => $sgSubsBtnProgressTitle,
								"sgSubsTextBorderWidth" => $sgSubsTextBorderWidth
							);

							wp_enqueue_script('sg-subscription', SG_APP_POPUP_URL . '/javascript/sg_subscription.js', array( 'jquery' ));
							wp_localize_script('sg-subscription', 'SgSubscriptionParams', $subscriptionParams);
							wp_enqueue_script('jquery');
							echo "<script type=\"text/javascript\">
								jQuery(document).ready(function() {
									sgSubscriptionObj = new SgSubscription();
									sgSubscriptionObj.SgSubscriptionParams = ".json_encode($subscriptionParams).";
									sgSubscriptionObj.setupPlaceholderColor('js-subs-text-inputs', '$sgSubsPlaceholderColor');
									sgSubscriptionObj.formStyles();
									sgSubscriptionObj.init();
									sgSubscriptionObj.livePreview();
								});
							</script>";
							echo "<style type=\"text/css\">
							.js-subs-submit-btn,
							.js-subs-text-inputs {
								padding: 5px !important;
							}
							</style>";
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>