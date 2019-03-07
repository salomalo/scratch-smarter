function SGPBRegistration()
{
	this.submissionPopupId = 0;
}

SGPBRegistration.prototype.setSubmissionPopupId = function(popupId)
{
	this.submissionPopupId = popupId;
};

SGPBRegistration.prototype.getSubmissionPopupId = function()
{
	return this.submissionPopupId;
};

SGPBRegistration.prototype.init = function()
{
	var that = this;
	sgAddEvent(window, 'sgpbDidOpen', function(e) {
		var args = e.detail;
		var popupId = parseInt(args.popupId);
		if (parseInt(that.submissionPopupId) == 0) {
			that.setSubmissionPopupId(popupId);
		}
		popupId = that.getSubmissionPopupId();
		var validateObj = eval('SGPB_VALIDATE_JSON_'+popupId);
		var popupOptions = SGPBPopup.getPopupOptionsById(popupId);
		validateObj = jQuery.parseJSON(validateObj);
		var additionalPopupParams = {};
		var currentLoginForm = jQuery('.sgpb-registration-form-'+popupId+' form');
		var submitButton = currentLoginForm.find('.js-registration-submit-btn');

		validateObj.errorPlacement = function(error, element) {
			var placement = jQuery(currentLoginForm).find(element).data('error');
			if (placement) {
				jQuery(placement).append(error)
			} else {
				error.insertAfter(element);
			}
	    }
		validateObj.submitHandler = function()
		{
			var userInput = jQuery('.sgpb-registration-form-'+popupId+' [data-username]');
			var userPassword = jQuery('.sgpb-registration-form-'+popupId+' [data-password]');
			var userEmail = jQuery('.sgpb-registration-form-'+popupId+' [data-email]');
			
			var data = {
				'action': 'sgpb_register_action',
				'nonce': SGPB_JS_PARAMS.nonce,
				'userForm': jQuery('.sgpb-registration-form-'+popupId+' form').serialize(),
				'userName': userInput.attr('name'),
				'emailName': userEmail.attr('name'),
				'passwordName': userPassword.attr('name'),
				beforeSend: function () {
					submitButton.prop('disabled',true);
					submitButton.val(submitButton.attr('data-progress-title'));
					if (popupOptions['sgpb-registration-success-behavior'] == 'redirectToURL' && popupOptions['sgpb-registration-success-redirect-new-tab']) {
						that.newWindow = window.open(popupOptions['sgpb-registration-success-redirect-URL']);
					}
				},
			};

			jQuery.post(SGPB_JS_PARAMS.ajaxUrl, data, function(response) {
				submitButton.prop('disabled',true);
				that.submissionPopupId = popupId;
				jQuery('.sgpb-registration-form-'+popupId+' .sgpb-alert').addClass('sg-hide-element');
				submitButton.val(submitButton.attr('data-title'));
				additionalPopupParams['res'] = response;
				that.showMessages(additionalPopupParams);
			})
		};

		currentLoginForm.validate(validateObj);
	});
};

SGPBRegistration.prototype.showMessages = function(res)
{
	var that = this;
	result = JSON.parse(res['res']);
	/*When successfully login*/
	if (result['status'] == 200) {
		this.registerSuccessBehavior();
	}
	else {
		if (that.newWindow != null) {
			that.newWindow.close();
		}

		this.showErrorMessage();
	}

	/*After login it's will be call reposition of popup*/
	window.dispatchEvent(new Event('resize'));
	return true;
};

SGPBRegistration.prototype.showErrorMessage = function()
{
	var popupId = parseInt(this.submissionPopupId);
	jQuery('.sgpb-registration-form-'+popupId+' .sgpb-alert-danger').removeClass('sg-hide-element');
};

SGPBRegistration.prototype.registerSuccessBehavior = function()
{
	var settings = {
		popupId: this.submissionPopupId,
		eventName: 'sgpbRegisterSuccess'
	};

	jQuery(window).trigger('sgpbFormSuccess', settings);

	var popupId = parseInt(this.submissionPopupId);
	var popupOptions = SGPBPopup.getPopupOptionsById(popupId);
	var behavior = 'refresh';
	jQuery('.sgpb-registration-form-'+popupId+' form').remove();

	if (typeof popupOptions['sgpb-registration-success-behavior'] != 'undefined') {
		behavior = popupOptions['sgpb-registration-success-behavior'];
	}
	this.resetFieldsValues();

	switch (behavior) {
		case 'refresh':
			SGPBPopup.closePopupById(this.submissionPopupId);
			window.location.reload();
			break;
		case 'redirectToURL':
			this.redirectToURL(popupOptions);
			break;
		case 'openPopup':
			this.openSuccessPopup(popupOptions);
			break;
		case 'hidePopup':
			SGPBPopup.closePopupById(this.submissionPopupId);
			break;
	}
};

SGPBRegistration.prototype.resetFieldsValues = function()
{
	if (!jQuery('.js-login-text-inputs').length) {
		return false;
	}

	jQuery('.js-login-text-inputs').each(function() {
		jQuery(this).val('');
	});
};

SGPBRegistration.prototype.redirectToURL = function(popupOptions)
{
	var redirectURL = popupOptions['sgpb-registration-success-redirect-URL'];
	var redirectToNewTab = popupOptions['sgpb-registration-success-redirect-new-tab'];
	SGPBPopup.closePopupById(this.submissionPopupId);

	if (redirectToNewTab) {
		return true;
	}

	window.location.href = redirectURL;
};

SGPBRegistration.prototype.openSuccessPopup = function(popupOptions)
{
	var that = this;

	/*We did this so that the "close" event works*/
	setTimeout(function() {
		SGPBPopup.closePopupById(that.submissionPopupId);
	}, 0);

	if (typeof popupOptions['sgpb-registration-success-popup'] != 'undefined') {
		sgAddEvent(window, 'sgpbDidClose', this.openPopup(popupOptions));
	}
};

SGPBRegistration.prototype.openPopup = function(popupOptions)
{
	if (typeof popupOptions['sgpb-registration-success-popup'] == 'undefined') {
		return false;
	}

	var subPopupId = parseInt(popupOptions['sgpb-registration-success-popup']);
	var subPopupOptions = SGPBPopup.getPopupOptionsById(subPopupId);

	var popupObj = new SGPBPopup();
	popupObj.setPopupId(subPopupId);
	popupObj.setPopupData(subPopupOptions);
	setTimeout(function() {
		popupObj.prepareOpen();
	}, 500);
};

jQuery(document).ready(function() {
	var obj = new SGPBRegistration();
	obj.init();
});
