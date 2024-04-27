<?php
/**
 * Class: UM_Login_Widget
 * 
 * @since 1.0.0
 * 
 * @package UM_Login_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UM_Login_Core {

    /**
     * Instance of this class.
     *
     * @var object
     */
    public function hooks() {
        add_shortcode( 'um_login_widget', array( $this, 'um_login_widget_shortcode_callback' ) );
        add_action( 'login_form_middle', array( $this, 'um_login_lost_password_link' ) );
        add_action( 'plugins_loaded', array( $this, 'um_login_widget_load_textdomain' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'register_block_assets' ) );
        add_action( 'wp_ajax_um_load_login_form', array( $this, 'um_load_login_form' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'um_login_widget_enqueue_scripts' ) );
        add_action( 'dynamic_sidebar_before', array( $this, 'um_login_widget_sidebar_enqueue_scripts' ), 10, 1 );
    }

    /**
     * Enqueue styles for login widget
     *
     * @param string $index
     */
    public function um_login_widget_sidebar_enqueue_scripts( $index ) {
        if ( is_active_sidebar( $index ) ) {
            ob_start(); // Start output buffering to capture widget content
            remove_action( 'dynamic_sidebar_before', array( $this, 'um_login_widget_sidebar_enqueue_scripts' ), 10, 1 );
            dynamic_sidebar($index); // Output the widget area content
            add_action( 'dynamic_sidebar_before', array( $this, 'um_login_widget_sidebar_enqueue_scripts' ), 10, 1 );
            $sidebar_content = ob_get_clean(); // Get the widget area content
            if ( strpos( $sidebar_content, 'um-login-widget' ) !== false || has_block( 'suiteplugins/um-login', $sidebar_content ) ) {
                wp_enqueue_style( 'um-login-widget', UM_LOGIN_URL . '/build/style-index.css', array(), UM_LOGIN_VERSION );
            }
        }
    }

    /**
     * Enqueue styles for login widget
     */
    public function um_login_widget_enqueue_scripts() {
        if ( has_block( 'suiteplugins/um-login' ) ) {
            wp_enqueue_style( 'um-login-widget', UM_LOGIN_URL . '/build/style-index.css', array(), UM_LOGIN_VERSION );
        }
    }

    /**
     * Load login form
     */
    public function um_load_login_form() {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( ! isset( $_POST['form_id'] ) ) {
            wp_send_json_error( __( 'Invalid form ID', 'login-widget-for-ultimate-member' ) );
        }
        $form_id = sanitize_text_field( absint( $_POST['form_id'] ) );

        $show_profile_url = isset( $_POST['show_profile_url'] ) ? sanitize_text_field( $_POST['show_profile_url'] ) : true;
        $show_profile_tabs = isset( $_POST['show_profile_tabs'] ) ? sanitize_text_field( $_POST['show_profile_tabs'] ) : true;
        $show_logout = isset( $_POST['show_logout'] ) ? sanitize_text_field( $_POST['show_logout'] ) : true;
        $show_edit_profile = isset( $_POST['show_edit_profile'] ) ? sanitize_text_field( $_POST['show_edit_profile'] ) : true;
        $show_account = isset( $_POST['show_account'] ) ? sanitize_text_field( $_POST['show_account'] ) : true;
        $show_avatar = isset( $_POST['show_avatar'] ) ? sanitize_text_field( $_POST['show_avatar'] ) : true;

        $args = array( 
            'form_id' => $form_id,
            'show_profile_url' => $show_profile_url,
            'show_avatar' => $show_avatar,
            'show_profile_tabs' => $show_profile_tabs,
            'show_logout' => $show_logout,
            'show_edit_profile' => $show_edit_profile,
            'show_account' => $show_account,
        );
        echo um_login_widget_render_block( $args );
        exit;
    }

    /**
     * Shortcode callback
     *
     * @param array $args
     * @return string
     */
    public function um_login_widget_shortcode_callback( $args ) {
        if ( ! function_exists( 'UM' ) ) {
            return;
        }
        
        return um_login_widget_render_block( $args );
    }

    /**
     * Lost password link
     *
     * @since 1.0.1
     * 
     * @return string
     */
    public function um_login_lost_password_link() {
        return '<a href="' . wp_lostpassword_url() . '" title="' . __( 'Lost Password?', 'login-widget-for-ultimate-member' ) . '">' . __( 'Lost Password?', 'login-widget-for-ultimate-member' ) . '</a>';
    }

    public function um_login_widget_load_textdomain() {
        $domain = 'login-widget-for-ultimate-member';

        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
    
        // wp-content/languages/um-events/plugin-name-de_DE.mo
        load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
    
        // wp-content/plugins/um-events/languages/plugin-name-de_DE.mo
        load_plugin_textdomain( $domain, false, basename( __DIR__ ) . '/languages/' );
    }

    /**
     * Load template
     *
     * @param string $template_name
     * @param array $args
     */
    public static function load_template( $template_name, $args = array() ) {
        if ( ! empty( $args ) && is_array( $args ) ) {
            extract( $args );
        }
        $located = UM_Login_Core::locate_template( $template_name );
        if ( ! file_exists( $located ) ) {
            return;
        }
        include( $located );
    }

    /**
     * Locate template
     *
     * @param string $template_name
     * @return string
     */
    public static function locate_template( $template_name ) {
        $located = '';
        $template_name = ltrim( $template_name, '/' );
        if ( ! $template_name ) {
            return $located;
        }

        $template_name = $template_name . '.php';
        if ( file_exists( get_stylesheet_directory() . '/' . $template_name ) ) {
            $located = get_stylesheet_directory() . '/' . $template_name;
        } elseif ( file_exists( get_stylesheet_directory() . '/' . $template_name ) ) {
            $located = get_stylesheet_directory() . '/' . $template_name;
        } elseif ( file_exists( UM_Login_Core::plugin_dir_path() . '/templates/' . $template_name ) ) {
            $located = UM_Login_Core::plugin_dir_path() . '/templates/' . $template_name;
        }
        return $located;
    }

    /**
     * Get plugin basename
     *
     * @return string
     */
    public function plugin_basename() {
        return UM_LOGIN_PLUGIN;
    }

    /**
     * Get plugin path
     *
     * @return string
     */
    public static function plugin_dir_path() {
        return UM_LOGIN_PATH;
    }

    /**
     * Get plugin URL
     *
     * @return string
     */
    public static function plugin_dir_url() {
        return UM_LOGIN_URL;
    }

    public function register_block_assets() {
        wp_enqueue_script( 'um-login-widget-admin', UM_LOGIN_URL . '/js/editor.js', );
        wp_localize_script(
            'um-login-widget-admin',
            'um_login_widget_admin',
            array(
                'member_forms' => um_login_widget_get_member_forms_js(),
            )
        );
    }
}