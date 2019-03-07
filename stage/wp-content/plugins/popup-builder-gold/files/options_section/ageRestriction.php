<div id="special-options">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="postbox-container-2" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox popup-builder-spetial-postbox">
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

							<span  class="liquid-width"><b>&quot;Yes&quot; button</b></span><br>

								<div class="specialSubDiv">
									<span  class="liquid-width">Label:</span>
									<input  class="input-width-static" type="text" name="yesButtonLabel" value="<?php echo (esc_attr(@$sgYesButton)) ? esc_attr(@$sgYesButton) : 'Yes'; ?>" required="required"/><br>

									<span  class="liquid-width">Button background color:</span>
									<div class="color-picker"><input  class="sgOverlayColor" id="sgOverlayColor" type="text" name="yesButtonBackgroundColor" value="<?php echo esc_attr(@$yesButtonBackgroundColor); ?>" /></div><br>

									<span  class="liquid-width">Button text color:</span>
									<div class="color-picker"><input  class="sgOverlayColor" id="sgOverlayColor" type="text" name="yesButtonTextColor" value="<?php echo esc_attr(@$yesButtonTextColor); ?>" /></div><br>

									<span  class="liquid-width">Button radius:</span>
									<input  class="input-width-percent" type="number" min="0" max="50" name="yesButtonRadius" value="<?php echo esc_attr(@$yesButtonRadius); ?>"/>
									<span class="span-percent">%</span><br>
								</div>
							<span  class="liquid-width"><b>&quot;No&quot; button </b></span><br>

								<div class="specialSubDiv">
									<span  class="liquid-width">Label:</span>
									<input  class="input-width-static" type="text" name="noButtonLabel" value="<?php echo (esc_attr(@$sgNoButton)) ? esc_attr(@$sgNoButton) : 'No'; ?>" required="required"/><br>

									<span  class="liquid-width">Button background color:</span>
									<div class="color-picker"><input  class="sgOverlayColor" id="sgOverlayColor" type="text" name="noButtonBackgroundColor" value="<?php echo esc_attr(@$noButtonBackgroundColor); ?>" /></div><br>

									<span  class="liquid-width">Button text color:</span>
									<div class="color-picker"><input  class="sgOverlayColor" id="sgOverlayColor" type="text" name="noButtonTextColor" value="<?php echo esc_attr(@$noButtonTextColor); ?>" /></div><br>

									<span  class="liquid-width">Button radius:</span>
									<input  class="input-width-percent" type="number" min="0" max="50" name="noButtonRadius" value="<?php echo esc_attr(@$noButtonRadius); ?>"/>
									<span class="span-percent">%</span><br>

									<span class="liquid-width">Push to bottom:</span>
									<input type="checkbox"  class="pushToBottom" name="pushToBottom" <?php echo $sgPushToBottom;?>><br>

									<span  class="liquid-width">Restriction URL:</span>
									<input class="input-width-static" type="text" name="restrictionUrl" value="<?php echo esc_attr(@$sgRestrictionUrl); ?>"/><br>

									<span  class="liquid-width">Expiration time:</span>
									<input type="text" name="sgRestrictionExpirationTime" class="input-width-static" value="<?php echo esc_attr($sgRestrictionExpirationTime);?>">
                                    <span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">The unit for the expiration time is "day". If you set the count of the days "0", the cookie will expire by session.</span><br>

									<span class="liquid-width">Page level cookie saving:</span>
									<input type="checkbox" name="restrictionCookeSavingLevel" <?php echo $sgRestrictionCookeSavingLevel; ?>>
									<span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">If this option is checked popup's cookie will be saved for a current page.By default cookie is set for all site.</span>
								</div>
						</div>
				</div>
			</div>
		</div>
	</div>
</div>
