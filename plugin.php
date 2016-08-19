<?php

/*
*Plugin Name: Simfolio Plugin
*Plugin URI: http://phoenix.sheridanc.on.ca/~ccit3671
*Description: A plugin that allows the user to display quotes anywhere on the site (via widget or shortcode).
*Author: Maria Rivera
*Author URI: http://phoenix.sheridanc.on.ca/~ccit3671
*Version 1.0
*/

//Enqueues plugin styles
function styles(){
    wp_enqueue_style('plugin-style', plugins_url('style.css', __FILE__));
}

add_action('wp_enqueue_scripts','styles');


//Create Widget - referenced thewidget.php file uploaded on slate, the widget we made in class.
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
			$title = apply_filters('widget_title', empty($instance['title']) ? 'Random Quote' :  $instance['title'], $instance, $this->id_base); //if there's no title it shows 'Random Quote'
            
			echo $args['before_widget'];
			
			if($title){
				echo $args['before_title'] . $title . $args['after_title']; //shows the title with before_title and after_title defaults
			}
				
            do_shortcode('[rquote]'); //shows the shortcode that shows a random quote
                
            $message = apply_filters('widget_content', empty($instance['message']) ? 'Have a good day!' :  $instance['message'], $instance, $this->id_base); //if there's no message it shows 'Have a good day!'
            
			echo $args['before_widget'];
			
			if($message){
				echo '<div id="qmessage"><p>' . $message . '</p></div>';
			}
				 
            echo $args['after_widget'];
					
					}
		//backend forms in the widget dashboard
        public function form($instance){
				$instance = wp_parse_args((array) $instance, array('title'=>''));
				$title = strip_tags($instance['title']);?>
						
				<p>
				    <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> 
				    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
                </p>
            <?php
				$instance = wp_parse_args((array) $instance, array('message'=>''));
				$message = strip_tags($instance['message']);?>

                <p>
				    <label for="<?php echo $this->get_field_id('message'); ?>">A Personal Message:</label> 
				    <input class="widefat" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" type="text" value="<?php echo esc_attr($message); ?>" />
                </p>
            <?php }
	//updates input
        public function update($new_instance,$old_instance){
					
					$instance = $old_instance;
					$new_instance = wp_parse_args((array) $new_instance, array('title' => '', 'message' => ''));
					$instance['title'] = strip_tags($new_instance['title']);
                    $instance['message'] = strip_tags($new_instance['message']); //grabs info that user input into the fields on the widget dashboard and returns the newly saved values for the title and message

					return $instance;
						
				}
						
	}
	
	add_action('widgets_init',function(){ register_widget('random_quote'); });
            
//Create Custom Post Type (CPT) 
/*referenced the WP Codex page for register_post_type() and its parameters: *https://codex.wordpress.org/Function_Reference/register_post_type
*/
function quote_pt() {
    
    $labels = array(  //adds labels associated with the post type
                'name'                  => 'Quotes',
                'singular_name'         => 'Quote',
                'add_new_item'          => 'Add New Quote',
                'new_item'              => 'New Quote',
                'edit_item'             => 'Edit Quote',
                'view_item'             => 'View Quote',
                'not_found'             => 'No quotes found',
                'not_found_in_trash'    => 'No quotes found in trash',
                'menu_name'             => 'Quotes'       
        );
        
    $args = array(  //adds the post type's functionality through various parameters
                'labels'                => $labels,
                'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'), 
                'public'                => true,
                'has_archive'           => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'can_export'            => true,
                'rewrite'               => array('slug' => 'quote'),
                'capability_type'       => 'post',
                'hierarchical'          => false,
                'exclude_from_search'   => false
    );
    
  register_post_type( 'quotes', $args);
}

add_action( 'init', 'quote_pt' );
//Create Shortcode

add_shortcode( 'rquote', 'show_quotes' ); //creates a query to loop the custom post type

    function show_quotes(){

        $args = array(
            'post_type'   => 'quotes',
            'post_status' => 'publish',
            'showposts'   => '1',
            'orderby'     => 'rand'
        ); //$args will show one random published quote

        $query = new WP_Query( $args );
        if( $query->have_posts() ){

            while( $query->have_posts() ){
                $query->the_post();?>
                <div id="rquote">
                    <?php the_post_thumbnail('thumbnail');?>
                <h4><?php the_title();?></h4>
                    <p><?php the_excerpt();?></p></div><?php
            }//shows the thumbnail, the post's title, and an excerpt of the post filtered by the query

        }
        wp_reset_postdata(); 


    }