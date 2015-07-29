<?php

class df_standard{

	protected $ID; // the post ID
	protected $post_type; // the post type for the note
	protected $category; // the post category - used for linking to the custom classes
	protected $taxonomy; // the taxonomy we are using for management of fields and custom classes

	protected $fields = array(); // an array of custom fields

	// both these arrays are indexed by the category ID
	protected $children = array(); // an array of arrays of child posts
	protected $child_category_details = array(); // an array of child category details

	protected $view_type; // this is where we hold the view_type of this object, can be main (for the page item), child (as part of the sub posts) or link

	function __construct($ID, $options = array()){
	//function __construct(){

		$this->ID = $ID;
		$this->post_type = get_post_type($ID);
		$this->category = $this->post_type . '_dftype';
		$this->taxonomy = $this->post_type . '_dftype';

		if (isset($options['view_type'])){
			$this->view_type = $options['view_type'];
		} else {
			$this->view_type = 'main';
		}

		// now do the specific view
		$view_method = 'do_view_type_' . $this->view_type;

		if ( is_callable( array($this, $view_method) ) ){
			$this->{$view_method}();
		}
	
	}

	public function do_view_type_main(){

		add_filter( 'the_content', array( $this,'show_attached_fields' ), 10 );

		add_filter( 'the_content', array( $this,'show_df_children' ), 20 );

		/**
		*
		* set up custom filters for the display of fields
		* @since 0.0.1
		*
		*/

		add_filter('df_field_display',array($this,'main_display_standard_field'),5,2);

		add_filter('df_field_display',array($this,'main_display_image_field'),5,2);

		add_filter('df_field_display',array($this,'main_display_date_picker_field'),5,2);

		add_filter('df_field_display',array($this,'main_display_file_field'),5,2);

	}

	public function main_display_standard_field($display_info, $field){

		// do the select and checkbox elements

		if ( ('select' == $field['type']) OR ('checkbox' == $field['type']) ){

			if (is_array($display_info['main'])){

				$display_info['main'] = implode( ', ', $display_info['main'] );

			}

		}

		if ('relationship' == $field['type']){

			
			$retval = '<ul>';
			foreach( $field['value'] as $p ){ 
				$retval .= '  <li>';
				$retval .= '     <a href="' . get_permalink( $p->ID ) . '">' . get_the_title( $p->ID ) . "</a>\n";
				$retval .= '  </li>';
			}

			$retval .= '</ul>';

			
			$display_info['main'] = $retval;

		}

		return $display_info;
	}

	public function main_display_image_field($display_info, $field){
		if ( 'image' == $field['type']){

			$image = get_field($field['name'], $this->ID);

			if( !empty($image) ){

				$display_info['main'] = '<img src="' . $image['url'] . '" alt="' . $image['alt'] . '" />';

			}

		}

		return $display_info;
	}

	public function main_display_date_picker_field($display_info, $field){
		if ( 'date_picker' == $field['type']){

			$str_date = get_field($field['name'], $this->ID);

			$date = DateTime::createFromFormat('Ymd', $str_date);

			$display_info['main'] =  $date->format('d-m-Y');

		}

		return $display_info;
	}

	public function main_display_file_field($display_info, $field){
		if ( 'file' == $field['type']){

			if( $attachment_id = get_field($field['name'], $this->ID) ){
				$display_info['main'] = '<a href="' . $attachment_id['url'] .'" >Download File ' . $attachment_id['title'] . '</a>'; 
			}

		}

		return $display_info;
	}

	public static function has_custom_class($ID, $taxonomy){

		$terms = get_the_terms( $ID, $taxonomy );

		$term_id = $terms[0]->term_id;

		$custom_class = get_tax_meta($term_id,'df_custom_class');

		if ( class_exists($custom_class) ){
			return $custom_class;
		} else {
			return false;
		}
	}

	public function show_custom_class(){
		

		if ( $custom_class = $this::has_custom_class($this->ID, $this->taxonomy)){

			echo '<h2>Custom Class = ' . $custom_class . '</h2>';
		} else {
			echo '<h2>No Custom Class</h2>';
		}
	}

	/**
	*
	* Displays attached additional meta fields
	* @since 0.0.1
	*
	*/

