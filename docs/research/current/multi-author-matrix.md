# Multi-Author Plugin Implementation Matrix

A side-by-side comparison of WordPress multi-author systems — their data models, features, architectural trade-offs, and the gaps that motivated the [Byline Feed plugin](../../../byline-feed/).

This matrix synthesizes findings from the [landscape analysis](landscape.md), [HM Authorship architecture review](architecture.md), [known gaps audit](known-gaps.md), and [implementation spec](../../planning/implementation-spec.md). For the vision of what structured author identity should look like across all output channels, see [author-identity-vision.md](../../vision/author-identity-vision.md).

**Companion document:** [protocol-coverage-map.md](protocol-coverage-map.md) — covers the *output side*: which protocols carry which identity signals across feeds, HTML, HTTP headers, and federation.

---

## Systems compared

| System | Active installs | Last updated | Status | License |
| --- | --- | --- | --- | --- |
| **Co-Authors Plus** | ~20,000 | Oct 2025 | Semi-abandoned | GPLv2+ |
| **PublishPress Authors** | ~20,000 | Feb 2026 | Active | GPLv2+ (freemium) |
| **HM Authorship** | N/A (Composer) | Jun 2024 | Inactive | GPLv3 |
| **Molongui Authorship** | ~10,000 | Jan 2026 | Active | GPLv2+ (freemium) |
| **Simple Author Box** | ~60,000 | Dec 2025 | Active | GPLv2+ (freemium) |
| **Core WordPress** | All sites | Ongoing | N/A | GPLv2+ |

Simple Author Box is an author-box display plugin, not a multi-attribution system. Its high install count reflects demand for author bio boxes. It is excluded from the architectural comparisons below.

