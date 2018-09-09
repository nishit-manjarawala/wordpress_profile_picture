<?php
/*
   Plugin Name: Wordpress Profile Picture
   Plugin URI: http://techblog4.tk
   description: It's For user's profile
   Version: 1
   Author: Nishit Manjarawala
   Author URI: https://profiles.wordpress.org/nishitmanjarawala
   */
   
function nishit_add_edit_form_multipart_encoding() {
    echo ' enctype="multipart/form-data"';
}
add_action('user_edit_form_tag', 'nishit_add_edit_form_multipart_encoding');
add_action('user_new_form_tag', 'nishit_add_edit_form_multipart_encoding');

   
add_action('show_user_profile', 'nishit_wordpress_profile_show_edit');
add_action('edit_user_profile', 'nishit_wordpress_profile_show_edit');
add_action('user_new_form', 'nishit_wordpress_profile_show_edit');
function nishit_wordpress_profile_show_edit( $user ) {
?>
    <table class="form-table">
        <tr>
            <th>
                <label for="nishit_user_profile_picture"><?php _e( 'Profile Picture' ); ?></label>
            </th>
            <td>
				<?php
				if(get_the_author_meta( 'nishit_user_profile_picture', $user->ID )){
					echo'<img style="max-width:100px;max-height:100px;" src="'.wp_get_attachment_url(get_the_author_meta( 'nishit_user_profile_picture', $user->ID )).'" />';
				}else{
				?>
				<img src='<?php echo esc_url( get_avatar_url( $user->ID ) ); ?>' />
				<?php
				}
				?><br/>
				<input type="file" name="nishit_user_profile_picture" accept="image/*" />
            </td>
        </tr>
    </table>
<?php
}

add_action( 'personal_options_update', 'nishit_update_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'nishit_update_extra_profile_fields' );
add_action( 'user_register', 'nishit_update_extra_profile_fields' );
function nishit_update_extra_profile_fields( $user_id ) {
	
    
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		if(isset($_FILES['nishit_user_profile_picture'])){
			$uploadedfile = $_FILES['nishit_user_profile_picture'];
			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			if ( $movefile && ! isset( $movefile['error'] ) ) {
				$attachment = array(
				 'post_mime_type' => $movefile['type'],
				 'guid' => $movefile['url'],
				 'post_parent' => 0,
				 'post_title' => preg_replace('/\.[^.]+$/', '', basename($movefile['file']) ),
				 'post_content' => '',
			   );
			   $id = wp_insert_attachment($attachment, $file, $parent);
				update_user_meta( $user_id, 'nishit_user_profile_picture', $id );
			}
		}
	
}

add_action('admin_head','nishit_remove_personal_options');
function nishit_remove_personal_options(){
    echo '<script type="text/javascript">jQuery(document).ready(function($) {$(\'form#your-profile tr.user-profile-picture\').remove();});</script>';
}


add_filter('get_avatar', 'nishit_gravatar_filter', 10, 5);
function nishit_gravatar_filter($avatar, $id , $size, $default, $alt) {
   if(get_the_author_meta( 'nishit_user_profile_picture', $id )){
        return "<img src='".wp_get_attachment_url(get_the_author_meta( 'nishit_user_profile_picture', $id ))."' width='32' height='32' />";
    }else{    
        return $avatar;
    }
}
?>