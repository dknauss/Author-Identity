<?php
/**
 * Plugin Name: Byline Feed Playground Demo
 * Description: Deterministic author fixtures for the Byline Feed output-demo blueprint.
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'byline_feed_authors',
	static function ( array $authors, WP_Post $post ): array {
		if ( 'post' !== $post->post_type ) {
			return $authors;
		}

		return array(
			(object) array(
				'id'           => 'jane-editor',
				'display_name' => 'Jane Editor',
				'description'  => 'Investigative editor covering publishing systems and attribution.',
				'url'          => 'https://example.org/authors/jane-editor',
				'avatar_url'   => 'https://secure.gravatar.com/avatar/11111111111111111111111111111111?s=96&d=identicon&r=g',
				'user_id'      => 0,
				'role'         => 'staff',
				'is_guest'     => false,
				'profiles'     => array(
					array(
						'href' => 'https://example.org/authors/jane-editor',
						'rel'  => 'author',
					),
					array(
						'href' => 'https://mastodon.example/@janeeditor',
						'rel'  => 'me',
					),
				),
				'now_url'      => 'https://example.org/now',
				'uses_url'     => 'https://example.org/uses',
				'fediverse'    => '@janeeditor@mastodon.example',
				'ap_actor_url' => 'https://mastodon.example/users/janeeditor',
				'ai_consent'   => '',
			),
			(object) array(
				'id'           => 'sam-guest',
				'display_name' => 'Sam Guest',
				'description'  => 'Guest contributor focused on AI policy and publishing rights.',
				'url'          => '',
				'avatar_url'   => 'https://secure.gravatar.com/avatar/22222222222222222222222222222222?s=96&d=identicon&r=g',
				'user_id'      => 0,
				'role'         => 'guest',
				'is_guest'     => true,
				'profiles'     => array(
					array(
						'href' => 'https://social.example/@samguest',
						'rel'  => 'me',
					),
				),
				'now_url'      => '',
				'uses_url'     => '',
				'fediverse'    => '@samguest@social.example',
				'ap_actor_url' => '',
				'ai_consent'   => '',
			),
		);
	},
	10,
	2
);
