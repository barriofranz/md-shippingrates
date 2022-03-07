(function( $ ) {
	'use strict';

	//change condition type
	jQuery(document).on('change', '#msr-main-div select.cond_type', function(e){
		e.preventDefault();
		var box_changed = jQuery(this);
		var box_container = box_changed.closest('div.condition_op');
		var box_selection = box_changed.val();
		box_container.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
		var option_ID = jQuery(this).closest('.single-row').attr('data-row_id');
		var row_ID = jQuery(this).closest('tr').index();
		var cond_ID = box_container.parent().find('div.condition_op').length - 1;
		var data = { action: 'betrs_add_conds_op_details', selected: box_selection, optionID: option_ID, rowID: row_ID, condID: cond_ID };
		$.post( ajaxurl, data, function( response ) {
			if( box_container.find('.cond_op_extras').length > 0 ) {
				box_container.find('.cond_op_extras').replaceWith( response );
			} else {
				jQuery(box_changed).parent().after( response );
			}
			jQuery(document).trigger( 'betrs_update_options' );
			box_container.find('.blockUI').remove();
			box_container.trigger('wc-enhanced-select-init');
		});
	});


	//add condition
	jQuery(document).on('click', '#msr-main-div a.add_table_condition_op', function(e){
        e.preventDefault();
        jQuery(this).block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
        var link_clicked = jQuery(this);
        var link_container = jQuery(this).closest('div.condition_op');
        var option_ID = jQuery(this).closest('.single-row').attr('data-row_id');
        var row_ID = jQuery(this).parents('tr').index();
		console.log(option_ID);
		console.log(row_ID);
        var data = { action: 'betrs_add_extra_conditions_op', optionID: option_ID, rowID: row_ID };
        $.post( ajaxurl, data, function( response ) {
            jQuery(link_clicked).before( response );
            jQuery(document).trigger( 'betrs_update_options' );
            link_clicked.find('.blockUI').remove();
        });
    });



	//add cost
    jQuery(document).on('click', '#msr-main-div a.add_table_cost_op', function(e){
        e.preventDefault();
        jQuery(this).block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
        var link_clicked = jQuery(this);
        var link_container = jQuery(this).closest('div.cost_op');
        var option_ID = jQuery(this).closest('.single-row').attr('data-row_id');
        var row_ID = jQuery(this).closest('tr').index();
        var data = { action: 'betrs_add_extra_costs_op', optionID: option_ID, rowID: row_ID };
        $.post( ajaxurl, data, function( response ) {
            jQuery(link_clicked).before( response );
            link_clicked.find('.blockUI').remove();
        });
    });



	//delete cond
	jQuery(document).on('click', '#msr-main-div span.betrs_delete_ops_cond', function(){
		var answer = confirm(betrs_data.text_delete_confirmation);
		if (answer) {
			var cost_table = jQuery(this).closest('div.condition_op').remove();
		}
		return false;
	});
	//delete cost
	jQuery(document).on('click', '#msr-main-div span.betrs_delete_ops_cost', function(){
		var answer = confirm(betrs_data.text_delete_confirmation);
		if (answer) {
			var cost_table = jQuery(this).closest('div.cost_op').remove();
		}
		return false;
	});


	//add tablerow
    jQuery(document).on('click', '#msr-main-div a.betrs_add_ops', function(e){
        e.preventDefault();
        jQuery(this).block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
        var link_clicked = jQuery(this);
        var cost_table = jQuery(this).closest('.single-row').find('.msr-table');
        var option_ID = jQuery(this).closest('.single-row').attr('data-row_id');
        var row_ID = cost_table.find("tbody > tr").length;
        if( row_ID == 1 && cost_table.find("tbody").children('tr:first').hasClass('no-items') ) {
            row_ID--;
        }

        var data = { action: 'betrs_add_table_costs_row', optionID: option_ID, rowID: row_ID };
        $.post( ajaxurl, data, function( response ) {
            // append new row to table
            jQuery(cost_table).find('tbody').append( response );
            // Remove 'No Items' row if exists
            if( cost_table.find( 'tr.no-items' ) != undefined ) {
                cost_table.find( 'tr.no-items' ).remove();
			}

			$('.description.column-description').remove();
			$('.sort.column-sort').remove();
            link_clicked.find('.blockUI').remove();
        });
    });


	//delete all checked rows
	jQuery(document).on('click', '#msr-main-div a.betrs_delete_ops', function(){
        var answer = confirm(betrs_data.text_delete_confirmation);
        if (answer) {
            var cost_table = jQuery(this).closest('.single-row').find('.msr-table tbody tr th input:checked');
            cost_table.each(function(i, el){
                jQuery(el).closest('tr').remove();
            });
            // redo variable names so IDs match their appropriate rows
            jQuery(this).closest('.single-row').find('.msr-table tbody ').children("tr").each(function (idx) {
                var $inp = jQuery(this).find('td:not(:first-child').find('input,textarea,select');
                $inp.each(function () {
                    str = this.name;
                    // find 2nd occurence of '[' string and assign to 'var i'
                    var i = -1;
                    var n = 2;
                    while( n-- && i++ < str.length ) {
                        i = str.indexOf('[', i);
                        if (i < 0) break;
                    }
                    removeID = str.substring( 0, i );
                    newName = removeID + '[' + idx + '][]';
                    // find condition key if multiple select type
                    if( this.attributes['multiple'] ) {
                        // find 3rd occurence of '[' string and assign to 'var i'
                        var i = -1;
                        var n = 3;
                        while( n-- && i++ < str.length ) {
                            i = str.indexOf('[', i);
                            if (i < 0) break;
                        }
                        cidx_str = str.substring( i+1 );
                        j = cidx_str.indexOf(']');
                        cidx = cidx_str.substring( 0, j );
                        newName = removeID + '[' + idx + '][' + cidx + '][]';
                    } else {
                        newName = removeID + '[' + idx + '][]';
                    }
                    this.name = newName;
                })
            });
        }
        return false;
    });




	jQuery(document).on('click', '.msr-save-btn', function(e){
		e.preventDefault();
		var dataOption = $(this).attr('data-option');
		var datas = $('.md-methods-div[data-option="'+dataOption+'"] :input').serialize();
		var thisbtn = this;
		$(thisbtn).attr('disabled','disabled');
		$(thisbtn).text('Saving...');
		var data = {
			action: 'msr_update_shipping_option',
			dataOption: dataOption,
			datas: datas
		};
		$.post( ajaxurl, data, function( response ) {
			$(thisbtn).removeAttr('disabled');
			$(thisbtn).text('Success');
			$(thisbtn).attr('class','button-primary msr-save-btn success');
			setTimeout(function(){
					 $(thisbtn).attr('class','button-primary msr-save-btn');
					 $(thisbtn).text('Save');
			}, 5000);

		});

	});

	jQuery(document).on('click', '.msr-save-all', function(e){
		e.preventDefault();
		var thisbtn = this;
		$(thisbtn).attr('disabled','disabled');

		var elemCount = jQuery('.msr-save-btn').length;
		jQuery('.msr-save-btn').each(function (idx) {
			sleep(500).then(() => {
				$(this).trigger('click');
				if (idx == (elemCount - 1)) {
					$(thisbtn).removeAttr('disabled');
				}
			});
		});

	});

	function sleep(ms) {
	  return new Promise(resolve => setTimeout(resolve, ms));
	}

	// setTimeout(function(){
		// jQuery('.wc_input_price').each(function (idx) {
			// $(this).attr('tabindex', '10000'+idx);
		// });
	// }, 500);
})( jQuery );
