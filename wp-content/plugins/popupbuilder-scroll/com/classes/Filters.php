<?php
namespace sgpbscroll;

class Filters
{
	public function __construct()
	{
		add_filter('sgPopupEventsData', array($this, 'eventsData'), 10, 1);
		add_filter('sgPopupEventAttrs', array($this, 'eventsAttrs'), 10, 1);
		add_filter('sgPopupEventTypes', array($this, 'eventsTypes'), 10, 1);
		add_filter('sgpbProEvents', array($this, 'proEvents'), 10, 1);
	}

	public function proEvents($events)
	{
		if (empty($events)) {
			return $events;
		}

		$key = array_search('onScroll', $events);
		if ($key !== false) {
			unset($events[$key]);
		}
		
		return $events;
	}

	public function eventsData($eventsData)
	{
		$eventsData['param'][SGPB_SCROLL_EVENT_KEY] = 'On Scroll';
		$eventsData[SGPB_SCROLL_EVENT_KEY] = 0;

		return $eventsData;
	}

	public function eventsAttrs($eventsAttrs)
	{
		$eventsAttrs[SGPB_SCROLL_EVENT_KEY] = array(
				'htmlAttrs' => array('class' => 'js-sg-onScroll-text', 'min' => 0),
				'infoAttrs' => array(
					'label' => 'After x percent',
					'info' => __('Specify the part of the page, in percentages, where the popup should appear after scrolling.', SG_POPUP_TEXT_DOMAIN)
				)
			);

		return $eventsAttrs;
	}

	public function eventsTypes($eventsTypes)
	{
		$eventsTypes[SGPB_SCROLL_EVENT_KEY] = 'number';

		return $eventsTypes;
	}
}