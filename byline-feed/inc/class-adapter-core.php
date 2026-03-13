<?php
/**
 * Core WordPress fallback adapter.
 *
 * Used when no multi-author plugin is active. Reads the standard
 * post_author field and returns a single-element author array.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed;

defined( 'ABSPATH' ) || exit;

/**
 * Core WordPress adapter implementation.
 */
class Adapter_Core implements Adapter {

	/**
	 * Get normalized authors for a post using core WordPress authorship.
	 *
	 * @param \WP_Post $post Post object.
	 * @return object[]
	 */
	public function get_authors( \WP_Post $post ): array {
		$user = get_userdata( (int) $post->post_author );

		if ( ! $user ) {
			return array();
		}

		$fediverse = get_user_meta( $user->ID, 'byline_feed_fediverse', true );
		if ( ! is_string( $fediverse ) ) {
			$fediverse = '';
		}

		$ai_consent = get_user_meta( $user->ID, 'byline_feed_ai_consent', true );
		if ( ! is_string( $ai_consent ) ) {
			$ai_consent = '';
		}

		return array(
			(object) array(
				'id'           => $user->user_nicename,
				'display_name' => $user->display_name,
				'description'  => $user->description,
				'url'          => $user->user_url,
				'avatar_url'   => get_avatar_url( $user->ID ),
				'user_id'      => $user->ID,
				'role'         => get_byline_role_from_user( $user ),
				'is_guest'     => false,
				'profiles'     => get_byline_feed_profiles_for_user( $user->ID ),
				'now_url'      => get_byline_feed_now_url_for_user( $user->ID ),
				'uses_url'     => get_byline_feed_uses_url_for_user( $user->ID ),
				'fediverse'    => $fediverse,
				'ai_consent'   => $ai_consent,
			),
		);
	}
}
