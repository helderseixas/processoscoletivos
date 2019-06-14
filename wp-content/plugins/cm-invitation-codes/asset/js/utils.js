(function($) {

window.CMREG = {};
window.CMREG.Utils = {
		
	addSingleHandler: function(handlerName, selector, action, func) {
		var obj;
		if (typeof selector == 'string') obj = $(selector);
		else obj = selector;
		obj.each(function() {
			var obj = $(this);
			if (obj.data(handlerName) != '1') {
				obj.data(handlerName, '1');
				obj.on(action, func);
			}
		});
	},
	
	leftClick: function(func) {
		return function(e) {
			// Allow to use middle-button to open thread in a new tab:
			if (e.which > 1 || e.shiftKey || e.altKey || e.metaKey || e.ctrlKey) return;
			func.apply(this, [e]);
			return false;
		}
	},
	
	
	toast: function(msg, className, duration) {
		if (typeof className == 'undefined') className = 'cmreg-toast-info';
		if (typeof duration == 'undefined') duration = 5;
		var toast = $('<div/>', {"class":"cmreg-toast "+ className, "style":"display:none"});
		toast.html(msg);
		$('body').append(toast);
		window.CMREG.Utils.fadeIn(toast, 'fast', function() {
			setTimeout(function() {
				window.CMREG.Utils.fadeOut(toast, 'fast');
			}, duration*1000);
		});
	},
	
	
	fadeIn: function(elem, time, callback) {
		if (time == 'fast') {
			time = 500;
		}
		elem = $(elem);
		elem.css('opacity', '0');
		elem.css('display', 'block');
		var step = 0;
		var timer = setInterval(function() {
			step += 10;
			elem.css('opacity', Math.min(1, step/time));
			if (step >= time) {
				clearInterval(timer);
				if (typeof callback == 'function') {
					callback.call(elem);
				}
			}
		}, 1);
	},
	
	fadeOut: function(elem, time, callback) {
		if (time == 'fast') {
			time = 500;
		}
		elem = $(elem);
		elem.css('opacity', '1');
		elem.css('display', 'block');
		var step = time;
		var timer = setInterval(function() {
			step -= 10;
			elem.css('opacity', Math.max(0, step/time));
			if (step <= 0) {
				clearInterval(timer);
				elem.css('display', 'none');
				if (typeof callback == 'function') {
					callback.call(elem);
				}
			}
		}, 1);
	}
		
};


$('.cmreg-delete-confirm').click(function(ev) {
	if (!confirm(CMREG__Utils.deleteConfirmText)) {
		ev.stopPropagation();
		ev.preventDefault();
	}
});


})(jQuery);
	


