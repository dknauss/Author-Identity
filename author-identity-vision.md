# Author Identity, Content Provenance, and Distribution Control

## Elevator pitch

Your byline should travel with your work — your name, your credentials, your perspective, your rights — everywhere it goes. Feeds, search engines, the fediverse, AI systems. One plugin, one source of truth, every output channel.

Right now, when your article leaves your site, it gets stripped down to a name and a URL. Your analysis looks identical to a press release in a feed reader. Your co-author disappears. Your "don't train on this" preference is invisible. Your fediverse identity is disconnected from your publishing identity.

We fix that. A WordPress plugin that takes the author data you already have — from Co-Authors Plus, PublishPress Authors, or plain WordPress — and pushes structured identity into RSS feeds (via the Byline spec), HTML schema (for search and AI), and fediverse metadata (via `fediverse:creator`). With content perspective labels so readers know if they're reading reporting or opinion, and per-author rights signals so your consent preferences are machine-readable.

Forty thousand multi-author WordPress sites. Zero structured identity in their feeds today. We go first.

## Purpose

This document extends the Byline feed adoption strategy into broader territory: how structured author identity in WordPress intersects with ActivityPub federation, LLM discoverability, technical SEO, and intellectual property protection. It is a vision document, not an implementation spec. It identifies convergences, tensions, and practical components that could be built incrementally on top of the Byline feed plugin described in `byline-adoption-strategy.md`.

## Target communities

### Professional journalists and newsrooms

The Byline spec's `perspective` element solves a real editorial problem: in a feed reader, an investigative analysis and a corporate press release are structurally identical. Newsrooms already care about this — ProPublica, The Markup, CalMatters, and similar WordPress-based outlets invest significant effort in distinguishing their journalism from the surrounding information environment.

The `affiliation` element also maps directly to existing editorial practice. FTC disclosure requirements for sponsored content and newsroom style guide conflict-of-interest rules are already standard. That disclosure currently lives in body text and gets lost in syndication. Structured feed metadata makes it machine-readable and displayable by reader applications — a badge that says "this author is employed by the company they're writing about."

For these organizations, the pitch is not "add metadata to your feed." It is: "your editorial standards already require this information — we make it travel with the content instead of getting stripped at syndication."

### Independent writers and bloggers

The IndieWeb community already values feed-first publishing, `rel="me"` mutual link verification, POSSE (Publish on your Own Site, Syndicate Elsewhere), `/now` pages, and `/uses` pages. Byline references these conventions natively. The audience is smaller but highly engaged, technically capable, and influential in setting norms for the open web.

For these writers, the pitch is: "your identity and context should follow your writing wherever it goes — not just on your own site."

### Publishers concerned about AI and distribution

This is the fastest-growing constituency. Writers and publishers want to be found and credited by AI systems but do not want their work harvested as training data without compensation or consent. These goals are in tension, and any tooling that pretends otherwise is not credible. What structured metadata can do is make the tension *manageable* by giving publishers granular, machine-readable control over identity, attribution, and rights signaling.

## ActivityPub convergence and tension

### What converges

The ActivityPub plugin for WordPress (Automattic project, maintained by Matthias Pfefferle) turns WordPress posts into ActivityPub objects that federate across Mastodon and the fediverse. An ActivityPub `Article` object has an `attributedTo` field pointing to an Actor — the author.

Byline's `byline:person` and ActivityPub's Actor concept solve the same problem — portable, verifiable author identity — in different distribution contexts. Byline addresses syndication feeds (RSS, Atom, JSON Feed). ActivityPub addresses federation (Mastodon, Pleroma, Misskey, etc.).

A plugin that populates both from the same WordPress author data gives writers a consistent identity across both channels. Their Mastodon followers and their RSS subscribers see the same structured identity, verified by the same `rel="me"` mutual links.

### What diverges

**Verification models.** ActivityPub uses cryptographic signatures — the origin server signs every object with its private key, and receiving servers verify. Byline uses IndieWeb-style `rel="me"` mutual linking, which is social proof rather than cryptographic proof. These are not incompatible but represent different trust levels. A sophisticated implementation could use ActivityPub's cryptographic identity as the strong verification layer and Byline's profile links as the human-readable discovery layer.

