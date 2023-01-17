<?php

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
		__( 'Sélectionnez les commentaires à supprimer', 'delete-comments-on-a-schedule' ),
		'bya_cron_comment_type',
		'discussion',
		'default'
	);

	// Enregistrement du champs text (nbrs de jrs) dans le Settings API.
	add_settings_field(
		'bya_cron_days_old_field',
		__( 'Supprimer les commentaires plus anciens que', 'delete-comments-on-a-schedule' ),
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
		echo '<option value="" ' . selected( $bya_comments, '', false ) . '">' . __( 'Aucun', 'delete-comments-on-a-schedule' ) . '</option>';
		echo '<option value="spam" ' . selected( $bya_comments, 'spam', false ) . '>' . __( 'Commentaires spam', 'delete-comments-on-a-schedule' ) . '</option>';
		echo '<option value="moderated" ' . selected( $bya_comments, 'moderated', false ) . '>' . __( 'Commentaires non approuvés', 'delete-comments-on-a-schedule' ) . '</option>';
		echo '<option value="both" ' . selected( $bya_comments, 'both', false ) . ' >' . __( 'Les deux', 'delete-comments-on-a-schedule' ) . '</option>';
		echo '</select>';
	}

	function bya_cron_days_old() {

		// recupère l'option 'bya_days_old' dans la BDD.
		$options      = get_option( 'bya_cron_comment_options' );
		$bya_days_old = ( $options['bya_days_old'] ) ? absint( $options['bya_days_old'] ) : 30;

		// Affichage du champ de texte.
		echo '<input type="text" name="bya_cron_comment_options[bya_days_old]" value=" ' . esc_attr( $bya_days_old ) . ' " size="3" /> '. __( 'Jours', 'delete-comments-on-a-schedule' );

	}
}
