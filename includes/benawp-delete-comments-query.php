<?php

// créer le hook personnalisé pour la programmation cron.
if ( ! function_exists( 'benawp_dcos_callback' ) ) {
	function benawp_dcos_callback() {

		global $wpdb;

		$options              = get_option( 'benawp_dcos_options' );
		$benawp_dcos_comments = $options['benawp_dcos_comments'];
		$bya_days_old         = ( $options['bya_days_old'] ) ? $options['bya_days_old'] : 30;

		// verifier si l'option est activé.
		if ( $benawp_dcos_comments ) {

			if ( $benawp_dcos_comments == "spam" ) {
				$benawp_dcos_comments_status = 'spam';
			} elseif ( $benawp_dcos_comments == "moderated" ) {
				$benawp_dcos_comments_status = '0';
			}

			$sql = "DELETE FROM wp_comments WHERE ( comment_approved = '$benawp_dcos_comments_status' ) AND DATEDIFF( now(), comment_date ) > %d";

			if ( $benawp_dcos_comments == "both" ) {
				$sql = "DELETE FROM wp_comments WHERE ( comment_approved = 'spam' OR  comment_approved = '0') AND DATEDIFF( now(), comment_date ) > %d";
			}

			$wpdb->query( $wpdb->prepare( $sql, $bya_days_old ) );
		}
	}
}

add_action( 'benawp_dcos_hook', 'benawp_dcos_callback' );

