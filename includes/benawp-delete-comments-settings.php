<?php

// Paramètres.
add_action( 'admin_init', 'benawp_dcos_init' );

if ( ! function_exists( 'benawp_dcos_init' ) ) {
	function benawp_dcos_init() {

		// Enregistrement des options dans le Settings API.
		register_setting(
			'discussion',
			'benawp_dcos_options'
		);

		// Enregistrement du champs select dans le Settings API.
		add_settings_field(
			'benawp_dcos_type_field',
			__( 'Sélectionnez les commentaires à supprimer', 'delete-comments-on-a-schedule' ),
			'benawp_dcos_comment_type',
			'discussion',
			'default'
		);

		// Enregistrement du champs text (nbrs de jrs) dans le Settings API.
		add_settings_field(
			'benawp_dcos_days_old_field',
			__( 'Supprimer les commentaires plus anciens que', 'delete-comments-on-a-schedule' ),
			'benawp_dcos_days_old',
			'discussion',
			'default'
		);

		// Récupère la valeure de l'option.
		$options = get_option( 'benawp_dcos_options' );
		$benawp_dcos_comments = $options['benawp_dcos_comments'];

		// Si l'option est activée et.
		// si elle n'est pas déjà programmée, programmons-la.
		if ( $benawp_dcos_comments && ! wp_next_scheduled( 'benawp_comment_cron_hook' ) ) {

			// programmer l'événement pour qu'il se déroule quotidiennement.
			wp_schedule_event( time(), 'daily', 'benawp_comment_cron_hook' );

			// si l'option n'est PAS activée et qu'elle est programmée, il faut la désactiver.
		} elseif ( ! $benawp_dcos_comments && wp_next_scheduled( 'benawp_comment_cron_hook' ) ) {

			// obtenir l'heure de la prochaine exécution programmée.
			$timestamp = wp_next_scheduled( 'benawp_comment_cron_hook' );

			// hook pour l'action personnalisée de déprogrammation.
			wp_unschedule_event( $timestamp, 'benawp_comment_cron_hook' );
		}

		// Créeons nos champs.
		if ( ! function_exists( 'benawp_dcos_comment_type' ) ) {
			function benawp_dcos_comment_type() {

				// récupère l'option 'benawp_dcos_comments' depuis la BDD.
				$options              = get_option( 'benawp_dcos_options' );
				$benawp_dcos_comments = $options['benawp_dcos_comments'];

				// Affichage des options dans le select.
				echo '<select name="benawp_dcos_options[benawp_dcos_comments]">';
				echo '<option value="" ' . selected( $benawp_dcos_comments, '', false ) . '">' . __( 'Aucun', 'delete-comments-on-a-schedule' ) . '</option>';
				echo '<option value="spam" ' . selected( $benawp_dcos_comments, 'spam', false ) . '>' . __( 'Commentaires spam', 'delete-comments-on-a-schedule' ) . '</option>';
				echo '<option value="moderated" ' . selected( $benawp_dcos_comments, 'moderated', false ) . '>' . __( 'Commentaires non approuvés', 'delete-comments-on-a-schedule' ) . '</option>';
				echo '<option value="both" ' . selected( $benawp_dcos_comments, 'both', false ) . ' >' . __( 'Les deux', 'delete-comments-on-a-schedule' ) . '</option>';
				echo '</select>';
			}
		}

		if ( ! function_exists( 'benawp_dcos_days_old' ) ) {
			function benawp_dcos_days_old() {

				// recupère l'option 'benawp_dcos_days_old' dans la BDD.
				$options              = get_option( 'benawp_dcos_options' );
				$benawp_dcos_days_old = ( $options['benawp_dcos_days_old'] ) ? absint( $options['benawp_dcos_days_old'] ) : 30;

				// Affichage du champ de texte.
				echo '<input type="text" name="benawp_dcos_options[benawp_dcos_days_old]" value=" ' . esc_attr( $benawp_dcos_days_old ) . ' " size="3" /> ' . __( 'Jours', 'delete-comments-on-a-schedule' );
			}
		}
	}
}
