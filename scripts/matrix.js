$(document).ready(function() {

	var expandIconClass = "glyphicon-triangle-top";
	var collapseIconClass = "glyphicon-triangle-bottom";

	// Event handler for Expand/Collapse icon buttons
	$('.expand-collapse').click(function() {

		$(this).toggleClass(expandIconClass);
		$(this).toggleClass(collapseIconClass);
		$(this).next().toggle("fast");
	});


});