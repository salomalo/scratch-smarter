<?php
namespace sgpbregistration;

class AdminHelper
{
	public static function defaultData()
	{
		$data = array();

		$data['registrationSuccessBehavior'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-6 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'col-md-6 sgpb-choice-option-wrapper sgpb-registration-option-label'
				),
				'groupWrapperAttr' => array(
					'class' => 'row form-group sgpb-choice-wrapper'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-registration-success-behavior',
						'class' => 'registration-success-refresh',
						'data-attr-href' => 'registration-success-refresh',
						'value' => 'refresh'
					),
					'label' => array(
						'name' => __('Refresh', SG_POPUP_TEXT_DOMAIN).':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-registration-success-behavior',
						'class' => 'registration-redirect-to-URL',
						'data-attr-href' => 'registration-redirect-to-URL',
						'value' => 'redirectToURL'
					),
					'label' => array(
						'name' => __('Redirect to url', SG_POPUP_TEXT_DOMAIN).':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-registration-success-behavior',
						'class' => 'registration-success-open-popup',
						'data-attr-href' => 'registration-open-popup',
						'value' => 'openPopup'
					),
					'label' => array(
						'name' => __('Open popup', SG_POPUP_TEXT_DOMAIN).':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-registration-success-behavior',
						'class' => 'registration-hide-popup',
						'value' => 'hidePopup'
					),
					'label' => array(
						'name' => __('Hide popup', SG_POPUP_TEXT_DOMAIN).':'
					)
				)
			)
		);

		return $data;
	}

	public static function renderForm($formFields)
	{
		$form = '';

		if (empty($formFields) || !is_array($formFields)) {
			return $form;
		}
		$simpleElements = array(
			'text',
			'email',
			'password',
			'hidden',
			'submit',
			'button'
		);

		$form = '<form class="sgpb-regitser-form" id="sgpb-form" method="post">';
		$fields = '<div class="sgpb-form-wrapper">';
		foreach ($formFields as $fieldKey => $formField) {
			$params = $formField;
			$htmlElement = '';
			$hideClassName = '';
			$type = 'text';

			if (!empty($formField['attrs']['type'])) {
				$type = $formField['attrs']['type'];
				if ($type == 'customCheckbox') {
					$formField['attrs']['type'] = 'checkbox';
				}
			}

			$styles = '';
			$attrs = '';
			$label = '';
			$gdprWrapperStyles = '';
			$gdprText = '';
			$errorMessageBoxStyles = '';
			$errorWrapperClassName = @$formField['attrs']['name'].'-error-message';
			if (isset($formField['errorMessageBoxStyles'])) {
				$errorMessageBoxStyles = 'style="width:'.$formField['errorMessageBoxStyles'].'"';
			}
			if (!empty($formField['label'])) {
				$label = $formField['label'];
				if (isset($formField['text'])) {
					$gdprText = $formField['text'];
				}
				$formField['style'] = array('color' => @$formField['style']['color'], 'width' => $formField['style']['width']);
				$gdprWrapperStyles = 'style="color:'.$formField['style']['color'].'"';
			}

			if ($type == 'checkbox') {
				$formField['style']['max-width'] = $formField['style']['width'];
				unset($formField['style']['width']);
			}
			if (!empty($formField['style'])) {
				$styles = 'style="';
				if (strpos(@$formField['attrs']['name'], 'gdpr') !== false) {
					unset($formField['style']['height']);
				}
				foreach ($formField['style'] as $styleKey => $styleValue) {
					if ($styleKey == 'placeholder') {
						$styles .= '';
					}
					$styles .= $styleKey.':'.$styleValue.'; ';
				}
				$styles .= '"';
			}

			if (!empty($formField['attrs'])) {
				foreach ($formField['attrs'] as $attrKey => $attrValue) {
					$attrs .= $attrKey.' = "'.esc_attr($attrValue).'" ';
				}
			}

			if (!$formField['isShow']) {
				$hideClassName = 'sg-js-hide';
			}

			if (in_array($type, $simpleElements)) {
				if (!isset($formField['attrs']['hasLabel']) || !$formField['attrs']['hasLabel']) {
					$params = array();
				}
				$htmlElement = self::createInputElement($attrs, $styles, $errorWrapperClassName, $errorMessageBoxStyles, $params);
			}
			else if ($type == 'checkbox') {
				$htmlElement = self::createCheckbox($attrs, $styles);

			}

			ob_start();
			?>
			<div class="sgpb-inputs-wrapper js-<?php echo $fieldKey; ?>-wrapper js-sgpb-form-field-<?php echo $fieldKey; ?>-wrapper <?php echo $hideClassName; ?>">
				<?php echo $htmlElement; ?>
			</div>
			<?php
			$fields .= ob_get_contents();
			ob_get_clean();
		}
		$fields .= '</div>';

		$form .= $fields;
		$form .= '</form>';

		return $form;
	}

	public static function createInputElement($attrs, $styles = '', $errorWrapperClassName = '', $errorMessageBoxStyles = '', $labelArgs = array())
	{
		$inputElement = "<input $attrs $styles>";
		if (!empty($labelArgs)) {
			$inputElement = '<label for="'.@$labelArgs['attrs']['sgpb-registration-username'].'"><p class="sgpb-registration-input-label '.@$labelArgs['attrs']['labelClass'].'">'.@$labelArgs['attrs']['hasLabel'].'</p>'.$inputElement.'</label>';
		}
		if (!empty($errorWrapperClassName)) {
			$inputElement .= "<div class='$errorWrapperClassName'></div>";
		}

		return $inputElement;
	}

	public static function createCheckbox($attrs, $styles)
	{
		$inputElement = "<input $attrs $styles>";

		return $inputElement;
	}
}
