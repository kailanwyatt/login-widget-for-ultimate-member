<?php
/**
 * Adds UM_Login_Widget widget.
 * 
 * @since 1.0.0
 * 
 * @package UM_Login_Widget
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
};

/**
 * Adds UM_Login_Widget widget.
 */
class UM_Login_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'UM_Login_Widget', // Base ID
			__( 'UM Login', 'login-widget-for-ultimate-member' ), // Name
			array(
				'description' => __( 'Login form for Ultimate Member', 'login-widget-for-ultimate-member' ),
			) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args   Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		if ( empty( $instance['title'] ) ) {
			$instance['title'] = '';
		}
		if ( empty( $instance['before_form'] ) ) {
			$instance['before_form'] = '';
		}
		if ( empty( $instance['after_form'] ) ) {
			$instance['after_form'] = '';
		}
		if ( empty( $instance['form_type'] ) ) {
			$instance['form_type'] = 'default';
		}
		if ( empty( $instance['hide_remember_me'] ) ) {
			$instance['hide_remember_me'] = false;
		}
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo um_login_widget_render_block( $instance );

		echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Load Template
	 *
	 * @param  string $tpl   Template File
	 * @param  array  $param Params
	 *
	 * @return void
	 */
	public static function load_template( $tpl = '', $params = array() ) {
		global $ultimatemember;
		extract( $params, EXTR_SKIP );
		$file       = UM_LOGIN_PATH . 'templates/' . $tpl . '.php';
		$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/' . $tpl . '.php';

		if ( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if ( file_exists( $file ) ) {
			include $file;
		}
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Login', 'login-widget-for-ultimate-member' );
		}
		if ( empty( $instance['before_form'] ) ) {
			$instance['before_form'] = '';
		}
		if ( empty( $instance['after_form'] ) ) {
			$instance['after_form'] = '';
		}

		if ( empty( $instance['form_type'] ) ) {
			$instance['form_type'] = 'default';
		}

		if ( empty( $instance['hide_remember_me'] ) ) {
			$instance['hide_remember_me'] = false;
		}
		$options = um_login_widget_get_member_forms();
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'login-widget-for-ultimate-member' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'form_type' ) ); ?>"><?php esc_html_e( 'Form Type:', 'login-widget-for-ultimate-member' ); ?></label>
			<br />
			<select id="<?php echo esc_attr( $this->get_field_id( 'form_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'form_type' ) ); ?>">
				<option value="default" <?php echo 'default' === $instance['form_type'] ? 'selected="selected"' : ''; ?>><?php esc_html_e( '-Default WordPress Login-', 'login-widget-for-ultimate-member' ); ?></option>
				<?php if ( ! empty( $options ) ) : ?>
					<?php foreach ( $options as $id => $title ) : ?>
						<option value="<?php echo absint( $id ); ?>" <?php echo $id === $instance['form_type'] ? 'selected="selected"' : ''; ?>><?php echo esc_html( $title ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'hide_remember_me' ) ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'hide_remember_me' ) ); ?>" value="1" <?php checked( 1, $instance['hide_remember_me'], true ); ?> />
				<?php esc_html_e( 'Hide Remember Me ( Default WordPress Login Only)', 'login-widget-for-ultimate-member' ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'before_form' ) ); ?>"><?php esc_html_e( 'Before Form Text:', 'login-widget-for-ultimate-member' ); ?></label>
			<textarea id="<?php echo esc_attr( $this->get_field_id( 'before_form' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'before_form' ) ); ?>" class="widefat"><?php echo wp_kses_post( $instance['before_form'] ); ?></textarea>
			<span class="description"><?php esc_html_e( 'Shortcodes accepted', 'login-widget-for-ultimate-member' ); ?></span>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'after_form' ) ); ?>"><?php esc_html_e( 'After Form Text:', 'login-widget-for-ultimate-member' ); ?></label>
			<textarea id="<?php echo esc_attr( $this->get_field_id( 'after_form' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'after_form' ) ); ?>" class="widefat"><?php echo wp_kses_post( $instance['after_form'] ); ?></textarea>
			<span class="description"><?php esc_html_e( 'Shortcodes accepted', 'login-widget-for-ultimate-member' ); ?></span>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                     = array();
		$instance['title']            = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['before_form']      = ( ! empty( $new_instance['before_form'] ) ) ? wp_kses_post( $new_instance['before_form'] ) : '';
		$instance['after_form']       = ( ! empty( $new_instance['after_form'] ) ) ? wp_kses_post( $new_instance['after_form'] ) : '';
		$instance['form_type']        = ( ! empty( $new_instance['form_type'] ) ) ? wp_kses_post( $new_instance['form_type'] ) : 'default';
		$instance['hide_remember_me'] = ( ! empty( $new_instance['hide_remember_me'] ) ) ? 1 : '';
		return $instance;
	}
} // class UM_Login_Widget
