<?php
/*
Plugin Name: bbPress - Moderation Tools
Description: Extends the basic bbPress moderation tools to give you more control over your Forum.
Author: Digital Arm
Version: 1.0.1
Author URI: https://www.digitalarm.co.uk
*/

if ( !defined( 'ABSPATH' ) ) exit;

class bbPressModTools {

	/**
	 * Plugin slug
	 * @var string
	 */
	protected $slug = 'moderation-tools-bbpress';

	private $version = '0.1.3';

	/**
	 * Set filters and actions
	 * @since  0.1.0
	 */
	function __construct() {

		// Moderate the post and mark as awaiting approval
		add_filter( 'bbp_new_topic_pre_insert', array( $this, 'moderate_post' ) );
		add_filter( 'bbp_new_reply_pre_insert', array( $this, 'moderate_post' ) );

		// Redirect anon users back to parent forum
		add_filter( 'bbp_new_topic_redirect_to', array( $this, 'redirect_pending_anon' ), 20, 3 );
		add_filter( 'bbp_new_reply_redirect_to', array( $this, 'redirect_pending_anon' ), 20, 3 );

		// Include pending posts into replies and topic lists
		add_filter( 'bbp_has_topics_query', array( $this, 'pending_query' ) );
		add_filter( 'bbp_has_replies_query', array( $this, 'pending_query' ) );

		// Intercept the content and show awaiting moderation message
		add_filter( 'bbp_get_reply_content', array( $this, 'moderate_content' ), 10, 2 );

		// Intercept the admin bar and add approve post
		add_filter( 'bbp_get_topic_admin_links', array( $this, 'admin_links'), 10, 3 );
		add_filter( 'bbp_get_reply_admin_links', array( $this, 'admin_links'), 10, 3 );

		// Add blocked link to user details and profile
		add_action( 'bbp_theme_after_reply_author_details', array( $this, 'user_admin_links' ), 10 );
		add_action( 'bbp_template_before_user_profile', array( $this, 'user_admin_links' ), 10 );

		// Do additional actions on approval of topics
		add_action( 'bbp_approved_topic', array( $this, 'moderation_approve_action' ) );
		add_action( 'bbp_approved_reply', array( $this, 'moderation_approve_action' ) );

		// Intercept the post title and add awaiting moderation notice
		add_filter( 'bbp_get_topic_title', array( $this, 'pending_title' ), 10, 2 );

		// Notify Admin when a new topic or reply is posted that needs moderating.
		add_action( 'bbp_new_topic', array( $this, 'new_topic'), 10, 1 );
		add_action( 'bbp_new_reply', array( $this, 'new_reply'), 10, 1 );

		// Disable ability to reply if topic is awaiting moderation.
		add_filter( 'bbp_current_user_can_publish_replies', array( $this, 'pending_topic_reply_check' ) );

		// Enqueue Scripts and CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Trigger action processing
		add_action( 'wp', array( $this, 'process_moderate_actions' ) );

		// Add admin pending notification counter to topics and replies
		add_action( 'admin_init', array( $this, 'admin_pending_counter' ) );

		// Show a notice to users
		add_action( 'bbp_template_before_single_forum', array( $this, 'anon_pending_notice' ) );
		add_action( 'bbp_template_before_single_topic', array( $this, 'anon_pending_notice' ) );

		// Add settings to Settings > Forums page
		add_filter( 'bbp_admin_get_settings_sections', array( $this, 'add_settings_section' ) );
		add_filter( 'bbp_admin_get_settings_fields', array( $this, 'add_settings_fields' ) );
		add_filter( 'bbp_map_settings_meta_caps', array( $this, 'set_settings_section_cap' ), 10, 4 );

		remove_action( 'bbp_template_redirect', 'bbp_forum_enforce_blocked', 1  );
		add_action( 'bbp_template_redirect', array( $this, 'bbp_forum_enforce_blocked' ), 1  );

		$this->_update();

	}

	/**
	 * perform any updates needed where database changes have happened
	 * @since  0.2.0
	 */
	private function _update() {

		$current_version = get_option( $this->slug . '-version' );

		if( version_compare( $current_version, '0.1.3' ) == -1 ) {

			// Change moderation options
			$moderation_type = get_option( '_bbp_moderation_type' );

			if( 'links' == $moderation_type || 'users' == $moderation_type ) {

				update_option( '_bbp_moderation_type', 'custom' );
				update_option( '_bbp_moderation_custom', array( $moderation_type ) );

			}

		}

		update_option( $this->slug . '-version', $this->version );

	}


	/**
	 * Enqueue Javascript
	 * @since  0.1.0
	 */
	function wp_enqueue_scripts() {

		wp_enqueue_style( $this->slug . '-css', plugin_dir_url( __FILE__ ) . '/css/bbp-moderation-tools.css' );

	}

	/**
	 * Enqueue CSS
	 * @since  0.2.0
	 */
	function admin_enqueue_scripts() {

		wp_enqueue_style( $this->slug . '-css', plugin_dir_url( __FILE__ ) . '/css/bbp-moderation-tools-admin.css' );

	}