**Content distribution.** ActivityPub federates full content — the `content` field of the Activity object typically includes the post body. RSS can include either full content or summaries. Publishers who want to control distribution may prefer to syndicate excerpts with full Byline identity via RSS (maximum discoverability, minimum content exposure) while gating full content behind their own site. ActivityPub complicates this because Mastodon expects full content. This tension is not something a plugin can fully resolve, but it can offer the choice.

**Multi-author representation.** This is the most active area of development at the intersection of WordPress and the fediverse, with several concrete threads converging:

**Mastodon's `fediverse:creator` meta tag (July 2024).** Mastodon introduced a new OpenGraph-style meta tag for author attribution on shared links: `<meta name="fediverse:creator" content="@user@instance" />`. When a link is shared on Mastodon, the author byline becomes clickable, opening the author's fediverse profile directly in the app. This was explicitly designed for journalism — the launch partners were The Verge, MacStories, and MacRumors. The tag works with any fediverse account (Mastodon, Flipboard, Threads, WordPress with ActivityPub plugin, PeerTube, Pixelfed).

Source: Eugen Rochko, "Highlighting journalism on Mastodon," blog.joinmastodon.org, July 2, 2024.

**Multi-author limitation acknowledged.** The same blog post states: "If multiple tags are present on the page, the first one will be displayed, but we may add support for showing multiple authors in the future. We intend to propose a specification draft for other ActivityPub platforms in the coming weeks." As of the June 2024 engineering update, Mastodon introduced an `authors` attribute in the REST API for link previews that "cannot contain more than one author on Mastodon, but this might change."

