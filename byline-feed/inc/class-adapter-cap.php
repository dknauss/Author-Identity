<?php
/**
 * Co-Authors Plus adapter.
 *
 * Resolves authors via get_coauthors() and normalizes them
 * into the Byline Feed author contract.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed;

defined( 'ABSPATH' ) || exit;

/**
 * Co-Authors Plus adapter implementation.
 */
class Adapter_CAP implements Adapter {

	/**
	 * Get normalized authors for a post using Co-Authors Plus.
	 *
	 * @param \WP_Post $post Post object.
	 * @return object[]
	 */
	public function get_authors( \WP_Post $post ): array {
		if ( ! function_exists( 'get_coauthors' ) ) {
			return array();
		}

		$coauthors = get_coauthors( $post->ID );

		return array_map( array( $this, 'normalize' ), $coauthors );
	}

	/**
	 * Normalize a Co-Authors Plus coauthor object.
	 *
	 * @param object $coauthor A CAP coauthor object.
	 * @return object Normalized author object.
	 */
	private function normalize( object $coauthor ): object {
		$is_guest = ( $coauthor->type ?? 'wpuser' ) === 'guest-author';
		$user_id  = $is_guest ? 0 : ( $coauthor->ID ?? 0 );
		$user     = $user_id ? get_userdata( $user_id ) : null;

		$role = $is_guest ? 'guest' : get_byline_role_from_user( $user );

		/**
		 * Filters the Byline role for an author.
		 *
		 * @param string   $role    The computed role.
		 * @param object   $author  The normalized author object (partially built).
		 * @param \WP_Post $post    Not available in this context — use byline_feed_authors filter instead.
		 */
		$role = apply_filters( 'byline_feed_role', $role, $coauthor, null );

		$fediverse  = '';
		$ai_consent = '';

		if ( $user_id ) {
			$fediverse_meta  = get_user_meta( $user_id, 'byline_feed_fediverse', true );
			$ai_consent_meta = get_user_meta( $user_id, 'byline_feed_ai_consent', true );
			$fediverse       = is_string( $fediverse_meta ) ? $fediverse_meta : '';
			$ai_consent      = is_string( $ai_consent_meta ) ? $ai_consent_meta : '';
		}

		return (object) array(
			'id'           => $coauthor->user_nicename ?? '',
			'display_name' => $coauthor->display_name ?? '',
			'description'  => $coauthor->description ?? '',
			'url'          => $coauthor->website ?? '',
			'avatar_url'   => get_avatar_url( $coauthor->ID ?? 0 ),
			'user_id'      => $user_id,
			'role'         => $role,
			'is_guest'     => $is_guest,
			'profiles'     => array(),
			'now_url'      => '',
			'uses_url'     => '',
			'fediverse'    => $fediverse,
			'ai_consent'   => $ai_consent,
		);
	}
}
