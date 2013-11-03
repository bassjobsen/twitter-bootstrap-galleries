<?php
/*
Plugin Name: Twitter Bootstrap Galleries
Plugin URI: https://github.com/bassjobsen/twitterbootstrap-galleries
Description: Wraps the content of a WordPress media gallery in a Twitter's Bootstrap grid
Version: 1.0
Author: Bass Jobsen
Author URI: http://bassjobsen.weblogs.fm/
License: GPLv2
*/

/*  Copyright 2013 Bass Jobsen (email : bass@w3masters.nl)

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

if(!class_exists('Twitterbootstrap_Galleries')) 
{ 
      	
class Twitterbootstrap_Galleries
{ 
/*
* Construct the plugin object 
*/ 
public function __construct() 
{ 
	load_plugin_textdomain( 'tbgal', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
 	// register actions 
	add_action('admin_init', array(&$this, 'admin_init')); 
	add_action('admin_menu', array(&$this, 'add_menu')); 
	
	
	
	add_filter( 'init', array( $this, 'init' ) );
} 
// END public 

/** 
 * Activate the plugin 
**/ 
public static function activate() 
{ 
	// Do nothing 
} 
// END public static function activate 

/** 
 * Deactivate the plugin 
 * 
**/ 
public static function deactivate() 

{ // Do nothing 
} 
// END public static function deactivate 

/** 
 * hook into WP's admin_init action hook 
 * */ 
 
public function admin_init() 
{ 
	// Set up the settings for this plugin 
	
	$this->init_settings(); 
	// Possibly do additional admin_init tasks 
} 
// END public static function activate - See more at: http://www.yaconiello.com/blog/how-to-write-wordpress-plugin/#sthash.mhyfhl3r.JacOJxrL.dpuf

/** * Initialize some custom settings */ 
public function init_settings() 
{ 
	// register the settings for this plugin 
	register_setting('twitterbootstrap-galleries-group', 'number_of_columns'); 
} // END public function init_custom_settings()


/** * add a menu */ 
public function add_menu() 
{
	 
	 add_options_page('Twitter Bootstrap Galleries Settings', 'Twitter Bootstrap Galleries', 'manage_options', 'twitterbootstrap-galleries', array(&$this, 'plugin_settings_page'));
} // END public function add_menu() 

/** * Menu Callback */ 
public function plugin_settings_page() 
{ 
	if(!current_user_can('manage_options')) 
	{ 
		wp_die(__('You do not have sufficient permissions to access this page.')); 
	
	} 
// Render the settings template 

include(sprintf("%s/templates/settings.php", dirname(__FILE__))); 

} 
// END public function plugin_settings_page() 


function init()
{
if( !function_exists( 'bssetstylesheets' ) ):
function bssetstylesheets()
{
	wp_register_style ( 'twitterbootstrap-galleries', plugins_url( 'css/twitterbootstrap-galleries.css' , __FILE__ ));
    wp_enqueue_style ( 'twitterbootstrap-galleries');
}
endif;	
add_action( 'wp_enqueue_scripts', 'bssetstylesheets', 99 );

function get_grid_classes($numberofcolumns)
{
/* the grid display */
/*
|  	columns		| mobile 	| tablet 	| desktop	|per page 	|
----------------------------------------------------|-----------|
|		1		|	1		|	1		|	1		| 	10		|
|---------------------------------------------------|-----------|
|		2		|	1		|	2		|	2		|	10		|
|---------------------------------------------------|-----------|
|		3		|	1		|	1		|	3		|	9		|
|---------------------------------------------------|-----------|
|		4		|	1		|	2		|	4		|	12		|
|---------------------------------------------------|-----------|
|		5		|	n/a		|	n/a		|	n/a		|	n/a	    |
|---------------------------------------------------|-----------|
|		6		|	2		|	4		|	6		|	12		|
|---------------------------------------------------|-----------|
|		>=6		|	n/a		|	n/a		|	n/a		|	n/a		|
|---------------------------------------------------------------|
* 
* 
*/


switch($numberofcolumns)
{
	
	case 6: $classes = 'col-xs-6 col-sm-3 col-md-2'; break;
	case 4: $classes = 'col-xs-12 col-sm-6 col-md-3'; break;
	case 3: $classes = 'col-xs-12 col-sm-12 col-md-4'; break;
	case 31: $classes = 'col-xs-12 col-sm-6 col-md-4'; break;
	case 2: $classes = 'col-xs-12 col-sm-6 col-md-6'; break;
	default: $classes = 'col-xs-12 col-sm-12 col-md-12';
	
}

return $classes;
}

remove_shortcode('gallery');
add_shortcode('gallery', 'gallery_shortcode_bootstrap');




}
}


