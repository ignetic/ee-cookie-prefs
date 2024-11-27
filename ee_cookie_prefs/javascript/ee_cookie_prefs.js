$('#ee_cookie_prefs_form .mainTable a.remove').each(function() {
	$(this).css('cursor', 'pointer');
	$(this).on('click', function() {
		
		var $tr = $(this).closest('tr');
		var index = $tr.index();
		
		if (index === 0) {
			$tr.find(':input').val('');
		} else {
			$tr.remove();
		}
		
	});
});