Historical lineage is documented in [landscape.md § Historical lineage](landscape.md#historical-lineage).

---

## Data model architecture

The fundamental architectural divide in this space is how authorship is stored. Every other design decision flows from this choice.

### Approach 1 — Taxonomy-as-author

**Used by:** Co-Authors Plus, PublishPress Authors, Byline (abandoned)

Authors exist as taxonomy terms. Profile data lives in term meta. The author-to-post relationship lives in `wp_term_relationships`. WordPress users may optionally be linked to their corresponding term, but the term is the canonical authorship entity.

| | Co-Authors Plus | PublishPress Authors |
| --- | --- | --- |
| **Taxonomy slug** | `author` | `author` |
| **Guest author entity** | Custom post type (`guest-author`) linked to taxonomy term | Pure taxonomy term, or `WP_User` with `guest-author` role |
| **Profile storage** | Post meta on the `guest-author` CPT | `wp_termmeta` on the author term |
| **Entity count** | 3 (`wp_users` + `author` terms + `guest-author` CPT) | 2 (`wp_termmeta` + optional `wp_users` link) |
| **Denormalized caches** | None | `ppma_authors_name` post meta per post |
| **Source of truth** | `author` taxonomy (not `post_author`) | `author` taxonomy (not `post_author`) |

**Trade-offs:** Efficient querying via `WP_Tax_Query`. No `wp_users` table pollution. But parallel data structures must be kept in sync, `post_author` diverges from actual authorship, and object types are inconsistent (sometimes `WP_User`, sometimes term, sometimes CPT object).

### Approach 2 — Users-only with hidden taxonomy bridge

**Used by:** HM Authorship

Every author — including guest authors — is a `WP_User`. A hidden custom taxonomy (`authorship`, registered with `public => false`, `show_in_rest => false`) serves as a relational bridge only. Taxonomy term slugs are user IDs cast to strings: user 42 gets term slug `"42"`. The taxonomy never surfaces to themes, API consumers, or the admin UI.

| | HM Authorship |
| --- | --- |
| **Taxonomy slug** | `authorship` (hidden) |
| **Guest author entity** | `WP_User` with custom `guest-author` role (zero capabilities) |
| **Profile storage** | Standard `wp_usermeta` |
| **Entity count** | 1 (`WP_User` is the only entity anyone interacts with) |
| **Source of truth** | `authorship` taxonomy (not `post_author`) |

**Trade-offs:** Single data model, consistent API surface, guest-to-user promotion is a role change. But guest authors create real `wp_users` rows (table growth), no active login prevention (see [known-gaps.md § Guest author login](known-gaps.md#guest-author-login-is-not-actively-blocked)), and username normalization fails for some non-ASCII names (see [known-gaps.md § Username normalization](known-gaps.md#guest-author-username-normalization)).

Full architecture walkthrough: [architecture.md](architecture.md).

### Approach 3 — Custom post type with post meta links

**Used by:** Molongui Authorship

Guest authors are a custom post type (`molongui_author`). Attribution uses post meta to link content posts to author CPT entities. Avoids both taxonomy and `wp_users` pollution entirely.

| | Molongui Authorship |
| --- | --- |
| **Author entity** | Custom post type |
| **Profile storage** | Post meta on the author CPT |
| **Attribution link** | Post meta on content posts |
| **Query method** | `WP_Meta_Query` (post meta joins) |

**Trade-offs:** Full custom fields per author, clean separation from `wp_users`. But post meta queries are slower than taxonomy joins on large datasets, and yet another entity type adds integration complexity.

### Approach 0 — Core WordPress

Single `post_author` column on `wp_posts` referencing `wp_users.ID`. One author per post. No guest authors. No extension points for multi-attribution.

---

## Feature matrix

Features that no existing plugin provides are highlighted — these are the gaps the [Byline Feed plugin](../../../byline-feed/) and the broader [author identity vision](../../vision/author-identity-vision.md) aim to fill.

| Feature | CAP | PPA | HM Authorship | Molongui | Core WP |
| --- | --- | --- | --- | --- | --- |
| **Multiple authors per post** | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Guest authors** | ✅ CPT | ✅ Term or User | ✅ WP_User | ✅ CPT | ❌ |
| **Block editor UI** | Blocks | Sidebar panel | Custom multi-select | Sidebar panel | Author dropdown |
| **Classic editor UI** | Metabox | Metabox | Metabox | Metabox | Author dropdown |
| **REST API — read** | Partial | ✅ | ✅ First-class | Partial | Single author |
| **REST API — write** | ❌ | ✅ | ✅ First-class | ❌ | Single author |
| **WP-CLI support** | ✅ | ✅ | ✅ | ❌ | Native |
| **Author display boxes** | Blocks only | ✅ Extensive | ❌ (developer-only) | ✅ Extensive | Theme-dependent |
| **Schema.org / JSON-LD** | ❌ | ✅ Pro | ❌ | ✅ | Theme-dependent |
| **Migration tools** | Origin | CAP + Byline import | CAP + PPA + wp-authors | CAP + PPA + One User Avatar | N/A |
| **Multisite support** | ✅ | ✅ | ✅ (shared wp_users) | ✅ | ✅ |
| **Author categories** | ❌ | ✅ Pro | ❌ | ❌ | ❌ |
| **Custom author fields** | ❌ | ✅ Pro | ❌ (uses user meta) | ✅ | User meta |

### Gaps — no existing plugin provides these

| Missing capability | Relevance | Addressed by |
| --- | --- | --- |
| **Structured feed author metadata** (Byline XML namespace) | Feed readers cannot distinguish authors or roles | [WP-02](../../../Implementation%20Strategy/wp-02.md) |
| **Content perspective in feeds** | Feed readers cannot distinguish reporting from opinion | [WP-03](../../../Implementation%20Strategy/wp-03.md) |
| **`fediverse:creator` meta tag** | Mastodon doesn't show author bylines on shared links | [WP-04](../../../Implementation%20Strategy/wp-04.md) |
| **Multi-author JSON-LD schema** | Search engines see single-author schema only | [WP-05](../../../Implementation%20Strategy/wp-05.md) |
| **Per-author AI training consent** | No machine-readable consent signal per author | [WP-06](../../../Implementation%20Strategy/wp-06.md) |
| **TDM-Rep headers / ai.txt** | No standardized rights declaration for crawlers | [WP-06](../../../Implementation%20Strategy/wp-06.md) |
| **Cross-plugin normalized author API** | No common interface across CAP/PPA/HM/Molongui/Core | [WP-01](../../../Implementation%20Strategy/wp-01.md) |

---

## Feed output comparison

Feed output is uniformly weak across all existing plugins. This is the core problem the [Byline spec](../../planning/byline-spec-plan.md) and the [Byline Feed plugin](../../../byline-feed/) address.

| Feed capability | CAP | PPA | HM Authorship | Molongui | Core WP | Byline Feed (new) |
| --- | --- | --- | --- | --- | --- | --- |
| **RSS2 `<author>` tag** | Via template tag | Automatic | `the_author` filter | Yes | Single author | Preserved (additive) |
| **`<dc:creator>` per co-author** | ❌ | ❌ | ❌ | ❌ | Single | Preserved (additive) |
| **`xmlns:byline` namespace** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| **`<byline:contributors>` block** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| **`<byline:person>` per author** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| **`<byline:author ref="">` per item** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| **`<byline:role>`** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| **`<byline:perspective>`** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| **Atom feed support** | ❌ | ❌ | ❌ | ❌ | Single author | ✅ |
| **Author bio/context in feed** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ (280 char cap) |
| **Author avatar in feed** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

---

## Query and archive architecture

How each system handles WordPress author queries and archive pages.

| Mechanism | CAP | PPA | HM Authorship | Molongui | Core WP |
| --- | --- | --- | --- | --- | --- |
| **Query rewrite method** | Taxonomy `WP_Tax_Query` | Taxonomy `WP_Tax_Query` | Hidden taxonomy `WP_Tax_Query` | `WP_Meta_Query` (post meta) | Native `post_author` index |
| **Author archive support** | ✅ Transparent | ✅ Transparent | ✅ Transparent (pre_get_posts) | ✅ | ✅ Native |
| **Theme compatibility** | Requires no changes | Requires no changes | Requires no changes | Requires no changes | Native |
| **`post_author` divergence risk** | ⚠️ High | ⚠️ High | ⚠️ Moderate | ⚠️ Low | N/A |
| **Performance at scale** | Good (taxonomy join) | Good (taxonomy join) | Good (taxonomy join) | Slower (meta join) | Best (column index) |

---

## Guest author security comparison

Guest author handling is where these systems diverge most sharply. See [known-gaps.md](known-gaps.md) for detailed security notes on HM Authorship.

| Security aspect | CAP | PPA | HM Authorship | Molongui |
| --- | --- | --- | --- | --- |
| **Guest entity type** | CPT (no login possible) | Term (no login) or User (role-blocked) | WP_User (role-blocked) | CPT (no login possible) |
| **Login prevention** | N/A (CPT, not a user) | Role has zero caps | ⚠️ Not actively blocked | N/A (CPT, not a user) |
| **Password reset risk** | None | None (term) or Low (user) | Low (email usually empty) | None |
| **Guest-to-user promotion** | Manual migration | Change backing type | Role change only | Manual migration |
| **wp_users table growth** | None | None (term) or Yes (user) | Yes (1 row per guest) | None |

---

## Capability and permission model

| Aspect | CAP | PPA | HM Authorship | Molongui | Core WP |
| --- | --- | --- | --- | --- | --- |
| **Custom capabilities** | None | None | `attribute_post_type`, `create_guest_authors` | None | None |
| **Attributed author can edit** | Via post_author compat | Via taxonomy ownership | ✅ Via `map_meta_cap` filter | Via post meta ownership | Only post_author |
| **Capability mapping source** | WordPress defaults | WordPress defaults | `edit_others_posts` → attribute, `edit_others_posts` → create guests | WordPress defaults | WordPress defaults |

---

## Adapter coverage in Byline Feed

The [Byline Feed plugin](../../../byline-feed/) normalizes author data from any of these systems into a single [author object contract](../../planning/implementation-spec.md#normalized-author-object-contract) via its adapter layer.

| Adapter | Status | Detection method | Implementation |
| --- | --- | --- | --- |
| Core WordPress | ✅ MVP | Always available (fallback) | [class-adapter-core.php](../../../byline-feed/inc/class-adapter-core.php) |
| Co-Authors Plus | ✅ MVP | `function_exists( 'get_coauthors' )` | [class-adapter-cap.php](../../../byline-feed/inc/class-adapter-cap.php) |
| PublishPress Authors | ✅ MVP | `function_exists( 'publishpress_authors_get_post_authors' )` | [class-adapter-ppa.php](../../../byline-feed/inc/class-adapter-ppa.php) |
| Molongui Authorship | 🔜 Planned | `class_exists( 'Molongui\\Authorship\\Author' )` | — |
| HM Authorship | 🔜 Planned | `function_exists( 'Authorship\\get_authors' )` | — |

Detection priority and the adapter interface are defined in the [implementation spec § Adapter detection](../../planning/implementation-spec.md#adapter-detection-and-priority).

---

## Architectural philosophy summary

Each generation addressed limitations of the previous, trading off differently between data model purity, feature breadth, and backwards compatibility.

| Dimension | Taxonomy-as-author (CAP, PPA) | Users-only (HM Authorship) | CPT-as-author (Molongui) |
| --- | --- | --- | --- |
| **Canonical entity** | Taxonomy term | WP_User | Custom post type |
| **Guest author model** | Term or CPT (varies) | User with zero-cap role | CPT entry |
| **Relational storage** | `wp_term_relationships` | Hidden `authorship` taxonomy | Post meta |
| **API surface** | Mixed (term + user objects) | Consistent (always WP_User) | Mixed (CPT + post objects) |
| **Query performance** | Good (taxonomy join) | Good (taxonomy join) | Slower (meta join) |
| **wp_users growth** | None | Yes | None |
| **Conceptual simplicity** | Moderate (sync required) | High (one entity type) | Moderate (custom entity) |
| **Feature breadth** | High (CAP: legacy; PPA: active) | Low (developer-focused) | High (display-focused) |

The Byline Feed plugin's adapter layer exists precisely because no single approach won. Sites use different systems and the output layer should not care which one. The [normalized author contract](../../planning/implementation-spec.md#normalized-author-object-contract) is the abstraction that makes this possible.

---

## Related documents

- [landscape.md](landscape.md) — Install counts, historical lineage, per-plugin data model detail
- [architecture.md](architecture.md) — HM Authorship source-level architecture review
- [known-gaps.md](known-gaps.md) — Security, data integrity, and performance gaps
- [implementation-spec.md](../../planning/implementation-spec.md) — Byline Feed plugin implementation spec and roadmap
- [author-identity-vision.md](../../vision/author-identity-vision.md) — Full vision: feeds, schema, fediverse, AI, rights
- [byline-spec-plan.md](../../planning/byline-spec-plan.md) — Byline RSS spec plan
- [byline-adoption-strategy.md](../../planning/byline-adoption-strategy.md) — Byline spec adoption strategy
- [Implementation Strategy/](../../../Implementation%20Strategy/) — Work package specs (WP-01 through WP-06)
