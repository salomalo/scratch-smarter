( function( blocks, element ) {
	var el 			= element.createElement,
		source 		= blocks.source,
		InspectorControls = blocks.InspectorControls,
		category 	= {slug:'cp-calculated-fields-form', title : 'Calculated Fields Form'};

	/* Plugin Category */
	blocks.getCategories().push({slug: 'cpcff', title: 'Calculated Fields Form'});

	/* ICONS */
	const iconCPCFF = el('img', { width: 20, height: 20, src:  "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAABHNCSVQICAgIfAhkiAAAAGdJREFUOI1jnHnk3X8GKgAmahhCVYNYGBgYGDq3PqLIkHJvOeq5iHHQBTYLjIEcTuXecgydWx8xMDISNqDMSw7VIHTAyMjAcKdVH68hKtUX4Wzqew0d/P+PaiMhQLVYGw1swmDwZREAIzIpNydZa8YAAAAASUVORK5CYII=" } );

	const iconCPCFFV = el('img', { width: 20, height: 20, src:  "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAABHNCSVQICAgIfAhkiAAAAIhJREFUOI1jnHnk3X8GKgAmahhCVYNYkDlp1oIkaZ519D2cjeEi5aqLDMpVF3FqxiWP1Wt32/RxGoRLDmcYYbMVn0txGnS3TR9Fo3LVRbwuZcEpg2YYPkPwuohUgNcgmHfQvUmSQehhQsgwvIFNjBhegwglSGwAI9YIxQ4ueRSDkPMOqWDwFSMAJOI0MlfsCoEAAAAASUVORK5CYII=" } );

	/* Form's shortcode */
	blocks.registerBlockType( 'cpcff/form-shortcode', {
		title: 'Insert CFF',
		icon: iconCPCFF,
		category: 'cpcff',
		supports: {
			customClassName: false,
			className: false
		},
		attributes: {
			shortcode : {
				type : 'string',
				source : 'text',
				default: '[CP_CALCULATED_FIELDS id=""]'
			}
		},

		edit: function( props ) {
			var focus = props.focus;
			return [
				!!focus && el(
					InspectorControls,
					{
						key: 'cpcff_inspector'
					},
					[
						el(
							'span',
							{
								key: 'cpcff_inspector_help',
								style:{fontStyle: 'italic'}
							},
							'If you need help: '
						),
						el(
							'a',
							{
								key		: 'cpcff_inspector_help_link',
								href	: 'https://cff.dwbooster.com/documentation#insertion-page',
								target	: '_blank'
							},
							'CLICK HERE'
						),
					]
				),
				el('textarea',
					{
						key: 'cpcff_form_shortcode',
						value: props.attributes.shortcode,
						onChange: function(evt){
							props.setAttributes({shortcode: evt.target.value});
						},
						style: {width:"100%", resize: "vertical"}
					}
				)
			];
		},

		save: function( props ) {
			return props.attributes.shortcode;
		}
	});

	/* variable shortcode */
	blocks.registerBlockType( 'cpcff/variable-shortcode', {
		title: 'Create var from POST, GET, SESSION, or COOKIES',
		icon: iconCPCFFV,
		category: 'cpcff',
		supports: {
			customClassName: false,
			className: false
		},
		attributes: {
			shortcode : {
				type : 'string',
				source : 'text',
				default: '[CP_CALCULATED_FIELDS_VAR name=""]'
			}
		},

		edit: function( props ) {
			var focus = props.focus;
			return [
				!!focus && el(
					InspectorControls,
					{
						key: 'cpcff_inspector'
					},
					[
						el(
							'span',
							{
								key: 'cpcff_inspector_help',
								style:{fontStyle: 'italic'}
							},
							'If you need help: '
						),
						el(
							'a',
							{
								key		: 'cpcff_inspector_help_link',
								href	: 'https://cff.dwbooster.com/documentation#javascript-variables',
								target	: '_blank'
							},
							'CLICK HERE'
						)
					]
				),
				el(
					'textarea',
					{
						key: 'cpcff_variable_shortcode',
						value: props.attributes.shortcode,
						onChange: function(evt){
							props.setAttributes({shortcode: evt.target.value});
						},
						style: {width:"100%", resize: "vertical"}
					}
				)
			];
		},

		save: function( props ) {
			return props.attributes.shortcode;
		}
	});
} )(
	window.wp.blocks,
	window.wp.element
);