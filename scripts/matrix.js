$(document).ready(function() {

	var expandIconClass = "glyphicon-triangle-top";
	var collapseIconClass = "glyphicon-triangle-bottom";

	/* When an Expand/Collapse icon buttons is clicked */
	$('.expand-collapse').click(function() {

		// Change icon to opposite icon
		$(this).toggleClass(expandIconClass);
		$(this).toggleClass(collapseIconClass);
		
		// Display nested options list
		$(this).next().toggle("fast");
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
				.prevAll('input.checkbox-all')
				.prop('checked', false);
		}

	})

	/*
		When an option checkbox's change event is triggered
	*/
	$('.filter-option .option-checkbox').change(function(e) {
		
		/*
			If unchecking this checkbox, uncheck ancestor
			checkbox with class = "checkbox-all"
		*/
		if (!$(this).prop('checked')) {
			$(this).parents('ul')
				.prevAll('input.checkbox-all')
				.prop('checked', false);
		}

		// Toggle checkbox
		if ($(this).prop('checked'))
			$(this).prop('checked', false);
		else
			$(this).prop('checked', true);	
	});

	/*
		When an "All" checkbox's change event is triggered
	*/
	$('.checkbox-all').change(function() {
		var checked = $(this).prop('checked');

		// Check or uncheck all decendant checkboxes
		$(this).nextAll('ul').find('input:checkbox').prop('checked', checked);
	});
	
});
