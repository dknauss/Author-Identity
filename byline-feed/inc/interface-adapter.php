<?php
/**
 * Adapter interface.
 *
 * Every multi-author plugin adapter must implement this interface.
 * The adapter layer is the load-bearing abstraction: all output
 * channels consume the same normalized author array that adapters produce.
 *
 * @package Byline_Feed
 */

namespace Byline_Feed;

defined( 'ABSPATH' ) || exit;

interface Adapter {

	/**
	 * Returns an ordered array of normalized author objects for the given post.
	 *
	 * Each object conforms to the normalized author contract defined in
	 * the implementation spec. Required fields: id, display_name.
	 * Optional fields must be set to their zero-value if unavailable.
	 *
	 * @param \WP_Post $post The post to resolve authors for.
	 * @return object[] Ordered array of author objects.
	 */
	public function get_authors( \WP_Post $post ): array;
}
