<?php
/**
 * Author meta helpers and user-profile fields.
 *
 * Canonical plugin-owned storage for Byline-specific author fields that are
 * not consistently available from upstream multi-author plugins.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed;

defined( 'ABSPATH' ) || exit;

/**
 * Register user-profile hooks for canonical Byline author fields.
 */
function register_author_meta_hooks(): void {
	add_action( 'show_user_profile', __NAMESPACE__ . '\\render_author_meta_fields' );
	add_action( 'edit_user_profile', __NAMESPACE__ . '\\render_author_meta_fields' );
	add_action( 'personal_options_update', __NAMESPACE__ . '\\save_author_meta_fields' );
	add_action( 'edit_user_profile_update', __NAMESPACE__ . '\\save_author_meta_fields' );
}

/**
 * Return normalized Byline profile links for a user.
 *
 * @param int $user_id User ID.
 * @return array<int, array{href:string,rel:string}>
 */
function get_byline_feed_profiles_for_user( int $user_id ): array {
	$profiles = get_user_meta( $user_id, 'byline_feed_profiles', true );

	return normalize_byline_profiles( $profiles );
}

/**
 * Return the stored /now URL for a user.
 *
 * @param int $user_id User ID.
 * @return string
 */
function get_byline_feed_now_url_for_user( int $user_id ): string {
	$now_url = get_user_meta( $user_id, 'byline_feed_now_url', true );

	return is_string( $now_url ) ? esc_url_raw( $now_url ) : '';
}

/**
 * Return the stored /uses URL for a user.
 *
 * @param int $user_id User ID.
 * @return string
 */
function get_byline_feed_uses_url_for_user( int $user_id ): string {
	$uses_url = get_user_meta( $user_id, 'byline_feed_uses_url', true );

	return is_string( $uses_url ) ? esc_url_raw( $uses_url ) : '';
}

/**
 * Normalize candidate Byline profile data.
 *
 * Accepts either an array of arrays/objects or the textarea input format used
 * by the user-profile screen: one `rel | URL` pair per line.
 *
 * @param mixed $profiles Candidate profile data.
 * @return array<int, array{href:string,rel:string}>
 */
function normalize_byline_profiles( $profiles ): array {
	$normalized = array();

	if ( is_string( $profiles ) ) {
		$profiles = parse_byline_profiles_textarea( $profiles );
	}

	if ( ! is_array( $profiles ) ) {
		return array();
	}

	foreach ( $profiles as $profile ) {
		$href = '';
		$rel  = '';

		if ( is_array( $profile ) ) {
			$href = isset( $profile['href'] ) && is_string( $profile['href'] ) ? esc_url_raw( $profile['href'] ) : '';
			$rel  = isset( $profile['rel'] ) && is_string( $profile['rel'] ) ? sanitize_text_field( $profile['rel'] ) : '';
		} elseif ( is_object( $profile ) ) {
			$href = isset( $profile->href ) && is_string( $profile->href ) ? esc_url_raw( $profile->href ) : '';
			$rel  = isset( $profile->rel ) && is_string( $profile->rel ) ? sanitize_text_field( $profile->rel ) : '';
		}

		if ( '' === $href || '' === $rel ) {
			continue;
		}

		$normalized[] = array(
			'href' => $href,
			'rel'  => $rel,
		);
	}

	return $normalized;
}

/**
 * Parse the profile textarea value into normalized profile entries.
 *
 * @param string $textarea Raw textarea content.
 * @return array<int, array{href:string,rel:string}>
 */
function parse_byline_profiles_textarea( string $textarea ): array {
	$entries = array();
	$lines   = preg_split( '/\r\n|\r|\n/', $textarea );

	if ( ! is_array( $lines ) ) {
		return array();
	}

	foreach ( $lines as $line ) {
		$line = trim( $line );

		if ( '' === $line ) {
			continue;
		}

		$parts = array_map( 'trim', explode( '|', $line, 2 ) );

		if ( 2 !== count( $parts ) ) {
			continue;
		}

		$entries[] = array(
			'rel'  => $parts[0],
			'href' => $parts[1],
		);
	}

	return normalize_byline_profiles( $entries );
}

/**
 * Render plugin-owned author fields on the user profile screen.
 *
 * @param \WP_User $user User being edited.
 */
function render_author_meta_fields( \WP_User $user ): void {
	$profiles = get_byline_feed_profiles_for_user( $user->ID );
	$now_url  = get_byline_feed_now_url_for_user( $user->ID );
	$uses_url = get_byline_feed_uses_url_for_user( $user->ID );

	$profile_lines = array_map(
		static function ( array $profile ): string {
			return $profile['rel'] . ' | ' . $profile['href'];
		},
		$profiles
	);
	?>
	<h2><?php esc_html_e( 'Byline Feed', 'byline-feed' ); ?></h2>
	<?php wp_nonce_field( 'byline_feed_author_meta', 'byline_feed_author_meta_nonce' ); ?>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="byline-feed-profiles"><?php esc_html_e( 'Profile links', 'byline-feed' ); ?></label></th>
			<td>
				<textarea name="byline_feed_profiles" id="byline-feed-profiles" rows="5" class="large-text code"><?php echo esc_textarea( implode( "\n", $profile_lines ) ); ?></textarea>
				<p class="description"><?php esc_html_e( 'One profile per line in the format: rel | https://example.com/profile', 'byline-feed' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="byline-feed-now-url"><?php esc_html_e( '/now URL', 'byline-feed' ); ?></label></th>
			<td>
				<input name="byline_feed_now_url" id="byline-feed-now-url" type="url" value="<?php echo esc_attr( $now_url ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th><label for="byline-feed-uses-url"><?php esc_html_e( '/uses URL', 'byline-feed' ); ?></label></th>
			<td>
				<input name="byline_feed_uses_url" id="byline-feed-uses-url" type="url" value="<?php echo esc_attr( $uses_url ); ?>" class="regular-text" />
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Save plugin-owned author fields from the user profile screen.
 *
 * @param int $user_id User ID.
 */
function save_author_meta_fields( int $user_id ): void {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	if ( ! isset( $_POST['byline_feed_author_meta_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['byline_feed_author_meta_nonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'byline_feed_author_meta' ) ) {
		return;
	}

	$profiles = array();
	$now_url  = '';
	$uses_url = '';

	if ( isset( $_POST['byline_feed_profiles'] ) ) {
		$profiles = parse_byline_profiles_textarea( sanitize_textarea_field( wp_unslash( $_POST['byline_feed_profiles'] ) ) );
	}

	if ( isset( $_POST['byline_feed_now_url'] ) ) {
		$now_url = esc_url_raw( wp_unslash( $_POST['byline_feed_now_url'] ) );
	}

	if ( isset( $_POST['byline_feed_uses_url'] ) ) {
		$uses_url = esc_url_raw( wp_unslash( $_POST['byline_feed_uses_url'] ) );
	}

	if ( empty( $profiles ) ) {
		delete_user_meta( $user_id, 'byline_feed_profiles' );
	} else {
		update_user_meta( $user_id, 'byline_feed_profiles', $profiles );
	}

	if ( '' === $now_url ) {
		delete_user_meta( $user_id, 'byline_feed_now_url' );
	} else {
		update_user_meta( $user_id, 'byline_feed_now_url', $now_url );
	}

	if ( '' === $uses_url ) {
		delete_user_meta( $user_id, 'byline_feed_uses_url' );
	} else {
		update_user_meta( $user_id, 'byline_feed_uses_url', $uses_url );
	}
}
