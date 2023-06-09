/**
 * alg-wc-export-datepicker.
 *
 * @version 1.5.2
 * @since   1.1.0
 */

jQuery( document ).ready( function() {
	jQuery( 'input[display="date"]' ).each( function() {
		if ( alg_wc_export_datepicker_options.do_add_timepicker ) {
			jQuery( this ).datetimepicker( {
				dateFormat: 'yy-mm-dd',
				changeYear: true,
				changeMonth: true,
			} );
		} else {
			jQuery( this ).datepicker( {
				dateFormat: 'yy-mm-dd',
				timeFormat: 'HH:mm',
				changeYear: true,
				changeMonth: true,
			} );
		}
	} );
} );
