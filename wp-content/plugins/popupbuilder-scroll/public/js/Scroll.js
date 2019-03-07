SgpbEventListener.prototype.sgpbOnScroll = function(listenerObj, eventData)
{
	var that = this;
	var percent = parseInt(eventData.value);
	var scrollStatus = false;

	jQuery(window).on('scroll', function() {

		var scrollTop = jQuery(window).scrollTop();
		var docHeight = jQuery(document).height();
		var winHeight = jQuery(window).height();
		var scrollPercent = (scrollTop) / (docHeight - winHeight);
		var scrollPercentRounded = Math.round(scrollPercent*100);
		if (percent < scrollPercentRounded) {
			if (scrollStatus == false) {
				listenerObj.getPopupObj().prepareOpen();
				scrollStatus = true;
			}
		}
	});
};