<div class="subscribers-container">
	<div class="subscribers-col1">
		<?php
		$list_table = new FUE_Subscribers_List_Table();
		$list_table->prepare_items();
		$list_table->display();
		?>
	</div>
	<div class="subscribers-col2">
		<form action="admin-post.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="fue_subscribers_manage" />

			<div class="meta-box no-padding">
				<h3 class="handle"><?php _e('Add a Subscriber', 'follow_up_emails'); ?></h3>
				<div class="inside">
					<p>
						<input type="email" name="email" placeholder="Email" />
					</p>
					<p>
						<input type="text" name="first_name" placeholder="First name" />
					</p>
					<p>
						<input type="text" name="last_name" placeholder="Last name" />
					</p>

					<div class="meta-box-actions">
						<input type="submit" name="button_add" class="button button-primary" value="<?php _e('Add Subscriber', 'follow_up_emails'); ?>">
						<div class="clear"></div>
					</div>
				</div>
			</div>

			<div class="meta-box no-padding">
				<h3 class="handle"><?php _e('Create a List', 'follow_up_emails'); ?></h3>
				<div class="inside">
					<p>
						<input type="text" name="list_name" placeholder="List name" />
					</p>

					<div class="meta-box-actions">
						<input type="submit" name="button_create_list" class="button button-primary" value="<?php _e('Create List', 'follow_up_emails'); ?>">
						<div class="clear"></div>
					</div>
				</div>
			</div>

			<div class="meta-box no-padding">
				<h3 class="handle"><?php _e('Bulk add subscribers', 'follow_up_emails'); ?></h3>
				<div class="inside">
					<p><?php _e('Import your existing mailing lists and email addresses.', 'follow_up_emails'); ?></p>

					<p class="form-field">
						<input type="file" name="csv" />
					</p>

					<p class="form-field">
						<label for="import_to_list"><?php _e('Import to list', 'follow_up_emails'); ?></label>
						<br/>
						<select name="import_to_list" id="import_to_list" style="min-width: 100%;">
							<option value=""><?php _e('Uncategorized', 'follow_up_emails'); ?></option>
							<?php foreach ( Follow_Up_Emails::instance()->newsletter->get_lists() as $list ): ?>
								<option value="<?php echo $list['id']; ?>"><?php echo esc_attr( $list['list_name'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>

					<div class="meta-box-actions">
						<input type="submit" class="button-primary" name="upload" value="<?php _e('Upload list', 'follow_up_emails'); ?>" />
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$("#div_lists").insertAfter($("#bulk-action-selector-bottom"));
		$("#new_list").insertAfter($("#bulk-action-selector-bottom"));
		$("#rename_subscriber").insertAfter($("#bulk-action-selector-bottom"));

		$(".btn-new-list").click(function() {
			var name = prompt("<?php _e('List Name:', 'follow_up_emails'); ?>")

			if ( !name ) {
				return;
			}

			$(".wrap").block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

			$.post(
				ajaxurl,
				{
					action: "fue_create_list",
					name: name
				},
				function() {
					window.location.reload();
				}
			)
		});

		$(".remove-from-list").click(function(e) {
			e.preventDefault();
			var btn         = $(this);
			var table       = $(this).parents("table");
			var list        = $(this).data("list");
			var subscriber  = $(this).data("subscriber");

			table.block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

			$.post(ajaxurl, {action: "fue_remove_subscriber_from_list", subscriber: subscriber, list: list}, function() {
				$(btn).parents("div.list").remove();
				table.unblock();
			});

		});

		$( '#bulk-action-selector-top' ).change( function() {
			var bulk_action_selector_bottom = $( '#bulk-action-selector-bottom' );
			bulk_action_selector_bottom.val( $(this).val() ).change();

			if ( $.inArray( $(this).val(), [ 'new', 'move', 'rename' ] ) !== -1 ) {
				// If the top action selected will show an additional field,
				// scroll to that field.
				$( 'html' ).animate( {
					scrollTop: ( bulk_action_selector_bottom.offset().top - 40 ) + 'px'
				}, 250, function() {
					var input_selector;
					switch ( bulk_action_selector_bottom.val() ) {
						case 'move':
							input_selector = $( '#div_lists input' );
							break;
						case 'rename':
							input_selector = $( '#rename_subscriber input' ).first();
							break;
						default:
							input_selector = $( '#new_list input' );
							break;
					}
					input_selector.focus();
				} );
			}
		} );

		$( '#bulk-action-selector-bottom' ).change( function() {
			switch ( $(this).val() ) {

				case 'move':
					$( '#div_lists' ).show();
					$( '#rename_subscriber' ).hide();
					$( '#new_list' ).hide();
					break;

				case 'new':
					$( '#new_list' ).show();
					$( '#rename_subscriber' ).hide();
					$( '#div_lists' ).hide();
					break;

				case 'rename':
					$( '#div_lists' ).hide();
					$( '#new_list' ).hide();
					$( '#rename_subscriber' ).show();
					break;

				default:
					$( '#div_lists' ).hide();
					$( '#new_list' ).hide();
					$( '#rename_subscriber' ).hide();
					break;

			}

			$( '#bulk-action-selector-top' ).val( $( this ).val() );
		} ).change();

		$("#select_lists").select2();

		$(".run-filter").click(function() {
			var filter = $("#filter_list").val();

			window.location.href = 'admin.php?page=followup-emails-subscribers&list='+ filter;
		});
	});
</script>