Sources: blog.joinmastodon.org/2024/07/highlighting-journalism-on-mastodon/ and blog.joinmastodon.org/2024/07/trunk-tidbits-june-2024/ (PR #30846).

**WordPress ActivityPub plugin — post author/object actor synchronization (closed).** Issue #2353 on the Automattic/wordpress-activitypub repo identified the fundamental problem: WordPress's `post_author` and the ActivityPub object's `actor`/`attributedTo` can diverge, especially with multi-author plugins like Co-Authors Plus. This led to Discussion #2358, a draft pre-FEP (Fediverse Enhancement Proposal) titled "Reassigning Actor and CoAuthor Representation for Federated CMS," which proposed new ActivityPub activity types (`Reattribute`, `Transfer`) for author reassignment and co-authorship.

**The issue was closed and the pre-FEP proposal remains draft/unadopted.** The proposal required novel activity types that would need broad fediverse-wide adoption — a high bar for what is essentially a CMS-specific concern. The practical implication: the multi-author federation problem is unlikely to be solved at the ActivityPub protocol level in the near term. The path forward runs through better use of existing primitives (`attributedTo` arrays, `tag` mentions) and HTML-level mechanisms (`fediverse:creator` tags) rather than new protocol extensions. This is exactly the layer where a Byline identity plugin would operate.

The underlying problem (post_author/actor divergence) remains real and unsolved in the ActivityPub plugin. It's worth monitoring whether the plugin team addresses it through internal architecture changes or new filter hooks, even though the FEP approach has not been adopted.

References: github.com/Automattic/wordpress-activitypub/issues/2353 (closed), github.com/Automattic/wordpress-activitypub/discussions/2358, socialhub.activitypub.rocks/t/pre-fep-reassigning-actor-and-coauthor-representation-for-federated-cms/8172.

**Ghost's ActivityPub implementation.** Ghost (TryGhost/ActivityPub) is also grappling with multi-author federation — the pre-FEP discussion explicitly references Ghost's implementation alongside WordPress. Ghost's forum has active threads on how multi-author content appears in the fediverse, with the current behavior attributing all content to a single site-level account.

Source: forum.ghost.org/t/multiple-authors-shared-to-the-fediverse-what-does-that-look-like/59502.

**Current limitations.** The default Mastodon web UI still displays a single author for interaction purposes (replying, liking). The `fediverse:creator` tag currently only shows the first tag when multiple are present. The pre-FEP for co-author representation is in draft status and has not been formally proposed. PeerTube and other platforms that handle multi-contributor content use workarounds — attributing to a single primary account and mentioning others in body or metadata.

### Practical interop

The `fediverse:creator` meta tag is the most immediate and concrete integration point for a WordPress author identity plugin. It requires no ActivityPub protocol changes — it's a standard HTML meta tag that WordPress can output in `wp_head` using the same normalized author data that feeds the Byline output.

Concrete steps:

1. **Output `fediverse:creator` meta tags** from normalized author data. For each attributed author who has a fediverse handle (stored as user meta), output `<meta name="fediverse:creator" content="@handle@instance" />`. This works today with Mastodon's existing support, even though only the first author is displayed.
2. **Populate the Mastodon REST API `authors` attribute** — this happens automatically when Mastodon fetches and parses the page's OpenGraph tags, so no additional work is needed beyond outputting the meta tags.
3. **Work within existing primitives.** The pre-FEP for new activity types remains draft and unadopted (#2358), which reinforces a pragmatic near-term path: solve multi-author federation with existing AP primitives and HTML mechanisms, not protocol extensions. A plugin that demonstrates effective multi-author attribution using `fediverse:creator` tags, `attributedTo` arrays, and `tag` mentions builds practical evidence for how the ecosystem should handle this — which is more persuasive than a speculative spec proposal.
4. **Coordinate with the ActivityPub plugin team.** The post_author/object_actor sync issue (#2353, closed) identified the problem but didn't resolve it. The actor management proposal (Discussion #547) suggests architectural changes are planned. If the ActivityPub plugin exposes filters for customizing `attributedTo`, the Byline identity plugin could use those filters to inject multi-author data without protocol changes.
5. **Map Byline `role` values to fediverse metadata.** A `guest` author vs. a `staff` author carries editorial meaning that could inform how platforms display the attribution. This is forward-looking — no current platform uses this — but establishing the convention early influences the spec.

## LLM discoverability and credit

### The problem

When an AI system consumes a feed or crawls a page, the author attribution it can extract from standard feed elements is minimal: a name, maybe an email (RSS), maybe a URL (Atom, JSON Feed). There is no structured way to convey *who this person is*, *what their expertise is*, or *why their perspective on this topic matters*.

This means AI-generated summaries and citations strip the context that makes authorship meaningful. "According to an article by Jane Doe" tells the reader nothing. "According to Jane Doe, a staff investigative reporter at The Markup covering surveillance technology" tells the reader everything.

### How Byline metadata helps

If an AI system is consuming a feed with Byline data, the structured `byline:person` element with `context` and `affiliation` provides exactly the richer attribution signal that plain-text bylines lack. The `byline:perspective` element tells the AI system whether it is looking at reporting, analysis, opinion, satire, or sponsored content — a distinction that matters enormously for responsible summarization.

This is not hypothetical. AI search products (Perplexity, Google AI Overviews, ChatGPT search) already consume web content and generate attributed summaries. The quality of those attributions is limited by the quality of the structured metadata available.

### Technical SEO: JSON-LD convergence

The same author data that feeds Byline elements should also produce JSON-LD schema on HTML pages. This is the SEO side of the same coin — Google's E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness) framework rewards structured author identity.

The relevant schema structures:

- `Article` with `author` as an array of `Person` objects (multi-author support).
- Each `Person` with `name`, `url`, `description`, `sameAs` (array of social profile URLs — identical data to Byline's `byline:profile` elements).
- `publisher` as an `Organization` (same data as Byline's `byline:org`).
- `Article.creditText` for explicit attribution/licensing requirements.

WordPress schema output today (via Yoast, Rank Math, etc.) handles single authors reasonably well but multi-author attribution poorly. A plugin that generates both Byline feed elements and JSON-LD Person/Article schema from the same normalized author data — the adapter pattern from the adoption strategy — would be more coherent than bolting together separate feed and schema plugins.

The practical path: offer a JSON-LD output component that hooks into `wp_head` and produces Article + Person schema derived from the same adapter that feeds the Byline output. Filter every field so existing schema plugins can integrate rather than conflict. If Yoast or Rank Math is detected, offer a compatibility mode that extends their schema rather than replacing it.

### Single source of truth

The architectural principle: author data entered once (in WordPress user profiles or multi-author plugin profiles) flows to:

1. HTML JSON-LD schema (for search engines and AI crawlers).
2. RSS/Atom Byline elements (for feed readers).
3. ActivityPub Actor objects (for the fediverse).
4. Content rights metadata (for AI training consent signaling).

No duplicate data entry. No divergence between what the feed says and what the HTML says. The adapter pattern from the Byline adoption strategy is the foundation — each multi-author plugin adapter resolves to the same normalized author object, and each output channel consumes that object.

## Intellectual property protection and harvesting resistance

### Honest framing

No technical mechanism can prevent a determined crawler from harvesting publicly accessible content. `robots.txt`, `ai.txt`, meta tags, and TDM headers are all advisory — they depend on the crawler choosing to honor them. Any tooling that claims otherwise is not credible.

What structured metadata *can* do is make publisher intent unambiguous, create an evidentiary record of rights expression, and provide practical levers for controlling the tradeoff between discoverability and exposure.

### Signaling layers

**TDM Reservation Protocol (often referenced as TDMRep).** A W3C draft that lets publishers express machine-readable preferences about text and data mining, including `tdm-reservation` and `tdm-policy` headers. A Byline identity plugin could include these signals alongside author identity in feed output and HTML headers: "here is who wrote this, and here are the terms under which it may be mined."

**`ai.txt` convention.** Analogous to `robots.txt` but specifically for AI training crawlers. A plugin settings page that generates and maintains an `ai.txt` file for site-wide policy (with optional section-level rules where feasible) would lower the barrier to adoption.

**C2PA (Coalition for Content Provenance and Authenticity).** A standard for content provenance that is currently image/video focused but has potential for text content. Worth monitoring but premature to implement for blog posts.

**Creative Commons machine-readable metadata.** The `cc:license` RSS element already exists. A Byline plugin could output it alongside Byline elements, creating a complete picture: "this person wrote this, with this perspective, under this license."

### Feed-level gating

Offer a choice between full-content and excerpt-only feeds. Excerpt-only feeds with full Byline identity give publishers maximum discoverability with minimum content exposure. The AI search engine sees "Jane Doe, staff reporter at X, wrote an analysis piece about Y" and must visit the publisher's site for the full text — where the publisher controls access, monetization, paywalls, and tracking.

This is not new — WordPress has offered excerpt vs. full-content feeds since forever. What is new is pairing the excerpt feed with rich structured identity so the excerpt *itself* carries attribution value rather than being an anonymous teaser.

### Per-author consent

On a multi-author site, different authors may have different preferences about AI use of their work. A staff reporter may want maximum reach. A guest contributor may want to opt out of AI training entirely. A columnist may want their opinion pieces excluded but their reported pieces included.

A per-author or per-post metadata field for AI training consent, expressed in both feed metadata and HTML meta tags, would give publishers granular control. Implementation:

- User meta field: `byline_feed_ai_consent` with values `allow`, `deny`, `unset`.
- Post meta field: `_byline_ai_consent` to override author-level preference per post.
- Output: per-response crawler-policy signals where consent is denied (for example `tdm-reservation` / `tdm-policy` headers and optional `X-Robots-Tag`/meta directives). `robots.txt` token directives (for example `Google-Extended`) should be offered only as site-wide mode, not as per-author/per-post controls. Feed items for opted-out authors could carry a dedicated rights-policy element or be excluded from the feed entirely (configurable).

This is genuinely novel. Nobody is doing per-author AI consent in structured metadata today. It would be an attention-getting feature for the journalism community, where this debate is live and urgent.

### The tension is the feature

The reason this matters is that discoverability and protection are not a binary choice. Publishers need a spectrum of control:

- Maximum visibility: full content in feeds, full Byline identity, JSON-LD schema, AI training allowed.
- Visible but protected: excerpt feeds with full identity, AI training denied, TDM reservation expressed.
- Minimal exposure: no feed output, minimal schema, comprehensive AI opt-out.

A single plugin that offers this spectrum, grounded in the same normalized author data, is more useful than separate tools for "SEO," "feeds," and "AI protection" that each have their own configuration and data model.

## Component roadmap

Building on the Byline adoption strategy, the broader vision breaks into incremental components. Each is useful on its own; together they form a coherent author identity and content provenance layer.

### Component 1: Byline feed output (from adoption strategy)

The adapter pattern, multi-author plugin support, RSS2/Atom output, perspective meta field. This is the foundation. Ship first on wp.org.

### Component 2: JSON-LD schema output

Article + Person + Organization schema on post pages, derived from the same normalized author data. Compatibility mode for Yoast/Rank Math. Multi-author support in schema (array of Person objects). `sameAs` populated from the same profile links that feed `byline:profile`.

### Component 3: Content rights and AI consent

Per-author and per-post AI training consent fields. TDM headers. `ai.txt` generation. Creative Commons metadata in feeds. Excerpt-only feed option with full identity metadata. This component is the most editorially complex but also the most likely to drive adoption in the journalism community.

### Component 4: ActivityPub and fediverse bridge

Output `fediverse:creator` meta tags from normalized author data — this is the most immediate win, working with Mastodon's existing support (launched July 2024 for journalism use cases). For multi-author posts, output multiple `fediverse:creator` tags; Mastodon currently displays only the first but has stated intent to support multiple authors and introduced an `authors` array in its REST API (PR #30846). The protocol-level pre-FEP for co-author representation (#2358) remains draft and unadopted, reinforcing that the practical path runs through HTML-level mechanisms and existing ActivityPub primitives rather than new protocol extensions. Monitor the ActivityPub plugin for new filter hooks on `attributedTo` and actor management (Discussion #547) that would allow injecting multi-author data without protocol changes.

### Component 5: IndieWeb integration

`rel="me"` output from Byline profile links. WebSub/PuSH hub advertising in feeds alongside Byline data. Microformats2 `h-card` output alongside or instead of JSON-LD for sites in the IndieWeb ecosystem. Webmention support for cross-site author verification.

### Component 6: ActivityPub C2S as a publication protocol (forward-looking)

This component is architecturally anticipated but not near-term. It depends on the C2S ecosystem maturing — which is currently at a discussion stage but has active energy behind it (see the C2S section below for full context). The adapter pattern should be designed so that a C2S output channel is natural to add when the infrastructure is ready.

## The neglected ActivityPub C2S API

### Background

ActivityPub defines two protocols: Server-to-Server (S2S) for federation between instances, and Client-to-Server (C2S) for users and applications to interact with their accounts on servers. The fediverse runs almost entirely on S2S. C2S has been largely ignored.

The current state is stark. The AP C2S API is not widely implemented in servers, and almost no clients exist for it. Mastodon has not shipped broad, production C2S support and the practical fediverse still relies on S2S federation between servers plus the Mastodon Client API (a proprietary REST API, not part of the W3C spec) as the de facto client interface. Pleroma had basic C2S support at one point.

The reasons for neglect go beyond inertia. As trwnh (a prominent AP contributor) articulated on SocialHub in November 2024: the C2S API suffers from an "impedance mismatch" — a social network wants timelines, search, streaming, and bookmarks, while AP C2S was written for simple resource manipulations and push notifications. It's not that C2S is broken; it's that it was designed for a different kind of interaction than what Mastodon-style social networking demands.

A "NextGen ActivityPub Social API" effort was proposed on SocialHub in November 2024 by Steve Bate, aiming to bring C2S to feature parity with the Mastodon Client API through a set of FEPs. The approach includes a façade that proxies the Mastodon API while exposing a standard C2S interface, plus a reference client implementation. Pixelfed's dansup expressed interest in adding C2S support to the Loops short video platform. The effort is at the discussion and prototyping stage.

Sources: socialhub.activitypub.rocks/t/nextgen-activitypub-social-api/4733, socialhub.activitypub.rocks/t/the-activitypub-client-api/3186, socialhub.activitypub.rocks/t/activitypub-client-to-server-faq/1941.

### Why C2S matters for WordPress publishing

WordPress is not a typical fediverse node. It's a publisher — it creates content and pushes it out. It doesn't need timelines, search, or streaming. It needs exactly what C2S was originally designed for: a client posting activities to an outbox.

Consider what WordPress actually is in the ActivityPub model. The WordPress ActivityPub plugin currently acts as both client *and* server in a single package — it creates `Article` objects, wraps them in `Create` activities, and handles federation delivery, all internally. This coupling is where the post_author/actor synchronization issue (#2353) originated: the plugin has to simultaneously resolve "who is the actor for this Create activity?" and "how do I deliver it to followers?"

C2S decouples these concerns. In a C2S model:

1. WordPress acts as the **client**. It composes a `Create` activity containing an `Article` object with full multi-author `attributedTo` metadata (names, bios, profile links — all the Byline data).
2. The client POSTs this activity to an **outbox endpoint** on an AP server.
3. The **server** handles federation delivery — signing, inbox discovery, delivery retries — without needing to know anything about WordPress's internal authorship model.

The editorial concern (who wrote this, how should it be attributed) is cleanly separated from the federation concern (how does it get delivered). WordPress controls the content and metadata; the AP server controls distribution.

This model was explored by the LAUTI community calendar project, where Bonfire Networks suggested implementing C2S instead of S2S precisely because it's simpler for a publishing application that wants to connect to an existing AP actor rather than becoming its own federation node.

Source: socialhub.activitypub.rocks/t/possible-c2s-implementation-in-lauti/8173.

### Multi-author attribution via C2S

The AP spec states: "When a Create activity is posted, the actor of the activity SHOULD be copied onto the object's `attributedTo` field." But `attributedTo` is not constrained to a single value. A C2S client could POST a Create activity where:

- `actor` is the publishing account (the WordPress site or primary author).
- `object.attributedTo` is an array of all co-authors, each an Actor URI.

This is spec-compliant today. The challenge has been that nobody implements C2S, so nobody has tested multi-author `attributedTo` arrays through this path. A WordPress plugin that does this would be a concrete demonstration of C2S's value for the publishing use case.

### Content provenance via C2S

C2S is also relevant to the content rights and provenance questions. A `Create` activity is a structured JSON-LD object. The client (WordPress) controls everything that goes into it before posting to the outbox. This means all Byline metadata, rights signals, license declarations, TDM reservations, and AI consent flags could be embedded directly in the activity object:

```json
{
  "@context": ["https://www.w3.org/ns/activitystreams", ...],
  "type": "Create",
  "actor": "https://example.com/authors/jane",
  "object": {
    "type": "Article",
    "attributedTo": [
      "https://example.com/authors/jane",
      "https://example.com/authors/alex"
    ],
    "content": "...",
    "name": "Article Title",
    "cc:license": "https://creativecommons.org/licenses/by-nc/4.0/",
    "tdm:reservation": "https://example.com/tdm-policy"
  }
}
```

This is more powerful than the HTML meta tag approach (`fediverse:creator`, TDM headers) because the metadata travels *with the activity object* through federation rather than requiring recipients to fetch and parse the origin page. Every server that receives the activity has the full provenance and rights information embedded in the object itself.

### Realistic assessment

C2S is neglected for real structural reasons, not just inertia:

- No popular fediverse clients use it.
- Most servers don't implement it (Mastodon notably does not).
- The Mastodon Client API is the de facto standard, and the Mastodon team has expressed discomfort with other servers cloning it but hasn't offered C2S as an alternative.
- The NextGen Social API FEP effort is at the discussion stage with no shipped implementations.
- Authentication and authorization for C2S are underspecified in the original standard.

For this project, C2S is a **forward-looking architectural consideration**, not a near-term implementation target. The immediate wins are `fediverse:creator` tags (Component 4), Byline feeds (Component 1), and JSON-LD schema (Component 2) — these work today with deployed infrastructure.

However, the adapter pattern (normalized author data flowing to multiple output channels) should be designed so that a C2S output channel is architecturally natural to add. If C2S revives — and the NextGen Social API effort, the LAUTI exploration, and Pixelfed/Loops interest suggest there is energy in that direction — WordPress multi-author content would be one of the strongest use cases for it. The publishing model (create structured content, post to outbox, let the server handle delivery) is exactly what C2S was designed for, even if the social networking model (timelines, search, streaming) is not.

The strategic play: build Components 1-5 using today's infrastructure (feeds, HTML meta tags, JSON-LD, `fediverse:creator`), but keep the normalized author data interface clean enough that a C2S adapter can slot in alongside the feed adapter, the schema adapter, and the `fediverse:creator` adapter when the time is right.

## Naming and positioning

The broader vision is no longer just "a Byline feed plugin." It is closer to "structured author identity and content provenance for WordPress." The wp.org plugin should start with the Byline feed name and scope, then grow into the broader positioning as components ship.

For the journalism and professional writing community, the framing should lead with the editorial problem: "your byline should travel with your work — with your credentials, your perspective, your rights, and your identity intact."

For the IndieWeb community: "own your identity across every channel your writing reaches."

For the technical SEO community: "E-E-A-T compliance from the same author data that powers your feeds."

For the AI-concerned community: "structured rights expression so your consent preferences are machine-readable, not just implied."

## Open-source license rationale for GPLv2-or-later

GPLv2-or-later is the WordPress ecosystem default, required for wp.org distribution, and the least restrictive GPL variant for a plugin designed to bridge multiple other GPL-licensed plugins. Avoids the one-way compatibility constraint that GPLv3 introduces when downstream code is GPLv2-only.
