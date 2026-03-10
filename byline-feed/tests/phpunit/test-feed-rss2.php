<?php
/**
 * Tests for RSS2 Byline feed output.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed\Tests;

use WP_UnitTestCase;

class Test_Feed_RSS2 extends WP_UnitTestCase {

	/**
	 * Get RSS2 feed output for the site.
	 *
	 * @return string The feed XML.
	 */
	private function get_rss2_feed(): string {
		ob_start();
		// Trigger the RSS2 feed template.
		$this->go_to( '/?feed=rss2' );
		do_feed_rss2( false );
		return ob_get_clean();
	}

	public function test_byline_namespace_is_declared(): void {
		self::factory()->post->create();

		$feed = $this->get_rss2_feed();

		$this->assertStringContainsString(
			'xmlns:byline="https://bylinespec.org/1.0"',
			$feed
		);
	}

	public function test_contributors_block_present(): void {
		$user_id = self::factory()->user->create( array(
			'display_name' => 'Test Author',
		) );

		self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		$feed = $this->get_rss2_feed();

		$this->assertStringContainsString( '<byline:contributors>', $feed );
		$this->assertStringContainsString( '</byline:contributors>', $feed );
		$this->assertStringContainsString( '<byline:person', $feed );
		$this->assertStringContainsString( '<byline:name>Test Author</byline:name>', $feed );
	}

	public function test_item_author_ref_present(): void {
		$user_id = self::factory()->user->create( array(
			'user_nicename' => 'test-author',
		) );

		self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		$feed = $this->get_rss2_feed();

		$this->assertStringContainsString( '<byline:author ref="test-author"/>', $feed );
	}

	public function test_perspective_in_feed_when_set(): void {
		$user_id = self::factory()->user->create();
		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		update_post_meta( $post_id, '_byline_perspective', 'analysis' );

		$feed = $this->get_rss2_feed();

		$this->assertStringContainsString(
			'<byline:perspective>analysis</byline:perspective>',
			$feed
		);
	}

	public function test_no_perspective_when_unset(): void {
		$user_id = self::factory()->user->create();
		self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		$feed = $this->get_rss2_feed();

		$this->assertStringNotContainsString( '<byline:perspective>', $feed );
	}

	public function test_feed_is_well_formed_xml(): void {
		$user_id = self::factory()->user->create( array(
			'display_name' => 'XML Test Author',
		) );

		self::factory()->post->create( array(
			'post_author' => $user_id,
			'post_status' => 'publish',
		) );

		$feed = $this->get_rss2_feed();
		$xml  = simplexml_load_string( $feed );

		$this->assertNotFalse( $xml, 'Feed output must be well-formed XML.' );
	}
}