	/**
	 * Add settings section to Settings > Forums page
	 * @since  0.1.0
	 * @param array $sections
	 */
	public function add_settings_section( $sections ) {

		$sections['bbp_settings_moderation_options'] = array(
			'title'    => __( 'Moderation Options', $this->slug ),
			'callback' => array( $this, 'settings_moderation_options_section_header' ),
			'page'     => 'bbpress',
		);

		$sections['bbp_settings_moderation_notifications'] = array(
			'title'    => __( 'Moderation Notifcations', $this->slug ),
			'callback' => array( $this, 'settings_moderation_notifications_section_header' ),
			'page'     => 'bbpress',
		);

		$sections['bbp_settings_moderation_user_settings'] = array(
			'title'    => __( 'User moderation settings', $this->slug ),
			'callback' => array( $this, 'settings_moderation_user_section_header' ),
			'page'     => 'bbpress',
		);

		return $sections;

	}


	/**
	 * Add moderation options section header
	 * @since  0.1.0
	 *
	 */
	public function settings_moderation_options_section_header(){

		_e( 'How you want to moderate forum posts. Unapproved users means those who don\'t have a previously approved post.', $this->slug );

	}


	/**
	 * Add moderation notifications section header
	 * @since  0.1.0
	 *
	 */
	public function settings_moderation_notifications_section_header(){

		_e( 'Who should be notified when posts are held for moderation.', $this->slug );

	}


	/**
	 * Add moderation user section header
	 * @since  0.1.0
	 *
	 */
	public function settings_moderation_user_section_header(){

		_e( 'Moderating users.', $this->slug );

	}



