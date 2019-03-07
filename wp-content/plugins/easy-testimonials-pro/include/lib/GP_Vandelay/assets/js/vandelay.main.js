init_vandelay_js = function($) { // wrapper

	/* Setup references for elements  of  the wizard */
	var _target = document.getElementById('vandelay_drop_target');
	var _match_cols = document.getElementById('match_columns');
	var _data_table = document.getElementById('vandelay_data_table');
	var _file_input = document.getElementById('vandelay_file_select_input');
	var _file_input_wrapper = document.getElementById('vandelay_file_select_wrapper');
	var _wizard = document.getElementById('vandelay_import_wizard');
	var _batch_id = '';
	var _do_poll = false;
	var _min_poll_interval = 200; // .2 seconds
	var _max_poll_interval = 30000; // 30 seconds
	var _poll_interval = _min_poll_interval;
	var _history_updater = false;

	/* Global vars */
	var _data_table_container,
		spinner,
		$container,
		$parent,
		$window,
		availableWidth,
		availableHeight;

	/*
	 * Updates the globals availableHeight and availableWidth. They will be set 
	 * to either the browser window's size, minus a 250px margin, or the minimum
	 * value allowed for that dimension (600 for height, 400 for width).
	 */
	var calculateSize = function () {
	  var offset = $container.offset();
	  availableWidth = Math.max( $(_wizard).width() - 50, 600 );
	  availableHeight = Math.max( $(_wizard).height() - 50, 400 );
	};

		
	/* 
	 * Initialize late-bound global vars and references on startup. Bind event 
	 * to resize which updates the availableHeight and availableWidthglobals.
	 */
	$(document).ready(function() {
		$container = $("#vandelay_data_table");
		$parent = $container.parent();
		$window = $(window);
		$window.on('resize', calculateSize);
		reset_wizard_steps();
	});
	
	var reset_wizard_steps = function () {
		$(_wizard).find('.wizard_step').off('click');
		$(_wizard).tabs('enable', 0);
		$(_wizard).tabs( 'disable', 1 );
		$(_wizard).tabs( 'disable', 2 );
		$(_wizard).tabs( 'disable', 3 );
		switch_to_tab_index(0);
	};
	
	/*
     * Initialize DropSheet
	 */
	var _workstart = function() {
		spinner = new Spinner().spin(_target);
		$(_match_cols).hide();
		$(_data_table).hide();
		$(_file_input_wrapper).css('display', 'none');
	}

	var _workend = function() {
		spinner.stop();
		$(_match_cols).show();
		$(_file_input_wrapper).css('display', 'block');
	}
	
	

	/** Alerts **/
	var _badfile = function() {
	  alert('Your file could not be read. Please try a different file.', function(){});
	};

	var _pending = function() {
	  alert('Please wait until the current file is processed.', function(){});
	};

	var _large = function(len, cb) {
		$r = confirm("This file is " + len + " bytes and may take a few moments.  Your browser may lock up during this process. Would you like to continue?");
		if ( $r && (typeof(cb) == 'function') ) {
		  cb();
		}	  
	};

	var _failed = function(e) {
	  alert('There was an unknown error. Please try again.');
	};

	var _onsheet = function(json, sheetnames, select_sheet_cb) {
		
		if (!json) {
			json = [];
		}

		// pad the first row (the headings) to the width of the longest data row
		json.forEach(function(r) {
			if (json[0].length < r.length) {
				json[0].length = r.length;
			}
		});
		
		// update globals availableWidth & availableHeight
		calculateSize();

		// build a list of <select>'s representing each of our headers, and 
		// replace the current content of the match panel with the new list
		var panel_html = build_match_columns_panel(gp_vandelay_vars.headers, json);
		$(_match_cols).html(panel_html);
		
		// preselect matching fields where possible
		auto_match_inputs( _match_cols );
		
		// add the continue button to go on to the preview screen
		var button_wrapper = $('<div class="vandelay_button_group">');
		append_reset_button(button_wrapper, '&laquo; Choose Another File');
		append_continue_button(button_wrapper, json);
		$(_match_cols).append(button_wrapper);
		switch_to_map_columns_tab(true);
	};

	/** Handsontable magic **/
	var boldRenderer = function (instance, td, row, col, prop, value, cellProperties) {
	  Handsontable.TextCell.renderer.apply(this, arguments);
	  $(td).css({'font-weight': 'bold'});
	};

	if (_target) {

		/* initialize DropSheet*/
		DropSheet( {
		  drop: _target,
		  on: {
			workstart: _workstart,
			workend: _workend,
			sheet: _onsheet,
			foo: 'bar'
		  },
		  errors: {
			badfile: _badfile,
			pending: _pending,
			failed: _failed,
			large: _large,
			foo: 'bar'
		  },
		  file_input: _file_input	  
		} );
		
		$(_target)
		.on('dragenter', function () {
			$(this).addClass('drag_hover');
		})
		.on('dragleave', function () {
			$(this).removeClass('drag_hover');
		})
		.on('drop', function () {
			$(this).removeClass('drag_hover');
		})
		.on('dragend', function () {
			$(this).removeClass('drag_hover');
		});
		
	}

	/*
     * Initialize tabs
	 */
	$(_wizard).tabs({
		'disabled' : true
	});
	
	var switch_to_tab_index = function (index)
	{
		$(_wizard).find('.wizard_step.completed_step').removeClass('completed_step');
		$(_wizard).find('.wizard_step:lt(' + index + ')').addClass('completed_step');
		$(_wizard).find('.wizard_step.current_step').removeClass('current_step');
		$(_wizard).find('.wizard_step:eq(' + index + ')').addClass('current_step');
		$(_wizard).tabs("option", "active", index);
	}

	var switch_to_map_columns_tab = function(skip_confirmation) {
		var do_reset = ( typeof(skip_confirmation) != 'undefined' ) && skip_confirmation
					   ? true
					   : confirm('Are you sure you want to re-map your columns? Any edits to you have made the current file will be lost.');
		if ( do_reset ) {
			$(_wizard).tabs('enable', 1);
			switch_to_tab_index(1);
			$(_wizard).tabs( 'disable', 0 );
			$(_wizard).tabs( 'disable', 2 );
			$(_wizard).tabs( 'disable', 3 );
			$(_wizard).find('.wizard_step:first').off('click', warn_before_uploading_new_file)
												 .on('click', warn_before_uploading_new_file);
			$(_wizard).find('.wizard_step:eq(1)').off('click')
												 .on('click', function () {
													switch_to_map_columns_tab()
												});			
		}		
	};
	
	var continue_to_preview_tab = function (ev, json_in) {
		var btn = ev.target;
		var selects = $(btn).parents('#match_columns:first')
							.find('.select_match');
							
		var field_map = build_field_map(selects);
		var reformatted_json = reformat_json_data(field_map, json_in);
		
		// show that data in a handsontable as a preview/edit screen
		init_data_table(reformatted_json);
		
		// add buttons for Remap Columns (aka Prev) and Run Import (aka Next)
		if ( $('#vandelay_wizard_step_3 .vandelay_button_group').length == 0 ) {
						
			// create the button group; we'll append both buttons to this
			var button_group = $('<div class="vandelay_button_group"></div>');

			// add "ready to import N rows" message to top of button group (placeholder for now)
			var count_msg = '<p class="import_count_msg"></p>';			
			button_group.append(count_msg);			
		
			// add Re-map Columns button (returns user to Step 2)
			var remap_button = $('<div id="remap_columns_button" class="button_wrapper"><button type="button" class="button">&laquo; Re-Map Columns</button></div>');
			remap_button.on('click', function (ev) {
				switch_to_map_columns_tab();
			});
			button_group.append(remap_button);

			// add Run Import button (starts job and moves to Step 4)
			var run_import_button = $('<div id="run_import_button" class="button_wrapper"><button type="button" class="button button-primary">Run Import &raquo;</button></div>');
			run_import_button.on('click', function (ev) {
				start_import(ev);
			});		
			
			button_group.append(remap_button);
			button_group.append(run_import_button);
			
			$('#vandelay_wizard_step_3').append(button_group);
			
			$(_wizard).find('.wizard_step:eq(1)').off('click')
												 .on('click', function () {
													switch_to_map_columns_tab()
												});
			
		}

		// Update "Ready to import N rows" message
		update_ready_to_import_message(reformatted_json.length - 1);
		
		//load preview/edit tab
		$(_wizard).tabs('enable', 2);
		switch_to_tab_index(2);
		$(_wizard).tabs('disable', 1);
	};

	var start_import = function(ev) {
		
		// switch to import tab
		$(_wizard).tabs('enable', 3);
		switch_to_tab_index(3);
		
		// TODO:
		
		// 1) Confirm that the user is ready to begin the import
		
		// 2) Disable data table from further edits (disable all prev steps too?)
		$(_wizard).tabs('disable', 0);
		$(_wizard).tabs('disable', 1);
		$(_wizard).tabs('disable', 2);
		// disable special click events on all other tabs
		$(_wizard).find('.wizard_step:lt(3)').off('click');
		
		// 3) Get JSON  from data table (including edits)
		var data_json =  get_table_data();
		
		// 4) Grab nonce from form
		var wpnonce = $('#vandelay_wpnonce').val();
		
		// 5) Post JSON to server, receive Batch ID + status
		jQuery.ajax(
			ajaxurl,
			{
				method: 'post',
				dataType: 'json',
				data: {
					action: 'vandelay_receive_import',
					'vandelay_wpnonce': wpnonce,
					'data_json': jQuery.stringify (data_json)
				},
				success: function (data) {
					// 5) Display batch ID + job status
					if (data.batch_id) {
						update_status_message('Your batch has been queued (ID: '  + data.batch_id + '). You may leave this screen now if you like.');
					}
					
					// 6) Setup polling for progress updates
					poll_batch_status(data.batch_id);
					
					// 7) In the background, update the job history table
					start_history_page_updates();
				},
				error: function(xhr, str_error, e_exception) {
					// TODO: add error handling / retry
				}
			}
		);
		
		// 7) Show an initial "Sending job.. " message, which will be updated
		//	  as the status updates
		var row_count = data_json.length - 1;
		update_progress_bar(0);
		update_status_message('Uploading ' + row_count + ' rows..');
		var status_panel = $('#vandelay_wizard_step_4');		
	};
	
	var update_ready_to_import_message = function(count) {
		// Update "Ready to import N rows" message
		var count_msg = '<p class="import_count_msg">'
			+ 'Ready to import ' + (count) + ' records.'
			+ '</p>';
		$('#vandelay_wizard_step_3 .vandelay_button_group .import_count_msg').html(count_msg);
	};
	
	var update_history_page = function () {
		var history_wrapper = $('#vandelay_import_history_wrapper');
		if (history_wrapper.length > 0) {
			$.ajax( {
				url: ajaxurl,
				type: "POST",
				data: {
					'action' : 'vandelay_get_job_history',
				},
				success: function(response) {
					if ( response.history_page ) {
						history_wrapper.html(response.history_page);
					}
				},
				dataType: "json",
			} );
		}
	};
	
	var start_history_page_updates = function() {
		update_history_page();
		_history_updater = setInterval( function () {
			update_history_page();
		}, 10000 );
	};	

	var stop_history_page_updates = function() {
		clearInterval(_history_updater);
	};
	
	var poll_batch_status = function(batch_id) {
		_do_poll = true;
		$.ajax( {
			url: ajaxurl,
			type: "POST",
			data: {
				'action' : 'vandelay_get_batch_status',
				'batch_id' : batch_id,
			},
			success: function(response) {
				if ( response.complete && response.total && response.percentage ) {
					var msg = response.percentage + '% complete. (' + response.complete + ' / ' + response.total + ' rows)';
					update_progress_bar(response.percentage);
					update_status_message(msg);
				}
				
				 // if job status is complete, stop polling
				if ( (response.complete >= response.total) || response.status == 'complete') {
					_do_poll = false;
					update_status_message('<strong>Import complete!</strong><br> ' + response.complete + ' records processed. ' + response.imported + ' records imported, ' + response.duplicate + ' records rejected as duplicate.');
					update_history_page();
					add_reset_button();
					stop_history_page_updates();
				}
				
				// gradually reduce poll interval back to 100ms
				if (_poll_interval > _min_poll_interval) {
					_poll_interval = (_poll_interval / 4 );
					_poll_interval = Math.max(_min_poll_interval, _poll_interval);
				}
			},
			dataType: "json",
			complete: function () {
				// start a new poll in 1 second
				if (_do_poll) {
					setTimeout( function() {
						poll_batch_status(batch_id);
					}, _poll_interval );
				}
			},
			error: function(xhr, type, str_error) {
				// gradually increase delay to 30s on error
				_poll_interval *= 2;
				_poll_interval = Math.min(_max_poll_interval, _poll_interval);
			},
			timeout: 10000 // 10 seconds
		} );
	};
	
	var init_data_table = function(json) {
		calculateSize(); // updates globals availableWidth & availableHeight
		
		/* init HandsonTable! */
		_data_table_container = $(_data_table).handsontable({
			data: json,
			startRows: 20,
			startCols: 3,
			rowHeaders: true,
			colHeaders: true,
			width: function() {
				return availableWidth;
			},
			height: 480,
			stretchH: 'none',
			colWidths: [150, 300, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150]
		})
		.show();
		
	};
	
	var get_table_data = function() {		
		return _data_table_container.data().handsontable.getData() 
				
	};

	// build array which contains a header mapped to a column index in the JSON data
	// (pulled from the values of the selects). If no JSON column was selected, the 
	// will be empty for that header
	var build_field_map = function (selects) {
		var field_map = [];
		selects.each( function (index, elem) {
			var header = $(this).data('header');
			var selected_option_val = $(this).find('option:selected').val();
			field_map.push( {
				'header': header,
				'col_index': selected_option_val
			} );
		});
		return field_map;
	};

	var reformat_json_data = function (field_map, json_in) {
		var json_out = [];
		var i, j;
		
		for (i in json_in) {
			var row = json_in[i];
			var new_json_row = [];
			for (j in field_map) {
				var field = field_map[j];
				var value = field.col_index && row[field.col_index]
							? row[field.col_index]
							: '';
				new_json_row.push(value);
			}
			json_out.push(new_json_row);
		}
		
		// add new headers
		var new_headers = [];
		for (j in field_map) {
			var field = field_map[j];
			new_headers.push(field.header);
		}
		json_out[0] = new_headers;
		
		return json_out;
	};

	var build_column_select = function(json) {
		var json_options = '<select>';
		json_options += '<option value="">-- Select a Column --</option>';	
		var first_row = json[0];
		var cell;
		var row_counter = 0;
		for (i in first_row) {
			cell = first_row[i];
			json_options += '<option value="' + row_counter + '">' + cell + '</option>';
			row_counter++;
		}
		json_options += '</select>';	
		return json_options;
	};
	
	var build_match_columns_panel = function (headers, json) {
		var panel_html = '',
			json_options = build_column_select(json),
			i;
			
		for (i in headers) {	
			panel_html += '<div class="select_match" data-header="' + headers[i] + '">' + 
							'<label><span class="label">' + headers[i] + ':</span> ' + 
								json_options +
							'</label>' + 
						  '</div>';
		}
		return panel_html;
	};
	
	var warn_before_uploading_new_file = function() {
		var do_reset = confirm('Are you sure you want to upload a new file? You will lose any changes you have made.');
		if ( do_reset ) {
			$(_wizard).tabs('enable', 0);
			switch_to_tab_index(0);
			$(_wizard).tabs( 'disable', 1 );
			$(_wizard).tabs( 'disable', 2 );
			$(_wizard).tabs( 'disable', 3 );
			$(this).off('click');
			$(_wizard).find('.wizard_step:eq(1)').off('click')
		}
	};	

	var auto_match_inputs = function(container) {
		var select_rows = $(container).find('.select_match');	
		select_rows.each( function (index, elem) {
			var label = $(this).find('.label').text().trim();
			
			// remove trailing ":" if present 
			var last_char = label.substring(label.length - 1);
			if (last_char == ':') {
				label = label.substring(0, label.length - 1);
			}
			matching_option = $(this).find('option').filter(function () {
				return fuzzy_str_match( $(this).html(), label );
			});
					
			if (matching_option.length > 0) {
				// unselect any currently selected items
				$(this).find('option:selected')
					   .removeAttr('selected');
				// select the match
				matching_option.attr('selected', 'selected');				
			}
		} );
	};
	
	var fuzzy_str_match = function(str1, str2) {
		
		// try for an exact match first
		if ( str1.localeCompare(str2) == 0 ) {
			return true;
		}
		
		// normalize strings by replacing any non-alphanumeric strings with 
		// a single "_", before comparing them again
		var regex = /[^a-z0-9+]+/gi;
		str1 = str1.toLowerCase()
			.replace(regex, '_');
			
		str2 = str2.toLowerCase()
			.replace(regex, '_');
		
		// compare the normalized strings and return the result
		return ( str1.localeCompare(str2) == 0 );
	};

	var append_continue_button = function(container, json) {
		var continue_button = $('<div class="button_wrapper"><button type="button" class="button button-primary">Continue &raquo;</button></div>');
		continue_button.on('click', function (ev) {
			continue_to_preview_tab(ev, json);
		});
		$(container).append(continue_button);
	};
	
	var append_reset_button = function(container, button_label) {
		if ( typeof(button_label) == 'undefined' ) {
			button_label = 'Import Another File';
		}
		
		if ( $(container).find('.reset_button').length == 0 ) {
			var reset_button = $('<div class="button_wrapper"><button class="button reset_button" type="button">' + button_label + '</button></div>')
							   .on('click', warn_before_uploading_new_file);
			$(container).append(reset_button);
		}		
	};
	
	var handle_reset_form = function (ev) {
		reset_wizard_steps();		
		$(_match_cols).hide();
		$(_data_table).hide();
		reset_status_panel();
		$(_file_input).val('');
		ev.preventDefault();		
	};
	
	var add_reset_button = function(msg) {
		var status_panel = $('#vandelay_wizard_step_4');
		if ( status_panel.find('.reset_button').length == 0 ) {
			var reset_button = $('<button class="button reset_button" type="button">Import Another File</button>')
							   .on('click', handle_reset_form);
			status_panel.append(reset_button);
		}
	};

	var update_status_message = function(msg) {
		var status_panel = $('#vandelay_wizard_step_4');
		if ( status_panel.find('.message').length == 0 ) {
			status_panel.append('<p class="message"></p>');
		}
		status_panel.find('.message').html(msg);
	};

	var update_progress_bar = function(percentage) {
		var status_panel = $('#vandelay_wizard_step_4');
		if ( status_panel.find('.vandelay_progress_bar').length == 0 ) {
			status_panel.prepend('<div class="vandelay_progress_bar"><div class="vandelay_progress_bar_inner"></div></div>');
		}
		var delay = (percentage < 100) ? 750 : 50;					
		status_panel.find('.vandelay_progress_bar_inner')
					.stop()
					.animate({'width': (percentage + '%')}, delay);
	};

	var reset_status_panel = function() {
		$('#vandelay_wizard_step_4').html('');
	};
	
	/* Add stringify method to jQuery - converts JSON object to JSON string */
	$.extend({
		stringify  : function stringify(obj) {         
			if ("JSON" in window) {
				return JSON.stringify(obj);
			}

			var t = typeof (obj);
			if (t != "object" || obj === null) {
				// simple data type
				if (t == "string") obj = '"' + obj + '"';

				return String(obj);
			} else {
				// recurse array or object
				var n, v, json = [], arr = (obj && obj.constructor == Array);

				for (n in obj) {
					v = obj[n];
					t = typeof(v);
					if (obj.hasOwnProperty(n)) {
						if (t == "string") {
							v = '"' + v + '"';
						} else if (t == "object" && v !== null){
							v = jQuery.stringify(v);
						}

						json.push((arr ? "" : '"' + n + '":') + String(v));
					}
				}

				return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
			}
		}
	});
	
};

init_vandelay_js(jQuery);