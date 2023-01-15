<?php

// créer le hook personnalisé pour la programmation cron.
add_action( 'bya_comment_cron_hook', 'bya_cron_delete_comments' );

function bya_cron_delete_comments() {

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