	public function show_attached_fields( $content ){

		$fields = get_field_objects($this->ID);

		if( $fields )
		{

			// create a string to hold the output
			$output = '';

			foreach( $fields as $field_name => $field )
			{

				//print_r($field);

				//echo '<br><br>';

				// setup defaults for filters
				$display_info = array(
					"before_field" => '<div>',
					"after_field" => '</div>',
					"show_label" => true,
					"label" => $field['label'],
					"before_label" => '<h3  class="df_field_label">',
					"after_label" => '</h3>',
					"main" => $field['value'], // the main output -> needs overidding for all non text types
				);
				
				// allow filtering
				if(has_filter('df_field_display')) {
					$display_info = apply_filters('df_field_display', $display_info, $field);
				}
				

				$output .= $display_info['before_field'];

				if ($display_info['show_label']){
					$output .= $display_info['before_label'] . $display_info['label'] . $display_info['after_label']; 
				}
					
					/*
					// some fields need to have a custom display
					$custom_class = 'df_field_' . $field['type'];

					if (class_exists($custom_class)){
						$custom_class::show_field($this->ID, $field_name);
					} else {
						echo $display_info['main'];
					}
					*/

				$output .= $display_info['main'];
					
				$output .= $display_info['after_field'];;
			}

			// add the output to the content
			$content .= $output;
		}

		return $content;
	}

	public function show_df_children( $content ){
		

		$args = array(
			'post_parent' => get_the_ID(),
			'post_type'   => get_post_type( get_the_ID() ), 
			'numberposts' => -1,
			'post_status' => 'any' 
		); 

		// allow filtering so that we can handle special cases
		if ( true == has_filter( 'df_children_args' ) ){
			$args = apply_filters( 'df_children_args', $args );
		}

		$this->children_array = get_children( $args, ARRAY_A );

		if ( 0 < count($this->children_array)){
			// now sort the children
			$this->sort_children();

			// disply the childre
			$content .= $this->display_children();
			
		}
			
		return $content;
		
	}

	/**
	*
	* Places the child posts into an array according to their category
	* @since 0.0.1
	*
	*/

	public function sort_children(){
		
		foreach ($this->children_array as $ID => $child){
			 $cur_terms =  get_the_terms( $ID, $this->category );
			 // there should be one and only cat, not much we can do about it here but...

			 $term_id = $cur_terms[0]->term_id;

			 if ( false == array_key_exists($term_id, $this->children)){
			 	//echo '<h2>Adding new category</h2>';
			 	// create item in $category_details
			 	$this->child_category_details[$term_id] = $cur_terms[0];

			 	// create the array to hold the children of this type within $children array
			 	$this->children[ $term_id ] = array();
			 }

			 $this->children[ $term_id ][] = $child;

		}
	}

	/**
	*
	* Displays child posts
	* @since 0.0.1
	*
	*/
	public function display_children(){

		$content = '';


		foreach ($this->children as $term_id => $child_group) {

			// create the category array for display
			$category_title = array(
					'before_title' => '<h2>',
					'after_title'  => '</h2>',
					'title'        => $this->child_category_details[$term_id]->name,
					'show'         => true,
					'term_id'      => $term_id,
				);

			// allow filtering
			if(has_filter('df_child_category_title')) {
				$category_title = apply_filters('df_child_category_title', $category_title);
			}

			if ( true == $category_title['show']){
				$content .= $category_title['before_title'] . $category_title['title'] . $category_title['after_title'];
			}

			foreach( $child_group as $child){



				if ( $custom_class = $this::has_custom_class( $child['ID'], $this->taxonomy)){
					$content .= $custom_class::show_custom_child_line($child['ID'], $child['post_title']);
				} else {
					$content .= $this->show_standard_child_line($child['ID'], $child['post_title']);
				}

			}

		}

		return $content;
	}

	public static function show_custom_child_line( $child, $title ){
		$link = get_permalink( $child );
		return '<a href="' . $link . '">' . $title . '</a><br>' . "\n";
	}

	public function show_standard_child_line( $child, $title ){
		$link = get_permalink( $child );

		// array for holding the child line
		$child_line = array(
				'before_line' => '',
				'after_line'  => "<br>\n",
				'line'        => '<a href="' . $link . '">' . $title . '</a>',
				'id'          => $child,
				'post_type'   => $this->post_type,
				'taxonomy'    => $this->taxonomy,
			);

		// allow filtering
		if(has_filter('df_child_line')) {
			$child_line = apply_filters('df_child_line', $child_line);
		}

		return $child_line['before_line'] . $child_line['line'] . $child_line['after_line'];
	}

}



