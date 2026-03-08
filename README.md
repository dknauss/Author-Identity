# Author-Identity

Structured author identity that travels with the work — across feeds, search, the fediverse, and AI — from one source of truth in WordPress.

# Documentation Index

Technical documentation for the Author Identity project — the Byline feed plugin and its broader vision for structured author identity in WordPress.

Documents in the "Planned enhancements" and "Implementation Strategy" sections live in this repo.
Documents in the other sections below (architecture, landscape, quality, audit) live in the Authorship fork repo and are referenced here for context (not included in this folder tree).

## Architecture and internals (Authorship fork repo)

- **Architecture Reference** *(external to this repo snapshot)* — Source-verified walkthrough of the data model, query rewriting, capability mapping, guest author mechanics, REST API, and file structure. Based on direct code audit of the `develop` branch (v0.2.17).

## Competitive landscape (Authorship fork repo)

- **Multi-Author Plugin Landscape** *(external to this repo snapshot)* — Comparison of Co-Authors Plus, PublishPress Authors, Molongui Authorship, WP Post Author, Simple Author Box, and Authorship. Includes active install counts from wp.org (March 2026), architectural approaches, feature comparison matrix, and historical lineage from Mark Jaquith's 2005 "Multiple Authors" through the present.

## Planned enhancements

- **[Byline Spec Assessment and Implementation Plan](Byline%20RSS%20Spec%20Adoption/byline-spec-plan.md)** — Analysis of the Byline open specification (bylinespec.org) for structured author identity in syndication feeds. Includes mapping from Authorship's data model to Byline elements, phased implementation plan, and strategic considerations for early adoption.

- **[Byline Feed Plugin — Cross-Plugin Adoption Strategy](byline-adoption-strategy.md)** — Strategy for a standalone wp.org plugin that outputs Byline-structured feed data across the WordPress multi-author plugin ecosystem. Covers the adapter architecture for Co-Authors Plus, PublishPress Authors, Molongui, and core WordPress; the addressable audience (~40K+ multi-author sites); perspective metadata; role mapping; and a phased adoption roadmap targeting both the supply side (WordPress feeds) and demand side (feed reader developers).

- **[Author Identity, Content Provenance, and Distribution Control](author-identity-vision.md)** — Vision document extending the Byline work into ActivityPub federation, LLM discoverability and E-E-A-T structured SEO, intellectual property protection (TDM headers, ai.txt, per-author AI training consent), and the journalism and IndieWeb adoption angles. Defines a six-component roadmap from Byline feeds through JSON-LD schema, content rights signaling, ActivityPub bridging, and IndieWeb integration.

## Implementation strategy

- **[Implementation Spec and Roadmap](Implementation%20Strategy/implementation-spec.md)** — Plugin architecture (adapter → normalized API → output layers), normalized author object contract, work package sequence (WP-01 through WP-06), release gates, filter/hook API, test strategy, file structure, and MVP acceptance criteria.
- **Work packages:** [WP-01](Implementation%20Strategy/wp-01.md) (scaffold + adapters) · [WP-02](Implementation%20Strategy/wp-02.md) (RSS2/Atom output) · [WP-03](Implementation%20Strategy/wp-03.md) (perspective UI) · [WP-04](Implementation%20Strategy/wp-04.md) (fediverse:creator) · [WP-05](Implementation%20Strategy/wp-05.md) (JSON-LD schema) · [WP-06](Implementation%20Strategy/wp-06.md) (content rights + AI consent)

## Quality and security (Authorship fork repo)

- **Known Gaps and Security Notes** *(external to this repo snapshot)* — Security findings (guest author login, username normalization), data integrity concerns (post_author divergence, silent failures), performance notes, feed limitations, and compatibility considerations.

## Audit artifacts (Authorship fork repo, Phase 01)

These documents were produced during the initial code audit and define the quality baseline and build queue:

- **HM vs WPCS Audit** *(external to this repo snapshot)* — Repo-grounded standards audit with command evidence, rule references, and five detailed follow-up items with patch scaffolds.
- **Foundation Quality Baseline** *(external to this repo snapshot)* — Support matrix, green gate definition, and CI/local parity rules.
- **Phase 01 Roadmap** *(external to this repo snapshot)* — Build queue ordering and current state.

### Patch scaffolds

- **01-02 Standards Tooling** *(external to this repo snapshot)* — PHPCS/PHPStan refresh for modern PHP compatibility.
- **01-02 Security Hardening** *(external to this repo snapshot)* — Guest author username normalization and filter scope.
- **01-02 Observability** *(external to this repo snapshot)* — Post-insert failure signaling.
- **01-02 Performance** *(external to this repo snapshot)* — Editor component and CLI migration cleanup.
