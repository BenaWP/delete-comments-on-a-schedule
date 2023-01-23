<?php
/*
Plugin Name: Delete Comments on a Schedule
Plugin URI: https://github.com/BenaWP/delete-comments-on-a-schedule
Description: Ce plugin vous permet de nettoyer facilement votre base de données en supprimant les spams et les commentaires modérés qui encombrent votre site.
Version: 1.0.0
Author: Yvon Benahita
Author URI: https://www.linkedin.com/in/benahitayvon/
License: GPLv2
Text Domain: delete-comments-on-a-schedule
*/

// Si ce fichier est acceder directement. On arrête tout.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/benawp-delete-comments-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/benawp-delete-comments-query.php';

$settings_filter_name = 'plugin_action_links_' . plugin_basename( __FILE__ );
add_filter( $settings_filter_name, 'benawp_dcos_add_settings_link' );
if ( ! function_exists( 'benawp_dcos_add_settings_link' ) ) {
	function benawp_dcos_add_settings_link( $links ) {

		// On crée notre lien.
		$settings_link = '<a href="options-discussion.php">' . __( 'Paramètres', 'delete-comments-on-a-schedule' ) . '</a>';

		// On ajoute au lien Activer/Désactiver.
		array_push( $links, $settings_link );

		return $links;
	}
}

add_action( 'plugins_loaded', 'benawp_dcos_load_text_domain' );
if ( ! function_exists( 'benawp_dcos_load_text_domain' ) ) {
	function benawp_dcos_load_text_domain() {
		load_plugin_textdomain( 'delete-comments-on-a-schedule', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

register_uninstall_hook( __FILE__, 'benawp_dcos_delete_plugin_options' );
function benawp_dcos_delete_plugin_options() {
	global $wpdb;
	$options_table = $wpdb->options;
	$wpdb->query( "DELETE FROM $options_table WHERE option_name LIKE '%delete-comments-on-a-schedule%'" );
}

