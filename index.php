<?php

/*
	Plugin Name: YouTube Responsive Gallery
	Plugin URI: https://wpgestalt.com/youtube-responsive-gallery/
	Description: A simple and responsive YouTube video gallery player.
	Text Domain: wpgestalt
	Author: WP Gestalt
	Author URI: https://wpgestalt.com
	Version: 1.0.1
	Tested up to: 5.0.3
	License: GPLv2 or later
*/
/*
                    'account'        => true,
                    'support'        => true,
                    'contact'        => true,
*/

if ( function_exists( 'wv_fs_videos' ) ) {
    wv_fs_videos()->set_basename( false, __FILE__ );
    return;
}


if ( !function_exists( 'wv_fs_videos' ) ) {
    // Create a helper function for easy SDK access.
    function wv_fs_videos()
    {
        global  $wv_fs_videos ;
        
        if ( !isset( $wv_fs_videos ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wv_fs_videos = fs_dynamic_init( array(
                'id'               => '3203',
                'slug'             => 'wpg_videos',
                'premium_slug'     => 'wpg-videos-premium',
                'type'             => 'plugin',
                'public_key'       => 'pk_0fb41b19e1e99ffcea1e7e57e4593',
                'is_premium'       => false,
                'premium_suffix'   => 'Professional',
                'has_addons'       => false,
                'has_paid_plans'   => true,
                'is_org_compliant' => false,
                'menu'             => array(
                'slug'    => 'edit.php?post_type=wpg_videos',
                'support' => false,
            ),
                'is_live'          => true,
            ) );
        }
        
        return $wv_fs_videos;
    }
    
    // Init Freemius.
    wv_fs_videos();
    // Signal that SDK was initiated.
    do_action( 'wv_fs_videos_loaded' );
}

//require Carbon Fields

if ( is_plugin_active( 'carbon-fields/carbon-fields-plugin.php' ) ) {
    //plugin is activated
} else {
    //activate
    require dirname( __FILE__ ) . '/lib/carbon-fields/carbon-fields-plugin.php';
}

use  Carbon_Fields\Container ;
use  Carbon_Fields\Field ;
//create CPT
function cptui_register_my_cpts_wpg_videos()
{
    /**
     * Post Type: YouTube Gallery.
     */
    $labels = array(
        "name"          => __( "YouTube Gallery", "generatepress" ),
        "singular_name" => __( "YouTube Gallery", "generatepress" ),
    );
    $args = array(
        "label"                 => __( "YouTube Gallery", "generatepress" ),
        "labels"                => $labels,
        "description"           => "",
        "public"                => true,
        "publicly_queryable"    => true,
        "show_ui"               => true,
        "delete_with_user"      => false,
        "show_in_rest"          => true,
        "rest_base"             => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive"           => false,
        "show_in_menu"          => true,
        "show_in_nav_menus"     => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => array(
        "slug"       => "wpg_videos",
        "with_front" => true,
    ),
        "query_var"             => true,
        "menu_icon"             => "dashicons-format-video",
        "supports"              => array( "title" ),
    );
    register_post_type( "wpg_videos", $args );
}

add_action( 'init', 'cptui_register_my_cpts_wpg_videos' );
function getVideoSources()
{
    $aryTest = array(
        'YouTube' => 'YouTube',
    );
    return $aryTest;
}

add_action( 'carbon_fields_register_fields', 'crb_attach_plugin_options' );
function crb_attach_plugin_options()
{
    Container::make( 'post_meta', 'YouTube Video Gallery' )->where( 'post_type', '=', 'wpg_videos' )->add_fields( array( Field::make( 'complex', 'crb_videos', 'Videos' )->set_layout( 'tabbed-horizontal' )->add_fields( array(
        //Field::make( 'select', 'crb_select', __( 'Video Source' ) )->set_options( $age ),
        Field::make( 'select', 'crb_select', __( 'Video Source' ) )->add_options( 'getVideoSources' )->set_required( true ),
        //test
        Field::make( 'text', 'video_id', 'YouTube Video ID' )->set_conditional_logic( array(
            'relation' => 'AND',
            array(
            'field'   => 'crb_select',
            'value'   => 'YouTube',
            'compare' => '=',
        ),
        ) )->set_required( true ),
        Field::make( 'file', 'wordpress_video', __( 'WordPress Media Library (.mp4)' ) )->set_value_type( 'url' )->set_conditional_logic( array(
            'relation' => 'AND',
            array(
            'field'   => 'crb_select',
            'value'   => 'wordpress',
            'compare' => '=',
        ),
        ) )->set_required( true ),
        Field::make( 'text', 'ext_url', 'External Video URL (.mp4)' )->set_conditional_logic( array(
            'relation' => 'AND',
            array(
            'field'   => 'crb_select',
            'value'   => 'external',
            'compare' => '=',
        ),
        ) )->set_required( true ),
        //Field::make( 'image', 'thumbnail', 'Thumbnail' )->set_required( true ),
        Field::make( 'image', 'thumbnail', 'Thumbnail' )->set_value_type( 'url' )->set_conditional_logic( array(
            'relation' => 'AND',
            array(
            'field'   => 'crb_select',
            'value'   => 'YouTube',
            'compare' => 'EXCLUDES',
        ),
        ) )->set_required( true ),
    ) ) ) );
    Container::make( 'post_meta', __( 'Player Options' ) )->where( 'post_type', '=', 'wpg_videos' )->add_fields( array( Field::make( 'color', 'active_color', 'Active Color' ), Field::make( 'select', 'autoplay', __( 'Autoplay' ) )->set_options( array(
        'yes' => 'Yes',
        'no'  => 'No',
    ) )->set_required( true ) ) );
}

//load CSS and JS resources
function LoadResources()
{
    wp_register_style( 'YouTubeGallery_css', plugins_url( '/css/YouTubeGallery.css', __FILE__ ) );
    wp_enqueue_style( 'YouTubeGallery_css' );
    //wp_register_script( 'YouTubeGallery_js', plugins_url('/js/YouTubeGallery.js',__FILE__ ), array('jquery'), false, true );
    //wp_enqueue_script('YouTubeGallery_js');
}

add_action( 'wp_enqueue_scripts', 'LoadResources' );
//shortcode function for video player
function DisplayPlayer( $attributes )
{
    extract( shortcode_atts( array(
        'width'  => '1920',
        'height' => '1100',
        'id'     => '0',
    ), $attributes ) );
    $single_quote = "'";
    $break = "\r\n";
    $tab = "\t";
    $tab2 = "\t\t";
    $tab3 = "\t\t\t";
    $tab4 = "\t\t\t\t";
    $tab5 = "\t\t\t\t\t";
    $tab6 = "\t\t\t\t\t\t";
    $player = '';
    $videoArray = "";
    $firstVideo = "";
    $videoSwitch = "";
    $i = 0;
    $active = "";
    $guid = "";
    //uniqid();
    $active_color = carbon_get_post_meta( $id, 'active_color' );
    //the hex color of the video being played
    $autoplay = carbon_get_post_meta( $id, 'autoplay' );
    if ( $active_color == '' ) {
        $active_color = "red";
    }
    $activeCSS = "<style>.active {border-color:" . $active_color . "}</style>";
    $videos = carbon_get_post_meta( $id, 'crb_videos' );
    //get the first video
    foreach ( $videos as $video ) {
        $source = $video['crb_select'];
        switch ( $source ) {
            case "wordpress":
                //$firstVideo = get_sub_field('video');
                $firstVideo = $video['wordpress_video'];
                break;
            case "external":
                //$firstVideo = get_sub_field('video_url');
                $firstVideo = $video['ext_url'];
                break;
            case "YouTube":
                $firstVideo = $video['video_id'];
                break;
            default:
                //default to WordPress media library
                //$firstVideo = get_sub_field('video');
                $firstVideo = $video['video_id'];
        }
        break;
    }
    //video player
    $player .= $break . $tab4 . '<div class="video-responsive"><!-- will expand to 100% of its outer container-->' . $break;
    
    if ( $source == 'YouTube' ) {
        $player .= $tab5 . '<div id="player"></div>' . $break;
    } else {
        
        if ( $autoplay == 'yes' ) {
            $player .= $tab5 . '<video width="320" height="240" autoplay controls id="videoPlayer">' . $break;
        } else {
            $player .= $tab5 . '<video width="320" height="240" controls id="videoPlayer">' . $break;
        }
        
        $player .= $tab6 . '<source src="' . $firstVideo . '" type="video/mp4"><!-- first video -->' . $break;
        $player .= $tab6 . 'Your browser does not support the video tag.' . $break;
        $player .= $tab5 . '</video>' . $break;
    }
    
    $player .= $tab4 . '</div> <!-- /video-responsive -->' . $break . $break;
    //thumbnails
    $player .= $tab4 . '<div class="thumbScroll">' . $break;
    $player .= $tab5 . '<center>' . $break;
    foreach ( $videos as $video ) {
        $title = "";
        $description = "";
        $thumbnail = $video['thumbnail'];
        $source = $video['crb_select'];
        switch ( $source ) {
            case "wordpress":
                $video = $video['wordpress_video'];
                break;
            case "external":
                $video = $video['ext_url'];
                break;
            case "YouTube":
                $video = $video['video_id'];
                $thumbnail = "https://img.youtube.com/vi/" . $video . "/0.jpg";
                break;
            default:
                //default to WordPress media library
                $video = $video['video_id'];
        }
        $videoSwitch = $videoSwitch . $tab3 . 'case "' . $video . '":' . $break . $tab4 . "curVideo = " . $i . ";" . $break . $tab4 . "break;" . $break;
        //array of videos to play
        $videoArray = $videoArray . $video . "','";
        //thumbnails
        
        if ( $i == 0 ) {
            $active = " active";
        } else {
            $active = "";
        }
        
        $player .= $tab6 . '<img src="' . $thumbnail . '" class="thumb' . $active . '" onclick="playVideo(' . $single_quote . $video . $single_quote . ', ' . $single_quote . extractFilename( $video ) . $single_quote . ')" id="' . extractFilename( $video ) . '">' . $break;
        $i = $i + 1;
    }
    $player .= '';
    $player .= '';
    $player .= '';
    $player .= '';
    $player .= $tab5 . '</center>' . $break;
    $player .= $tab4 . '</div> <!-- /thumbScroll -->' . $break . $break;
    //JS
    
    if ( $source == 'YouTube' ) {
        //$response = wp_remote_get(PluginUrl() . "js/youtube.js");
        $response = wp_remote_get( plugin_dir_url( __FILE__ ) . "js/youtube.js" );
        $js = $response['body'];
    } else {
        $response = wp_remote_get( plugin_dir_url( __FILE__ ) . "js/video.js" );
        $js = $response['body'];
    }
    
    //trim the trailing single quote
    if ( right( $videoArray, 2 ) == ",'" ) {
        $videoArray = left( $videoArray, strlen( $videoArray ) - 2 );
    }
    
    if ( $source == 'YouTube' ) {
        $js = str_replace( '[arryVideoIDs]', "'" . $videoArray, $js );
        
        if ( $autoplay == 'yes' ) {
            $js = str_replace( '[autoplay]', "loadVideoById", $js );
        } else {
            $js = str_replace( '[autoplay]', "cueVideoById", $js );
        }
    
    } else {
        $js = str_replace( '[videoArray]', "['" . $videoArray . ",'" . $firstVideo . "']", $js );
        $js = str_replace( '[videoSwitch]', $videoSwitch, $js );
    }
    
    return $player . $js . $activeCSS;
}

add_shortcode( 'YouTubeGallery', 'DisplayPlayer' );
//custom metabox
function wpg_videos_add_custom_box()
{
    $screens = [ 'post', 'wpg_videos' ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'wpg_videos_box_id',
            // Unique ID
            'Shortcode',
            // Box title
            'wpg_videos_custom_box_html',
            // Content callback, must be of type callable
            $screen,
            // Post type
            'side',
            'default'
        );
    }
}

