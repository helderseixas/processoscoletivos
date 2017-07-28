<?php
/*
Plugin Name: WP Content Filter
Plugin URI: http://wordpress.org/plugins/wp-content-filter/
Description: Censor and filter out profanity, swearing, abusive comments and any other keywords from your site.
Version: 2.7
Author: David Gwyer
Author URI: http://www.wpgoplugins.com
*/

/*  Copyright 2017 David Gwyer (email : david@wpgoplugins.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// pccf_ prefix is derived from [p]ress [c]oders [c]ontent [f]ilter
register_activation_hook( __FILE__, 'pccf_add_defaults' );
register_uninstall_hook( __FILE__, 'pccf_delete_plugin_options' );
add_action( 'admin_init', 'pccf_init' );
add_action( 'admin_menu', 'pccf_add_options_page' );
add_action( 'plugins_loaded', 'pccf_contfilt' );
add_filter( 'plugin_row_meta', 'pccf_plugin_action_links', 10, 2 );
add_filter( 'plugin_action_links', 'pccf_plugin_settings_link', 10, 2 );
add_action( 'admin_enqueue_scripts', 'pccf_register_admin_scripts' );
add_action( 'admin_notices', 'pccf_admin_notice' );
register_activation_hook( __FILE__, 'pccf_admin_notice_set_transient' );

/* Runs only when the plugin is activated. */
function pccf_admin_notice_set_transient() {

	/* Create transient data */
	set_transient( 'pccf-admin-notice', true, 5 );
}

/* Admin Notice on Activation. */
function pccf_admin_notice(){

	/* Check transient, if available display notice */
	if( get_transient( 'pccf-admin-notice' ) ){
		?>
		<div class="updated notice is-dismissible">
			<p><a href="https://wpgoplugins.com/plugins/content-censor/" target="_blank"><strong>WP Content Filter PRO</strong></a> is now available! Access great new features such as the batch processor to actively scan your entire site. *NEW* Filter BuddyPress content now too! <b>Try risk free today with our 100% money back guarantee! <span class="dashicons dashicons-smiley"></span></b></p>
		</div>
		<?php
		/* Delete transient, only display this notice once. */
		delete_transient( 'pccf-admin-notice' );
	}
}

// Setup Plugin default options
global $pccf_defaults;
$pccf_defaults = array(
	"chk_post_title"         => "1",
	"chk_post_content"       => "1",
	"chk_comments"           => "1",
	"txtar_keywords"         => "Saturn, Jupiter, Pluto",
	"txt_exclude"            => "",
	"rdo_word"               => "all",
	"drp_filter_char"        => "star",
	"rdo_case"               => "insen",
	"chk_default_options_db" => "",
	"rdo_strict_filtering"   => "strict_on"
);

// delete options table entries ONLY when plugin deactivated AND deleted
function pccf_delete_plugin_options() {
	delete_option( 'pccf_options' );
}

// Define default option settings
function pccf_add_defaults() {

	global $pccf_defaults;
	$tmp = get_option( 'pccf_options', $pccf_defaults );

	if ( ( ( isset( $tmp['chk_default_options_db'] ) && $tmp['chk_default_options_db'] == '1' ) ) || ( ! is_array( $tmp ) ) ) {
		update_option( 'pccf_options', $pccf_defaults );
	}
}

// Init plugin options to white list our options
function pccf_init() {
	// put the below into a function and add checks all sections (especially radio buttons) have a valid choice (i.e. no section is blank)
	// this is primarily to check newly added options have correct initial values
	global $pccf_defaults;
	$tmp = get_option( 'pccf_options', $pccf_defaults );

	if ( ! $tmp['rdo_strict_filtering'] ) { // check strict filtering option has a starting value
		$tmp["rdo_strict_filtering"] = "strict_off";
		update_option( 'pccf_options', $tmp );
	}
	register_setting( 'pccf_plugin_options', 'pccf_options', 'pccf_validate_options' );
}

// Add menu page
function pccf_add_options_page() {

	$page = add_options_page( 'WP Content Filter Options Page', 'WP Content Filter', 'manage_options', __FILE__, 'pccf_render_form' );

	/* Enqueue scripts and styles for the theme option page */
	add_action( "admin_print_styles-$page", 'pccf_theme_admin_styles' );
	//add_action( "admin_print_scripts-$page", 'pccf_theme_admin_scripts' );
}