/**
 * The Gallery shortcode.
 *
 * This implements the functionality of the Gallery Shortcode for displaying
 * WordPress images on a post.
 */
 
function gallery_shortcode_bootstrap($attr) {
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) )
			$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	}

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery'));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$icontag = tag_escape($icontag);
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) )
		$itemtag = 'dl';
	if ( ! isset( $valid_tags[ $captiontag ] ) )
		$captiontag = 'dd';
	if ( ! isset( $valid_tags[ $icontag ] ) )
		$icontag = 'dt';

	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	if ( apply_filters( 'use_default_gallery_style', true ) )
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				margin-top: 10px;
				text-align: center;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>";
	$size_class = sanitize_html_class( $size );
	$gallery_div = "<div id='$selector' class='row gallery galleryid-{$id} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

	$i = 1;
	$numberofcolumns = get_option('number_of_columns', 4 );	
	$classes = get_grid_classes($numberofcolumns);
	
	foreach ( $attachments as $id => $attachment ) {
		if ( ! empty( $link ) && 'file' === $link )
			$image_output = wp_get_attachment_link( $id, $size, false, false );
		elseif ( ! empty( $link ) && 'none' === $link )
			$image_output = wp_get_attachment_image( $id, $size, false );
		else
		$image_output = wp_get_attachment_link( $id, $size, true, false );
        
        $image_output = preg_replace('/height="[0-9]+"/','',$image_output);
        $image_output = preg_replace('/width="[0-9]+"/','',$image_output);
        $image_output = str_replace('class="', 'class="img-responsive ', $image_output);
		$image_meta  = wp_get_attachment_metadata( $id );

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) )
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

		$output .= "<div class='gallery-item ".$classes."'>";
		$output .= "
			<div class='gallery-icon {$orientation}'>
				$image_output
			</div>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</div>";

				if($numberofcolumns == 6) 
				{
					if(0 == ($i % 6)){$output .= '<div class="clearfix visible-md visible-lg"></div>'; }
					if(0 == ($i % 4)){$output .= '<div class="clearfix visible-sm"></div>'; }
					if(0 == ($i % 2)){$output .= '<div class="clearfix visible-xs"></div>'; }
			    }	
			    elseif($numberofcolumns == 4) 
				{
					if(0 == ($i % 4)){$output .= '<div class="clearfix visible-md visible-lg"></div>'; }
					if(0 == ($i % 2)){$output .= '<div class="clearfix visible-sm"></div>'; }
			    }
			    elseif($numberofcolumns == 3) 
				{
					if(0 == ($i % 3)){$output .= '<div class="clearfix visible-md visible-lg"></div>'; }
				}
				elseif($numberofcolumns == 31) 
				{
					if(0 == ($i % 3)){$output .= '<div class="clearfix visible-md visible-lg"></div>'; }
					if(0 == ($i % 2)){$output .= '<div class="clearfix visible-sm"></div>'; }
				}
			    elseif($numberofcolumns == 2) 
				{
					if(0 == ($i % 2)){$output .= '<div class="clearfix invisible-xs"></div>'; }
				}
	$i++;
	}

	$output .= "
		</div>\n";

	return $output;
}
}

if(class_exists('Twitterbootstrap_Galleries')) 
{ // Installation and uninstallation hooks 
	register_activation_hook(__FILE__, array('Twitterbootstrap_Galleries', 'activate')); 
	register_deactivation_hook(__FILE__, array('Twitterbootstrap_Galleries', 'deactivate')); 
	
	$twitterbootstrapgalleries  = new Twitterbootstrap_Galleries();
	// Add a link to the settings page onto the plugin page 
	if(isset($twitterbootstrapgalleries))
	{
		
		 function twitterbootstrapgalleries_plugin_settings_link($links) 
		 { 
			 $settings_link = '<a href="options-general.php?page=twitterbootstrap-galleries">Settings</a>';
			 array_unshift($links, $settings_link); 
			
			 return $links; 
		 } 	
		 $plugin = plugin_basename(__FILE__); 
		 	
		
		 add_filter("plugin_action_links_$plugin", 'twitterbootstrapgalleries_plugin_settings_link'); 
	}
	
}
