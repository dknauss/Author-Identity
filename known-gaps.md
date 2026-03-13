# Known Gaps, Security Notes, and Hardening Opportunities

This document supplements the Phase 01 audit (`docs/audit/HM_WPCS_audit.md`) with additional findings from the source-level architecture review.

## Security

### Guest author login is not actively blocked

**Severity:** Low (mitigated by design, but defense-in-depth gap)

Guest authors are `WP_User` rows with the `guest-author` role (zero capabilities). Authorship does not register an `authenticate` filter or `wp_login` action to prevent login. The defense relies on:

- Passwords are random 24-character strings generated at creation time, never returned to any caller.
- Email is set to empty string by default, preventing password reset.
- Even if authenticated, the session has zero capabilities.

**Risk scenario:** An Administrator creates a guest author with an email address. The guest author uses WordPress's password reset flow to obtain credentials. They log in with an empty-capability session. The session itself may have side effects with plugins that check `is_user_logged_in()` rather than specific capabilities.

**Recommendation:** Add an `authenticate` filter that returns `WP_Error` for users whose only role is `guest-author`. This is a one-line defense-in-depth addition:

```php
add_filter( 'authenticate', function( $user ) {
    if ( $user instanceof WP_User && in_array( GUEST_ROLE, $user->roles, true ) && count( $user->roles ) === 1 ) {
        return new WP_Error( 'guest_author_login_blocked', __( 'Guest authors cannot log in.', 'authorship' ) );
    }
    return $user;
}, 100, 1 );
```

### Guest author username normalization

**Severity:** Low (edge case)

`create_item()` in `class-users-controller.php:194-195` derives usernames from the display name:

```php
$username = sanitize_title( sanitize_user( $request->get_param( 'name' ), true ) );
$username = preg_replace( '/[^a-z0-9]/', '', $username );
```

This can produce empty strings for non-ASCII names (e.g., Japanese, Arabic, Chinese names) or collide for near-duplicate display names. See `docs/audit/patch_scaffolds/01-02-security_build.md` for the planned hardening.

### Signup validation filter scope

**Severity:** Low (code hygiene)

`create_item()` adds an anonymous `wpmu_validate_user_signup` filter and never removes it. This is a request-scoped side effect that is inconsistent with the pattern used in `get_items()` where the filter is explicitly removed after use. See `docs/audit/patch_scaffolds/01-02-security_build.md`.

## Data integrity

### Post-insert author assignment failures are silent

`InsertPostHandler::action_wp_insert_post()` catches exceptions from `set_authors()` and discards them. This means author attribution can silently fail during post save, migration, or programmatic post creation. The REST API path handles the same exceptions by returning `WP_Error`. See `docs/audit/patch_scaffolds/01-02-observability_build.md`.

### `post_author` field divergence

WordPress core's `post_author` field on `wp_posts` is not the source of truth for Authorship — the hidden taxonomy is. However, `post_author` continues to exist and may be set/read by other plugins and themes. Authorship does not currently synchronize `post_author` with the first attributed author.

