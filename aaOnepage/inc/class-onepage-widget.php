<?php
/**
 * @todo Add a widget option...
 */

class AAOnepage_Widget_Meta extends WP_Widget {
/*
	function AAOnepage_Widget_Meta() {
            $widget_ops = array('classname' => 'widget_meta', 'description' => __( "Log in/out, admin, feed and WordPress links - nofollowed") );
            $this->WP_Widget('meta', __('Meta - Nofollowed'), $widget_ops);
	}

	function widget( $args, $instance ) {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? __('Meta') : $instance['title'], $instance, $this->id_base);

            echo $before_widget;
            if ( $title )
                echo $before_title . $title . $after_title;
?>
                <?php // html goes here... ?>

<?php
            echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);

            return $instance;
	}

	function form( $instance ) {
            $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
            $title = strip_tags($instance['title']);
?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
	}
*/
}