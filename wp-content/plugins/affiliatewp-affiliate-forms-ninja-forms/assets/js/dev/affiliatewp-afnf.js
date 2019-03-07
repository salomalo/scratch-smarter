jQuery( document ).ready( function( $ ) {

	/**
	 * afnf object is a getter for localized error messages,
	 * and setting a few jQuery objects.
	 *
	 * @since 1.1
	 *
	 * @type {Object}
	 */
	var afnf = {
		input_email: $( '.nf-form-content .email-container .email-wrap input' ),
		email_value: $( '.nf-form-content .email-container .email-wrap input' ).val(),
		input_username: $( '.nf-form-content .affwp_afnf_username-container .affwp_afnf_username-wrap input' ),
		loggedIn: affiliatewp_afnf.logged_in,
		isValidAffiliate: affiliatewp_afnf.is_valid_affiliate,
		afnfFormID: affiliatewp_afnf.afnf_form_id,
		isAFNFForm: function() {
			var currentForm = '';
			if ( currentForm == afnf.afnfFormID ) {
				return true;
			} else {
				return false;
			}
		},
		emailExists: affiliatewp_afnf.error_email_exists,
		missingEmail: affiliatewp_afnf.error_missing_email,
		email: affiliatewp_afnf.user_email,
		isEmpty: affiliatewp_afnf.error_email_empty,
		missingFields: affiliatewp_afnf.error_missing_fields,
		missingUsername: affiliatewp_afnf.error_missing_username,
		submit: $( ".nf-form-cont input[type='button']" ),
		hasEmail: false,
		hasUsername: false,
		registrationFormIDSelector: '#nf-form-' + affiliatewp_afnf.afnf_form_id + '-cont',
		debugger: function( errorMessage ) {
			errorMessage = '';
			return 'Affiliate Forms for Ninja Forms: ' + '\n' + errorMessage;
		},
		debug: affiliatewp_afnf.debug
	};

	console.afnf = function() {

		Array.prototype.unshift.call(

			arguments,
			afnf.debugger() );

		console.error.apply( console, arguments );
	};

	/**
	 * Print an error message.
	 *
	 * @since  1.1
	 *
	 * @param  {[string]} error [Error message]
	 *
	 * @return {[string]}       [Error element and message]
	 */
	function afnfPrintError( error ) {
		return '<div class="error afnf-error nf-error-msg">' + error + '</div>';
	}

	/**
	 * Check for presence of email and username fields,
	 * disable form and return an error if one or more
	 * fields are missing from the form.
	 *
	 * @since  1.1
	 *
	 * @return void
	 */
	function afnfCheckFields() {

		if ( $( afnf.registrationFormIDSelector + '.nf-form-cont' ).has( '.email-wrap' ).length ) {

			afnf.hasEmail = true;

		} else {

			afnf.hasEmail = false;

		}

		if ( $( afnf.registrationFormIDSelector + '.nf-form-cont' ).has( '.affwp_afnf_username-wrap' ).length ) {

			afnf.hasUsername = true;

		} else {

			afnf.hasUsername = false;

		}

		$( afnf.registrationFormIDSelector + '.nf-form-cont input' ).disabled = true;
		$( afnf.registrationFormIDSelector + ".nf-form-cont input[type='button']" ).disabled = true;

		/**
		 * Print the missing fields error.
		 */

		if ( !afnf.hasEmail && !afnf.hasUsername ) {

			/**
			 * Both fields missing. Disable inputs.
			 */
			$( afnf.registrationFormIDSelector + '.nf-form-cont input' ).disabled = true;
			$( afnf.registrationFormIDSelector + ".nf-form-cont input[type='button']" ).disabled = true;

			/**
			 * Print an error specific to both fields missing.
			 */
			$( afnf.registrationFormIDSelector + '.nf-form-content' ).prepend( afnfPrintError( afnf.missingFields ) );

			if ( afnf.debug ) {
				console.afnf( afnf.missingFields );
				console.debugger;
			}

		} else if ( ! afnf.hasEmail ) {

			/**
			 * Email field missing. Disable inputs.
			 */
			document.querySelector( afnf.registrationFormIDSelector + '.nf-form-cont input' ).disabled = true;
			document.querySelector( afnf.registrationFormIDSelector + ".nf-form-cont input[type='button']" ).disabled = true;

			/**
			 * Print an error specific to an email field missing.
			 */
			$( afnf.registrationFormIDSelector + '.nf-form-content' ).prepend( afnfPrintError( afnf.missingEmail ) );

			if ( afnf.debug ) {
				console.afnf( afnf.missingEmail );
				console.debugger;
			}

		} else if ( afnf.loggedIn && afnf.hasEmail ) {

			// Ensure email input contains a value.
			$( afnf.registrationFormIDSelector + ".nf-form-cont .email-container input" ).val( afnf.email );

			/**
			 * If logged in, disable the email and username fields,
			 * but ensure the submit button is enabled.
			 */
			document.querySelector( afnf.registrationFormIDSelector + ".nf-form-cont input[type='button']" ).disabled = false;

			if ( afnf.hasEmail ) {
				document.querySelector( afnf.registrationFormIDSelector + ".nf-form-cont input[type='email']" ).disabled = true;
			}

			if ( afnf.hasUsername ) {
				document.querySelector( afnf.registrationFormIDSelector + ".nf-form-cont .affwp_afnf_username-wrap input" ).disabled = true;
			}

		} else {

			if ( afnf.hasEmail ) {
				/**
				 * Enable inputs if at least an email address is specified.
				 */
				document.querySelector( afnf.registrationFormIDSelector + '.nf-form-cont input' ).disabled = false;
				document.querySelector( afnf.registrationFormIDSelector + ".nf-form-cont input[type='button']" ).disabled = false;

				if ( $( afnf.registrationFormIDSelector + '.nf-form-cont' ).has( '.afnf-error' ).length ) {
					$( afnf.registrationFormIDSelector + '.nf-form-cont .afnf-error' ).remove();
				}
			}
		}

		/**
		 * If logged in and user is a valid affiliate, disable the email and username fields,
		 */

		if ( afnf.loggedIn ) {
			$( afnf.registrationFormIDSelector + ".nf-form-cont .email-container").prop('disabled', true );
			$( afnf.registrationFormIDSelector + ".nf-form-cont .affwp_afnf_username-wrap input").prop('disabled', true );
			$( afnf.registrationFormIDSelector + ".nf-form-cont .password-container" ).remove();
			$( afnf.registrationFormIDSelector + ".nf-form-cont .passwordconfirm-container" ).remove();
			$( ".hello" ).remove();
		}

	}

	/**
	 * The high DOM paint time for the async module stack present within NF3
	 * causes a variable load time. A delay of one second provides adequate time for NF3 to load.
	 *
	 * @since 1.1.8
	 */
	setTimeout( afnfCheckFields, 200 );

	/**
	 * AFNF field validation via Backbone and Marionette
	 *
	 * - 2 custom fields (username, payment email)
	 * - 1 core NF3 field extended with validation (email)
	 */

	/**
	 * Backbone model controller for email field validation
	 *
	 * @since  1.1
	 *
	 * @param  {[object]} initialize        Marionette object listeners
	 * @param  {[object]} validateRequired  Define required field
	 *
	 * @return {[object][controller]}       AffWPAFNFController_Email instance
	 */
	var AffWPAFNFController_Email = Marionette.Object.extend( {
		initialize: function() {

			/**
			 * Define NF3 listeners
			 *
			 */
			var submitChannel = Backbone.Radio.channel( 'submit' );
			this.listenTo( submitChannel, 'validate:field', this.validateRequired );

			// on the Field's model value change...
			var fieldsChannel = Backbone.Radio.channel( 'fields' );
			this.listenTo( fieldsChannel, 'change:modelValue', this.validateRequired );
		},
		/**
		 * Validate fields.
		 *
		 * @since  1.1
		 *
		 * @param  {[object]} model
		 *
		 * @return {[void]}
		 */
		validateRequired: function( model ) {

			/**
			 * Check and validate account email field only.
			 */

			if ( 'email' != model.get( 'type' ) ) return;

			/**
			 * Value checks for email fields
			 *
			 * @since  1.1
			 *
			 * @param  {[string]} model.get
			 *
			 * @return {[void]}
			 */
			if ( model.get( 'value' ) ) {

				/**
				 * Remove errors by default
				 */
				Backbone.Radio.channel( 'fields' ).request( 'remove:error', model.get( 'id' ), 'afnf-error' );

				/**
				 * Validate the submitted email address via ajax
				 *
				 * @since  1.1
				 *
				 * @param  {[bool|string]}  $.ajax.response  The ajax error handler response
				 *
				 * @return {[void]}
				 */
				$.ajax( {
					url: affiliatewp_afnf.ajax_url,
					type: 'get',
					data: {
						action: 'affiliatewp_afnf_validate_email',
						afnf_get_email: model.get( 'value' ),
						form_id: afnf.afnfFormID
					},
					success: function( response ) {

						if ( response == 0 ) {

							return;

						} else {

							if ( afnf.debug ) {
								console.afnf( response );
							}

							/**
							 * Print an email-exists error via wp-ajax.
							 */
							Backbone.Radio.channel( 'fields' ).request( 'add:error', model.get( 'id' ), 'afnf-error', response );

							/**
							 * Provide a11y notice, if available.
							 *
							 * @since  1.1
							 */
							if ( typeof wp.a11y === 'undefined' ) {
								return;
							} else {
								wp.a11y.speak( response, 'assertive' );
							}
						}
					}

				} );

			} else {

				/**
				 * Add an error if there is no email input field value.
				 */
				Backbone.Radio.channel( 'fields' ).request( 'add:error', model.get( 'id' ), 'afnf-error', affiliatewp_afnf.error_email_empty );

				/**
				 * Provide a11y notice, if available.
				 *
				 * @since  1.1
				 */
				if ( typeof wp.a11y === 'undefined' ) {
					return;
				} else {
					wp.a11y.speak( affiliatewp_afnf.error_email_empty, 'assertive' );
				}

			}
		}
	} );

	/**
	 * Backbone model controller for payment email field validation
	 *
	 * @since  1.1.9
	 *
	 * @param  {[object]} initialize        Marionette object listeners
	 * @param  {[object]} validateRequired  Define required field
	 *
	 * @return {[object][controller]}       AffWPAFNFController_Email instance
	 */
	var AffWPAFNFController_Payment_Email = Marionette.Object.extend( {
		initialize: function() {

			/**
			 * Define NF3 listeners
			 *
			 */
			var submitChannel = Backbone.Radio.channel( 'submit' );
			this.listenTo( submitChannel, 'validate:field', this.validateRequired );

			// on the Field's model value change...
			var fieldsChannel = Backbone.Radio.channel( 'fields' );
			this.listenTo( fieldsChannel, 'change:modelValue', this.validateRequired );
		},
		/**
		 * Validate fields.
		 *
		 * @since  1.1.9
		 *
		 * @param  {[object]} model
		 *
		 * @return {[void]}
		 */
		validateRequired: function( model ) {

			/**
			 * Check and validate payment email field only.
			 */
			if ( 'affwp_afnf_payment_email' != model.get( 'type' ) ) return;

			/**
			 * Value checks for payment email fields
			 *
			 * @since  1.1.9
			 *
			 * @param  {[string]} model.get
			 *
			 * @return {[void]}
			 */
			if ( model.get( 'value' ) ) {

				/**
				 * Remove errors by default
				 */
				Backbone.Radio.channel( 'fields' ).request( 'remove:error', model.get( 'id' ), 'afnf-error' );

				/**
				 * Validate the submitted email address via ajax
				 *
				 * @since  1.1
				 *
				 * @param  {[bool|string]}  $.ajax.response  The ajax error handler response
				 *
				 * @return {[void]}
				 */
				$.ajax( {
					url: affiliatewp_afnf.ajax_url,
					type: 'get',
					data: {
						action: 'affiliatewp_afnf_validate_payment_email',
						afnf_get_payment_email: model.get( 'value' ),
						form_id: afnf.afnfFormID
					},
					success: function( response ) {

						if ( response == 0 ) {

							return;

						} else {

							if ( afnf.debug ) {
								console.afnf( response );
							}

							/**
							 * Print an email-exists error via wp-ajax.
							 */
							Backbone.Radio.channel( 'fields' ).request( 'add:error', model.get( 'id' ), 'afnf-error', response );

							/**
							 * Provide a11y notice, if available.
							 *
							 * @since  1.1
							 */
							if ( typeof wp.a11y === 'undefined' ) {
								return;
							} else {
								wp.a11y.speak( response, 'assertive' );
							}
						}
					}

				} );

			}
		}
	} );

	/**
	 * Backbone model controller for username field validation
	 *
	 * @since  1.1
	 *
	 * @param  {[object]} initialize        Marionette object listeners
	 * @param  {[object]} validateRequired  Define required field
	 *
	 * @return {[object][controller]}       AffWPAFNFController_Username instance
	 */
	var AffWPAFNFController_Username = Marionette.Object.extend( {
		initialize: function() {

			if ( afnf.isValidAffiliate && afnf.loggedIn ) {
				$( afnf.registrationFormIDSelector + ".nf-form-cont .affwp_afnf_username-wrap input" ).attr('disabled',true);
				return;
			}

			/**
			 * Define NF3 listeners
			 *
			 */
			var submitChannel = Backbone.Radio.channel( 'submit' );
			this.listenTo( submitChannel, 'validate:field', this.validateRequired );

			// on the Field's model value change...
			var fieldsChannel = Backbone.Radio.channel( 'fields' );
			this.listenTo( fieldsChannel, 'change:modelValue', this.validateRequired );
		},
		/**
		 * Validate fields.
		 *
		 * @since  1.1
		 *
		 * @param  {[object]} model
		 *
		 * @return {[void]}
		 */
		validateRequired: function( model ) {
			/**
			 * Check and validate username field only.
			 */
			if ( 'affwp_afnf_username' != model.get( 'type' ) ) return;

			/**
			 * Value checks for username fields
			 *
			 * @since  1.1
			 *
			 * @param  {[string]} model.get
			 *
			 * @return {[void]}
			 */
			if ( model.get( 'value' ) ) {

				/**
				 * Remove errors by default
				 */
				Backbone.Radio.channel( 'fields' ).request( 'remove:error', model.get( 'id' ), 'afnf-error' );

				/**
				 * Validate the username via ajax
				 *
				 * @since  1.1
				 *
				 * @param  {[bool|string]}  $.ajax.response  The ajax error handler response
				 *
				 * @return {[void]}
				 */
				$.ajax( {
					url: affiliatewp_afnf.ajax_url,
					type: 'get',
					data: {
						action: 'affiliatewp_afnf_validate_username',
						afnf_get_username: model.get( 'value' )
					},
					success: function( response ) {

						if ( afnf.isValidAffiliate && afnf.loggedIn ){
							return;
						}

						if ( response == 0 ) {

							return;

						} else {

							if ( afnf.debug ) {
								console.afnf( response );
							}

							/**
							 * Print a username-exists error via wp-ajax.
							 */
							Backbone.Radio.channel( 'fields' ).request( 'add:error', model.get( 'id' ), 'afnf-error', response );

							/**
							 * Provide a11y notice, if available.
							 *
							 * @since  1.1
							 */
							if ( typeof wp.a11y === 'undefined' ) {
								return;
							} else {
								wp.a11y.speak( response, 'assertive' );
							}
						}
					}

				} );

			} else {

				/**
				 * Add an error if there is no username input field value.
				 */
				Backbone.Radio.channel( 'fields' ).request( 'add:error', model.get( 'id' ), 'afnf-error', affiliatewp_afnf.error_missing_username );

				/**
				 * Provide a11y notice, if available.
				 *
				 * @since  1.1
				 */
				if ( typeof wp.a11y === 'undefined' ) {
					return;
				} else {
					wp.a11y.speak( affiliatewp_afnf.error_missing_username, 'assertive' );
				}

			}
		}
	} );

	/**
	 * AffWPAFNFController_* controller instances
	 */
	new AffWPAFNFController_Email();
	new AffWPAFNFController_Username();
	new AffWPAFNFController_Payment_Email();

} );