This can cause divergence where `$post->post_author` says user A but Authorship says users B and C. Theme code that reads `post_author` directly (rather than using `the_author()` or Authorship's template functions) will show stale data.

### Object cache considerations

Taxonomy term lookups and `get_users()` calls are cached by WordPress's object cache. On persistent cache backends (Redis, Memcached), stale cache entries after attribution changes could show incorrect authors. Authorship relies on WordPress's built-in cache invalidation for `wp_set_post_terms()` and `get_users()`, which is generally correct but worth noting for debugging.

## Performance

### Author archive queries

Author archives use the `action_pre_get_posts()` taxonomy rewrite, which converts `author` and `author_name` query vars into `tax_query` clauses. This is more performant than a post meta query but involves an additional join compared to the native `post_author` column index.

On sites with Elasticsearch (e.g., WordPress VIP), this is likely irrelevant as the taxonomy query will be handled by ES. On MySQL-only sites with very large post tables, the join performance should be tested.

### Editor component render behavior

`AuthorsSelect.tsx` performs state initialization and can trigger `apiFetch()` from render-time conditionals. See `docs/audit/patch_scaffolds/01-02-performance_build.md`.

## Feed output limitations

**HM Authorship (upstream)** has minimal feed support: RSS2 outputs a comma-separated name list via `the_author` filter, Atom has no Authorship-specific handling, JSON Feed is not addressed, there is no `dc:creator` output for individual co-authors, and no Schema.org / JSON-LD author metadata in feeds.

**Byline Feed plugin (this project)** addresses these gaps. Current status:

- RSS2: Byline namespace, channel-level contributors, per-item author refs with role and perspective. Implemented in `inc/feed-rss2.php`.
- Atom: Parallel Byline implementation. Implemented in `inc/feed-atom.php`.
- JSON Feed: Byline `_byline` extension on standard `authors` entries, standalone `/feed/json` endpoint with fallback. Implemented in `inc/feed-json.php`.
- Standard `<author>`, `<dc:creator>`, and Atom `<author>` elements are preserved. Byline output is additive.

Remaining feed gaps: no `dc:creator` per co-author (standard WordPress limitation), no JSON-LD in feeds (addressed by WP-05 in HTML head instead).

See `byline-spec-plan.md` for the Byline assessment, `implementation-spec.md` for the work package roadmap, and `first-implementor-notes.md` for feedback from implementation experience.

## Byline extension issues (first-implementor findings)

These are ambiguities and gaps in the Byline extension vocabulary discovered during implementation. They are documented in detail in `first-implementor-notes.md` and should be submitted as issues or PRs to the Byline repository.

### `byline:role` is ambiguous with multiple authors per item

**Severity:** Medium (affects all multi-author output)

The spec defines `byline:role` as a standalone per-item element. When multiple `byline:author ref` elements are present, it is unclear which author the role applies to. The current `feed-rss2.php` output emits a `byline:role` after each `byline:author ref`, relying on document order — but XML elements are unordered, so parsers that sort or regroup elements will lose the association.

**Proposed resolution:** Add `role` as an attribute on `byline:author`: `<byline:author ref="jdoe" role="creator"/>`. The standalone `byline:role` element could remain as an item-level default that the attribute overrides. See `first-implementor-notes.md § 1` for the full analysis and code examples.

### No canonical JSON Feed mapping

**Severity:** Medium (prevents interoperable JSON Feed implementations)

Byline extends RSS 2.0, Atom, and JSON Feed, but no canonical JSON Feed mapping is documented. JSON Feed's extension mechanism (underscore-prefixed properties) is freeform, so without a defined `_byline` object shape, implementors will invent incompatible mappings.

**Proposed resolution:** Document a canonical `_byline` extension object that sits inside standard JSON Feed `authors` entries. See `first-implementor-notes.md § 2` and `inc/feed-json.php` for the proposed mapping and reference implementation.

### No channel-level perspective default

**Severity:** Low (editorial friction)

Single-purpose sites (tutorial blogs, news outlets, satire publications) would need to set perspective on every post. A channel-level default with per-item override would reduce editorial friction and improve adoption.

**Proposed resolution:** Support a channel-level `byline:perspective` that items inherit unless overridden. Already implementable via the `byline_feed_perspective` filter, but the pattern should be documented in the spec.

## Compatibility

### WordPress version

Plugin header declares `Requires at least: 5.4`, tested up to 6.2. The 6.2 cap is stale — the plugin likely works with current WordPress but testing has not been updated.

### PHP version

Plugin requires PHP 7.2+. Tooling (PHPCS, PHPStan) is pinned to PHP 7.4 and does not run on PHP 8.5 without deprecation suppression. See `docs/audit/foundation-quality-baseline.md`.

### Multisite

The plugin has multisite-specific tests and uses `'blog_id' => 0` in `get_users()` calls to search across all sites. Guest authors created on one site exist in the shared `wp_users` table.

### Theme compatibility

Authorship intercepts `the_author`, author query vars, and capability checks transparently. Themes that use standard WordPress template tags (`the_author()`, `get_the_author()`, author archive templates) will work. Themes that read `$post->post_author` directly may show stale data (see `post_author` divergence above).

### Plugin compatibility

Co-Authors Plus and PublishPress Authors both use the `author` taxonomy slug. Authorship uses `authorship`. These should not conflict if multiple plugins are active, though running multiple multi-author plugins simultaneously is not recommended. Authorship provides WP-CLI migration commands for both CAP and PPA data.
