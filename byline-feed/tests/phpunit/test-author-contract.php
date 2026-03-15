<?php
/**
 * Tests for normalized author contract validation.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed\Tests;

use Byline_Feed\Adapter;
use WP_UnitTestCase;
use function Byline_Feed\byline_feed_get_authors;

class Test_Author_Contract extends WP_UnitTestCase {

	public function set_up(): void {
		parent::set_up();

		global $_byline_feed_adapter;

		$_byline_feed_adapter = null;
	}

	public function tear_down(): void {
		global $_byline_feed_adapter;

		remove_all_filters( 'byline_feed_adapter' );
		remove_all_filters( 'byline_feed_authors' );
		$_byline_feed_adapter = null;

		parent::tear_down();
	}

	public function test_invalid_required_fields_are_dropped(): void {
		$post_id = self::factory()->post->create();
		$post    = get_post( $post_id );

		add_filter(
			'byline_feed_adapter',
			static function () {
				return new class() implements Adapter {
					public function get_authors( \WP_Post $post ): array {
						return array(
							(object) array(
								'id' => '',
							),
							(object) array(
								'id'           => 'valid-author',
								'display_name' => 'Valid Author',
							),
						);
					}
				};
			}
		);

		$authors = byline_feed_get_authors( $post );

		$this->assertCount( 1, $authors );
		$this->assertSame( 'valid-author', $authors[0]->id );
	}

	public function test_non_object_entries_are_dropped(): void {
		$post_id = self::factory()->post->create();
		$post    = get_post( $post_id );

		add_filter(
			'byline_feed_adapter',
			static function () {
				return new class() implements Adapter {
					public function get_authors( \WP_Post $post ): array {
						return array(
							'invalid',
							(object) array(
								'id'           => 'valid-author',
								'display_name' => 'Valid Author',
							),
						);
					}
				};
			}
		);

		$authors = byline_feed_get_authors( $post );

		$this->assertCount( 1, $authors );
		$this->assertSame( 'valid-author', $authors[0]->id );
	}

	public function test_optional_fields_are_normalized_to_zero_values(): void {
		$post_id = self::factory()->post->create();
		$post    = get_post( $post_id );

		add_filter(
			'byline_feed_adapter',
			static function () {
				return new class() implements Adapter {
					public function get_authors( \WP_Post $post ): array {
						return array(
							(object) array(
								'id'           => 'partial-author',
								'display_name' => 'Partial Author',
								'user_id'      => '7',
								'is_guest'     => 1,
							),
						);
					}
				};
			}
		);

		$authors = byline_feed_get_authors( $post );
		$author  = $authors[0];

		$this->assertSame( '', $author->description );
		$this->assertSame( '', $author->url );
		$this->assertSame( '', $author->avatar_url );
		$this->assertSame( 7, $author->user_id );
		$this->assertSame( '', $author->role );
		$this->assertTrue( $author->is_guest );
		$this->assertSame( array(), $author->profiles );
		$this->assertSame( '', $author->now_url );
		$this->assertSame( '', $author->uses_url );
		$this->assertSame( '', $author->fediverse );
		$this->assertSame( '', $author->ap_actor_url );
		$this->assertSame( '', $author->ai_consent );
	}

	public function test_filtered_author_array_is_validated_after_filtering(): void {
		$post_id = self::factory()->post->create();
		$post    = get_post( $post_id );

		add_filter(
			'byline_feed_adapter',
			static function () {
				return new class() implements Adapter {
					public function get_authors( \WP_Post $post ): array {
						return array(
							(object) array(
								'id'           => 'adapter-author',
								'display_name' => 'Adapter Author',
							),
						);
					}
				};
			}
		);

		add_filter(
			'byline_feed_authors',
			static function () {
				return array(
					(object) array(
						'id'           => 'filtered-author',
						'display_name' => 'Filtered Author',
					),
					(object) array(
						'id' => 'broken-author',
					),
				);
			}
		);

		$authors = byline_feed_get_authors( $post );

		$this->assertCount( 1, $authors );
		$this->assertSame( 'filtered-author', $authors[0]->id );
	}

	public function test_invalid_contract_event_fires_when_entry_is_dropped(): void {
		$post_id  = self::factory()->post->create();
		$post     = get_post( $post_id );
		$messages = array();

		add_filter(
			'byline_feed_adapter',
			static function () {
				return new class() implements Adapter {
					public function get_authors( \WP_Post $post ): array {
						return array(
							(object) array(
								'id' => '',
							),
						);
					}
				};
			}
		);

		add_action(
			'byline_feed_invalid_author_contract',
			static function ( string $message ) use ( &$messages ): void {
				$messages[] = $message;
			}
		);

		$authors = byline_feed_get_authors( $post );

		$this->assertSame( array(), $authors );
		$this->assertCount( 1, $messages );
		$this->assertStringContainsString( 'missing a valid string id', $messages[0] );
	}

	public function test_profiles_are_normalized_to_valid_rel_href_pairs_only(): void {
		$post_id = self::factory()->post->create();
		$post    = get_post( $post_id );

		add_filter(
			'byline_feed_adapter',
			static function () {
				return new class() implements Adapter {
					public function get_authors( \WP_Post $post ): array {
						return array(
							(object) array(
								'id'           => 'profile-author',
								'display_name' => 'Profile Author',
								'profiles'     => array(
									array(
										'rel'  => 'me',
										'href' => 'https://example.com/@profile-author',
									),
									array(
										'rel'  => '',
										'href' => 'https://example.com/invalid',
									),
									array(
										'rel' => 'author',
									),
								),
							),
						);
					}
				};
			}
		);

		$authors = byline_feed_get_authors( $post );

		$this->assertCount( 1, $authors[0]->profiles );
		$this->assertSame( 'me', $authors[0]->profiles[0]['rel'] );
		$this->assertSame( 'https://example.com/@profile-author', $authors[0]->profiles[0]['href'] );
	}
}
