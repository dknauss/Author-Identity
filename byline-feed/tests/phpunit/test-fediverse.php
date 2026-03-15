<?php
/**
 * Tests for fediverse:creator meta tag output.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed\Tests;

use WP_UnitTestCase;
use function Byline_Feed\Fediverse\render_meta_tags;

class Test_Fediverse extends WP_UnitTestCase {

	/**
	 * Capture fediverse meta-tag output for a given URL.
	 *
	 * @param string $url URL to route the test request to.
	 * @return string
	 */
	private function capture_meta_output( string $url ): string {
		$this->go_to( $url );

		ob_start();
		render_meta_tags();
		return (string) ob_get_clean();
	}

	public function tear_down(): void {
		remove_all_filters( 'byline_feed_authors' );
		remove_all_filters( 'byline_feed_fediverse_handle' );
		parent::tear_down();
	}

	public function test_single_author_with_handle_outputs_meta_tag(): void {
		$user_id = self::factory()->user->create(
			array(
				'display_name'  => 'Fediverse Author',
				'user_nicename' => 'fediverse-author',
			)
		);

		update_user_meta( $user_id, 'byline_feed_fediverse', '@author@example.social' );

		$post_id = self::factory()->post->create(
			array(
				'post_author' => $user_id,
				'post_status' => 'publish',
				'post_title'  => 'Fediverse Post',
			)
		);

		$output = $this->capture_meta_output( get_permalink( $post_id ) );

		$this->assertStringContainsString( 'name="fediverse:creator"', $output );
		$this->assertStringContainsString( 'content="@author@example.social"', $output );
	}

	public function test_single_author_without_handle_outputs_no_meta_tag(): void {
		$user_id = self::factory()->user->create();
		$post_id = self::factory()->post->create(
			array(
				'post_author' => $user_id,
				'post_status' => 'publish',
			)
		);

		$output = $this->capture_meta_output( get_permalink( $post_id ) );

		$this->assertSame( '', $output );
	}

	public function test_multiple_authors_output_one_meta_tag_per_handle(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Multiple Authors',
			)
		);

		add_filter(
			'byline_feed_authors',
			static function ( $authors, $post ) use ( $post_id ) {
				if ( $post->ID !== $post_id ) {
					return $authors;
				}

				return array(
					(object) array(
						'id'           => 'first-author',
						'display_name' => 'First Author',
						'fediverse'    => '@first@example.social',
					),
					(object) array(
						'id'           => 'second-author',
						'display_name' => 'Second Author',
						'fediverse'    => '@second@example.social',
					),
					(object) array(
						'id'           => 'third-author',
						'display_name' => 'Third Author',
						'fediverse'    => '',
					),
				);
			},
			10,
			2
		);

		$output = $this->capture_meta_output( get_permalink( $post_id ) );

		$this->assertSame( 2, substr_count( $output, 'name="fediverse:creator"' ) );
		$this->assertStringContainsString( 'content="@first@example.social"', $output );
		$this->assertStringContainsString( 'content="@second@example.social"', $output );
	}

	public function test_handle_is_normalized_to_leading_at_before_output(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Normalized Handle',
			)
		);

		add_filter(
			'byline_feed_authors',
			static function ( $authors, $post ) use ( $post_id ) {
				if ( $post->ID !== $post_id ) {
					return $authors;
				}

				return array(
					(object) array(
						'id'           => 'normalized-author',
						'display_name' => 'Normalized Author',
						'fediverse'    => 'normalized@example.social',
					),
				);
			},
			10,
			2
		);

		$output = $this->capture_meta_output( get_permalink( $post_id ) );

		$this->assertStringContainsString( 'content="@normalized@example.social"', $output );
	}

	public function test_filter_can_override_fediverse_handle(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Filtered Handle',
			)
		);

		add_filter(
			'byline_feed_authors',
			static function ( $authors, $post ) use ( $post_id ) {
				if ( $post->ID !== $post_id ) {
					return $authors;
				}

				return array(
					(object) array(
						'id'           => 'filtered-author',
						'display_name' => 'Filtered Author',
						'fediverse'    => '@ignored@example.social',
					),
				);
			},
			10,
			2
		);

		add_filter(
			'byline_feed_fediverse_handle',
			static function ( string $handle, object $author ): string {
				if ( 'filtered-author' !== $author->id ) {
					return $handle;
				}

				return 'override@example.social';
			},
			10,
			2
		);

		$output = $this->capture_meta_output( get_permalink( $post_id ) );

		$this->assertStringContainsString( 'content="@override@example.social"', $output );
		$this->assertStringNotContainsString( 'content="@ignored@example.social"', $output );
	}

	public function test_non_singular_routes_output_no_meta_tags(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Archive Gap',
			)
		);

		add_filter(
			'byline_feed_authors',
			static function ( $authors, $post ) use ( $post_id ) {
				if ( $post->ID !== $post_id ) {
					return $authors;
				}

				return array(
					(object) array(
						'id'           => 'archive-author',
						'display_name' => 'Archive Author',
						'fediverse'    => '@archive@example.social',
					),
				);
			},
			10,
			2
		);

		$output = $this->capture_meta_output( home_url( '/' ) );

		$this->assertSame( '', $output );
	}

	public function test_profiles_and_ap_actor_url_do_not_substitute_for_handle_output(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'No Handle Substitution',
			)
		);

		add_filter(
			'byline_feed_authors',
			static function ( $authors, $post ) use ( $post_id ) {
				if ( $post->ID !== $post_id ) {
					return $authors;
				}

				return array(
					(object) array(
						'id'           => 'profile-only-author',
						'display_name' => 'Profile Only Author',
						'profiles'     => array(
							array(
								'rel'  => 'me',
								'href' => 'https://mastodon.social/@profile-only-author',
							),
						),
						'ap_actor_url' => 'https://example.com/?author=42',
					),
				);
			},
			10,
			2
		);

		$output = $this->capture_meta_output( get_permalink( $post_id ) );

		$this->assertSame( '', $output );
	}
}