	/**
	 * Adds settings fields to the bbPress settings page
	 *
	 * @param array $settings
	 * @since  0.1.0
	 * @since  0.2.0 added _bbp_moderation_custom and _bbp_moderation_english_threshold options. Added _bbp_blocked_page option
	 *
	 * @return array
	 */
	public function add_settings_fields( $settings ) {

		$settings['bbp_settings_users']['_bbp_blocked_page_id'] = array(
			'title'				=> __( 'Redirect blocked users', $this->slug ),
			'callback'			=> array( $this, 'bbp_admin_setting_callback_blocked_users' ),
			'sanitize_callback'	=> 'intval',
			'args'				=> array(),
		);

		$settings['bbp_settings_moderation_options'] = array(

			// Moderation type
			'_bbp_moderation_type' => array(
				'title'             => __( 'Hold for Moderation', $this->slug ),
				'callback'          => array( $this, 'bbp_admin_setting_callback_moderation_type' ),
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'_bbp_moderation_custom' => array(
			),
			'_bbp_moderation_english_threshold' => array(
				'sanitize_callback' => 'intval',
			),

		);

		$settings['bbp_settings_moderation_notifications'] = array(

			// Notify moderators
			'_bbp_notify_moderator' => array(
				'title'				=> __( 'Notify Moderators', $this->slug ),
				'callback'			=> array( $this, 'bbp_admin_setting_callback_notify_moderator' ),
				'sanitize_callback' => 'intval',
				'args'				=> array(),
			),

			// Notify keymasters
			'_bbp_notify_keymaster' => array(
				'title'				=> __( 'Notify Keymasters', $this->slug ),
				'callback'			=> array( $this, 'bbp_admin_setting_callback_notify_keymaster' ),
				'sanitize_callback' => 'intval',
				'args'				=> array(),
			),

			// Notify custom email addresses
			'_bbp_notify_email' => array(
				'title'				=> __( 'Notify Custom Emails', $this->slug ),
				'callback'			=> array( $this, 'bbp_admin_setting_callback_notify_custom' ),
				'sanitize_callback' => 'sanitize_text_field',
				'args'				=> array(),
			),

		);

		return $settings;

	}


	/**
	 *  Settings field for moderation type
	 * @since  0.1.0
	 * @since  0.2.0 Added extra english detection option, expanded options to allow multiple rules
	 */
	public function bbp_admin_setting_callback_moderation_type() {
		$bbp_moderation_type_value = get_option( '_bbp_moderation_type' );
		$bbp_moderation_english_threshold = get_option( '_bbp_moderation_english_threshold' );
		?>
		<div>
			<p>
				<input type="radio" id="_bbp_moderation_type_off" name="_bbp_moderation_type" value="off" <?php if ( $bbp_moderation_type_value == 'off' or ! $bbp_moderation_type_value ): echo 'checked'; endif; ?>>
				<label for="_bbp_moderation_type_off"><?php _e('None', $this->slug ); ?></label>
			</p>
		</div>
		<div>
			<p>
				<input type="radio" id="_bbp_moderation_type_custom" name="_bbp_moderation_type" value="custom" <?php if ( $bbp_moderation_type_value == 'custom' ): echo 'checked'; endif; ?>>
				<label for="_bbp_moderation_type_custom"><?php _e('Custom', $this->slug ); ?></label>
			</p>
			<?php $moderation_custom = get_option( '_bbp_moderation_custom' ) ?>
			<div class="bbp_moderation_custom_option">
				<p>
					<input type="checkbox" id="_bbp_moderation_type_users" name="_bbp_moderation_custom[]" value="users" <?php if ( is_array( $moderation_custom ) && in_array( 'users', $moderation_custom ) ): echo 'checked'; endif; ?>>
					<label for="_bbp_moderation_type_users"><?php _e('Unapproved users posting', $this->slug ); ?></label>
				</p>
			</div>
			<div class="bbp_moderation_custom_option">
				<p>
					<input type="checkbox" id="_bbp_moderation_type_links" name="_bbp_moderation_custom[]" value="links" <?php if ( is_array( $moderation_custom ) && in_array( 'links', $moderation_custom ) ): echo 'checked'; endif; ?>>
					<label for="_bbp_moderation_type_links"><?php _e('Unapproved users posting links', $this->slug ); ?></label>
				</p>
			</div>
			<div class="bbp_moderation_custom_option">
				<p>
					<input type="checkbox" id="_bbp_moderation_type_ascii_unapproved" name="_bbp_moderation_custom[]" value="ascii_unnaproved" <?php if ( is_array( $moderation_custom ) && in_array( 'ascii_unnaproved', $moderation_custom ) ): echo 'checked'; endif; ?>>
					<label for="_bbp_moderation_type_ascii_unapproved"><?php _e('Unapproved users posting below the English character threshold', $this->slug ); ?></label>
				</p>
			</div>
			<div class="bbp_moderation_custom_option">
				<p>
					<input type="checkbox" id="_bbp_moderation_type_ascii" name="_bbp_moderation_custom[]" value="ascii" <?php if ( is_array( $moderation_custom ) && in_array( 'ascii', $moderation_custom ) ): echo 'checked'; endif; ?>>
					<label for="_bbp_moderation_type_ascii"><?php _e('All posts below the English character threshold', $this->slug ); ?></label>
				</p>
			</div>
			<div class="bbp_moderation_custom_option">
				<p>
					<label for="_bbp_moderation_english_threshold"><?php _e( 'English character threshold', $this->slug ) ?> </label>
					<input type="number" id="_bbp_moderation_english_threshold" name="_bbp_moderation_english_threshold" min="0" max="100" value="<?php echo ! empty( $bbp_moderation_english_threshold ) ? $bbp_moderation_english_threshold : 70; ?>">
					<label for="_bbp_moderation_english_threshold">%</label>
				</p>
			</div>
		</div>
		<div>
			<p>
				<input type="radio" id="_bbp_moderation_type_all" name="_bbp_moderation_type" value="all" <?php if ( $bbp_moderation_type_value == 'all' ): echo 'checked'; endif; ?>>
				<label for="_bbp_moderation_type_all"><?php _e('All posts (lockdown)', $this->slug ); ?></label>
			</p>
		</div>
		<script>
			jQuery( function( $ ) {
				$( '[name="_bbp_moderation_type"]' ).on( 'change input', function() {

					if ( $( this ).val() == 'custom' ) {

						$( '[name="_bbp_moderation_custom[]"]' ).prop( 'disabled', false );
						$( '[name="_bbp_moderation_english_threshold"]' ).prop( 'disabled', false );

					} else {

						$( '[name="_bbp_moderation_custom[]"]' ).prop( 'disabled', true );
						$( '[name="_bbp_moderation_english_threshold"]' ).prop( 'disabled', true );

					}

				});

				if ( $( '[name="_bbp_moderation_type"][value="custom"]' ).is( ':checked' ) ) {

					$( '[name="_bbp_moderation_custom[]"]' ).prop( 'disabled', false );
					$( '[name="_bbp_moderation_english_threshold"]' ).prop( 'disabled', false );

				} else {

					$( '[name="_bbp_moderation_custom[]"]' ).prop( 'disabled', true );
					$( '[name="_bbp_moderation_english_threshold"]' ).prop( 'disabled', true );

				}
			})
		</script>
	<?php
	}


	/**
	 *  Settings field for notifying moderators
	 * @since  0.1.0
	 */
	public function bbp_admin_setting_callback_notify_moderator() {
		?>
		<div>
			<input type="checkbox" id="_bbp_notify_moderator" name="_bbp_notify_moderator" value="1"<?php if ( get_option( '_bbp_notify_moderator' ) ) : echo ' checked'; endif; ?>>
			<label for="_bbp_notify_moderator"><?php _e( 'Notify all moderators when a post is held for moderation', $this->slug ); ?></label>
		</div>
	<?php
	}


	/**
	 *  Settings field for notifying keymasters
	 * @since  0.1.0
	 */
	public function bbp_admin_setting_callback_notify_keymaster() {
		?>
		<div>
			<input type="checkbox" id="_bbp_notify_keymaster" name="_bbp_notify_keymaster" value="1"<?php if ( get_option( '_bbp_notify_keymaster' ) ) : echo ' checked'; endif; ?>>
			<label for="_bbp_notify_keymaster"><?php _e( 'Notify all keymasters when a post is held for moderation', $this->slug ); ?></label>
		</div>
	<?php
	}


	/**
	 *  Settings field for notifying custom email addresses
	 * @since  0.1.0
	 */
	public function bbp_admin_setting_callback_notify_custom() {
		?>
		<div>
			<input type="text" name="_bbp_notify_email" value="<?php echo get_option( '_bbp_notify_email' ); ?>" class="regular-text">
			<p class="description"><?php _e('Comma separated to add multiple email addresses.', $this->slug ) ?></p>
		</div>
	<?php
	}

	/**
	 * Setting field for setting blocked user redirection
	 * @since  0.2.0
	 */
	public function bbp_admin_setting_callback_blocked_users() {
		?>
		<div>
			Direct blocked users to
			<select name="_bbp_blocked_page_id">
				<option value="0">404</option>
				<?php foreach ( get_pages() as $page ) : ?>
					<option value="<?php echo $page->ID; ?>" <?php echo ( get_option( '_bbp_blocked_page_id' ) == $page->ID ) ? 'selected' : '' ?>><?php echo $page->post_title; ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php _e('Setting the option to 404 will keep the bbPress default behaviour.', $this->slug ) ?></p>
		</div>
	<?php
	}


	/**
	 * Set settings section capabilities
	 *
	 *	@since  0.1.0 [<description>]
	 *
	 * @param $caps
	 * @param $cap
	 * @param $user_id
	 * @param $args
	 *
	 * @return array
	 */
	public function set_settings_section_cap( $caps, $cap, $user_id, $args ) {

		if ( $cap !== 'bbp_settings_moderation_options' && $cap !== 'bbp_settings_moderation_notifications' ) {

			return $caps;

		}

		return array( bbpress()->admin->minimum_capability );

	}


	/**
	 * Mark topics and replies as awaiting moderation
	 *
	 *	@since  0.1.0
	 *	@since  0.2.0 Added english detection and expanded logic to allow multiple rules
	 *
	 * @param  INT $post_id
	 */
	public function moderate_post( $post ) {

		global $wpdb;

		// Check if the topic is a reply or topic
		if ( 'reply' != $post['post_type'] && 'topic' != $post['post_type'] ) {

			return $post;

		}

		// Skip moderation if the post is marked as spam
		if ( 'spam' == $post['post_status'] ) {

			return $post;

		}

		// Skip moderation if the user has moderation power
		if ( $this->user_can_moderate() ) {

			return $post;

		}

		$moderation_type = get_option( '_bbp_moderation_type' );

		// Check if any moderation type is set, if not or is off return the post as is.
		if ( empty( $moderation_type ) || 'off' == $moderation_type ) {

			return $post;

		}

		// If the moderation type is set to 'all', set post status to pending and return $post
		if ( ! empty( $moderation_type ) && 'all' == $moderation_type ) {

			$post['post_status'] = 'pending';
			return $post;

		}

		// If moderation type is custom, run valid checks
		if ( ! empty( $moderation_type ) && 'custom' == $moderation_type ) {

			$test_content = htmlspecialchars_decode( stripslashes( $post['post_content'] ) );

			$custom_moderation_options = get_option( '_bbp_moderation_custom' );

			// Run the ascii english detection check
			if ( ! empty( $custom_moderation_options ) && ( in_array( 'ascii', $custom_moderation_options ) || in_array( 'ascii_unnaproved', $custom_moderation_options ) ) ) {

				$ascii_approved = get_user_meta( $post['post_author'], '_ascii_moderation_approved', TRUE );

				if ( ! $ascii_approved ) {

					$len = strlen( $test_content );
					for ($i = 0; $i < $len; $i++) {
						$ord = ord( $test_content[$i] );
						if ( $ord == 10 || $ord == 32 || $ord == 194 || $ord == 163 ) {

						} else if ( $ord > 127 ) {
							$non_english_arr[] = $ord;
						} else {
							$english_arr[] =  $ord;
						}
					}

					$english_percent = ( count( $english_arr ) / ( count( $non_english_arr ) + count( $english_arr ) ) ) * 100 ;
					$bbp_moderation_english_threshold = get_option( '_bbp_moderation_english_threshold');
					$english_threshold = ! empty( $bbp_moderation_english_threshold ) ? $bbp_moderation_english_threshold : 70;

					if ( (int) $english_percent < (int) $english_threshold ) {

						$post['post_status'] = 'pending';
						$post['meta_input'] = array( '_bbp_moderation_ascii_found' => true );

					}

				}

			}

			// Moderate posts with links
			if ( ! empty( $custom_moderation_options ) && in_array( 'links', $custom_moderation_options ) ) {

				// Check if user has not had a previous post with a link approved
				if ( ! get_user_meta( $post['post_author'], '_link_moderation_approved', TRUE ) ) {

					// Check for a link in the post content
					$pattern = '#(https?\://)?(www\.)?[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(/\S*)?#';

					if ( preg_match( $pattern, $post['post_content'] ) ) {

						$post['post_status'] = 'pending';
						$post['meta_input'] = array( '_bbp_moderation_link_found' => true );

					}

				}

			}

			// Moderate first post
			if ( ! empty( $custom_moderation_options ) && in_array( 'users', $custom_moderation_options ) ) {

				// Check if user has any published posts
				$sql = $wpdb->prepare( "SELECT COUNT( ID ) FROM {$wpdb->posts} WHERE post_author = %d AND post_type IN ('topic','reply') AND post_status = 'publish'", $post['post_author'] );
				$count = $wpdb->get_var($sql);

				if ( $count < 1 ) {

					$post['post_status'] = 'pending';

				}

			}

		}

		return $post;

	}

	/**
	 * Redirect users back to parent with pending variable to display notice for anon users
	 *
	 *	@since  0.2.0
	 *
	 * @param  $redirect_url
	 * @param  $redirect_to
	 * @param  $post_id
	 *
	 * @return $redirect_url
	 *
	 */
	public function redirect_pending_anon( $redirect_url, $redirect_to, $post_id ) {
		$post = get_post( $post_id );

		if ( in_array( $post->post_type, array( 'topic', 'reply' ) ) && 'pending' == $post->post_status && $post->post_author == 0 ) {

			$redirect_url = get_permalink( $post->post_parent ) . '?moderation_pending=' . $post_id;

		}

		return $redirect_url;

	}

	/**
	 * Display pending notice to anon users when submitted post is pending
	 * @since 0.2.0
	 */
	public function anon_pending_notice() {

		if ( ! empty( $_GET['moderation_pending'] ) ) {

			$post_id = $_GET['moderation_pending'];
			$post = get_post( $post_id );

			if ( in_array( $post->post_type, array( 'topic', 'reply' ) ) && 0 == $post->post_author ) {

				switch( $post->post_type ) {

					case 'topic':
						$message = __('Your topic has been submitted and is pending further moderation', $this->slug );
					break;
					case 'reply':
						$message = __('Your reply has been submitted and is pending further moderation', $this->slug );
					break;

				}

				echo '<div class="bbp-template-notice"><p>' . $message . '</p></div>';

			}

		}

	}


	/**
	 * Add the post status to the topic and reply query
	 *
	 *	@since  0.1.0
	 *
	 * @param  array $bbp
	 *
	 * @return array $bbp
	 */
	public function pending_query( $bbp ) {
		$user = wp_get_current_user();

		if ( ! $user->ID ) {
			return $bbp;
		}

		$bbp['post_status'] = 'pending,publish,closed,private,hidden,reported';

		$user_can_moderate = $this->user_can_moderate( $user->ID, bbp_get_forum_id() );

		if ( ( isset( $_GET['view'] ) && $_GET['view'] == 'all' ) && $user_can_moderate ) {

			$bbp['post_status'] .= ',spam,trash';

		}

		if ( ! $user_can_moderate ) {

			add_filter( 'posts_where', array( $this, 'posts_where' ) );

		}

		return $bbp;

	}


	/**
	 * Posts where...
	 *
	 *	@since 0.1.0
	 *
	 * @param str $where
	 *
	 * @return str $where
	 */
	public function posts_where( $where = '' ) {

		global $wpdb;

		$user = wp_get_current_user();

		$where = str_ireplace( $wpdb->prefix . "posts.post_status = 'pending'", "(" . $wpdb->prefix . "posts.post_status = 'pending' AND " . $wpdb->prefix . "posts.post_author = " . $user->ID . ")", $where );

		return $where;

	}


	/**
	 * Replace the content with an awaiting moderation message
	 *
	 *	@since  0.1.0
	 *
	 * @param  string $content
	 * @param  int $post_id
	 *
	 * @return string $content
	 */
	public function moderate_content( $content, $post_id ) {

		$post = get_post( $post_id );

		// Why would it be empty? no one knows, but better safe than sorry!
		if ( empty( $post ) ) {

			return $content;

		}

		$notice = '<div class="bbp-mt-template-notice">' . __( 'This post is awaiting moderation.', $this->slug ) . '</div>';

		if ( 'pending' == $post->post_status ) {

			if ( $this->user_can_moderate( get_current_user_ID(), bbp_get_forum_id() ) ) {

				$content = $notice . '<br>' . $content;

			} else {

				$content = $notice;

			}

		}

		return $content;

	}


	/**
	 * Add 'approve' or 'pending' link into the mod links
	 *
	 * @since  0.1.0
	 * @since  0.2.0 Added bbPress version check to remove links being added to unapprove/approve posts
	 *
	 * @param  string $retval [description]
	 * @param  array $r      [description]
	 * @param  array $args   [description]
	 *
	 * @return string $retval         [description]
	 */
	public function admin_links( $retval, $r, $args ) {

		$post_id = $r['id'];

		// Why would it be empty? no one knows, but better safe than sorry!
		if ( empty( $post_id ) ) {

			return $retval;

		}

		if ( $this->user_can_moderate() ) {

			$post_status = bbp_get_topic_status( $post_id );
			$post_type = get_post_type( $post_id );
			$bbpress_version = explode( '-', bbp_get_version() )[0];
			$is_pre_bbpress_2_6 = ( version_compare("2.6", $bbpress_version ) <= 0 ) ? true : false;

			if( 'spam' != $post_status && 'pending' == $post_status && ! $is_pre_bbpress_2_6 ) {

				$url = add_query_arg( array(
					$post_type . '_id' => $post_id,
					'action' => $this->slug . '-approve',
				));

				$nonce_url = wp_nonce_url( $url, 'moderator_action', $this->slug . '-wp_nonce' );
				array_push( $r['links'], '<a href="' . $nonce_url . '" class="bbp-reply-edit-link">' . __( 'Approve', $this->slug ) . '</a>' );

			} else if ( 'spam' != $post_status && 'pending' != $post_status && ! $is_pre_bbpress_2_6 ) {

				$url = add_query_arg( array(
					$post_type . '_id' => $post_id,
					'action' => $this->slug . '-remove',
				));

				$nonce_url = wp_nonce_url( $url, 'moderator_action', $this->slug . '-wp_nonce' );
				array_push( $r['links'], '<a href="' . $nonce_url . '" class="bbp-reply-edit-link">' . __( 'Unapprove', $this->slug ) . '</a>' );

			}

			$links  = implode( $r['sep'], array_filter( $r['links'] ) );
			$retval = $r['before'] . $links . $r['after'];

		}

		return $retval;

	}

	public function user_admin_links() {
		global $current_user;

		if ( $this->user_can_moderate( $current_user->ID ) ) {
			global $post;
			if ( ! empty( $post ) && $post->post_author ) {
				$author_id = $post->post_author;
			} else {
				$author_id = bbp_get_displayed_user_field( 'ID' );
			}

			$role = bbp_get_user_display_role( $author_id );

			if ( ! in_array( $role, array( 'Blocked', 'Keymaster', 'Moderator' ) ) && $author_id > 0 ) {
				$url = add_query_arg( array(
					'author_id' => $author_id,
					'action' => $this->slug . '-block_user',
				));

				$nonce_url = wp_nonce_url( $url, 'moderator_action', $this->slug . '-wp_nonce' );

				echo '<a href="' . $nonce_url . '" class="bbp-reply-edit-link">' . __( 'Block User', $this->slug ) . '</a>';
			}
		}
	}


	/**
	 * Process moderator actions
	 *
	 *	@since  0.1.0
	 *	@since  0.2.0 moved flag setting and added actions to handle flag in preperation for bbPress 2.6
	 *
	 */
	public function process_moderate_actions() {

		if ( ! $this->user_can_moderate() ) {

			return;

		}

		if ( ! isset( $_GET[$this->slug . '-wp_nonce'] ) or ! wp_verify_nonce( $_GET[$this->slug . '-wp_nonce'], 'moderator_action' ) ) {

			return;

		}

		$action_array = array( $this->slug . '-approve', $this->slug . '-remove', $this->slug . '-block_user' );

		if ( ! isset( $_GET['action'] ) ) {

			return;

		}

		if ( ! in_array( $_GET['action'], $action_array ) ) {

			return;

		}

		$post_id = null;

		if ( isset( $_GET['topic_id'] ) ) {

			$post_id = $_GET['topic_id'];

		} else if ( isset( $_GET['reply_id'] ) ) {

			$post_id = $_GET['reply_id'];

		} else if ( isset( $_GET['author_id'] ) ) {

			$author_id = $_GET['author_id'];

		}

		if ( $_GET['action'] == $this->slug . '-block_user' && ! empty( $author_id ) ) {

			bbp_set_user_role( $author_id, 'bbp_blocked' );
			return;

		}

		if ( ! $post_id ) {

			return;

		}

		$post = get_post( $post_id );

		if ( empty( $post ) ) {

			return;

		}

		if ( $_GET['action'] == $this->slug . '-approve' ) {

			// Execute pre pending code
			if ( 'topic' == $post->post_type ) {
				do_action( 'bbp_approve_topic', $post->ID );
			} else if ( 'reply' == $post->post_type ) {
				do_action( 'bbp_approve_reply', $post->ID );
			}

			wp_update_post( array(
				'ID' => $post->ID,
				'post_status' => 'publish',
			) );

			// Execute post pending code
			if ( 'topic' == $post->post_type ) {
				do_action( 'bbp_approved_topic', $post->ID );
			} else if ( 'reply' == $post->post_type ) {
				do_action( 'bbp_approved_reply', $post->ID );
			}

		} elseif ( $_GET['action'] == $this->slug . '-remove' ) {

			wp_update_post( array(
				'ID' => $post->ID,
				'post_status' => 'pending',
			));

		}

		if ( $post->post_type == 'reply' ) {

			wp_redirect( remove_query_arg( array( 'reply_id', 'topic_id', 'action', $this->slug . '-wp_nonce' ), $_SERVER['REQUEST_URI'] ) );
			exit;

		} else {

			wp_redirect( site_url( '?post_type=' . $post->post_type . '&p=' . $post->ID ) );
			exit;

		}

	}


	/**
	 * Action to add flags to users on topic/reply approval
	 *
	 * @since 0.2.0
	 *
	 * @param  $post_id
	 */
	public function moderation_approve_action( $post_id ) {

		if ( 'custom' != get_option( '_bbp_moderation_type' ) ) {

			return;

		}

		$post_author = get_post_field( 'post_author', $post_id );

		if ( 0 == $post_author ) {

			return;

		}

		$custom_moderation_options = get_option( '_bbp_moderation_custom' );
		if ( ! empty( $custom_moderation_options ) && in_array( 'links', $custom_moderation_options ) && get_post_meta( $post_id, '_bbp_moderation_link_found', true ) ) {

			update_user_meta( $post_author, '_link_moderation_approved', TRUE );

		}

		if ( ! empty( $custom_moderation_options ) && in_array( 'ascii_unnaproved', $custom_moderation_options ) && get_post_meta( $post_id, '_bbp_moderation_ascii_found', true ) ) {

			update_user_meta( $post_author, '_ascii_moderation_approved', TRUE );

		}

	}


	/**
	 * Add pending indicator to message title
	 *
	 *	@since  0.1.0
	 *
	 * @param string $title
	 * @param int $post_id
	 *
	 * @return string $title
	 *
	 */
	public function pending_title( $title, $post_id ) {

		$post = get_post( $post_id );

		// Why would it be empty? no one knows, but better safe than sorry!
		if ( empty( $post ) ) {

			return $title;

		}

		if ( 'pending' == $post->post_status ) {

			return $title . ' ('. __( 'Awaiting moderation', $this->slug ) . ')';

		}

		if ( $this->user_can_moderate() ) {

			$args = array(
				'post_parent' => $post_id,
				'post_type'   => 'reply',
				'numberposts' => -1,
				'post_status' => 'pending'
			);
			$children = get_children( $args );
			$child_count = count ( $children );

			if ( $child_count > 1 ) {

				return $title . ' ('. $child_count . ' ' . __( 'Replies awaiting moderation', $this->slug ) . ')';

			} else if ( $child_count == 1 ) {

				return $title . ' ('. $child_count . ' ' . __( 'Reply awaiting moderation', $this->slug ) . ')';

			}

		}

		return $title;

	}


	/**
	 * Check if the topic is waiting moderation. Disable ability to reply if it is
	 *
	 *	@since 0.1.0
	 *
	 * @param boolean $retval
	 *
	 * @return boolean - true can reply
	 */
	public function pending_topic_reply_check( $retval ) {

		if ( ! $retval ) {

			return $retval;

		}

		$topic_id = bbp_get_topic_id();

		return ( 'publish' == bbp_get_topic_status( $topic_id ) );
	}


	/**
	 * Notify admin of new reply with pending status
	 *
	 *	@since  0.1.0
	 *
	 * @param int $reply_id
	 * @param int $topic_id
	 * @param int $forum_id
	 * @param boolean $anonymous_data
	 * @param int $reply_author
	 */
	function new_reply( $reply_id = 0 ) {

		$reply_id = bbp_get_reply_id( $reply_id );
		$status = bbp_get_reply_status( $reply_id );

		if ( 'pending' == $status ) {

			$this->process_notifications( $reply_id );

		}

	}


	/**
	 * Notify admin of new topic with pending status
	 * @since  0.1.0
	 * @param int $topic_id
	 * @param int $forum_id
	 * @param boolean $anonymous_data
	 * @param int $reply_author
	 */
	function new_topic( $topic_id = 0 ) {

		$topic_id = bbp_get_topic_id( $topic_id );
		$status = bbp_get_topic_status( $topic_id );

		if ( 'pending' == $status ) {

			$this->process_notifications( $topic_id );

		}

	}


	/**
	 * Process notifications for moderated post
	 * @since  0.1.0
	 */
	function process_notifications( $post_id ) {

		$blogname = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
		$blogurl = get_option( 'home' );
		$post = get_post( $post_id );
		$recipients = array();

		if ( ! $post ) {

			return;

		}

		$post_link = get_permalink( $post->ID );
		$title = $post->post_title;

		// If no post tite, check if reply and get parent post title
		if ( ! $title && 'reply' == $post->post_type ) {

			$parent_post = get_post( $post->parent_post );
			$title = 'RE: ' . $parent_post->post_title;

		}

		if ( $author = get_userdata( $post->post_author ) ) {

			$author_name = $author->display_name;

		} else {

			$author_name = 'Anonymous';

		}

		$message = '';
		$message .= sprintf( __( 'A new %s has been flagged for moderation: %s', $this->slug ) . "<br><br>", $post->post_type, '<a href="' . $post_link . '">' . $post_link . '</a>' );
		$message .= sprintf( __( 'User: %s', $this->slug ) . "<br>", $author_name );
		$message .= sprintf( __( 'Title: %s', $this->slug ) . "<br>", $title );
		$message .= sprintf( __( 'Content: %s', $this->slug ) . "<br>", nl2br( $post->post_content ) );

		// Check if notify moderators is on
		if ( get_option( '_bbp_notify_moderator ') ) {

			// Get list of moderators
			$moderators = get_users( array( 'role' => 'bbp_moderator' ) );

			foreach ( $moderators as $user ) {

				$recipients[] = $user->user_email;

			}

			if ( function_exists( 'bbp_get_moderator_ids' ) ) {

				$forums_moderators_ids = bbp_get_moderator_ids( bbp_get_forum_id() );

				if ( ! empty( $forums_moderators_ids ) ) {

					$forum_moderators = get_users( array( 'include' => $forums_moderators_ids ) );

					foreach ( $forum_moderators as $user ) {

						$recipients[] = $user->user_email;

					}

				}

			}

		}

		// Check if notify keymasters is on
		if ( get_option( '_bbp_notify_keymaster' ) ) {

			// Get list of keymasters
			$keymasters = get_users( array( 'role' => 'bbp_keymaster' ) );

			foreach( $keymasters as $user ) {

				$recipients[] = $user->user_email;

			}

		}

		// Check if any custom email addresses are to be notified
		if ( get_option( '_bbp_notify_email' ) ) {

			// List of emails should be comma or semi colon separated, and a valid email address
			$emails = get_option( '_bbp_notify_email' );

			foreach ( preg_split( "/( |\;|\,)/", $emails ) as $email ) {

				if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {

					$recipients[] =  $email;

				}

			}

		}

		// Do we have anyone to send to
		if ( ! empty( $recipients ) ) {

			@add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );
			@wp_mail( $recipients, sprintf( __( '%s Moderation - %s', $this->slug ), $blogname, $title ), $message );

		}

	}


