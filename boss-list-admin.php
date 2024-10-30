<?php

	function create_post_type_boss_list()
	{
		$labels = array('name' => __( 'BossList', 'bosslist' ),
						'singular_name' => __( 'BossList', 'bosslist' ),
						'add_new' => __( 'Add New', 'bosslist' ),
						'add_new_item' => __( 'Add New', 'bosslist' ),
						'edit_item' => __( 'Edit', 'bosslist' ),
						'new_item' => __( 'New', 'bosslist' ),
						'all_items' => __( 'All', 'bosslist' ),
						'view_item' => __( 'View', 'bosslist' ),
						'search_items' => __( 'Search', 'bosslist' ),
						'not_found' =>  __( 'No Items found', 'bosslist' ),
						'not_found_in_trash' => __( 'No Items found in Trash', 'bosslist' ),
						'parent_item_colon' => '',
						'menu_name' => 'BossList' );
		
		$args = array(  'labels' => $labels,
						'public' => true,
						'exclude_from_search' => true,
						'publicly_queryable' => true,
						'show_ui' => true,
						'query_var' => true,
						'show_in_nav_menus' => false,
						'menu_icon' => plugins_url( 'boss-list.png' , __FILE__ ),
						'capability_type' => 'post',
						'hierarchical' => false,
						'menu_position' => 100,
						'supports' => array( 'title', 'editor', 'thumbnail' ),
						'rewrite' => array( 'slug' => 'bosslist', 'with_front' => false ));
					
		register_post_type( 'bosslist' , $args );
	}
	// end create_post_type_boss_list
	
	add_action( 'init', 'create_post_type_boss_list' );

	
	function boss_list_updated_messages( $messages )
	{
		global $post, $post_ID;
		
		$messages['bosslist'] = array(0 => '', // Unused. Messages start at index 1.
									1 => sprintf( __( '<strong>Updated.</strong>', 'bosslist' ), esc_url( get_permalink( $post_ID) ) ),
									2 => __( 'Updated.', 'bosslist' ),
									3 => __( 'Updated.', 'bosslist' ),
									4 => 'Updated.',
									// translators: %s: date and time of the revision
									5 => isset( $_GET['revision'] ) ? sprintf( __( 'Restored to revision from %s', 'bosslist' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
									6 => sprintf( __( '<strong>Saved.</strong>', 'bosslist' ), esc_url( get_permalink( $post_ID ) ) ),
									7 => __( 'Saved.', 'bosslist' ),
									8 => sprintf( __( 'Submitted.', 'bosslist' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
									9 => sprintf( __( 'Scheduled.', 'bosslist' ),
									// translators: Publish box date format, see http://php.net/date
									date_i18n( __( 'M j, Y @ G:i', 'bosslist' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID) ) ),
									10 => sprintf( 'Draft updated.', esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID) ) ) ) );
			
		return $messages;
	}
	// end boss_list_updated_messages
	
	add_filter( 'post_updated_messages', 'boss_list_updated_messages' );


	function boss_list_edit_columns( $bl_columns )
	{
		$bl_columns = array('cb' => '<input type="checkbox">',
							'title' => __( 'Title', 'bosslist' ),
							'boss_list_featured_image' => __( 'Featured Image', 'bosslist' ),
							'boss_list_list' => __( 'List', 'bosslist' ),
							'boss_list_excerpt' => __( 'Excerpt', 'bosslist' ),
							'date' => __( 'Date', 'bosslist' ) );
		
		return $bl_columns;
	}
	// end boss_list_edit_columns
	
	add_filter( 'manage_edit-bosslist_columns', 'boss_list_edit_columns' );

	
	function boss_list_columns( $bl_column )
	{
		global $post, $post_ID;
		
		switch ( $bl_column )
		{
			case 'boss_list_featured_image':
			
				if ( has_post_thumbnail() )
				{
					the_post_thumbnail( 'thumbnail' );
				}
				
			break;
			
			case 'boss_list_list':
			
				$taxon = 'bl_lists';
				$terms_list = get_the_terms( $post_ID, $taxon );
				
				if ( ! empty( $terms_list ) )
				{
					$out = array();
					
					foreach ( $terms_list as $term_list )
					{
						$out[] = '<a href="edit.php?post_type=bosslist&bl_lists=' .$term_list->slug .'">' .$term_list->name .' </a>';
					}
					
					echo join( ', ', $out );
				}
				
			break;
			
			case 'boss_list_excerpt':
			
				the_excerpt();
				
			break;
		}
		// end switch
	}
	// end boss_list_columns
	
	add_action( 'manage_posts_custom_column',  'boss_list_columns' );
	
	
	function boss_list_taxonomy()
	{
		$labels = array('name' => __( 'Lists', 'bosslist' ),
						'singular_name' => __( 'List', 'bosslist' ),
						'search_items' =>  __( 'Search', 'bosslist' ),
						'all_items' => __( 'All Lists', 'bosslist' ),
						'parent_item' => __( 'Parent List', 'bosslist' ),
						'parent_item_colon' => __( 'Parent List:', 'bosslist' ),
						'edit_item' => __( 'Edit', 'bosslist' ),
						'update_item' => __( 'Update List', 'bosslist' ),
						'add_new_item' => __( 'Add New', 'bosslist' ),
						'new_item_name' => __( 'New List Name', 'bosslist' ),
						'menu_name' => __( 'Lists', 'bosslist' ) );

		register_taxonomy(  'bl_lists',
							array( 'bosslist' ),
							array( 'hierarchical' => true,
							'labels' => $labels,
							'show_ui' => true,
							'public' => true,
							'query_var' => true,
							'rewrite' => array( 'slug' => 'bl_lists' ) ) );
	}
	// end boss_list_taxonomy
	
	add_action( 'init', 'boss_list_taxonomy' );
	
	
	function boss_list_only_show_lists()
	{
		global $typenow;
		
		if ( $typenow == 'bosslist' )
		{
			$filters = array( 'bl_lists' );
			
			foreach ( $filters as $tax_slug )
			{
				$tax_obj = get_taxonomy( $tax_slug );
				$tax_name = $tax_obj->labels->name;
				$terms = get_terms( $tax_slug );
			
				echo '<select name="' .$tax_slug .'" id="' .$tax_slug .'" class="postform">';
				echo '<option value="">' . __( 'Show All', 'bosslist' ) . " " . $tax_name .'</option>';
				
				foreach ( $terms as $term )
				{
					echo '<option value=' . $term->slug, @$_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name . ' (' . $term->count . ')</option>';
				}
				
				echo '</select>';
			}
			// end foreach
		}
		// end typenow
	}
	// end boss_list_only_show_lists

	add_action( 'restrict_manage_posts', 'boss_list_only_show_lists' );

?>