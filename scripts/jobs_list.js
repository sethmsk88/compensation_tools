$(document).ready(function() {

	/* Click event handler for Jobs in Jobs list */
	$('.job').click(function() {
		var jobCode = $(this).find('td').first().text();
		window.location.href = "?page=job_details&jc=" + jobCode;
	});

});
