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
class random_quote extends WP_Widget{
		//sets it up
		public function __construct(){
			$widget_info = array(
				'classname' => 'widget_quotes',
				'description' => 'Shows one random quote.'
			);
			parent::__construct('rand_quote','Random Quote', $widget_info);
		}
		//html output on webpage
		public function widget($args, $instance){
			$title = apply_filters('widget_title', empty($instance['title']) ? 'Random Quote' : $instance['title'], $instance, $this->id_base);
            
			echo $args['before_widget'];
			
			if($title){
				echo $args['before_title'] . $title . $args['after_title'];
			}?>
				
				<div id="rquote"><?php do_shortcode('[rquote]')?>
                </div>
				<?php  
					echo $args['after_widget'];
					
					}
		//backend forms		
        public function form($instance){
				$instance = wp_parse_args((array) $instance, array('title'=>''));
				$title = strip_tags($instance['title']);?>
						
				<p>
				    <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> 
				    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
                </p>
							
            <?php }
	//updates input
        public function update($new_instance,$old_instance){
					
					$instance = $old_instance;
					$new_instance = wp_parse_args((array) $new_instance, array('title' => ''));
					$instance['title'] = strip_tags($new_instance['title']);

					return $instance;
						
				}
						
	}
	
	add_action('widgets_init',function(){ register_widget('random_quote'); });
            
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

add_shortcode( 'rquote', 'show_quotes' ); //creates a query to loop the custom post type

    function show_quotes(){
        $args = array(
            'post_type' => 'quotes',
            'post_status' => 'publish',
            'showposts' => '1',
            'orderby' => 'rand',
        );

        $query = new WP_Query( $args );
        if( $query->have_posts() ){

            while( $query->have_posts() ){
                $query->the_post();
                the_post_thumbnail('thumbnail');?>
                <h4><?php the_title();?></h4>
                <p><?php the_excerpt();?></p><?php
            }

        }
        wp_reset_postdata(); 


    }