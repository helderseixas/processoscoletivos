var CMREG_Frontend = {
	debug: 1,
	log: function(msg) {
		if (CMREG_Frontend.debug) {
			console.log(msg);
		}
	}
};

CMREG_Frontend.LOGIN_BTN_SELECTOR = '.cmreg-login-click, a[href="#cmreg-login-click"], .cmreg-only-login-click, a[href="#cmreg-only-login-click"]';
CMREG_Frontend.REGISTER_BTN_SELECTOR = '.cmreg-only-registration-click, a[href="#cmreg-only-registration-click"]';

CMREG_Frontend.LOGIN_BTN_TEXT_SELECTOR = 'a.cmreg-login-click, .cmreg-login-click a, a[href="#cmreg-login-click"], '
				+ 'a.cmreg-only-login-click, .cmreg-only-login-click a, a[href="#cmreg-only-login-click"]';
CMREG_Frontend.REGISTER_BTN_TEXT_SELECTOR = 'a.cmreg-only-registration-click, .cmreg-only-registration-click a, a[href="#cmreg-only-registration-click"]';


/**
 * Stop event
 */
CMREG_Frontend.stopEvent = function(ev) {
	ev.preventDefault();
	ev.stopPropagation();
};

CMREG_Frontend.setupButtonHandlers = function() {
	
	var $ = jQuery;
	
	CMREG_Frontend.log('CMREG setup');
		
	/**
	 * Check each click on every element because of the bugs on the mobile versions
	 */
	$('body').click(function(ev) {
		
		// Check if this is click on the CMREG button
		var btn = CMREG_Frontend.getTheButton(ev.target);
		if (btn.length == 0) return true;
				
		if (CMREG_Settings.isUserLoggedIn == '1') return true;
		else CMREG_Frontend.stopEvent(ev);
		
		CMREG_Frontend.log('CMREG click success')
		
		var overlayReady = function() {
			$('.cmreg-overlay').removeClass('cmreg-only-login').removeClass('cmreg-only-registration');
			if (btn.hasClass('cmreg-only-login-click') || btn.attr('href') == '#cmreg-only-login-click') {
				$('.cmreg-overlay').addClass('cmreg-only-login');
			}
			if (btn.hasClass('cmreg-only-registration-click') || btn.attr('href') == '#cmreg-only-registration-click') {
				$('.cmreg-overlay').addClass('cmreg-only-registration');
			}
			CMREG_Frontend.loginClick();
		};
		
		if ($('.cmreg-overlay').length == 0) {
			// Load overlay by AJAX
			var loader = $('<div/>', {"class": 'cmreg-overlay cmreg-loader-overlay'});
//			loader.click(function() {
//				loader.remove();
//			});
			$('body').append(loader);
			window.CMREG.Utils.fadeIn(loader, 'fast', function() {
				CMREG_Frontend.loadOverlay(function() {
					overlayReady();
				});
			});
//			$.post(CMREG_Settings.ajaxUrl, {action: "cmreg_login_overlay"}, function(response) {
//				$('body').append(response);
//				CMREG_Frontend.initOverlayHandlers();
//				overlayReady();
//			});
		} else {
			overlayReady();
		}
	});
	
};



CMREG_Frontend.loadOverlay = function(callback) {
	var $ = jQuery;
	
//	setTimeout(function() {
		
		$.post(CMREG_Settings.ajaxUrl, {action: "cmreg_login_overlay"}, function(response) {
			var overlay = $('.cmreg-overlay');
			if (overlay.length == 0) {
				CMREG_Frontend.log('append new overlay')
				$('body').append(response);
			} else {
				CMREG_Frontend.log('replace overlay')
				overlay.html($(response).html());
			}
			
			CMREG_Frontend.initOverlayHandlers();
			if (callback) callback();
		});
	
//	}, 2000);
	
};


CMREG_Frontend.getTheButton = function(elem) {
	var obj = jQuery(elem);
	var selector = CMREG_Frontend.LOGIN_BTN_SELECTOR + ', ' + CMREG_Frontend.REGISTER_BTN_SELECTOR;
	var test = obj.is(selector);
//	CMREG_Frontend.log('CMREG button test = ', test);
	if (test) return obj;
	else return obj.parents(selector).first();
};
	
	
	
/**
 * Called after the login button click when the overlay is ready
 */
CMREG_Frontend.loginClick = function() {
	var $ = jQuery;
	$('body').addClass('cmreg-overlay-visible');
	var elem = $('.cmreg-overlay').first();
	var that = this;
	
	var completed = function() {
		$('.cmreg-overlay .cmreg-login-form input[type=email]').focus();
//		$('.cmreg-wrapper', that).trigger('cmreg:init');
		$('.cmreg-wrapper').trigger('cmreg:init');
	};
	
	if (elem.hasClass('cmreg-loader-overlay')) {
		elem.removeClass('cmreg-loader-overlay')
		completed();
	} else {
		CMREG_Frontend.log('fadein')
		window.CMREG.Utils.fadeIn(elem, 'fast', function() {
			completed();
		});
	}
	
};


/**
 * Login and registration form common handler
 */
CMREG_Frontend.formSubmitHandler = function(ev, callback) {
	CMREG_Frontend.stopEvent(ev);
	var $ = jQuery;
	var form = $(this);
	var btn = form.find('button[type=submit]');
	var loader = $('<div/>', {"class": "cmreg-loader-inline"});
	loader.width(btn.width());
	loader.height(btn.height());
	loader.css('padding', btn.css('padding'));
	btn.hide();
	btn.after(loader);
	$.post(form.data('ajax-url'), form.serialize(), function(response) {
		callback(response, form);
	});
};
	
	
/**
 * Called after the overlay is ready
 */
