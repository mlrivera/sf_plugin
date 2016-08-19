<?php

/*
*Plugin Name: Simfolio Plugin
*Plugin URI: http://phoenix.sheridanc.on.ca/~ccit3671
*Description: A plugin that allows the user to display quotes anywhere on the site (via widget or shortcode).
*Author: Maria Rivera
*Author URI: http://phoenix.sheridanc.on.ca/~ccit3671
*Version 1.0
*/


//Create Widget

//Create Custom Post Type (CPT)
function quote_pt() {
  register_post_type( 'quotes',
    array(
        'labels' => array(
                        'name' => __( 'Quotes' ),
                        'singular_name' => __( 'Quote' )),
        'supports' => ['title', 'editor', 'thumbnail'], //adds featured image option
                        'public' => true,
                        'has_archive' => true,
    )
  );

}

add_action( 'init', 'quote_pt' );
//Create Shortcode

add_shortcode( 'quote', 'display_custom_post_type' ); //creates a query to loop the custom post type

    function display_custom_post_type(){
        $args = array(
            'post_type' => 'quotes',
            'post_status' => 'publish',
            'showposts' => '4'
        );

        //$string = '';
        $query = new WP_Query( $args );
        if( $query->have_posts() ){
            //$string .= '<ul>';
            while( $query->have_posts() ){
                $query->the_post();
                the_title();
                the_excerpt();
                //$string .= '<li>' . 
                the_post_thumbnail(); 
                //. '</li>';
            }
            //$string .= '</ul>';
        }
        wp_reset_postdata();
        //return $string;
    }