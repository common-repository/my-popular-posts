<?php
/*
Plugin Name: Popular post
Plugin URI: http://www.gorakh.com.np/
Version: 1.0
Author: Gorakh Shrestha
Description: This plugin allow you to easily fetch popular post and get total view in post. Simply use 'orderby'=>'popularity' in query argument.
*/
Class Popular_post{

	function __construct() {
		add_action('save_post', array($this,'check_save_post'), 10, 3 );
		add_action('wp_head',array($this, 'set_in_single'));
		add_action('pre_get_posts', array($this,'change_query'));
		add_action('wp_loaded',array($this,'set_meta_key'));
	 	//$this->set_meta_key();
	}

	function update($post_id){
		$meta_key = '_popular_post';
		$views = $this->get_views($post_id);
		if($views==''){
			delete_post_meta($post_id, $meta_key);
			add_post_meta($post_id, $meta_key, 0);
		}else{
			$views++;
			update_post_meta($post_id, $meta_key, $views);
		}
	}

	function set_in_single(){
		$slug = get_post_type(get_the_ID());
		if(is_single() && in_array($slug, $this->get_active_post_types())){
			$this->update(get_the_ID());
		}
	}
	
	function get_views($post_id){
		$count = get_post_meta($post_id, '_popular_post', true);
		return $count; 
	}

	function change_query( $query ) {
		if ( $query->get('orderby') == 'popularity' ) {
			$query->set( 'orderby', 'meta_value_num');
			$query->set('meta_key','_popular_post');
		}
	}

	function check_save_post( $post_id, $post, $update ) {
		$meta_key = '_popular_post';
		$views = $this->get_views($post_id);
		if($views == '' && in_array($post->post_type,$this->get_active_post_types())){
			delete_post_meta($post_id, $meta_key);
			add_post_meta($post_id, $meta_key, 0);
		}
	}

	function get_active_post_types(){
		$pts = get_post_types(array('public'=>true, 'publicly_queryable'=>true)); 
		$arr = array();
		foreach($pts as $pt): if($pt == get_option('_mpp_'.$pt)):
			array_push($arr,get_option('_mpp_'.$pt));  
		endif; endforeach;
		return $arr;
	}

	function set_meta_key(){
		$arr = $this->get_active_post_types();
		$query_string  = new WP_Query(array('post_type'=>$arr,'posts_per_page'=>-1));
		if($query_string->have_posts()): while($query_string->have_posts()) : $query_string->the_post();
		$meta_key = '_popular_post';
		$views = $this->get_views(get_the_ID());
		if($views == ''){
			delete_post_meta(get_the_ID(), $meta_key);
			add_post_meta(get_the_ID(), $meta_key, 0);
		}
		endwhile; endif;
		wp_reset_postdata();
	}



}

new Popular_post;

function get_view_number(){
	global $post; 
	return Popular_post::get_views($post->ID);
}

function the_view_number(){
	global $post; 
	echo Popular_post::get_views($post->ID);
}

function get_view_string(){
	global $post; 
	$view = Popular_post::get_views($post->ID);
	if($view == 1){
		$view .= _e(' View','popular-post');
	}
	else{
		$view.= _e(' Views','popular-post');
	}
	return $view; 
}
function the_view_string(){
	global $post; 
	$view = Popular_post::get_views($post->ID);
	if($view == 1){
		$view .= _e(' View','popular-post');
	}
	else{
		$view.= _e(' Views','popular-post');
	}
	echo $view; 
}



/**
 * Option page class
 **/
class options_page {
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array($this,'register_mpp_theme_option' ));
	}

	function admin_menu() {
		add_options_page('MPP Option page', 'MPP Setting', 'manage_options', 'popular_posts_options', array($this, 'settings_page') );
	}

	function  settings_page() {
		$this->mpp_theme_option_page();
	}

	function register_mpp_theme_option() {
		$post_types = get_post_types(array('public'=>true, 'publicly_queryable'=>true)); 
		foreach($post_types as $post_type): if($post_type != 'attachment'):
			register_setting( 'mpp-option-group', '_mpp_'.$post_type );
		endif; endforeach; 
	}

	function mpp_theme_option_page() {
		?>
		<div class="wrap">
			<h3><?php _e('Pleaes select post type to allow for Popular Posts','popular-post') ?></h3>
			<form method="post" action="options.php">
				<?php settings_fields( 'mpp-option-group' ); ?>
				<?php do_settings_sections( 'mpp-option-group' ); ?>
				<?php  $post_types = get_post_types(array('public'=>true, 'publicly_queryable'=>true)); ?>
				<table class="form-table">
					<?php foreach($post_types as $post_type): if($post_type != 'attachment'):  ?>
						<tr valign="top">
							<th style="text-transform:capitalize; padding:10px 10px 10px 0; " scope="row"><?php echo $post_type; ?></th>
							<td style="padding:10px 10px;"><input type="checkbox" <?php if($post_type == get_option('_mpp_'.$post_type)): echo 'checked'; endif;  ?> name="<?php echo '_mpp_'.$post_type; ?>" value="<?php echo $post_type; ?>" /></td>
						</tr>
					<?php endif; endforeach;  ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php 
	}

}
new options_page;
