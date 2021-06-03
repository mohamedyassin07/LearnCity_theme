<?php
/****************************************** Set Debug Enviroment ******************************************/
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

/****************************************** Debug Helpers ******************************************/

function pre($element,$title = '',$return = false){
	if($title != 'no_title'){
		$excuting_line = debug_backtrace()[0]['line'];

		$excuting_file = debug_backtrace()[0]['file'];
		$excuting_file = explode("\\" ,$excuting_file);
		
		$count = count($excuting_file);
	
		$excuting_folder = @$excuting_file[($count-2)];		
		$excuting_file = $excuting_file[($count-1)];
		$excuting_file = explode('.',$excuting_file)[0];
	
		$title  =  "$title ($excuting_folder/$excuting_file@$excuting_line)";
	
		$title = $title != '' ? "<h3>$title</h3>\n" : '';
		$title = $title ."\n";	
	}
	
	if($return ==  false){
		echo "$title<pre>";
		print_r ($element);
		echo "</pre>";
		return;
	}else {
		return "$title<pre>".print_r($element)."</pre>";
	}
}
function text( $text= NULL , $return =  false){

	if ($text == NULL) {
		$text = "<h6 >A long text to be shown in places when you can't see results so perhaps you will need to make a standard check . You can search for me in the page with 6s ssssss.</h6>";
	}else {
		$text =  "<h3 >$text</h3>";
	}
	if($return ==  false){
		echo $text;
	}else {
		return $text;
	}
};
function remote_pre($args = array()){
	if(!is_array($args) && !is_object($args)){
		$args =  array('info' => $args);
	}
	$args = http_build_query($args);
	$url  = 'https://2e21c71901483ef0d66e120c844272c0.m.pipedream.net'.'/?'.$args;
	$response = wp_remote_get($url);
	return json_decode( wp_remote_retrieve_body( $response ), true );
}
function log_pre($content='',$title =  null){	
	$title = $title != null ? $title :  date('h:i:s') ;
	$title = is_object($content) && isset($content->post_title) ? $title . " :: " . $content->post_title :  $title ;
	$content =  is_string($content) ?  $content :  json_encode(json_decode(json_encode($content),true));

	$my_post = array(
    'post_type' => 'deug_log',
    'post_title'    => $title ,
    'post_content'  => $content,
    'post_status'   => 'publish',
  );  
  wp_insert_post( $my_post );
}
function is_local(){
	if ($_SERVER['REMOTE_ADDR']=='127.0.0.1' || $_SERVER['REMOTE_ADDR']=='::1') {
		return TRUE;
	}
};
/****************************************** Actions Debuging ******************************************/
function dump_hook( $tag, $hook ) {
    echo ">>>>>\t<strong>$tag</strong><br>";

    foreach( $hook as $priority => $functions ) {

	echo $priority;

	foreach( $functions as $function )
	    if( $function['function'] != 'list_hook_details' ) {

		echo "\t";

		if( is_string( $function['function'] ) )
		    echo $function['function'];

		elseif(is_array($function) && is_string( $function['function'][0] ) )
		     echo $function['function'][0] . ' -> ' . $function['function'][1];

		elseif( is_object( $function['function'][0] ) )
		    echo "(object) " . get_class( $function['function'][0] ) . ' -> ' . $function['function'][1];

		else
		    print_r($function);

		echo ' (' . $function['accepted_args'] . ') <br>';
		}
    }

    echo '';
}
function list_hooks( $filter = false ){
	global $wp_filter;
	$hooks = (array)$wp_filter;
	foreach( $hooks as $tag => $hook ){
	    if ( false === $filter || false !== strpos( $tag, $filter ) ){
			dump_hook($tag, $hook);
        }
    }
}
function list_hook_details( $input = NULL ) {
    global $wp_filter;

    $tag = current_filter();
    if( isset( $wp_filter[$tag] ) )
		dump_hook( $tag, $wp_filter[$tag] );

	return $input;
}
function list_live_hooks( $hook = false ) {
    if ( false === $hook )
		$hook = 'all';

    add_action( $hook, 'list_hook_details', -1 );
}
pre_action();
function pre_action($name=null){
	$name = $name !== null ? $name : 'all';
	add_action($name,'pre_hook');
}
function pre_hook(){
	if (stripos(current_action(), "world") !== false) {
		pre(current_action());
	}
}
/****************************************** Debug Log CPT ******************************************/
function regist_my_debug_log_cpt() {

	/**
	 * Post Type: Deug Log.
	 */

	$labels = [
		"name" => __( "Deug Log", "martfury" ),
		"singular_name" => __( "Deug Logs", "martfury" ),
		"menu_name" => __( "Deug Log", "martfury" ),
		];

	$args = [
		"label" => __( "Deug Log", "martfury" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "deug_log", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-text-page",
		"supports" => [ "title", "editor", "thumbnail" ],
	];

	register_post_type( "deug_log", $args );
}

add_action( 'init', 'regist_my_debug_log_cpt' );

add_filter( 'manage_edit-deug_log_columns','tjr_deug_log_orders_content_column');
function tjr_deug_log_orders_content_column($columns)
{
	$new_columns =  array();
	foreach ($columns as $key => $value) {
		$new_columns[$key] = $value;
		if($key ==  'title'){
			$new_columns['content'] = __('Content','tjr');
		}
	}
    return $new_columns;    
}

add_action( 'manage_deug_log_posts_custom_column' , 'tjr_deug_log_orders_content_column_content' );
function tjr_deug_log_orders_content_column_content( $column ) {
	global $post;
	$json ='';
	$content = get_the_content(null,false,$post);
	$json = json_decode($content,true);
	if(is_array($json) && count($json) > 1){
		pre($json,'no_title');
	}else {
		echo $content;
	}
}

/****************************************** Temporarly Debug ******************************************/
// as a dashboard notice as a thing allways appears in all admin pages
add_action('admin_notices', 'debug_admin_notice');
function debug_admin_notice(){
	echo '<div class="notice notice-warning is-dismissible">';
	include_once('test.php');
	echo '</div>';
}

// so it will appears if in front end
if (! is_admin()){
	include_once('test.php');
}

// if some thing need to be excuted outside hook or before the 'admin_notices' hook
include_once('test_ouside_hooks.php');

// if we need a full page/screen to test
add_action('admin_menu', 'test_page_in_menu');
function test_page_in_menu(){
    add_menu_page( 'Testing Page', 'Testing Page', 'manage_options', 'testing-page', 'test_page_content' );
} 
function test_page_content(){
	include_once('test_page.php');
}