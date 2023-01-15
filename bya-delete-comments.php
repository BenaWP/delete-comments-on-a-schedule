<?php
/*
Plugin Name: Delete Comments on a Schedule
Plugin URI: http://example.com/wordpress-plugins/my-plugin
Description: Ce plugin vous permet de nettoyer facilement votre base de données en supprimant les spams et les commentaires modérés qui encombrent votre site.
Version: 1.0
Author: Yvon Aulien
Author URI: https://www.linkedin.com/in/yvon-aulien-benahita-733350164/
License: GPLv2
Text Domain: wpcamp
*/

// Si ce fichier est acceder directement. On arrête tout.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/bya-delete-comments-settings.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/bya-delete-comments-query.php';

/* ======= Liens vers les paramètres ====== */
$settings_filter_name = 'plugin_action_links_' . plugin_basename( __FILE__ );
add_filter( $settings_filter_name, 'bya_add_settings_link' );

function bya_add_settings_link( $links ) {

	// On crée notre lien.
	$settings_link = '<a href="options-discussion.php">' . __( 'Paramètres', 'wpcamp' ) . '</a>';

	// On ajoute au lien Activer/Désactiver.
	array_push( $links, $settings_link );

	return $links;
}
/* ======= FIN Liens vers les paramètres ====== */
