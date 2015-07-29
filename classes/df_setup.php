<?php

class df_setup{
	
	public function __construct(){
		if (is_admin()){

			// we need it for all data flexor taxonomies

			$ay_df_taxonomies = array();

			$taxonomies = get_taxonomies(); 
			foreach ( $taxonomies as $taxonomy ) {
			    
			    if ( $this->endswith( $taxonomy, '_dftype' ) ){
			    	$ay_df_taxonomies[] = $taxonomy;
			    }
			}

		  
		  /* 
		   * configure your meta box
		   */
		  $config = array(
		    'id' => 'df_meta_box',          // meta box id, unique per meta box
		    'title' => 'Data Flexor',          // meta box title
		    'pages' => $ay_df_taxonomies,         // taxonomy name, accept categories, post_tag and custom taxonomies
		    'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
		    'fields' => array(),            // list of meta fields (can be added by field arrays)
		    'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
		    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
		  );
		  
		  
		  /*
		   * Initiate your meta box
		   */
		  $my_meta =  new Tax_Meta_Class($config);

		  //text field
		  $my_meta->addText('df_custom_class',array('name'=> __('Custom Class Name ','tax-meta'),'desc' => 'Complete if using a dataflexor custom class for display'));
  
  		  /*
		   * Don't Forget to Close up the meta box decleration
		   */
		  //Finish Meta Box Decleration
		  $my_meta->Finish();

		}
	}

	public function endswith($string, $test) {
	    $strlen = strlen($string);
	    $testlen = strlen($test);
	    if ($testlen > $strlen) return false;
	    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
	}
}