	/**
	 * Add pending counter to topics and replies in admin
	 *
	 * @since  0.1.0
	 *
	 */
	public function admin_pending_counter() {

		global $menu, $wpdb;

		// Are there any pending items
		$sql = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'topic' AND post_status = 'pending'";
		$topic_count = $wpdb->get_var($sql);
		$sql = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'reply' AND post_status = 'pending'";
		$reply_count = $wpdb->get_var($sql);

		if ( $reply_count or $topic_count ) {

			// Add awaiting posts count next to the topic/reply menu item
			// Use in_array to find the edit post type to identify the right menu item
			if ( ! empty( $menu ) ) {
				foreach ( $menu as $key => $item ) {

					if ( $topic_count && ! empty( $item ) && in_array( 'edit.php?post_type=topic', $item ) ) {

						$bubble = '<span class="awaiting-mod count-'.$topic_count.'"><span class="pending-count">'.number_format_i18n($topic_count) .'</span></span>';
						$menu[$key][0] .= $bubble;

					}

					if ( $reply_count && ! empty( $item ) && in_array( 'edit.php?post_type=reply', $item ) ) {

						$bubble = '<span class="awaiting-mod count-'.$reply_count.'"><span class="pending-count">'.number_format_i18n($reply_count) .'</span></span>';
						$menu[$key][0] .= $bubble;

					}

				}
			}

		}

	}

