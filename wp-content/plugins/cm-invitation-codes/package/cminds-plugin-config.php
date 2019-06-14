<?php

use com\cminds\registration\controller\SettingsController;

use com\cminds\registration\App;

$cminds_plugin_config = array(
	'plugin-is-pro'				 => App::isPro(),
	'plugin-has-addons'      => TRUE,
	'plugin-is-addon'			 => FALSE,
	'plugin-version'			 => App::VERSION,
	'plugin-abbrev'				 => App::PREFIX,
    'plugin-affiliate'               => '',
    'plugin-redirect-after-install'  => admin_url( 'admin.php?page=' . SettingsController::getMenuSlug() ),
	'plugin-settings-url'		 => admin_url( 'admin.php?page=' . SettingsController::getMenuSlug() ),
        'plugin-show-guide'              => TRUE,
    'plugin-guide-text'              => '    <div style="display:block">
        <ol>
            <li>Go to <strong>"Plugin Settings"</strong> and configure the desired behavior of the login and logout</li>
            <li>Use the css class <strong>"cmreg-login-click"</strong> and add it to a link in your site navigation</li>
            <li>Alternatively use the shortcode  <strong>"[cmreg-login]Login[/cmreg-login]" </strong> in your site side bar widget.</li>
            <li><strong>Troubleshooting:</strong> Make sure your site does not have any JavaScript error which might prevent registraion popup from appearing</li>
        </ol>
    </div>',
    'plugin-guide-video-height'      => 240,
    'plugin-guide-videos'            => array(
        array( 'title' => 'Installation tutorial', 'video_id' => '158514902' ),
    ),
	'plugin-addons'        => array(
		array(
			'title' => 'CM Registration EDD Payment Addon',
			'description' => 'Require users make payment in order to activate the registered user account.',
			'link' => 'https://www.cminds.com/downloads/cm-registration-edd-payment-addon/',
			'link_buy' => 'https://www.cminds.com/checkout/?edd_action=add_to_cart&download_id=139000&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=1'
		),
		array(
			'title' => 'CM Registration Bulk Invitation Addon',
			'description' => 'Bulk send invitation codes to the emails from a CSV file.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/registration-and-invitation-codes-plugin-for-wordpress/',
			'link_buy' => 'https://www.cminds.com/checkout/?edd_action=add_to_cart&download_id=137386&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=0'
		),
		array(
			'title' => 'CM Registration Approve New Users Addon',
			'description' => 'Approve or reject new users registrations.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/registration-and-invitation-codes-plugin-for-wordpress/',
			'link_buy' => 'https://www.cminds.com/?edd_action=add_to_cart&download_id=159093&edd_options[price_id]=1'
		),
	),
	'plugin-show-shortcodes'	 => TRUE,
	'plugin-shortcodes'			 => '<p>You can use the following available shortcodes.</p>',
	'plugin-shortcodes-action'	 => 'cmreg_display_available_shortcodes',
	'plugin-parent-abbrev'		 => '',
	'plugin-file'				 => App::getPluginFile(),
	'plugin-dir-path'			 => plugin_dir_path( App::getPluginFile() ),
	'plugin-dir-url'			 => plugin_dir_url( App::getPluginFile() ),
	'plugin-basename'			 => plugin_basename( App::getPluginFile() ),
	'plugin-icon'				 => '',
	'plugin-name'				 => App::getPluginName(true),
	'plugin-license-name'		 => App::getPluginName(true),
	'plugin-slug'				 => App::PREFIX,
	'plugin-short-slug'			 => App::PREFIX,
	'plugin-parent-short-slug'	 => '',
	'plugin-menu-item'			 => App::SLUG,
	'plugin-textdomain'			 => '',
	'plugin-userguide-key'		 => '637-cm-registration',
	'plugin-store-url'			 => 'https://www.cminds.com/wordpress-plugins-library/cm-registration-and-invitation-codes-plugin-for-wordpress/',
	'plugin-support-url'		 => 'https://wordpress.org/support/plugin/cm-registration',
	'plugin-review-url'			 => 'http://wordpress.org/support/view/plugin-reviews/cm-registration',
	'plugin-changelog-url'		 => 'https://www.cminds.com/wordpress-plugins-library/cm-registration-and-invitation-codes-plugin-for-wordpress/#changelog',
	'plugin-licensing-aliases'	 => App::getLicenseAdditionalNames(),
   'plugin-compare-table'       => '
            <div class="suite-package" style="padding-left:10px;"><h2>The premium version of this plugin is included in CreativeMinds All plugins suite:</h2><a href="https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/" target="_blank"><img src="'.plugin_dir_url( __FILE__ ).'CMWPPluginssuite.png"></a></div>
            <hr style="width:1000px; height:3px;">
            <div class="pricing-table" id="pricing-table"><h2 style="padding-left:10px;">Upgrade The Registration Plugin:</h2>
                <ul>
                    <li class="heading" style="background-color:black;">Current Edition</li>
                    <li class="price">FREE<br /></li>
                   <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Login and Registration PopUp</li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Ajax login or registration</li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Stay on the same page after login</li>
                 </ul>

                <ul>
                    <li class="heading">Pro<a href="https://www.cminds.com/wordpress-plugins-library/cm-registration-and-invitation-codes-plugin-for-wordpress/" style="float:right;font-size:11px;color:white;" target="_blank">More</a></li>
                    <li class="price">$29.00<br /> <span style="font-size:14px;">(For one Year / Site)<br />Additional pricing options available <a href="https://www.cminds.com/wordpress-plugins-library/cm-registration-and-invitation-codes-plugin-for-wordpress/" target="_blank"> >>> </a></span> <br /></li>
                    <li class="action"><a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=88183&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=1" style="font-size:18px;" target="_blank">Upgrade Now</a></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>All Free Version Features <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All free features are supported in the pro"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Invitation codes support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Admin can define multiple invitation codes and limit registration only to users who have an invite. Invitation codes can include additional registration configuration information and restrictions"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Email verification <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Restrict registration only to users who verify their email address"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Email templates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Edit email template sent to users once they register"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>reCaptcha support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support adding reCaptcha to the registration form"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Change any label easily <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All plugin front-end labels can be easily customizes from the plugin settings"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Remove users who did not verify email <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Automatically delete users who did not verify their email address"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Set user role <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support setting user role based on the invitation code used"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Shortcode support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Use shortcodes to display login, registration and forget email forms"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Add/Edit registration fields <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Add additional registration fields to the user profile with a registration form builder"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Multiple registration forms <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Offer a unique registration form for each user role"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Export All User Data To CSV <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Export all user meta information to a CSV file. This includes also additional fields added with the registration form builder"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Edit user profile after registration <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Shortcode support to edit user profile information"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Facebook registration <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support registration and login with a Facebook user"></span></li>
                     <li class="support" style="background-color:lightgreen; text-align:left; font-size:14px;"><span class="dashicons dashicons-yes"></span> One year of expert support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="You receive 365 days of a WordPress expert support. We will answer questions you have and also support any issue related to the plugin. We also provide on site support."></span><br />
                        <span class="dashicons dashicons-yes"></span> Unlimited product updates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="During the year, you can update the plugin as many times as needed and receive any new release and security update"></span><br />
                        <span class="dashicons dashicons-yes"></span> Plugin can be used forever <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose not to renew the plugin license, you can still continue to use a long as you want."></span><br />
                        <span class="dashicons dashicons-yes"></span> Save 35% once renewing license <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose to renew the plugin license you can do this anytime you choose. The renewal cost will be 35% off the product cost."></span></li>
                 </ul>
               
              <ul>
                    <li class="heading">Deluxe<a href="https://www.cminds.com/wordpress-plugins-library/cm-registration-and-invitation-codes-plugin-for-wordpress/" style="float:right;font-size:11px;color:white;" target="_blank">More</a></li>
                    <li class="price">$59.00<br /> <span style="font-size:14px;">(For one Year / Site)<br />Additional pricing options available <a href="https://www.cminds.com/wordpress-plugins-library/cm-registration-and-invitation-codes-plugin-for-wordpress/" target="_blank"> >>> </a></span> <br /></li>
                    <li class="action"><a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=184080&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=1" style="font-size:18px;" target="_blank">Upgrade Now</a></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>All Free and Pro Versions Features <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All features includes in the Free and Pro versions  are supported in the the Deluxe version"></span></li>
                       <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Payment Support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Charge users for registering to your site. The cart system being used is Easy Digital Downloads"></span></li>
                     <li class="support" style="background-color:lightgreen; text-align:left; font-size:14px;"><span class="dashicons dashicons-yes"></span> One year of expert support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="You receive 365 days of a WordPress expert support. We will answer questions you have and also support any issue related to the plugin. We also provide on site support."></span><br />
                        <span class="dashicons dashicons-yes"></span> Unlimited product updates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="During the year, you can update the plugin as many times as needed and receive any new release and security update"></span><br />
                        <span class="dashicons dashicons-yes"></span> Plugin can be used forever <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose not to renew the plugin license, you can still continue to use a long as you want."></span><br />
                        <span class="dashicons dashicons-yes"></span> Save 35% once renewing license <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose to renew the plugin license you can do this anytime you choose. The renewal cost will be 35% off the product cost."></span></li>
                 </ul> 
                <ul>
                    <li class="heading">Ultimate<a href="https://www.cminds.com/wordpress-plugins-library/cm-registration-and-invitation-codes-plugin-for-wordpress/" style="float:right;font-size:11px;color:white;" target="_blank">More</a></li>
                    <li class="price">$99.00<br /> <span style="font-size:14px;">(For one Year / Site)<br />Additional pricing options available <a href="https://www.cminds.com/wordpress-plugins-library/cm-registration-and-invitation-codes-plugin-for-wordpress/" target="_blank"> >>> </a></span> <br /></li>
                    <li class="action"><a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=184081&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=1" style="font-size:18px;" target="_blank">Upgrade Now</a></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>All Free, Pro and Deluxe Versions Features <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All features includes in the Free, Pro and Deluxe versions are supported in the the Ultimate version"></span></li>
                       <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Approve users after registration <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Addon: Support manually approving new users who registered to your site. Admin receive a notification whenever a new user registers and can approve or remove user"></span></li>
                       <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Send in bulk email invitations <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Addon: Support sending to a list of user emails an invitation code. You can also generate a list of invitation codes without an email. Each invitation code can have it own rules"></span></li>
                       <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Restrict access to content <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Plugin: Additional plugin that support restricting access to specific pages or custom post on your site using user role. You can also restrict content based on URL. Complete site or specific pages can be blocked to non logged in users."></span></li>
                     <li class="support" style="background-color:lightgreen; text-align:left; font-size:14px;"><span class="dashicons dashicons-yes"></span> One year of expert support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="You receive 365 days of a WordPress expert support. We will answer questions you have and also support any issue related to the plugin. We also provide on site support."></span><br />
                        <span class="dashicons dashicons-yes"></span> Unlimited product updates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="During the year, you can update the plugin as many times as needed and receive any new release and security update"></span><br />
                        <span class="dashicons dashicons-yes"></span> Plugin can be used forever <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose not to renew the plugin license, you can still continue to use a long as you want."></span><br />
                        <span class="dashicons dashicons-yes"></span> Save 35% once renewing license <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="If you choose to renew the plugin license you can do this anytime you choose. The renewal cost will be 35% off the product cost."></span></li>
                     <li class="heading" style="background-color:orange">BEST VALUE</li>
                </ul> 


            </div>',
);