CMREG_Frontend.initOverlayHandlers = function() {
	
	var $ = jQuery;

	/**
	 * Close overlay when clicked at the background
	 */
	$('.cmreg-overlay').off('click.cmreg').on('click.cmreg', function(ev) {
		if (ev.target !== this) return;
		CMREG_Frontend.stopEvent(ev);
		var elem = $(this); //.fadeOut('fast');
		window.CMREG.Utils.fadeOut(elem, 'fast');
		$('body').removeClass('cmreg-overlay-visible');
	});
	
	
	/**
	 * Close overlay button click
	 */
	$('.cmreg-overlay-close').click(function() {
		var elem = $(this).parents('.cmreg-overlay'); //.fadeOut('fast');
		window.CMREG.Utils.fadeOut(elem, 'fast');
		$('body').removeClass('cmreg-overlay-visible');
	});
	
	
	/**
	 * After submit the login form
	 */
	$('.cmreg-login-form').submit(function(ev) {
		CMREG_Frontend.formSubmitHandler.call(this, ev, function(response, form) {
			window.CMREG.Utils.toast(response.msg);
			if (response.success) {
				var elem = form.parents('.cmreg-overlay'); //.fadeOut('fast');
				window.CMREG.Utils.fadeOut(elem, 'fast');
				$('body').removeClass('cmreg-overlay-visible');
				if (response.redirect) {
					location.href = response.redirect;
				} else {
					location.reload();
				}
			} else {
				form.find('.cmreg-loader-inline').remove();
				form.find('button[type=submit]').show();
				
				if (typeof response.showCaptcha == 'string') {
					console.log('adding captcha');
					if (form.find('.cmreg-recaptcha').length == 0) {
						form.find('.cmreg-buttons-field').before(response.showCaptcha);
						CMREG_Frontend.initCaptcha(form);
					}
				}
				
				form.find('.cmreg-recaptcha').each(function() {
					grecaptcha.reset($(this).data('recaptchaResetId'));
				});
			}
		});
	});
	
	
	/**
	 * After submit the registration form
	 */
	$('.cmreg-registration-form').submit(function(ev) {
		CMREG_Frontend.formSubmitHandler.call(this, ev, function(response, form) {
			window.CMREG.Utils.toast(response.msg);
			form.find('.cmreg-loader-inline').remove();
			form.find('button[type=submit]').show();
			if (response.success) {
				var elem = form.parents('.cmreg-overlay'); //.fadeOut('fast');
				window.CMREG.Utils.fadeOut(elem, 'fast');
				$('body').removeClass('cmreg-overlay-visible');
				if (response.redirect == 'reload') {
					location.reload();
				}
				else if (response.redirect && response.redirect.length > 0) {
					location.href = response.redirect;
				} else {
					//location.reload();
				}
			} else {
				form.find('.cmreg-recaptcha').each(function() {
					grecaptcha.reset($(this).data('recaptchaResetId'));
				});
			}
		});
	});
	
	
	/**
	 * After submit the lost password form
	 */
	$('.cmreg-lost-password-form').submit(function(ev) {
		CMREG_Frontend.formSubmitHandler.call(this, ev, function(response, form) {
			window.CMREG.Utils.toast(response.msg);
			form.find('.cmreg-loader-inline').remove();
			form.find('button[type=submit]').show();
		});
	});
	
	
	/**
	 * Show the lost password form
	 */
	$('.cmreg-lost-password-link a').click(function(ev) {
		CMREG_Frontend.stopEvent(ev);
		$(this).hide();
		var form = $(this).parents('.cmreg-login').find('.cmreg-lost-password-form');
		form.show();
		form.find('input[type=email]').focus();
	});
	
	/**
	 * Show the invitation form
	 */
	$('.cmreg-invitation-code-field a').click(function(ev) {
		CMREG_Frontend.stopEvent(ev);
		$(this).hide();
		$(this).parents('div').first().find('input').show().focus();
	});

};


CMREG_Frontend.initCaptcha = function(target) {
	var $ = jQuery;
	$('.cmreg-recaptcha', target).each(function() {
			var container = $(this);
			var parameters = {"sitekey" : container.data('sitekey')};
			try {
				var id = grecaptcha.render(container[0], parameters);
				container.data('recaptchaResetId', id);
			} catch (e) {}
		});
};

	
// Setup handler after new node added
//document.addEventListener('DOMNodeInserted', function(ev) {
//	CMREG_Frontend.setupButtonHandlers(jQuery(ev.target));
//}, false);
	

jQuery(function($) {
	
	/**
	 * Change the login button into logout button
	 */
	if (CMREG_Settings.isUserLoggedIn == '1') {
		$(CMREG_Frontend.LOGIN_BTN_TEXT_SELECTOR).attr('href', CMREG_Settings.logoutUrl).text(CMREG_Settings.logoutButtonLabel);
		$(CMREG_Frontend.REGISTER_BTN_TEXT_SELECTOR).hide();
	}
	
	$(document).on('cmreg:init', function(ev) {
//		CMREG_Frontend.log('cmreg:init');
		var target = $(ev.target);
		// Init recaptcha
		CMREG_Frontend.initCaptcha(target);
	});
	
	// Setup button handler immidiately
	CMREG_Frontend.setupButtonHandlers();
	
	// Init in case that some elements has been already added to the page
	CMREG_Frontend.initOverlayHandlers();
	
	// Init recaptcha for existing shortcodes
	setTimeout(function() {
		$('.cmreg-wrapper').trigger('cmreg:init');
	}, 1000);
	
	// Preload the overlay if needed
	if (CMREG_Settings.isUserLoggedIn && CMREG_Settings.overlayPreload == '1') {
		CMREG_Frontend.loadOverlay();
	}
	
});