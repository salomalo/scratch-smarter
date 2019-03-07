<?php
use sgpbrs\DefaultOptionsData;
use sgpb\AdminHelper;
use sgpb\MultipleChoiceButton;
use sgpb\Functions;
use sgpbregistration\AdminHelper as AdminHelperRegistration;

$popupId = 0;
$defaultData = AdminHelperRegistration::defaultData();
$registrationSubPopups = $popupTypeObj->getPopupsIdAndTitle();
$successPopup = $popupTypeObj->getOptionValue('sgpb-subs-success-popup');

if (!empty($_GET['post'])) {
	$popupId = (int)$_GET['post'];
}

$forceRtlClass = '';
$forceRtl = $popupTypeObj->getOptionValue('sgpb-force-rtl');
if ($forceRtl) {
	$forceRtlClass = ' sgpb-forms-preview-direction';
}
?>

<div class="sgpb-wrapper">
	<div class="row">
		<div class="col-md-7">
			<!-- form background options start -->
			<div class="row form-group">
				<label class="col-md-12 control-label">
					<?php _e('Form background options', SG_POPUP_TEXT_DOMAIN); ?>
				</label>
			</div>
			<div class="row form-group">
				<label class="col-md-6 control-label sgpb-sub-option">
					<?php _e('Form background color', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<div class="sgpb-color-picker-wrapper">
						<input class="sgpb-color-picker js-registration-color-picker" data-registration-rel="sgpb-registration-form-admin-wrapper" data-style-type="background-color" type="text" name="sgpb-registration-form-bg-color" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-form-bg-color')); ?>" autocomplete="off">
					</div>
				</div>
			</div>
			<div class="row form-group">
				<label for="content-padding" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Form background opacity', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-5 sgpb-slider-wrapper">
					<div class="slider-wrapper">
						<input type="text" name="sgpb-registration-form-bg-opacity" class="js-registration-bg-opacity" value="<?php echo $popupTypeObj->getOptionValue('sgpb-registration-form-bg-opacity'); ?>" rel="<?php echo $popupTypeObj->getOptionValue('sgpb-registration-form-bg-opacity'); ?>">
						<div id="js-registration-bg-opacity" data-init="false" class="display-box"></div>
					</div>
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-registration-form-padding" class="col-md-6 control-label sgpb-sub-option">
					<?php _e('Form padding', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-5">
					<div class="sgpb-color-picker-wrapper">
						<input type="number" min="0" data-default="<?php echo esc_attr($popupTypeObj->getOptionDefaultValue('sgpb-registration-form-padding'))?>" class="form-control js-sgpb-form-padding sgpb-full-width-events" id="sgpb-registration-form-padding" name="sgpb-registration-form-padding" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-registration-form-padding'))?>" autocomplete="off">
					</div>
				</div>
				<div class="col-md-1">
					<span class="sgpb-restriction-unit">px</span>
				</div>
			</div>
			<!-- username field -->
			<div class="row form-group">
				<label for="sgpb-username-label" class="col-md-6 control-label sgpb-static-padding-top">
					<?php _e('Username', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
			</div>
			<div class="row form-group">
				<label for="sgpb-username-label" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Label', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-username-label" class="sgpb-full-width-events form-control js-registration-username-label js-registration-labels" data-registration-rel="js-registration-username-label-edit" type="text" name="sgpb-username-label" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-username-label')); ?>" >
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-username-placeholder" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Placeholder', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-username-placeholder" class="sgpb-full-width-events form-control js-registration-field-placeholder js-registration-username-input" data-registration-rel="js-registration-username-input" type="text" name="sgpb-username-placeholder" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-username-placeholder')); ?>" >
				</div>
			</div>
			<!-- email field -->
			<div class="row form-group">
				<label for="sgpb-username-label" class="col-md-6 control-label sgpb-static-padding-top">
					<?php _e('Email Address', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
			</div>
			<div class="row form-group">
				<label for="sgpb-username-label" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Label', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-email-label" class="sgpb-full-width-events form-control js-registration-email-label js-registration-labels" data-registration-rel="js-registration-email-label-edit" type="text" name="sgpb-email-label" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-email-label')); ?>" >
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-email-placeholder" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Placeholder', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-email-placeholder" class="sgpb-full-width-events form-control js-registration-field-placeholder js-registration-email-input" data-registration-rel="js-registration-email-input" type="text" name="sgpb-email-placeholder" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-email-placeholder')); ?>" >
				</div>
			</div>
			<!-- password field -->
			<div class="row form-group">
				<label for="sgpb-password-label" class="col-md-6 control-label sgpb-static-padding-top">
					<?php _e('Password', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
			</div>
			<div class="row form-group">
				<label for="sgpb-password-label" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Label', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-password-label" class="sgpb-full-width-events form-control js-registration-password-label js-registration-labels" type="text" name="sgpb-password-label" data-registration-rel="js-registration-password-label-edit" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-password-label')); ?>" >
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-password-placeholder" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Placeholder', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-password-placeholder" class="sgpb-full-width-events form-control js-registration-field-placeholder js-registration-password-input" data-registration-rel="js-registration-password-input"  type="text" name="sgpb-password-placeholder" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-password-placeholder')); ?>" >
				</div>
			</div>
			<!-- confirm confirm-password field -->
			<div class="row form-group">
				<label for="sgpb-confirm-confirm-password-label" class="col-md-6 control-label sgpb-static-padding-top">
					<?php _e('Confirm password', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
			</div>
			<div class="row form-group">
				<label for="sgpb-confirm-confirm-password-label" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Label', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-confirm-password-label" class="sgpb-full-width-events form-control js-registration-confirm-password-label js-registration-labels" type="text" name="sgpb-confirm-password-label" data-registration-rel="js-registration-confirm-password-label-edit" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-confirm-password-label')); ?>" >
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-confirm-password-placeholder" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Placeholder', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-confirm-password-placeholder" class="sgpb-full-width-events form-control js-registration-field-placeholder js-registration-confirm-password-input" data-registration-rel="js-registration-confirm-password-input"  type="text" name="sgpb-confirm-password-placeholder" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-confirm-password-placeholder')); ?>" >
				</div>
			</div>
			<!-- input styles -->
			<div class="row form-group">
				<label class="col-md-12 control-label sgpb-static-padding-top">
					<?php _e('Inputs\' style', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
			</div>
			<div class="row form-group">
				<label for="sgpb-registration-text-width" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Width', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<input type="text" class="form-control js-registration-dimension sgpb-full-width-events" data-field-type="input" data-registration-rel="js-registration-text-inputs" data-style-type="width" name="sgpb-registration-text-width" id="sgpb-registration-text-width" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-text-width')); ?>">
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-registration-text-height" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Height', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<input class="form-control js-registration-dimension sgpb-full-width-events" data-registration-rel="js-registration-text-inputs" data-style-type="height" type="text" name="sgpb-registration-text-height" id="sgpb-registration-text-height" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-text-height')); ?>">
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-registration-text-border-width" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Border width', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<input class="form-control js-registration-dimension sgpb-full-width-events" data-registration-rel="js-registration-text-inputs" data-style-type="border-width" type="text" name="sgpb-registration-text-border-width" id="sgpb-registration-text-border-width" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-text-border-width')); ?>">
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-6 control-label sgpb-sub-option">
					<?php _e('Background color', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<div class="sgpb-color-picker-wrapper">
						<input class="sgpb-color-picker js-registration-color-picker" data-registration-rel="js-registration-text-inputs" data-style-type="background-color" type="text" name="sgpb-registration-text-bg-color" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-text-bg-color')); ?>">
					</div>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-6 control-label sgpb-sub-option">
					<?php _e('Border color', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<div class="sgpb-color-picker-wrapper">
						<input class="sgpb-color-picker js-registration-color-picker" data-registration-rel="js-registration-text-inputs" data-style-type="border-color" type="text" name="sgpb-registration-text-border-color" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-text-border-color')); ?>">
					</div>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-6 control-label sgpb-sub-option">
					<?php _e('Text color', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<div class="sgpb-color-picker-wrapper">
						<input class="sgpb-color-picker js-registration-color-picker" data-registration-rel="js-registration-text-inputs" data-style-type="color" type="text" name="sgpb-registration-text-color" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-text-color')); ?>" >
					</div>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-6 control-label sgpb-sub-option">
					<?php _e('Placeholder color', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<div class="sgpb-color-picker-wrapper">
						<input class="sgpb-color-picker js-registration-color-picker sgpb-full-width-events" data-registration-rel="js-registration-text-inputs" data-style-type="placeholder" type="text" name="sgpb-registration-text-placeholder-color" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-text-placeholder-color')); ?>" >
					</div>
				</div>
			</div>
			<!-- error messages -->
			<div class="row form-group">
				<label for="sgpb-registration-required-error" class="col-md-6 control-label sgpb-static-padding-top">
					<?php _e('Required field message', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-registration-required-error" class="sgpb-full-width-events form-control" type="text" name="sgpb-registration-required-error" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-required-error')); ?>" >
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-registration-error-message" class="col-md-6 control-label sgpb-static-padding-top">
					<?php _e('Error message', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="col-md-6">
					<input id="sgpb-registration-error-message" class="sgpb-full-width-events form-control" type="text" name="sgpb-registration-error-message" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-error-message')); ?>" >
				</div>
			</div>
			<!-- submit styles -->
			<div class="row form-group">
				<label class="col-md-12 control-label">
					<?php _e('Registration button styles', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
			</div>
			<div class="row form-group">
				<label for="sgpb-registration-btn-width" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Width', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<input class="form-control js-registration-dimension sgpb-full-width-events" data-registration-rel="js-registration-submit-btn" data-style-type="width" type="text" name="sgpb-registration-btn-width" id="sgpb-registration-btn-width" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-btn-width')); ?>">
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-registration-btn-height" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Height', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<input class="form-control js-registration-dimension sgpb-full-width-events" data-registration-rel="js-registration-submit-btn" data-style-type="height" type="text" name="sgpb-registration-btn-height" id="sgpb-registration-btn-height" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-btn-height')); ?>">
				</div>
			</div>
			<div class="row form-group">
				<label for="sgpb-registration-btn-title" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Title', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<input type="text" name="sgpb-registration-btn-title" id="sgpb-registration-btn-title" class="form-control js-registration-btn-title sgpb-full-width-events" data-registration-rel="js-registration-submit-btn" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-btn-title')); ?>">
				</div>
			</div>
			<div class="row form-group">
				<label for="btn-progress-title" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
					<?php _e('Title (in progress)', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<input type="text" name="sgpb-registration-btn-progress-title" id="sgpb-registration-btn-progress-title" class="form-control sgpb-full-width-events" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-btn-progress-title')); ?>">
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-6 control-label sgpb-sub-option">
					<?php _e('Background color', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<div class="sgpb-color-picker-wrapper">
						<input class="sgpb-color-picker js-registration-color-picker" data-registration-rel="js-registration-submit-btn" data-style-type="background-color" type="text" name="sgpb-registration-btn-bg-color" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-btn-bg-color')); ?>" >
					</div>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-md-6 control-label sgpb-sub-option">
					<?php _e('Text color', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
				<div class="col-md-6">
					<div class="sgpb-color-picker-wrapper">
						<input class="sgpb-color-picker js-registration-color-picker" data-registration-rel="js-registration-submit-btn" data-style-type="color" type="text" name="sgpb-registration-btn-text-color" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-registration-btn-text-color')); ?>" >
					</div>
				</div>
			</div>
			<!-- after successful registration -->
			<div class="row form-group">
				<label class="col-md-12 control-label sgpb-static-padding-top">
					<?php _e('After successful registration', SG_POPUP_TEXT_DOMAIN); ?>:
				</label>
			</div>
				<?php
				$multipleChoiceButton = new MultipleChoiceButton($defaultData['registrationSuccessBehavior'], $popupTypeObj->getOptionValue('sgpb-registration-success-behavior'));
				echo $multipleChoiceButton;
				?>
			<div class="sg-hide sg-full-width" id="registration-redirect-to-URL">
				<div class="row form-group">
					<label for="sgpb-registration-success-redirect-URL" class="col-md-6 control-label sgpb-double-sub-option">
						<?php _e('Redirect URL', SG_POPUP_TEXT_DOMAIN)?>:
					</label>
					<div class="col-md-6"><input type="url" name="sgpb-registration-success-redirect-URL" id="sgpb-registration-success-redirect-URL" placeholder="https://www.example.com" class="form-control sgpb-full-width-events" value="<?php echo $popupTypeObj->getOptionValue('sgpb-registration-success-redirect-URL'); ?>"></div>
				</div>
				<div class="row form-group">
					<label for="registration-success-redirect-new-tab" class="col-md-6 control-label sgpb-double-sub-option">
						<?php _e('Redirect to new tab', SG_POPUP_TEXT_DOMAIN)?>:
					</label>
					<div class="col-md-6"><input type="checkbox" name="sgpb-registration-success-redirect-new-tab" id="registration-success-redirect-new-tab" placeholder="https://www.example.com" <?php echo $popupTypeObj->getOptionValue('sgpb-registration-success-redirect-new-tab'); ?>></div>
				</div>
			</div>
			</div>
			<div class="sg-hide sg-full-width" id="registration-open-popup">
				<div class="row form-group">
					<label for="sgpb-registration-success-redirect-URL" class="col-md-6 control-label sgpb-double-sub-option">
						<?php _e('Select popup', SG_POPUP_TEXT_DOMAIN)?>:
					</label>
					<div class="col-md-6">
						<?php echo AdminHelper::createSelectBox($registrationSubPopups, $successPopup, array('name' => 'sgpb-registration-success-popup', 'class'=>'js-sg-select2 sgpb-full-width-events')); ?>
					</div>
				</div>
			<!-- after successful registration -->
			</div>
		<div class="col-md-5">
			<div>
				<h1 class="sgpb-align-center"><?php _e('Live preview', SG_POPUP_TEXT_DOMAIN);?></h1>
				<?php
				$popupTypeObj->setRegistrationFormData(@$_GET['post']);
				$formData = $popupTypeObj->createFormFieldsData();
				?>
				<div class="sgpb-registration-form-<?php echo $popupId; ?> sgpb-registration-form-admin-wrapper<?php echo $forceRtlClass; ?>">
					<?php echo AdminHelperRegistration::renderForm(@$formData); ?>
				</div>
				<?php
				$styleData = array(
					'placeholderColor' => $popupTypeObj->getOptionValue('sgpb-registration-text-placeholder-color')
				);
				echo $popupTypeObj->getFormCustomStyles(@$styleData)
				?>
			</div>
		</div>
	</div>
</div>
