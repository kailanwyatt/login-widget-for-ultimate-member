<?php
/*
 * Plugin Name: SuitePlugins - Login Widget for Ultimate Member
 * Plugin URI: http://www.suiteplugins.com
 * Description: A login widget for Ultimate Member.
 * Author: SuitePlugins
 * Version: 1.1.2
 * Author URI: http://www.suiteplugins.com
 * Text Domain: login-widget-for-ultimate-member
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 4.0
 * Tested up to: 6.5.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'UM_LOGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'UM_LOGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'UM_LOGIN_PLUGIN', plugin_basename( __FILE__ ) );
define( 'UM_LOGIN_VERSION', '1.1.2' );

require_once UM_LOGIN_PATH . 'includes/class-um-login-widget.php';
require_once UM_LOGIN_PATH . 'includes/class-um-login-core.php';

/**
 * UM Login Widget Loader
 */
class UM_Login_Widget_Loader {
    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization, filters, and administration functions.
     */
    private function __construct() {
        add_action( 'init', array( $this, 'load_files' ) );
    }

    /**
     * Return an instance of this class.
     *
     * @return object A single instance of this class.
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load plugin files.
     */
    public function load_files() {
        

        $core = new UM_Login_Core();
        $core->hooks();
    }
}

UM_Login_Widget_Loader::get_instance();

add_action( 'widgets_init', 'um_login_form_register_widgets' );
function um_login_form_register_widgets() {
	register_widget( 'UM_Login_Widget' );
}

function um_login_widget_render_block( $attributes ) {
    if ( ! function_exists( 'UM' ) ) {
        return;
    }

    $defaults = array(
        'form_id'          => 0,
        'form_type'        => 0,
        'hide_remember_me' => '',
        'number'           => uniqid(),
        'before_form'      => '',
        'after_form'       => '',
        'show_avatar'      => true,
        'show_name'        => true,
        'show_logout'      => true,
        'show_account'    => true,
        'show_edit_profile' => true,
        'show_profile_url' => true,
        'show_profile_tabs' => true,
    );

    $args = wp_parse_args( $attributes, $defaults );

    if ( wp_validate_boolean( $args['show_avatar'] ) ) {
        $args['show_avatar'] = true;
    } else {
        $args['show_avatar'] = false;
    }

    if ( wp_validate_boolean( $args['show_name'] ) ) {
        $args['show_name'] = true;
    } else {
        $args['show_name'] = false;
    }

    if ( wp_validate_boolean( $args['show_logout'] ) ) {
        $args['show_logout'] = true;
    } else {
        $args['show_logout'] = false;
    }

    if ( wp_validate_boolean( $args['show_account'] ) ) {
        $args['show_account'] = true;
    } else {
        $args['show_account'] = false;
    }

    if ( wp_validate_boolean( $args['show_edit_profile'] ) ) {
        $args['show_edit_profile'] = true;
    } else {
        $args['show_edit_profile'] = false;
    }

    if ( wp_validate_boolean( $args['show_profile_url'] ) ) {
        $args['show_profile_url'] = true;
    } else {
        $args['show_profile_url'] = false;
    }

    if ( wp_validate_boolean( $args['show_profile_tabs'] ) ) {
        $args['show_profile_tabs'] = true;
    } else {
        $args['show_profile_tabs'] = false;
    }

    $args = apply_filters( 'um_login_widget_render_block', $args );

    // Set form_id if form_type is not empty. Fallback for old versions.
    if ( ! empty( $args['form_type'] ) && empty( $args['form_id'] ) ) {
        $args['form_id'] = $args['form_type'];
    }

    if ( empty( $args['form_type'] ) && ! empty( $args['form_id'] ) ) {
        $args['form_type'] = $args['form_id'];
    }

    ob_start();
    ?>
    <div id="um-login-widget-<?php echo esc_attr( $args['form_id'] ); ?>" class="um-login-widget">
        <?php
        if ( is_user_logged_in() ) :
            UM_Login_Core::load_template( 'login-widget/login-view', $args );
        else :
            UM_Login_Core::load_template( 'login-widget/login-form', $args );
        endif;
        ?>
    </div>
    <?php
     if ( um_is_core_page( 'password-reset' ) ) {
        UM()->fields()->set_mode  = '';
        UM()->form()->form_suffix = '';
    }
    return ob_get_clean();
}

function um_login_widget_register_block() {
    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }

    register_block_type( __DIR__ . '/build',
    array(
        'render_callback' => 'um_login_widget_render_block',
     ) );
}
add_action( 'init', 'um_login_widget_register_block' );

function um_login_widget_get_member_forms() {
	$args    = array(
		'post_type'   => 'um_form',
		'orderby'     => 'title',
		'numberposts' => -1,
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'meta_query'  => array(
			array(
				'key'     => '_um_mode',
				'compare' => '=',
				'value'   => 'login',
			),
		),
	);
	$posts   = get_posts( $args );
	$options = array();
	if ( ! empty( $posts ) ) {
		$options = wp_list_pluck( $posts, 'post_title', 'ID' );
	}
	return $options;
}

function um_login_widget_get_member_forms_js() {
    $options = um_login_widget_get_member_forms();
    $json_ar = array(
        array(
            'value'   => 0,
            'label'  => __( 'Select a form', 'login-widget-for-ultimate-member' ),
        ),
    );
    if ( ! empty( $options ) ) {
        foreach ( $options as $id => $title ) {
            $json_ar[] = array(
                'value'   => $id,
                'label'  => $title,
            );
        }
    }

    return $json_ar;
}
