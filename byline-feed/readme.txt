=== Byline Feed ===
Contributors: dknauss
Tags: rss, feed, byline, author, attribution
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enriches RSS and Atom feeds with structured author identity metadata following the Byline specification.

== Description ==

Byline Feed adds rich, machine-readable author metadata to your WordPress feeds using the [Byline specification](https://bylinespec.org). It works with any multi-author WordPress plugin or falls back to core WordPress author data.

**Key features:**

* Adds `byline:contributors` with full author profiles to feed channels
* Adds per-item `byline:author`, `byline:role`, and `byline:perspective` elements
* Auto-detects Co-Authors Plus, PublishPress Authors, or uses core WordPress
* Content Perspective editorial field with block editor sidebar panel
* Preserves standard `<author>` and `<dc:creator>` elements (additive, not replacive)
* Extensible via filters and actions

**Supported multi-author plugins:**

* Co-Authors Plus
* PublishPress Authors
* Core WordPress (fallback — always available)

== Installation ==

1. Upload the `byline-feed` folder to `/wp-content/plugins/`.
2. Activate the plugin through the Plugins menu.
3. Your RSS and Atom feeds now include Byline metadata automatically.

== Frequently Asked Questions ==

= Does this replace my SEO plugin? =

No. Byline Feed focuses on feed output. JSON-LD and fediverse:creator support are planned for future versions.

= What is the Byline spec? =

The Byline specification (bylinespec.org) defines a standard XML namespace for structured author attribution in RSS and Atom feeds.

= What is Content Perspective? =

An editorial field that communicates the intent behind a piece of content (e.g., reporting, analysis, opinion, satire). Feed readers can use this to help users distinguish content types.

== Changelog ==

= 0.1.0 =
* Initial development release.
* Adapter layer with Co-Authors Plus, PublishPress Authors, and Core WordPress support.
* RSS2 and Atom Byline namespace output.
* Content Perspective meta field with block editor panel and classic editor metabox.
