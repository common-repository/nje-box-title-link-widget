<?php
/*
Plugin Name: NJE Box Title Link Widget
Description: A simple plugin that lets user add a widget that includes a box with title, link, button text and description
Version: 1.4.6
Author: Norm Euker
Author URI: http://www.njedesign.com/
Notes: Includes: CSS file /css/nje_btlw.css
****/

class NJE_Box_Title_Link_Widget extends WP_Widget {

	// constructor
    function __construct() {
		$widget_ops = 
		array('classname' => 'widget_box', 'description' => __('Displays box with title, website link', 'nje_box_title_widget_plugin'));
        parent::__construct(false, $name = __('NJE Box Title Link Widget', 'nje_box_title_widget_plugin') );
    }

	// widget form creation
	function form( $instance ) {
		// Check values 
		if( $instance ) { 
			$title    	= esc_attr( $instance['title'] ); 
			$url     	= esc_attr( $instance['url'] );
			$url_btn_text = esc_attr( $instance['url_btn_text'] );
			$open_new_window = esc_attr($instance['open_new_window']);
			$desc = esc_textarea( $instance['desc'] );
			$cssClass = $instance['cssClass'];
		} else { 
			$title    = ''; 
			$url     = ''; 
			$url_btn_text = '';
			$open_new_window = '';
			$desc = '';
			$cssClass = '';
		} ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'nje_box_title_widget_plugin' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('desc') ); ?>"><?php _e('Text', 'nje_box_title_widget_plugin'); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id('desc') ); ?>" name="<?php echo esc_attr( $this->get_field_name('desc') ); ?>"><?php echo esc_html( $desc ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('url') ); ?>"><?php _e( 'Website', 'nje_box_title_widget_plugin' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('url') ); ?>" name="<?php echo esc_attr( $this->get_field_name('url') ); ?>" type="text" value="<?php echo $url; ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('url_btn_text') ); ?>"><?php _e( 'Website Button Text', 'nje_box_title_widget_plugin' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('url_btn_text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('url_btn_text') ); ?>" type="text" value="<?php echo $url_btn_text; ?>" />
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('open_new_window'); ?>" name="<?php echo $this->get_field_name('open_new_window'); ?>" type="checkbox" value="1" <?php checked( '1', $open_new_window ); ?>/>
	        <label for="<?php echo $this->get_field_id('open_new_window'); ?>"><?php _e('Open Website in new tab'); ?></label>
		</p>
		<p>
            <label for="<?php echo $this->get_field_id('cssClass'); ?>"><?php _e('CSS Classes', 'nje_box_title_widget_plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('cssClass'); ?>" name="<?php echo $this->get_field_name('cssClass'); ?>" type="text" value="<?php echo $cssClass; ?>" />
        </p>
	<?php
	}

	// update widget
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		// Fields
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url'] = strip_tags($new_instance['url']);
		$instance['url_btn_text'] = strip_tags($new_instance['url_btn_text']);
		$instance['open_new_window'] = strip_tags($new_instance['open_new_window']);
		$instance['desc'] = strip_tags($new_instance['desc']);
		$instance['cssClass'] = strip_tags($new_instance['cssClass']);
		return $instance;
	}

	// display widget
	function widget($args, $instance) {
		extract( $args );
		// these are the widget options
		$title = apply_filters('widget_title', $instance['title']);
		$url = $instance['url'];
		$url_btn_text = $instance['url_btn_text'];
		$open_new_window = $instance['open_new_window'];
		$desc = $instance['desc'];
		$cssClass = $instance['cssClass'];
		
		// build css class and add to <aside>
		/*$cssClass = empty($instance['cssClass']) ? '' : $instance['cssClass'];
		if ( $cssClass ) {
            if( strpos($before_widget, 'class') === false ) {
                $before_widget = str_replace('>', 'class="'. $cssClass . '"', $before_widget);
            } else {
                $before_widget = str_replace('class="', 'class="'. $cssClass . ' ', $before_widget);
            }
        }*/

		// build html for text field, if it exists
		if ($desc) {
			$desc = '<p class="btlw-txt-area">' . $desc . '</p>';
		}

		// build <a> tag target attribute to open link in new tab or not
        $target_open_in_new_tab = '';

        $open_in_new_tab_icon_class = '';
		if( $open_new_window AND $open_new_window == '1' ) {
			$target_open_in_new_tab = ' target="_blank" ';
			// add class to display icon showing link will open in new tab
			// this is dependent on having FontAwesome and css styles, but should be fine without it
			$open_in_new_tab_icon_class = ' website';
		}

		// if there is no button text, set a default value of >>
		$url_btn_text = empty($url_btn_text) ? '&gt;&gt;' : $url_btn_text;

		// build <a> tag with url, button text, open in new tab option
		$target_link_final = '<a class="btn" href="' .  $url . '"' . $target_open_in_new_tab . '>' . $url_btn_text .'</a>';

		// Display the widget
		// Using $before_widget won't work if theme doesn't have expected <aside...> tag in functions.php
		// so we're using $args["widget_id"] to get the id
		// echo $before_widget;
		$w_id = $args["widget_id"];
		?>
		<aside id="<?php echo $w_id;?>" class="widget widget_nje_box_title_link_widget <?php echo $cssClass; ?>">
			<div class="widget-title-heading">
				<h3 class="widget-title"><?php echo $title; ?></h3>
			</div>
			<div class="textwidget widget-text <?php echo $open_in_new_tab_icon_class;?>">
				<?php echo $desc; ?>
				<?php echo $target_link_final; ?>
			</div>
		</aside>
		<?php
		echo $after_widget;
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("NJE_Box_Title_Link_Widget");'));

function add_btlw_stylesheet() 
{
    wp_enqueue_style( 'btlwCSS', plugin_dir_url( __FILE__ ) . 'css/nje-btlw.css');
}
add_action('wp_enqueue_scripts', 'add_btlw_stylesheet');