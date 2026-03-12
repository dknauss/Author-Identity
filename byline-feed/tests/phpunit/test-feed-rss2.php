<?php
/**
 * Tests for RSS2 Byline feed output.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed\Tests;

use WP_UnitTestCase;
use function Byline_Feed\Feed_RSS2\output_contributors;
use function Byline_Feed\Feed_RSS2\output_item;
use function Byline_Feed\Feed_RSS2\output_namespace;

class Test_Feed_RSS2 extends WP_UnitTestCase {

	/**
	 * Capture output from a callback.
	 *
	 * @param callable $callback Callback to execute.
	 * @return string
	 */
	private function capture_output( callable $callback ): string {
		ob_start();
		$callback();
		return (string) ob_get_clean();
	}

	/**
	 * Set the global feed query posts used by output_contributors().
	 *
	 * @param int[] $post_ids Post IDs.
	 */
	private function set_feed_posts( array $post_ids ): void {
		global $wp_query;

		$wp_query        = new \WP_Query();
		$wp_query->posts = array_map( 'get_post', $post_ids );
	}

	/**
	 * Set current global post used by output_item().
	 *
	 * @param int $post_id Post ID.
	 */
	private function set_current_post( int $post_id ): void {
		global $post;

		$post = get_post( $post_id );
		setup_postdata( $post );
	}

	public function tear_down(): void {
		wp_reset_postdata();
		parent::tear_down();
	}

	public function test_byline_namespace_is_declared(): void {
		$feed = $this->capture_output(
			static function () {
				output_namespace();
			}
		);

		$this->assertStringContainsString(
			'xmlns:byline="https://bylinespec.org/1.0"',
			$feed
		);
	}

	public function test_contributors_block_present(): void {
		$user_id = self::factory()->user->create( array(
			'display_name' => 'Test Author',
		) );

		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		$this->set_feed_posts( array( $post_id ) );

		$feed = $this->capture_output(
			static function () {
				output_contributors();
			}
		);

		$this->assertStringContainsString( '<byline:contributors>', $feed );
		$this->assertStringContainsString( '</byline:contributors>', $feed );
		$this->assertStringContainsString( '<byline:person', $feed );
		$this->assertStringContainsString( '<byline:name>Test Author</byline:name>', $feed );
	}

	public function test_item_author_ref_present(): void {
		$user_id = self::factory()->user->create( array(
			'user_nicename' => 'test-author',
		) );

		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		$this->set_current_post( $post_id );

		$feed = $this->capture_output(
			static function () {
				output_item();
			}
		);

		$this->assertStringContainsString( '<byline:author ref="test-author"/>', $feed );
	}

	public function test_perspective_in_feed_when_set(): void {
		$user_id = self::factory()->user->create();
		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		update_post_meta( $post_id, '_byline_perspective', 'analysis' );

		$this->set_current_post( $post_id );

		$feed = $this->capture_output(
			static function () {
				output_item();
			}
		);

		$this->assertStringContainsString(
			'<byline:perspective>analysis</byline:perspective>',
			$feed
		);
	}

	public function test_no_perspective_when_unset(): void {
		$user_id = self::factory()->user->create();
		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		$this->set_current_post( $post_id );

		$feed = $this->capture_output(
			static function () {
				output_item();
			}
		);

		$this->assertStringNotContainsString( '<byline:perspective>', $feed );
	}

	public function test_feed_is_well_formed_xml(): void {
		$user_id = self::factory()->user->create( array(
			'display_name' => 'XML Test Author',
		) );

		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		$this->set_feed_posts( array( $post_id ) );
		$this->set_current_post( $post_id );

		$contributors = $this->capture_output(
			static function () {
				output_contributors();
			}
		);

		$item = $this->capture_output(
			static function () {
				output_item();
			}
		);

		$feed = '<rss xmlns:byline="https://bylinespec.org/1.0"><channel>' . $contributors . '<item>' . $item . '</item></channel></rss>';
		$xml  = simplexml_load_string( $feed );

		$this->assertNotFalse( $xml, 'Feed output must be well-formed XML.' );
	}
}
