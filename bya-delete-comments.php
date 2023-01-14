<?php
/*
Plugin Name: Delete Comments on a Schedule
Plugin URI: http://example.com/wordpress-plugins/my-plugin
Description: Ce plugin vous permet de nettoyer facilement votre base de données en supprimant les spams et les commentaires modérés qui encombrent votre site.
Version: 1.0
Author: Yvon Aulien
Author URI: https://www.linkedin.com/in/yvon-aulien-benahita-733350164/
License: GPLv2
*/

// Si ce fichier est acceder directement. On arrête tout.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* =========== Mise en places des champs ( inputs ) ============= */

// Paramètres.
add_action( 'admin_init', 'bya_cron_coment_init' );

function bya_cron_coment_init() {

	// Enregistrement des options dans le Settings API.
	register_setting(
		'discussion',
		'bya_cron_comment_options'
	);

	// Enregistrement du champs select dans le Settings API.
	add_settings_field(
		'bya_cron_comment_type_field',
		'Selectionnez les commentaires à Supprimer',
		'bya_cron_comment_type',
		'discussion',
		'default'
	);

	// Enregistrement du champs text (nbrs de jrs) dans le Settings API.
	add_settings_field(
		'bya_cron_days_old_field',
		'Supprimer les commentaires plus anciens que',
		'bya_cron_days_old',
		'discussion',
		'default'
	);

	// Récupère la valeure de l'option.

	// D'abord ajout.
	add_option( 'bya_cron_comment_options' );

	$options      = get_option( 'bya_cron_comment_options' );
	$bya_comments = $options['bya_comments'];

	// Si l'option est activée et.
	// si elle n'est pas déjà programmée, programmons-la.
	if ( $bya_comments && ! wp_next_scheduled( 'bya_comment_cron_hook' ) ) {

		// programmer l'événement pour qu'il se déroule quotidiennement.
		wp_schedule_event( time(), 'daily', 'bya_comment_cron_hook' );

		// si l'option n'est PAS activée et qu'elle est programmée, il faut la désactiver.
	} elseif ( ! $bya_comments && wp_next_scheduled( 'bya_comment_cron_hook' ) ) {

		// obtenir l'heure de la prochaine exécution programmée.
		$timestamp = wp_next_scheduled( 'bya_comment_cron_hook' );

		// hook pour l'action personnalisée de déprogrammation.
		wp_unschedule_event( $timestamp, 'bya_comment_cron_hook' );
	}

	// Créeons nos champs.
	function bya_cron_comment_type() {

		// récupère l'option 'bya_comments' depuis la BDD.
		$options      = get_option( 'bya_cron_comment_options' );
		$bya_comments = $options['bya_comments'];

		// Affichage des options dans le select.
		echo '<select name="bya_cron_comment_options[bya_comments]">';
		echo '<option value="" ' . selected( $bya_comments, '', false ) . '">Aucun</option>';
		echo '<option value="spam" ' . selected( $bya_comments, 'spam', false ) . '>Commentaires spam</option>';
		echo '<option value="moderated" ' . selected( $bya_comments, 'moderated', false ) . '>Commentaires modérés</option>';
		echo '<option value="both" ' . selected( $bya_comments, 'both', false ) . ' >Les deux</option>';
		echo '</select>';
	}

	function bya_cron_days_old() {

		// recupère l'option 'bya_days_old' dans la BDD.
		$options      = get_option( 'bya_cron_comment_options' );
		$bya_days_old = ( $options['bya_days_old'] ) ? absint( $options['bya_days_old'] ) : 30;

		// Affichage du champ de texte.
		echo '<input type="text" name="bya_cron_comment_options[bya_days_old]" value=" ' . esc_attr( $bya_days_old ) . ' " size="3" /> Jours';

	}
}
/*========== FIN Mise en places des champs ( inputs ) ========== */

/*========= Hook personnalise et fonction pour supprimer les commentaires ==========*/

// créer le hook personnalisé pour la programmation cron.
add_action( 'bya_comment_cron_hook', 'bya_cron_delete_comments' );

function bya_cron_delete_comments() {

	// Sécurité check, encore.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $wpdb;

	$options      = get_option( 'bya_cron_comment_options' );
	$bya_comments = $options['bya_comments'];
	$bya_days_old = ( $options['bya_days_old'] ) ? $options['bya_days_old'] : 30;

	// verifier si l'option est activé.
	if ( $bya_comments ) {

		if ( $bya_comments == "spam" ) {
			$bya_comments_status = 'spam';
		} elseif ( $bya_comments == "moderated" ) {
			$bya_comments_status = '0';
		}

		$sql = "DELETE FROM wp_comments WHERE ( comment_approved = '$bya_comments_status' ) AND DATEDIFF( now(), comment_date ) > %d";

		if ( $bya_comments == "both" ) {
			$sql = "DELETE FROM wp_comments WHERE ( comment_approved = 'spam' OR  comment_approved = '0') AND DATEDIFF( now(), comment_date ) > %d";
		}

		$wpdb->query( $wpdb->prepare( $sql, $bya_days_old ) );
	}
}
/*======= FIN Hook personnalise et fonction pour supprimer les commentaires ======== */

