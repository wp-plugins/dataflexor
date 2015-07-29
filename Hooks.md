Hooks and Filters

DataFlexor uses many hooks and filters for extension and customisation of the plugin.

In particular for all front end presentation there is a filter to allow modification of the output.

Filters:

'df_field_display' runs before output of a field attached to a post. It takes two parameters, $display_info and $field.
$display_info = array(
					"before_field" => '<div>',
					"after_field" => '</div>',
					"show_label" => true,
					"label" => set to the label value in ACF,
					"before_label" => '<h3  class="df_field_label">',
					"after_label" => '</h3>',
					"main" => the standard output for that field - varies according to type, 
				); 
The field is displayed by outputing array members in the following order: "before_field", "before_label", "label", "after_label", "main", "after_field".

The $field parameter is an array that contains the field array elelments provided by Advanced Custom Fields.

'df_children_args'

Runs before getting the children of the current post. Allows ammending of the arguments supplied to WordPress function get_children:
$args = array(
			'post_parent' => current post,
			'post_type'   => current post type, 
			'numberposts' => -1,
			'post_status' => 'any' 
		); 

'df_child_category_title' runs before the output of the title of a child group. Child items are sorted by the category that they are placed in so that they all appear together. This is the title element of the group and takes a parameter of $category_title:
$category_title = array(
					'before_title' => '<h2>',
					'after_title'  => '</h2>',
					'title'        => The term title,
					'show'         => true,
					'term_id'      => The terms id,
				);

Displayed in the following order "before_title", "title", "after_title".

'df_child_line' used for an individual child line. Takes a single parameter:
$child_line = array(
				'before_line' => '',
				'after_line'  => "<br>\n",
				'line'        => '<a href="' . permalink . '">' . child post title . '</a>',
				'id'          => id of child item,
			);

Displayed in the following order "bfore_line", "line", "after_line".