$(document).ready(function() {

	var expandIconClass = "glyphicon-triangle-top";
	var collapseIconClass = "glyphicon-triangle-bottom";

	/*
		When an Expand/Collapse icon buttons is clicked
	*/
	$('.expand-collapse').click(function() {

		// Change icon to opposite icon
		$(this).toggleClass(expandIconClass);
		$(this).toggleClass(collapseIconClass);
		
		// Display nested options list
		$(this).next().toggle("fast");
	});


	/*
		When a click container is clicked
	*/
	$('.filter-group .click-container').click(function() {
		
		// Get list item's descendant checkbox
		var $checkbox = $(this).find('.checkbox-all');

		// Toggle checkbox
		if ($checkbox.prop('checked'))
			$checkbox.prop('checked', false);
		else
			$checkbox.prop('checked', true);

		// Check or uncheck all decendant checkboxes
		$checkbox.parent().nextAll('ul').find('input:checkbox').prop('checked', $checkbox.prop('checked'));		
	});


	/*
		When an "All" checkbox's change event is triggered
	*/
	$('.checkbox-all').change(function() {

		// Toggle checkbox
		if ($(this).prop('checked'))
			$(this).prop('checked', false);
		else
			$(this).prop('checked', true);	

		var checked = $(this).prop('checked');

		// Check or uncheck all decendant checkboxes
		$(this).parent().nextAll('ul').find('input:checkbox').prop('checked', checked);		
	});


	/*
		When a list item containing a checkbox is clicked
	*/
	$('.options-list li').click(function() {
		
		// Get list item's descendant checkbox
		var $checkbox = $(this).find('.option-checkbox');

		// Toggle checkbox
		if ($checkbox.prop('checked'))
			$checkbox.prop('checked', false);
		else
			$checkbox.prop('checked', true);

		/*
			If unchecking this checkbox, uncheck ancestor
			checkbox with class = "checkbox-all"
		*/
		if (!$(this).prop('checked')) {
			$(this).parents('ul')
				.prevAll('span.click-container')
				.find('input.checkbox-all')
				.prop('checked', false);
		}
	});


	/*
		When an option checkbox's change event is triggered
	*/
	$('.filter-option .option-checkbox').change(function(e) {
		
		// Toggle checkbox
		if ($(this).prop('checked'))
			$(this).prop('checked', false);
		else
			$(this).prop('checked', true);	

		/*
			If unchecking this checkbox, uncheck ancestor
			checkbox with class = "checkbox-all"
		*/
		if (!$(this).prop('checked')) {
			$(this).parents('ul')
				.prevAll('span.click-container')
				.find('input.checkbox-all')
				.prop('checked', false);
		}
	});

	/* Event handler for "Apply Filters" button */
	$('#applyFilters-btn').on('click', function(e) {
		e.preventDefault();

		$.ajax({
			type: 'post',
			url: './content/act_matrix.php',
			data: $('#filters-form').serialize(),
			success: function(response) {
				$('#table-container').html(response);
			}
		});
	})
});