add_action( 'add_meta_boxes', 'wpg_videos_add_custom_box' );
//function to display custom metabox content
function wpg_videos_custom_box_html( $post )
{
    $id = get_the_ID();
    $help = "Use the following shortcode in your posts or pages to display this video gallery: <br /><br />";
    ?>
    
    <?php 
    echo  $help . "<div style='background-color:#eee; padding:5px; border-radius:3px; text-align:center;'><b>[YouTubeGallery id=" . $id . "]</b></div><br /><br /><center><a target='_new' href='https://wpgestalt.com/youtube-responsive-gallery/help'><b>Online documentation</b></a></center>" ;
    ?>
    
    <?php 
}

//extracts the filename from a full path
function extractFilename( $path )
{
    return basename( $path );
    //return basename($path, ".mp4");	//removes the extension
}

function left( $str, $length )
{
    return substr( $str, 0, $length );
}

function right( $str, $length )
{
    return substr( $str, -$length );
}

//returns the plugin's URL
function PluginUrl()
{
    //Try to use WP API if possible, introduced in WP 2.6
    if ( function_exists( 'plugins_url' ) ) {
        return trailingslashit( plugins_url( basename( dirname( __FILE__ ) ) ) );
    }
    //Try to find manually... can't work if wp-content was renamed or is redirected
    $path = dirname( __FILE__ );
    $path = str_replace( "\\", "/", $path );
    $path = trailingslashit( get_bloginfo( 'wpurl' ) ) . trailingslashit( substr( $path, strpos( $path, "wp-content/" ) ) );
    return $path;
}
