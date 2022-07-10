<?php
/**
 * Plugin Name: Inesonic WordPress Login Blocker
 * Plugin URI: http://www.inesonic.com
 * Description: A small proprietary plug-in to block people from using the wp-login form.
 * Version: 1.0.0
 * Author: Inesonic, LLC
 * Author URI: http://www.inesonic.com
 */

/***********************************************************************************************************************
 * Copyright 2020 - 2022, Inesonic, LLC.
 *
 * GNU Public License, Version 3:
 *   This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 *   License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any
 *   later version.
 *   
 *   This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 *   warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 *   details.
 *   
 *   You should have received a copy of the GNU General Public License along with this program.  If not, see
 *   <https://www.gnu.org/licenses/>.
 ***********************************************************************************************************************
 */

class InesonicWpLoginBlocker {
    const VERSION = '1.0.0';
    const SLUG    = 'inesonic-wp-login-blocker';
    const NAME    = 'Inesonic Wordpress Login Blocker';
    const AUTHOR  = 'Inesonic, LLC';
    const PREFIX  = 'InesonicWpLoginBlocker';

    const REGISTER_SLUG = 'pricing';
    const LOGIN_SLUG = 'customer-sign-in';

    private static $instance;  /* Plug-in instance */
    public static  $dir = '';  /* Plug-in directory */
    public static  $url = '';  /* Plug-in URL */

    /* Method that is called to initialize a single instance of the plug-in */
    public static function instance() {
        if (!isset(self::$instance) && !(self::$instance instanceof InesonicWpLoginBlocker)) {
            self::$instance = new InesonicWpLoginBlocker();
            self::$dir      = plugin_dir_path(__FILE__);
            self::$url      = plugin_dir_url(__FILE__);

            spl_autoload_register(array(self::$instance, 'autoloader'));
        }
    }

    /* This method ties the plug-in into the rest of the WordPress framework by adding hooks where needed. */
    public function __construct() {
        add_action('init', array($this, 'block_login'));
    }

    /* SPL autoloader */
    public function autoloader($class_name) {
        if (!class_exists($class_name) and (FALSE !== strpos($class_name, self::PREFIX))) {
            $class_name = str_replace(self::PREFIX, '', $class_name);
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

            if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }
    }

    /* Method that blocks the wp-login form. */
    public function block_login() {
        global $pagenow;

        if ($pagenow == 'wp-login.php' && $_REQUEST['action'] != 'logout') {
            if ($_REQUEST['action'] == 'register') {
                $redirect_to = get_page_by_path(self::REGISTER_SLUG, OBJECT, 'page');
            } else {
                $redirect_to = null;
            }

            if ($redirect_to === null) {
                $redirect_to = get_page_by_path(self::LOGIN_SLUG, OBJECT, 'page');
            }

            if ($redirect_to !== null) {
                wp_redirect(get_permalink($redirect_to));
            }
        }
    }
}

/* Function that returns the main plug-in instance. */
function InesonicSetupWpLoginBlocker() {
    return InesonicWpLoginBlocker::instance();
}

InesonicSetupWpLoginBlocker();
