# Documentation Index

Technical documentation for the Authorship fork. These documents cover the upstream architecture, competitive landscape, planned enhancements, and audit findings.

## Architecture and internals

- **[Architecture Reference](architecture.md)** — Source-verified walkthrough of the data model, query rewriting, capability mapping, guest author mechanics, REST API, and file structure. Based on direct code audit of the `develop` branch (v0.2.17).

## Competitive landscape

- **[Multi-Author Plugin Landscape](landscape.md)** — Comparison of Co-Authors Plus, PublishPress Authors, Molongui Authorship, WP Post Author, Simple Author Box, and Authorship. Includes active install counts from wp.org (March 2026), architectural approaches, feature comparison matrix, and historical lineage from Mark Jaquith's 2005 "Multiple Authors" through the present.

## Planned enhancements

- **[Byline Assessment and Implementation Plan](byline-spec-plan.md)** — Analysis of the Byline extension vocabulary (bylinespec.org) for structured author identity in syndication feeds. Includes mapping from Authorship's data model to Byline elements, phased implementation plan, and strategic considerations for early adoption.

- **[Byline Feed Plugin — Cross-Plugin Adoption Strategy](byline-adoption-strategy.md)** — Strategy for a standalone wp.org plugin that outputs Byline-structured feed data across the WordPress multi-author plugin ecosystem. Covers the adapter architecture for Co-Authors Plus, PublishPress Authors, Molongui, and core WordPress; the addressable audience (~40K+ multi-author sites); perspective metadata; role mapping; and a phased adoption roadmap targeting both the supply side (WordPress feeds) and demand side (feed reader developers).

- **[Author Identity, Content Provenance, and Distribution Control](author-identity-vision.md)** — Vision document extending the Byline work into ActivityPub federation, LLM discoverability and E-E-A-T structured SEO, intellectual property protection (TDM headers, ai.txt, per-author AI training consent), and the journalism and IndieWeb adoption angles. Defines a five-component roadmap from Byline feeds through JSON-LD schema, content rights signaling, ActivityPub bridging, and IndieWeb integration.

## Quality and security

- **[Known Gaps and Security Notes](known-gaps.md)** — Security findings (guest author login, username normalization), data integrity concerns (post_author divergence, silent failures), performance notes, feed limitations, Byline extension ambiguities, and compatibility considerations.

## Extension feedback

- **[First Implementor's Notes](first-implementor-notes.md)** — Findings from building the first implementation of the Byline extension vocabulary. Covers the role-per-author ambiguity in multi-author items, a proposed canonical JSON Feed `_byline` extension mapping, the perspective/velocity connection for feed reader UX (specifically Current), the contributor pre-pass pattern for feed generators, and a summary of proposed changes. Intended for submission to the Byline repository.

## Audit artifacts (from Phase 01)

These documents were produced during the initial code audit and define the quality baseline and build queue:

- **[HM vs WPCS Audit](audit/HM_WPCS_audit.md)** — Repo-grounded standards audit with command evidence, rule references, and five detailed follow-up items with patch scaffolds.
- **[Foundation Quality Baseline](audit/foundation-quality-baseline.md)** — Support matrix, green gate definition, and CI/local parity rules.
- **[Phase 01 Roadmap](audit/roadmap-01.md)** — Build queue ordering and current state.

### Patch scaffolds

- **[01-02 Standards Tooling](audit/patch_scaffolds/01-02-hm-wpcs_build.md)** — PHPCS/PHPStan refresh for modern PHP compatibility.
- **[01-02 Security Hardening](audit/patch_scaffolds/01-02-security_build.md)** — Guest author username normalization and filter scope.
- **[01-02 Observability](audit/patch_scaffolds/01-02-observability_build.md)** — Post-insert failure signaling.
- **[01-02 Performance](audit/patch_scaffolds/01-02-performance_build.md)** — Editor component and CLI migration cleanup.
