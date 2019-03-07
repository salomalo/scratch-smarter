( 
	function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://github.com/WordPress/gutenberg/tree/master/blocks#api
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://github.com/WordPress/gutenberg/tree/master/element#element
	 */
	var el = wp.element.createElement;
	/**
	 * Retrieves the translation of text.
	 * @see https://github.com/WordPress/gutenberg/tree/master/i18n#api
	 */
	var __ = wp.i18n.__;
	
	var build_category_options = function(categories) {
		var opts = [
			{
				label: 'No Category',
				value: ''
			}
		];

		// build list of options from goals
		for( var i in categories ) {
			cat = categories[i];
			opts.push( 
			{
				label: cat.name,
				value: cat.id
			});
		}
		return opts;
	};	

	var iconGroup = [];
	iconGroup.push(	el(
			'path',
			{ d: "M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"}
		)
	);
	iconGroup.push(	el(
			'path',
			{ d: "M0 0h24v24H0z", fill: 'none' }
		)
	);
	
	var iconEl = el(
		'svg', 
		{ width: 24, height: 24 },
		iconGroup
	);

	/**
	 * Every block starts by registering a new block type definition.
	 * @see https://wordpress.org/gutenberg/handbook/block-api/
	 */
	registerBlockType( 'easy-testimonials-pro/submit-testimonial', {
		/**
		 * This is the display title for your block, which can be translated with `i18n` functions.
		 * The block inserter will show this name.
		 */
		title: __( 'Submit a Testimonial' ),

		/**
		 * Blocks are grouped into categories to help users browse and discover them.
		 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
		 */
		category: 'easy-testimonials',

		/**
		 * Optional block extended support features.
		 */
		supports: {
			// Removes support for an HTML mode.
			html: false,
		},

		/**
		 * The edit function describes the structure of your block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
		 *
		 * @param {Object} [props] Properties passed from the editor.
		 * @return {Element}       Element to render.
		 */
		edit: wp.data.withSelect( function( select ) {
					return {
						categories: select( 'core' ).getEntityRecords( 'taxonomy', 'easy-testimonial-category', {
							order: 'asc',
							orderby: 'id'
						})
					};
				} ) ( function( props ) {
						var retval = [];
						var inspector_controls = [],
							id = props.attributes.id || '',
							submit_to_category = props.attributes.submit_to_category || '',
							focus = props.isSelected;
							
			
						var category_fields = [];
						
						// add <select> to choose the Category
						var controlOptions = {
							label: __('Submit To Category:'),
							value: submit_to_category,
							onChange: function( newVal ) {
								props.setAttributes({
									submit_to_category: newVal
								});
							},
							options: build_category_options(props.categories),
						};
					
						category_fields.push(
							el(  wp.components.SelectControl, controlOptions )
						);

						inspector_controls.push(							
							el (
								wp.components.PanelBody,
								{
									title: __('Category'),
									className: 'gp-panel-body',
									initialOpen: true,
								},
								category_fields
							)
						);

						retval.push(
							el( wp.editor.InspectorControls, {}, inspector_controls ) 
						);

						// show a box in the editor representing the block
						var inner_fields = [];
						if ( !! focus && false ) {
							retval.push( el('h3', { className: 'block-heading' }, __('Easy Testimonials - Submit a Testimonial') ) );
						} else {
							inner_fields.push( el('h3', { className: 'block-heading' }, 'Easy Testimonials - Submit a Testimonial') );
						}
						
						inner_fields.push( el('blockquote', { className: 'submit-testimonial-placeholder' }, __('A form to collect new testimonials from your customers.') ) );
						retval.push( el('div', {'className': 'easy-testimonials-editor-not-selected'}, inner_fields ) );
					
				return el( 'div', { className: 'easy-testimonials-submit-testimonial-editor'}, retval );
			} ),

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
		 *
		 * @return {Element}       Element to render.
		 */
		save: function() {
			return null;
		},
		attributes: {
			id: {
				type: 'string',
			},
			submit_to_category: {
				type: 'string',
			},
		},
		icon: iconEl,
	} );
} )(
	window.wp
);
