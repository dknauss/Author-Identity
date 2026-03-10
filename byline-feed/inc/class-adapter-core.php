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

class Adapter_Core implements Adapter {

	/**
	 * {@inheritDoc}
	 */
	public function get_authors( \WP_Post $post ): array {
		$user = get_userdata( (int) $post->post_author );

		if ( ! $user ) {
			return array();
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
				'profiles'     => array(),
				'now_url'      => '',
				'uses_url'     => '',
				'fediverse'    => get_user_meta( $user->ID, 'byline_feed_fediverse', true ) ?: '',
				'ai_consent'   => get_user_meta( $user->ID, 'byline_feed_ai_consent', true ) ?: '',
			),
		);
	}
}
