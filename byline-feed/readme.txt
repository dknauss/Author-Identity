=== Byline Feed ===
Contributors: dknauss
Tags: rss, atom, byline, author, attribution, feeds
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enriches RSS2 and Atom feeds with structured author identity metadata following the Byline specification.

== Description ==

Byline Feed adds machine-readable author metadata to WordPress feeds using the [Byline specification](https://bylinespec.org/). It is designed to work with multi-author editorial sites while preserving standard feed elements for compatibility.

Implementation-level output details, including example RSS2 and Atom fragments, are documented in the repository at `byline-feed/docs/output-reference.md`.

The plugin currently supports:

* RSS2 and Atom Byline output
* Co-Authors Plus adapter
* PublishPress Authors adapter
* Core WordPress fallback adapter
* Content Perspective editorial field
* Filter and action hooks for output customization

Byline Feed is additive. It preserves core feed elements such as `<author>` and `<dc:creator>` and adds Byline metadata alongside them.

== Features ==

* Adds `byline:contributors` with structured contributor profiles at feed level
* Adds item-level `byline:author`, `byline:role`, and `byline:perspective` elements
* Supports `byline:profile`, `byline:now`, and `byline:uses` from plugin-owned user meta
* Auto-detects Co-Authors Plus, PublishPress Authors, or falls back to core WordPress
* Adds a Content Perspective field in the block editor and classic editor
* Validates normalized author data before output
* Works without requiring a specific multi-author plugin

== Supported author sources ==

* Co-Authors Plus
* PublishPress Authors
* Core WordPress

== Installation ==

1. Upload the `byline-feed` folder to `/wp-content/plugins/`, or install it through the WordPress admin once published.
2. Activate the plugin through the Plugins screen in WordPress.
3. If you use Co-Authors Plus or PublishPress Authors, keep that plugin active.
4. Visit your RSS2 or Atom feed and inspect the output for Byline elements.

== Frequently Asked Questions ==

= Does this replace my SEO plugin? =

No. Byline Feed currently focuses on feed output and perspective metadata. JSON-LD and `fediverse:creator` output are planned, but are not part of the current release.

= What is the Byline spec? =

The Byline specification defines an XML namespace for structured author attribution in feeds. It allows consumers to distinguish contributors, roles, and editorial perspective in a way standard RSS fields do not.

= Which feed formats are supported? =

RSS2 and Atom are supported now.

= Where can I see the exact XML this plugin emits? =

See `byline-feed/docs/output-reference.md` in the project repository for current examples, hook references, and field mapping notes.

= Which multi-author plugins are supported? =

Co-Authors Plus and PublishPress Authors are supported directly. If neither is active, Byline Feed uses core WordPress author data.

= What is Content Perspective? =

Content Perspective is an editorial field that communicates the intent behind a piece of content, such as reporting, analysis, opinion, or satire. Feed consumers can use it to distinguish content types more clearly.

== Changelog ==

= 0.1.0 =
* Initial development release.
* Adapter layer with Co-Authors Plus, PublishPress Authors, and core WordPress support.
* RSS2 and Atom Byline namespace output.
* Content Perspective field with block editor panel and classic editor support.
* Test and CI baseline established for supported PHP and WordPress versions.
