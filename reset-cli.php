<?php

/**
 * Reset WordPress Install
 */
class Reset_CLI extends WP_CLI_Command {

	/**
	 * Reset immediately
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     wp reset now
	 *
	 */
	public function now( $args = array(), $assoc_args = array() ) {

		$title      = get_option( 'blogname' );
		$user_email = get_option( 'admin_email' );
		$user_name  = get_user_by( 'email', $user_email )->user_login;
		$password   = 'password';
		$public     = get_option( 'blog_public' );

		$siteurl = get_option('siteurl');
		$homeurl = get_option('home');

		$_SERVER['HTTP_HOST'] = ''; // just because otherwise it'll throw a warning

		global $wpdb;
		$tables = array_merge( $wpdb->tables, $wpdb->global_tables );
		foreach( $tables as $tbl ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$tbl}" );
		}
		WP_CLI::line( 'Tables dropped' );

		if ( ! function_exists( 'wp_install' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}

		WP_CLI::line( 'Installing...' );
		$install = wp_install( $title, $user_name, $user_email, $public, '', $password );
		update_option( 'siteurl', $siteurl );
		update_option( 'home', $homeurl );
		WP_CLI::success( "Done." );

		WP_CLI::line( WP_CLI::colorize( "%PUsername:%n $user_name" ) );
		WP_CLI::line( WP_CLI::colorize( "%PPassword:%n $password" ) );

	}

}

WP_CLI::add_command( 'reset', 'Reset_CLI' );