// Enqueue styles for theme options page.
function pccf_theme_admin_styles() {

	/* Styles for plugin options page only. */
	wp_enqueue_style( 'pccf-admin-css' );
}

// Draw the menu page itself
function pccf_render_form() {
	?>
	<style>
		a:focus{ box-shadow: none;}
		.pcdm.dashicons { width: 32px; height: 32px; font-size: 32px; }
		.pcdm.dashicons-yes { color: #1cc31c; }
		.pcdm.dashicons-no { color: red; }
	</style>
	<div class="wrap">
		<h2>WP Content Filter Options</h2>

		<div class="notice" style="border: 2px #32cd32 solid;margin: 20px 0;">
			<p>Upgrade to the Pro version and access great new features, such as the batch processor to actively scan your entire site. *NEW* Filter BuddyPress content now too! Try risk free today with our 100% money back guarantee!</span></p>
			<div style="display:flex;">
				<div style="margin:15px 0;"><iframe width="350" height="197" src="https://www.youtube.com/embed/fBtf4QEhP2w" frameborder="0" allowfullscreen></iframe></div>
				<div style="margin-left: 15px;">
					<p><b>New feature in <a style="color:#32cd32;" href="https://wpgoplugins.com/plugins/content-censor/" target="_blank">Content Censor 0.4</a> <small>(13th March, 2017)</small> ignores banned keywords in HTML tags and attributes!</b></p>
					<p>Occasionally you might have a keyword that is used as the source URL for an image or as part of a HTML link. Normally this will be censored too, breaking the link. But in <a style="color:#32cd32;" href="https://wpgoplugins.com/plugins/content-censor/" target="_blank">Content Censor 0.4</a> you can now optionally choose to ignore HTML tags and all their attributes.</p>
				</div>
			</div>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields( 'pccf_plugin_options' ); ?>
			<?php
			global $pccf_defaults;
			$options = get_option( 'pccf_options', $pccf_defaults );
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Keywords to Remove</th>
					<td>
						<textarea name="pccf_options[txtar_keywords]" rows="7" cols="50" type='textarea'><?php echo $options['txtar_keywords']; ?></textarea>

						<p class="description">Separate keywords with commas.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Exclude Pages</th>
					<td>
						<input type="text" class="regular-text code" name="pccf_options[txt_exclude]" value="<?php echo $options['txt_exclude']; ?>">

						<p class="description">Enter a comma separate list of page ID's that will be excluded from the content filter.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Strict Filtering</th>
					<td>
						<label><input name="pccf_options[rdo_strict_filtering]" type="radio" value="strict_off" <?php checked( 'strict_off', $options['rdo_strict_filtering'] ); ?>> Strict Filtering OFF
							<span style="font-family:lucida console;color:#888;margin-left:119px;">[e.g. 'ass' becomes 'p***able']</span></label><br>
						<label><input name="pccf_options[rdo_strict_filtering]" type="radio" value="strict_on" <?php checked( 'strict_on', $options['rdo_strict_filtering'] ); ?>> Strict Filtering ON (recommended)
							<span style="font-family:lucida console;color:#888;margin-left:32px;">[e.g. 'ass' becomes 'passable']</span></label>

						<p class="description">Note: When strict filtering is ON, embedded keywords are no longer filtered.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Filter main content</th>
					<td>
						<label><input name="pccf_options[chk_post_content]" type="checkbox" value="1" <?php if ( isset( $options['chk_post_content'] ) ) {
								checked( '1', $options['chk_post_content'] );
							} ?>> Post Content/Excerpt<?php if ( class_exists( 'bbPress' ) ) {
								echo " (including bbPress content)";
							} ?></label><br>
						<label><input name="pccf_options[chk_post_title]" type="checkbox" value="1" <?php if ( isset( $options['chk_post_title'] ) ) {
								checked( '1', $options['chk_post_title'] );
							} ?>> Post Titles<?php if ( class_exists( 'bbPress' ) ) {
								echo " (including bbPress titles)";
							} ?></label><br>
						<label><input name="pccf_options[chk_comments]" type="checkbox" value="1" <?php if ( isset( $options['chk_comments'] ) ) {
								checked( '1', $options['chk_comments'] );
							} ?>> Comments <span class="description">(filters comment author names too)</span></label><br>
						<label><input name="pccf_options[chk_tags]" type="checkbox" value="1" <?php if ( isset( $options['chk_tags'] ) ) {
								checked( '1', $options['chk_tags'] );
							} ?>> Tags</label><br>
						<label><input name="pccf_options[chk_tag_cloud]" type="checkbox" value="1" <?php if ( isset( $options['chk_tag_cloud'] ) ) {
								checked( '1', $options['chk_tag_cloud'] );
							} ?>> Tag Cloud</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Word Rendering</th>
					<td>
						<label><input name="pccf_options[rdo_word]" type="radio" value="first" <?php checked( 'first', $options['rdo_word'] ); ?>> First letter retained
							<span style="font-family:lucida console;color:#888;margin-left:39px;">[e.g. 'dog' becomes 'd**']</span></label><br>
						<label><input name="pccf_options[rdo_word]" type="radio" value="all" <?php checked( 'all', $options['rdo_word'] ); ?>> All letters removed
							<span style="font-family:lucida console;color:#888;margin-left:40px;">[e.g. 'dog' becomes '***']</span></label><br>
						<label><input name="pccf_options[rdo_word]" type="radio" value="firstlast" <?php checked( 'firstlast', $options['rdo_word'] ); ?>> First/last letter retained
							<span style="font-family:lucida console;color:#888;margin-left:16px;">[e.g. 'dog' becomes 'd*g']</span></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Filter Character</th>
					<td>
						<select name='pccf_options[drp_filter_char]'>
							<option value='star' <?php selected( 'star', $options['drp_filter_char'] ); ?>>[*] Asterisk</option>
							<option value='dollar' <?php selected( 'dollar', $options['drp_filter_char'] ); ?>>[$] Dollar</option>
							<option value='question' <?php selected( 'question', $options['drp_filter_char'] ); ?>>[?] Question</option>
							<option value='exclamation' <?php selected( 'exclamation', $options['drp_filter_char'] ); ?>>[!] Exclamation</option>
							<option value='hyphen' <?php selected( 'hyphen', $options['drp_filter_char'] ); ?>>[-] Hyphen</option>
							<option value='hash' <?php selected( 'hash', $options['drp_filter_char'] ); ?>>[#] Hash</option>
							<option value='tilde' <?php selected( 'tilde', $options['drp_filter_char'] ); ?>>[~] Tilde</option>
							<option value='blank' <?php selected( 'blank', $options['drp_filter_char'] ); ?>>[ ] Blank</option>
						</select>

						<p class="description">'Blank' completely removes the filtered keywords from view.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Case Matching</th>
					<td>
						<label><input name="pccf_options[rdo_case]" type="radio" value="sen" <?php checked( 'sen', $options['rdo_case'] ); ?>> Case Sensitive</label><br>
						<label><input name="pccf_options[rdo_case]" type="radio" value="insen" <?php checked( 'insen', $options['rdo_case'] ); ?>> Case Insensitive (recommended)</label>

						<p class="description">Note: 'Case Insensitive' matching type is better as it captures more words!</p>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2">
						<div style="margin-top:10px;"></div>
					</td>
				</tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="pccf_options[chk_default_options_db]" type="checkbox" value="1" <?php if ( isset( $options['chk_default_options_db'] ) ) {
								checked( '1', $options['chk_default_options_db'] );
							} ?>> Restore defaults upon plugin deactivation/reactivation</label>

						<p class="description">Only check this if you want to reset plugin settings upon reactivation.</p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>">
			</p>
		</form>

		<div style="margin-top:15px;">
			<p style="margin-bottom:10px;">If you use this FREE Plugin on your website <b><em>please</em></b> consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BG4RPM3LLP5GU" target="_blank">donation</a> to support continued development. Thank you.<span style="margin-left:5px;" class="dashicons dashicons-smiley"></span></p>
		</div>

		<div style="clear:both;">
			<span><a href="http://www.twitter.com/dgwyer" title="Follow us on Twitter" target="_blank"><img src="<?php echo plugins_url(); ?>/wp-content-filter/images/twitter.png" /></a></span>
			<span><a href="https://www.facebook.com/wpgoplugins/" title="Our Facebook page" target="_blank"><img src="<?php echo plugins_url(); ?>/wp-content-filter/images/facebook.png" /></a></span>
			<span><a href="https://www.youtube.com/channel/UCWzjTLWoyMgtIfpDgJavrTg" title="View our YouTube channel" target="_blank"><img src="<?php echo plugins_url(); ?>/wp-content-filter/images/yt.png" /></a></span>
			<span><a style="text-decoration:none;" title="Need help with ANY aspect of WordPress? We're here to help!" href="https://wpgoplugins.com/need-help-with-wordpress/" target="_blank"><span style="margin-left:-2px;color:#d41515;font-size:39px;line-height:32px;width:39px;height:39px;" class="dashicons dashicons-sos"></span></a></span>
			<span style="margin-left:20px;"><input class="button" style="vertical-align:12px;" type="button" value="Visit Our Site" onClick="window.open('http://www.wpgoplugins.com')"></span>
			<span style="margin-left:3px;"><input class="button" style="vertical-align:12px;" type="button" value="Subscribe (free)" title="Signup today for all the latest plugin news and updates!" onClick="window.open('http://eepurl.com/bXZmmD')"></span>
		</div>

	</div>
<?php
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function pccf_validate_options( $input ) {
	// strip html from textboxes
	$input['txtar_keywords'] = wp_filter_nohtml_kses( $input['txtar_keywords'] );
	$input['txt_exclude']    = wp_filter_nohtml_kses( $input['txt_exclude'] );

	return $input;
}

function pccf_contfilt() {

	if ( is_admin() ) {
		return;
	} /* Only filter front end content. */

	global $pccf_defaults;
	$tmp = get_option( 'pccf_options', $pccf_defaults );

	if ( isset( $tmp['chk_post_content'] ) ) {
		if ( $tmp['chk_post_content'] == '1' ) {
			add_filter( 'the_content', 'pccf_filter' );
			add_filter( 'get_the_excerpt', 'pccf_filter' );
		}

		/* bbPress specific filtering (only if bbPress is present). */
		if ( class_exists( 'bbPress' ) ) {
			add_filter( 'bbp_get_topic_content', 'pccf_filter' );
			add_filter( 'bbp_get_reply_content', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_post_title'] ) ) {
		if ( $tmp['chk_post_title'] == '1' ) {
			add_filter( 'the_title', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_comments'] ) ) {
		if ( $tmp['chk_comments'] == '1' ) {
			add_filter( 'comment_text', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_comments'] ) ) {
		if ( $tmp['chk_comments'] == '1' ) {
			add_filter( 'get_comment_author', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_tags'] ) ) {
		if ( $tmp['chk_tags'] == '1' ) {
			add_filter( 'term_links-post_tag', 'pccf_filter' );
		}
	}
	if ( isset( $tmp['chk_cloud'] ) ) {
		if ( $tmp['chk_cloud'] == '1' ) {
			add_filter( 'wp_tag_cloud', 'pccf_filter' );
		}
	}
}

function pccf_filter( $text ) {

	global $post;

	// get comma separated list of page ID's to exclude
	global $pccf_defaults;
	$tmp = get_option( 'pccf_options', $pccf_defaults );

	$exclude_id_list  = $tmp['txt_exclude'];
	$exclude_id_array = explode( ', ', $exclude_id_list );

	// if current page ID is in exclude list then don't filter
	if ( isset( $post ) && in_array( $post->ID, $exclude_id_array ) ) {
		return $text;
	}

	$wildcard_filter_type = $tmp['rdo_word'];
	$wildcard_char        = $tmp['drp_filter_char'];

	if ( $wildcard_char == 'star' ) {
		$wildcard = '*';
	} else {
		if ( $wildcard_char == 'dollar' ) {
			$wildcard = '$';
		} else {
			if ( $wildcard_char == 'question' ) {
				$wildcard = '?';
			} else {
				if ( $wildcard_char == 'exclamation' ) {
					$wildcard = '!';
				} else {
					if ( $wildcard_char == 'hyphen' ) {
						$wildcard = '-';
					} else {
						if ( $wildcard_char == 'hash' ) {
							$wildcard = '#';
						} else {
							if ( $wildcard_char == 'tilde' ) {
								$wildcard = '~';
							} else {
								$wildcard = '';
							}
						}
					}
				}
			}
		}
	}

	$filter_type = $tmp['rdo_case'];
	$db_search_string = $tmp['txtar_keywords'];
	$keywords = array_map( 'trim', explode( ',', $db_search_string ) ); // explode and trim whitespace
	$keywords = array_unique( $keywords ); // get rid of duplicates in the keywords textbox
	$whole_word = $tmp['rdo_strict_filtering'] == 'strict_off' ? false : true;

	foreach ( $keywords as $keyword ) {
		$keyword = trim( $keyword ); // remove whitespace chars from start/end of string
		if ( strlen( $keyword ) > 2 ) {
			$replacement = censor_word($wildcard_filter_type, $keyword, $wildcard);
			if ( $filter_type == "insen" ) {
				$text = str_replace_word_i( $keyword, $replacement, $text, $wildcard_filter_type, $keyword, $wildcard, $whole_word );
			} else {
				$text = str_replace_word( $keyword, $replacement, $text, $whole_word );
			}
		}
	}

	return $text;
}

function censor_word( $wildcard_filter_type, $keyword, $wildcard ) {

	if ( $wildcard_filter_type == 'first' ) {
		$keyword = substr( $keyword, 0, 1 ) . str_repeat( $wildcard, strlen( substr( $keyword, 1 ) ) );
	} else {
		if ( $wildcard_filter_type == 'all' ) {
			$keyword = str_repeat( $wildcard, strlen( substr( $keyword, 0 ) ) );
		} else {
			$keyword = substr( $keyword, 0, 1 ) . str_repeat( $wildcard, strlen( substr( $keyword, 2 ) ) ) . substr( $keyword, - 1, 1 );
		}
	}

	return $keyword;
}

// case insensitive
function str_replace_word_i( $needle, $replacement, $haystack, $wildcard_filter_type, $keyword, $wildcard, $whole_word = true ) {

	$needle   = str_replace( '/', '\\/', preg_quote( $needle ) ); // allow '/' in keywords
	$pattern  = $whole_word ? "/\b$needle\b/i" : "/$needle/i";
	$haystack = preg_replace_callback(
		$pattern,
		function($m) use($wildcard_filter_type, $keyword, $wildcard) {
			return censor_word( $wildcard_filter_type, $m[0], $wildcard );
		},
		$haystack);

	return $haystack;
}

// case sensitive
function str_replace_word( $needle, $replacement, $haystack, $whole_word = true ) {
	$needle   = str_replace( '/', '\\/', preg_quote( $needle ) ); // allow '/' in keywords
	$pattern  = $whole_word ? "/\b$needle\b/" : "/$needle/";
	$haystack = preg_replace( $pattern, $replacement, $haystack );

	return $haystack;
}

function pccf_plugin_action_links( $links, $file ) {

	//if ( $file == plugin_basename( __FILE__ ) ) {
		// add a link to pro plugin
		//$links[] = '<a style="color:red;" href="https://wpgoplugins.com/plugins/content-censor/" target="_blank" title="Try out the Pro version today. Risk FREE - 100% money back guarantee!"><span class="dashicons dashicons-awards"></span></a>';
	//}

	return $links;
}

function pccf_plugin_settings_link( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$pccf_links = '<a style="color:limegreen;" href="https://wpgoplugins.com/plugins/content-censor/" target="_blank" title="Upgrade to Pro - 100% money back guarantee"><span class="dashicons dashicons-awards"></span></a> | ';
		$pccf_links .= '<a href="' . get_admin_url() . 'options-general.php?page=wp-content-filter/wp-content-filter.php">' . __( 'Settings' ) . '</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $pccf_links );
	}

	return $links;
}

// Register admin scripts and styles to be enqueued on the theme options page
function pccf_register_admin_scripts() {

	// Register theme options page style sheets
	wp_register_style( 'pccf-admin-css', plugins_url().'/wp-content-filter/css/pccf-admin.css' );
}