	/**
	 * Check if a user is blocked, or cannot spectate the forums.
	 *
	 * @since 0.2.0
	 *
	 * @uses is_user_logged_in() To check if user is logged in
	 * @uses bbp_is_user_keymaster() To check if user is a keymaster
	 * @uses current_user_can() To check if the current user can spectate
	 * @uses is_bbpress() To check if in a bbPress section of the site
	 * @uses bbp_set_404() To set a 404 status
	 */

	public function bbp_forum_enforce_blocked() {
		// Bail if not logged in or keymaster
		if ( ! is_user_logged_in() || bbp_is_user_keymaster() ) {
			return;
		}

		// Redirect to custom block page or Set 404 if in bbPress and user cannot spectate
		if ( is_bbpress() && ! current_user_can( 'spectate' ) ) {

			if ( $page_id = get_option( '_bbp_blocked_page_id' ) ) {

				wp_redirect( get_permalink( $page_id ) );
				exit;

			} else {

				bbp_set_404();

			}

		}

	}

	/**
	 * Function to check whether user can moderate the current forum. Added ready for bbPress 2.6
	 * @since 0.2.0
	 * @param  $user_id
	 * @param  $forum_id
	 * @return bool
	 */
	private function user_can_moderate( $user_id = 0, $forum_id = 0 ) {

		if ( ! $user_id ) {

			$user_id = get_current_user_ID();

		}

		if ( ! $forum_id ) {

			$forum_id = bbp_get_forum_id();

		}

		$user_can_moderate = user_can( $user_id, 'moderate' );

		if ( function_exists( 'bbp_is_user_forum_moderator' ) && ! $user_can_moderate ) {

			$user_can_moderate = bbp_is_user_forum_moderator( $user_id, $forum_id );

		}

		return $user_can_moderate;

	}

}

$bbPressModTools = new bbPressModTools();