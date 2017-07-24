<?php
/**
 * Plugin Name: bbPress Protected Forums
 * Plugin URI:  http://jordiplana.com
 * Description: Disables new topic creation in some forums for determined roles.
 * Author:      Jordi Plana
 * Author URI:  http://jordiplana.com
 * Version:     1.0
 */

load_plugin_textdomain( 'bbpf', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 

add_action( 'add_meta_boxes', 'pf_add_custom_box' );
add_action( 'save_post', 'pf_link_save_postdata' );

add_action( 'bbp_theme_before_topic_form', 'pf_start_capture');

function pf_start_capture(){
    global $post;
    if($post->post_type!='forum') return;
    
    //Protected?
    $enable_protection = get_post_meta((Integer)$post->ID, 'pf_enable_protection', true);
    if(!$enable_protection) return;
    
    global $current_user;
    $disallowed_roles = get_post_meta((Integer)$post->ID, 'pf_disallowed_roles', true);
    if($disallowed_roles) $disallowed_roles = json_decode($disallowed_roles);

    //Disallowed?
    foreach($current_user->roles as $role){
        if(in_array($role, $disallowed_roles)){
            ob_start();
            add_action( 'bbp_theme_after_topic_form', 'pf_stop_capture');
        }
    }
}

function pf_stop_capture(){
    ob_get_clean();
    remove_action('bbp_theme_after_topic_form', 'pf_stop_capture');
}

function pf_add_custom_box(){
    add_meta_box( 
        'protect_forum',
        __( 'Protection details', 'bbpf' ),
        'pf_render_metabox',
        'forum',
        'side',
        'high'
    );
}

function pf_render_metabox(){
    global $post;
    $enable_protection = get_post_meta((Integer)$post->ID, 'pf_enable_protection', true);
    $disallowed_roles = get_post_meta((Integer)$post->ID, 'pf_disallowed_roles', true);
    if($disallowed_roles) $disallowed_roles = json_decode($disallowed_roles);
    ?>
    <p>
        <input type="checkbox" name="pf_enable_protection" value="true" <?php echo $enable_protection == true ? 'checked="checked"' : '';  ?>>
        <label for="pf_enable_protection"><?php _e('Enable New Topic protection','bbpf'); ?></label>
    </p>
    <p class="howto"><?php _e('Select the roles you wish to disallow','bbpf'); ?>
        <select multiple="multiple" name="pf_disallowed_roles[]" size="7" style="width:100%">
            <?php
            //User roles
            global $wp_roles;
            $all_roles = $wp_roles->roles;
            $empty_roles = empty($disallowed_roles);
            foreach($all_roles as $role=>$role_details){
                if(!$empty_roles && in_array($role, $disallowed_roles)){
                    $selection = 'selected="selected"';
                }else{
                    $selection = '';
                }
                ?>
                <option value="<?php echo $role; ?>" <?php echo $selection; ?>><?php echo $role_details['name']; ?></option>
                <?php
            }
            ?>
        </select>
    </p>
    <?php
}

function pf_link_save_postdata(){
    //Edició ràpida? No, gràcies
    if(isset($_POST['action'])){
        if($_POST['action']=='inline-save' || $_POST['action']=='autosave'){
            return;
        }
    }
    if (!isset($_POST['post_ID']))
        return;
    
    $post_type = get_post_type($_POST['post_ID']);
    if ('forum' != $post_type)
        return;
    if(empty($_POST['pf_enable_protection'])){
        delete_post_meta($_POST['post_ID'], 'pf_enable_protection');
        delete_post_meta($_POST['post_ID'], 'pf_disallowed_roles'); 
    }else{
        update_post_meta($_POST['post_ID'], 'pf_enable_protection', $_POST['pf_enable_protection']);   
        update_post_meta($_POST['post_ID'], 'pf_disallowed_roles', json_encode($_POST['pf_disallowed_roles']));   
    }
    
}