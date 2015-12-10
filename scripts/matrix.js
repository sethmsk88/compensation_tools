// $(function(){ // this is shorthand for the next line
$(document).ready(function(){

	// Back button event handler
	$('#goBack').click(function(){
		window.location = "./?page=JFPL_matrix";
	});

	/* Why No Work?
	function addChangeURLVar(urlVarName)
	{
		var fullURL = window.location.href;
		var newURL = fullURL;
		var urlVarExists = false;
		var urlVarVal = '';

		// For each URL piece
		$(fullURL.split('&')).each(function(key, val){
			var urlVar = val.split('=');
			if (urlVar[0] == urlVarName){
				urlVarVal = val;
				urlVarExists = true;
			}
		});

		// If the var exists, remove it.
		if (urlVarExists){
			newURL = fullURL.replace('&' + urlVarVal, '');
		}

		// Append URL var to newURL and return result
		return newURL + '&' + urlVarName + '=' + $(this).val();
	}
	*/

	// Triggered when Working Department filter changes
	$('#workDept_filter').change(function(e){
		
		// Add or modify deptID filter in URL
		var pageName = 'JFPL_matrix';
		var fullURL = window.location.href;
		var urlVarKey = 'deptID';
		var urlVarVal = '';
		var urlVarExists = false;

		// If page name is not already in URL, append it
		if (fullURL.search(/\?page=JFPL_matrix/) == -1){
			fullURL += "?page=" + pageName;
		}
		var newURL = fullURL;

		// For each URL piece
		$(fullURL.split('&')).each(function(key, val){
			var urlVar = val.split('=');
			if (urlVar[0] == urlVarKey){
				urlVarVal = val;
				urlVarExists = true;
			}
		});

		// If the var exists, remove it.
		if (urlVarExists){
			newURL = fullURL.replace('&' + urlVarVal, '');
		}

		// Append URL var to newURL
		newURL += '&' + urlVarKey + '=' + $(this).val();
		window.location = newURL;

	});

	// Triggered when Pay Level filter changes
	$('#payLevel_filter').change(function(e){

		// Add or modify deptID filter in URL
		var pageName = 'JFPL_matrix';
		var fullURL = window.location.href;
		var urlVarKey = 'pl';
		var urlVarVal = '';
		var urlVarExists = false;

		// If page name is not already in URL, append it
		if (fullURL.search(/\?page=JFPL_matrix/) == -1){
			fullURL += "?page=" + pageName;
		}
		var newURL = fullURL;

		// For each URL piece
		$(fullURL.split('&')).each(function(key, val){
			var urlVar = val.split('=');
			if (urlVar[0] == urlVarKey){
				urlVarVal = val;
				urlVarExists = true;
			}
		});

		// If the var exists, remove it.
		if (urlVarExists){
			newURL = fullURL.replace('&' + urlVarVal, '');
		}

		// Append URL var to newURL
		newURL += '&' + urlVarKey + '=' + $(this).val();
		window.location = newURL;
	});

	// Triggered when Pay Level filter changes
	$('#jobFamily_filter').change(function(e){

		// Add or modify deptID filter in URL
		var pageName = 'JFPL_matrix';
		var fullURL = window.location.href;
		var urlVarKey = 'jf';
		var urlVarVal = '';
		var urlVarExists = false;

		// If page name is not already in URL, append it
		if (fullURL.search(/\?page=JFPL_matrix/) == -1){
			fullURL += "?page=" + pageName;
		}
		var newURL = fullURL;

		// For each URL piece
		$(fullURL.split('&')).each(function(key, val){
			var urlVar = val.split('=');
			if (urlVar[0] == urlVarKey){
				urlVarVal = val;
				urlVarExists = true;
			}
		});

		// If the var exists, remove it.
		if (urlVarExists){
			newURL = fullURL.replace('&' + urlVarVal, '');
		}

		// Append URL var to newURL
		newURL += '&' + urlVarKey + '=' + $(this).val();
		window.location = newURL;
	});
});