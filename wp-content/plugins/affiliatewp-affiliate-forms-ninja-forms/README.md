AffiliateWP - Affiliate Forms For Ninja Forms
====================

#### Version 1.1.12, 25th October 2018

* Fix: JavaScript error when trying to map form fields to affiliate registration fields
* Fix: Undefined index notices if the affiliate registration form consists of only the username and email address form fields
* Tweak: Add Polish language files

#### Version 1.1.11, 3rd October 2018

* Fix: Re-minify JavaScript asset.
* Fix: CSS disabled attribute added to all input elements if affiliate is logged in

#### Version 1.1.10, 27th September 2018

* Fix: CSS disabled attribute added to all input elements if affiliate is logged in

#### Version 1.1.9, 13th July 2018

- New: Registration form fields can now be used as email tags in AffiliateWP emails
- New: A merge tag for the affiliate area is now available
- Fix: Fatal error when activating plugin on PHP 7
- Fix: Improved validation on keyup for payment email field
- Fix: Payment email is required even when the field is set to optional
- Fix: Added missing validation for email field
- Fix: Existing users cannot register as affiliates through Ninja Forms registration form
- Fix: Form submission data not shown on affiliate edit screen
- Fix: Form submission data not shown on affiliate review screen

#### Version 1.1.8, 23rd October 2017

- Fix: Resolved an issue where in some cases a blank title would be shown in the Affiliate Registration Ninja Forms action.
- Fix: Update notifications were incorrectly being displayed when no update was available.
- New: Hide password fields in the affiliate registration form if user is logged in while registering an affiliate account.
- New: WordPress email and username fields are now disabled in the affiliate settings form (if the user is logged in).

#### Version 1.1.7, 14th July 2017

- Fix: Resolved intermittent javascript errors on non-AffiliateWP Ninja Forms forms. AFNF now bails if not using the Affiliate Forms for Ninja Forms registration form.
- Fix: Fatal error on activation when loading in Ninja Forms versions lower than 3.0.

#### Version 1.1.6, 10th February 2017

- Fix: Email does not match user account email error when logged in

#### Version 1.1.5, 13th January 2017

- Fix: Affiliate Forms for Ninja Forms no longer appears in the Ninja Forms extension licensing screen.
- Fix: Plugin-updater compatibility.

#### Version 1.1.4, 14th December 2016

- Fix: Plugin version showed incorrectly on some sites

#### Version 1.1.3, 1st December 2016

- Update version integer from NF3 constant to AFNF loader instance variable

#### Version 1.1.2, 14th October 2016

- Fix: Correctly display the payment email for logged in users

#### Version 1.1.1, 10th October 2016

- Fix: A white-screen error was caused in some cases when updating to version 1.1
- Fix: Affiliate forms now show up correctly in the affiliate area

### Version 1.1, 4th October 2016

- New: Add support for Ninja Forms version 3

#### Version 1.0.7, 1st August 2016

- Fix: An extra admin registration email was being sent when the "Auto Register New Users" option was enabled

#### Version 1.0.6, 16th June 2016

- Fix: Fatal error that could occur when AffiliateWP was deactivated

#### Version 1.0.5, 18th December 2015

- New: affiliatewp_afnf_insert_user filter for modifying wp_insert_user args

- Fix: Field data was not showing on the affiliate review screen due to a caching issue on some hosts

#### Version 1.0.4, 16th December 2015

- Fix: Removed submission row on the edit affiliate screen for manually added affiliates

Tweak: When an affiliate registration form has already been set, a message will be shown on other forms with a link to that form

Tweak: AffiliateWP related form elements and options will only be shown if the Ninja Forms integration is enabled

#### Version 1.0.3, 1st October 2015

- Fix: bug with activation that could cause a fatal error in some instances

#### Version 1.0.2, 23rd June 2015

- New: Allowed First Name field to generate an email tag

- Fix: Affiliate registration form did not submit properly when a user was already logged in.

- Fix: Login details email was sent to affiliate even if they had a WP user account.

- Fix: Add-on updates were not working.

- Fix: Undefined index PHP Notice when submitting the form without entering a first name.

- Fix: Prevent password field type from having email tag option. Ninja Forms does not save the password in submission or CSV so using the password in emails is not possible.

- Fix: Prevent submit field type from having email tag option.

- Fix: Some text strings were not properly set up for translation

#### Version 1.0.1

- Fix: If the affiliate registration form had no password field, the password sent to the affiliate via email contained an erroneous exclamation mark which caused issues logging in

#### Version 1.0

Initial release
