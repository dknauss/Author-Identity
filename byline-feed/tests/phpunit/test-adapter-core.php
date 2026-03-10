<?php
/**
 * Tests for the Core WordPress adapter.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed\Tests;

use Byline_Feed\Adapter_Core;
use WP_UnitTestCase;

class Test_Adapter_Core extends WP_UnitTestCase {

	/**
	 * @var Adapter_Core
	 */
	private $adapter;

	public function set_up(): void {
		parent::set_up();
		$this->adapter = new Adapter_Core();
	}

	public function test_returns_single_author_for_standard_post(): void {
		$user_id = self::factory()->user->create( array(
			'display_name'  => 'Jane Doe',
			'user_nicename' => 'jane-doe',
			'description'   => 'A test author.',
			'user_url'      => 'https://example.com',
		) );

		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
		) );

		$post    = get_post( $post_id );
		$authors = $this->adapter->get_authors( $post );

		$this->assertCount( 1, $authors );
		$this->assertSame( 'jane-doe', $authors[0]->id );
		$this->assertSame( 'Jane Doe', $authors[0]->display_name );
		$this->assertSame( 'A test author.', $authors[0]->description );
		$this->assertSame( 'https://example.com', $authors[0]->url );
		$this->assertFalse( $authors[0]->is_guest );
		$this->assertSame( $user_id, $authors[0]->user_id );
	}

	public function test_returns_empty_for_invalid_author(): void {
		$post_id = self::factory()->post->create( array(
			'post_author' => 0,
		) );

		$post    = get_post( $post_id );
		$authors = $this->adapter->get_authors( $post );

		$this->assertSame( array(), $authors );
	}

	public function test_role_is_staff_for_editor(): void {
		$user_id = self::factory()->user->create( array(
			'role' => 'editor',
		) );

		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
		) );

		$post    = get_post( $post_id );
		$authors = $this->adapter->get_authors( $post );

		$this->assertSame( 'staff', $authors[0]->role );
	}

	public function test_role_is_contributor_for_author_role(): void {
		$user_id = self::factory()->user->create( array(
			'role' => 'author',
		) );

		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
		) );

		$post    = get_post( $post_id );
		$authors = $this->adapter->get_authors( $post );

		$this->assertSame( 'contributor', $authors[0]->role );
	}

	public function test_all_optional_fields_have_zero_values(): void {
		$user_id = self::factory()->user->create( array(
			'display_name'  => 'Minimal User',
			'user_nicename' => 'minimal',
		) );

		$post_id = self::factory()->post->create( array(
			'post_author' => $user_id,
		) );

		$post    = get_post( $post_id );
		$authors = $this->adapter->get_authors( $post );
		$author  = $authors[0];

		$this->assertIsArray( $author->profiles );
		$this->assertEmpty( $author->profiles );
		$this->assertSame( '', $author->now_url );
		$this->assertSame( '', $author->uses_url );
		$this->assertSame( '', $author->fediverse );
		$this->assertSame( '', $author->ai_consent );
	}
}
