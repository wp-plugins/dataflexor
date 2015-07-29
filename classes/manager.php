<?php

/**
*
* Decides which class to load
* @since 0.0.1
* @todo change relying on the slug from a taxonomy
*
*/

class df_manager{
	public function __construct(){
		global $post;

		// first check to make sure ACF is loaded, if not bail and if in admin post message
		if( false == class_exists('acf') ){

			return false;
		}


		// only use dataflexor where we have a dataflexor category list
		// the categorys must accord to posttype plus _dftype

		$taxonomy = get_post_type($post->ID) . '_dftype';

		if ( is_single() && taxonomy_exists($taxonomy) ){
			// now see if we have a custom display class
			// there must be at least one term
			
			/*
			$terms = get_the_terms( $post->ID, $taxonomy );

			$term_id = $terms[0]->term_id;

			$custom_class = get_tax_meta($term_id,'df_custom_class');

			if ( class_exists($custom_class) ){
				// use the custom class if it exists
				$data_flexor = new $custom_class($post->ID);
			} else {
				$data_flexor = new df_standard($post->ID);
			}
			*/

			if ( $custom_class = df_standard::has_custom_class($post->ID, $taxonomy) ){
				// use the custom class if it exists
				$data_flexor = new $custom_class($post->ID);
			} else {
				$data_flexor = new df_standard($post->ID);
			}
		}
	}	

	public function acf_admin_error_notice() {
		$class = "update-nag";
		$message = "DataFlexor needs the Advanced Custom Fields plugin to Run";
	        echo"<div class=\"$class\"> <p>$message</p></div>"; 
	}
}