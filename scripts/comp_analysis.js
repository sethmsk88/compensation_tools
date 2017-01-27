$(document).ready(function(){

	/***
	 Event Handler:
	 Whenever something changes in the External Market Ratio
	 calculator, update calculated fields
	***/
	$(".calc").change(function(){

		// Calculate lowest value in all Min and 25% cells
		var all_array = [];     // Contains all numbers in the input cells of all columns.
		var lowest_array = [];  // Contains all numbers in the input cells of the Min and 25% columns.
		var mid_array = [];     // Contains all numbers in the input cells of the Midpoint column.
		var highest_array = []; // Contains all numbers in the input cells of the 75% and Max columns.

		// For each value in the lowest 2 columns
		$('input.calc-lowest').each(function(){
			if ($(this).val().length > 0)
				lowest_array.push(parseMoney($(this).val()));
		});

		// For each value in the midpoint column
		$('input.calc-mid').each(function(){
			if ($(this).val().length > 0)
				mid_array.push(parseMoney($(this).val()));
		});

		// For each value in the highest 2 columns
		$('input.calc-highest').each(function(){
			if ($(this).val().length > 0)
				highest_array.push(parseMoney($(this).val()));
		});

		// For each input value in calculator
		$('input.calc').each(function(){
			if ($(this).val().length > 0)
				all_array.push(parseMoney($(this).val()));
		});

		// Find min of lowest_array and load value into appropriate cell
		$('#aggregate_low').text(Math.min.apply(Math, lowest_array).formatMoney(2, '$', ',', '.'));
		$('#aggregate_mid').text(mid_array.median().formatMoney(2, '$', ',', '.'));
		$('#aggregate_high').text(Math.max.apply(Math, highest_array).formatMoney(2, '$', ',', '.'));


		/** Calculate External Market Ratio **/
		var recommended_midpoint = parseMoney($('#recommended-mid').text());
		var workDept_midpoint = parseMoney($('#workDept-mid').text());
		var actual_midpoint = parseMoney($('#actual-mid').text());
		var external_midpoint = all_array.median();
		$('#overall_market_ratio').text((recommended_midpoint / external_midpoint * 100).formatMoney(1, '', '.', '') + '%');
		$('#workDept_ext_market_ratio').text((workDept_midpoint / external_midpoint * 100).formatMoney(1, '', '.', '') + '%');
		$('#workDept_int_market_ratio').text((workDept_midpoint / actual_midpoint * 100).formatMoney(1, '', '.', '') + '%');
	});

	/* For demo purposes only */
	// Load some info into External Market Ratio Calculator
	$('#src_0').val('DOL');
	$('#min_0').val('$26,000.00');
	$('#Q1_0').val('$31,000.00');
	$('#mid_0').val('$35,000.00');
	$('#Q3_0').val('$40,000.99');
	$('#max_0').val('$44,000.51');

	$('#src_1').val('CUPA');
	$('#Q1_1').val('$32,465.00');
	$('#mid_1').val('$35,500.00');
	$('#Q3_1').val('$42,846.67');
	/* End Demo Code */

	// Manually trigger the event handler for the External Market Ratio	calculator.
	$('.calc').trigger('change');

});