<?php
/**
 * PublishPress Authors adapter.
 *
 * Resolves authors via publishpress_authors_get_post_authors() and
 * normalizes them into the Byline Feed author contract.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed;

defined( 'ABSPATH' ) || exit;

class Adapter_PPA implements Adapter {

	/**
	 * {@inheritDoc}
	 */
	public function get_authors( \WP_Post $post ): array {
		if ( function_exists( 'publishpress_authors_get_post_authors' ) ) {
			$authors = publishpress_authors_get_post_authors( $post->ID );
		} elseif ( function_exists( 'get_post_authors' ) ) {
			$authors = get_post_authors( $post->ID );
		} else {
			return array();
		}

		if ( ! is_array( $authors ) ) {
			return array();
		}

		return array_map( array( $this, 'normalize' ), $authors );
	}

	/**
	 * Normalize a PublishPress Authors author object.
	 *
	 * @param object $author A PPA author object.
	 * @return object Normalized author object.
	 */
	private function normalize( object $author ): object {
		$is_guest = ! empty( $author->is_guest );
		$user_id  = $author->user_id ?? 0;
		$user     = $user_id ? get_userdata( $user_id ) : null;
		$term_id  = $author->term_id ?? 0;

		$role = $is_guest ? 'guest' : get_byline_role_from_user( $user );
		$role = apply_filters( 'byline_feed_role', $role, $author, null );

		// PPA stores profile data in term meta for guest authors, user meta for linked users.
		$description = '';
		$url         = '';
		$avatar_url  = '';

		if ( $term_id ) {
			$description = get_term_meta( $term_id, 'description', true ) ?: '';
			$avatar_url  = get_term_meta( $term_id, 'avatar', true ) ?: '';
		}

		if ( $user ) {
			$description = $description ?: $user->description;
			$url         = $user->user_url;
			$avatar_url  = $avatar_url ?: get_avatar_url( $user->ID );
		}

		return (object) array(
			'id'           => $author->slug ?? '',
			'display_name' => $author->display_name ?? '',
			'description'  => $description,
			'url'          => $url,
			'avatar_url'   => $avatar_url,
			'user_id'      => (int) $user_id,
			'role'         => $role,
			'is_guest'     => $is_guest,
			'profiles'     => array(),
			'now_url'      => '',
			'uses_url'     => '',
			'fediverse'    => $user_id ? ( get_user_meta( $user_id, 'byline_feed_fediverse', true ) ?: '' ) : '',
			'ai_consent'   => $user_id ? ( get_user_meta( $user_id, 'byline_feed_ai_consent', true ) ?: '' ) : '',
		);
	}
}
