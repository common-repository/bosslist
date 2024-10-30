<?php
/*
Plugin Name: BossList
Plugin URI:  http://profiles.wordpress.org/silver530
Description: A to-do list plugin.
Version:     1.0
Author:      silver530
Author URI:  http://www.pixelwars.org
License:     GPLv2 or later
Text Domain: post_thumbnail_column
Domain Path: /langs/
*/


/*
Copyright (c) 2014, silver530.

This program is free software; you can redistribute it and/or 
modify it under the terms of the GNU General Public License 
as published by the Free Software Foundation; either version 2 
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details.

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/* ============================================================================================================================================= */

	if ( function_exists( 'add_theme_support' ) )
	{
		add_theme_support( 'post-thumbnails', array( 'bosslist' ) );
	}
	
	
	if ( is_admin() )
	{
		include_once 'boss-list-admin.php';		
	}

/* ============================================================================================================================================= */
	
	class Boss_List_Widget extends WP_Widget
	{
		public function __construct()
		{
			parent::__construct('boss_list_widget',
								__( 'BossList', 'bosslist' ),
								array( 'description' => __( 'Displays your BossList tasks', 'bosslist' ) ) );
		}
		
		
		public function form( $instance )
		{
			if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; } else { $title = ""; }
			if ( isset( $instance[ 'bl_items_count' ] ) ) { $bl_items_count = $instance[ 'bl_items_count' ]; } else { $bl_items_count = 5; }
		?>
		
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title:', 'bosslist' ); ?></label>
				
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'bl_items_count' ); ?>"><?php echo __( 'Number of items to show:', 'bosslist' ); ?></label>
				
				<select class="widefat" id="<?php echo $this->get_field_id( 'bl_items_count' ); ?>" name="<?php echo $this->get_field_name( 'bl_items_count' ); ?>">
					
					<option <?php if ( $bl_items_count == '3' ) { echo 'selected="selected"'; } ?>>3</option>
					<option <?php if ( $bl_items_count == '5' ) { echo 'selected="selected"'; } ?>>5</option>
					<option <?php if ( $bl_items_count == '10' ) { echo 'selected="selected"'; } ?>>10</option>
					<option <?php if ( $bl_items_count == '15' ) { echo 'selected="selected"'; } ?>>15</option>
					<option <?php if ( $bl_items_count == '20' ) { echo 'selected="selected"'; } ?>>20</option>
					
				</select>
			</p>
			
		<?php
		}
		// end form
		
		
		public function update( $new_instance, $old_instance )
		{
			$instance = array();
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['bl_items_count'] = strip_tags( $new_instance['bl_items_count'] );

			return $instance;
		}
		// end update

		
		public function widget( $args, $instance )
		{
			extract( $args );
			
			$title = apply_filters( 'widget_title', $instance['title'] );
			$bl_items_count = apply_filters( 'widget_bl_items_count', $instance['bl_items_count'] );

			echo $before_widget;
			
			
			if ( ! empty( $title ) )
			{
				echo $before_title . $title . $after_title;
			}
			
			
			echo '<ul class="bosslist">';
				
				$args_bosslist = array( 'post_type' => 'bosslist', 'posts_per_page' => $bl_items_count );
				$loop_bosslist = new WP_Query( $args_bosslist );
				
				if ( $loop_bosslist->have_posts() ) :
				
				
					while ( $loop_bosslist->have_posts() ) : $loop_bosslist->the_post();
					
						echo '<li>' . the_title() . '</li>';
					
					endwhile;
					
				endif;
				
				wp_reset_query();
				
			echo '</ul>';
			
			echo $after_widget;
		}
		// end widget

	}
	// end Boss_List_Widget

	add_action( 'widgets_init', create_function( "", 'register_widget( "boss_list_widget" );' ) );

/* ============================================================================================================================================= */

?>