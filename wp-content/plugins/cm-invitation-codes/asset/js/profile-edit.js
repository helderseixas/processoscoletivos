jQuery(function($) {
	
	
	$('.cmreg-profile-edit-form').submit(function(ev) {
		
		ev.preventDefault();
		ev.stopPropagation();
		
		var form = $(this);
		
		var data = form.serialize();
		
		$.post(form.attr('action'), data, function(response) {
			window.CMREG.Utils.toast(response.msg);
			if (response.success) {
				
			}
		});
		
	});
	
	
	$('.cmreg-change-password-form').submit(function(ev) {
		
		ev.preventDefault();
		ev.stopPropagation();
		
		var form = $(this);
		
		var data = form.serialize();
		
		$.post(form.attr('action'), data, function(response) {
			window.CMREG.Utils.toast(response.msg);
			if (response.success) {
				form[0].reset();
			}
		});
		
	});
	
	
});