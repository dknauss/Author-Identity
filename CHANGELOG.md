# Changelog

All notable changes to this repository should be documented here.

This file complements:

- [README.md](README.md) for repository orientation
- [RELEASE_NOTES.md](RELEASE_NOTES.md) for release-note wording conventions
- commit history for exact implementation detail

## Unreleased

### Added

- Repository governance files:
  - `CONTRIBUTING.md`
  - `RELEASE_NOTES.md`
  - `.github/CODEOWNERS`
  - `.github/dependabot.yml`
  - issue templates
  - pull request template

### Changed

- Root `README.md` refined for denser table-based navigation
- `byline-feed/readme.txt` updated for clearer WordPress.org-facing scope and installation guidance
- Mermaid architecture source moved to `docs/research/`

### Security

- GitHub Actions workflow hardened for public-repo use:
  - least-privilege `permissions`
  - immutable action SHA pinning
  - deterministic `npm ci`
  - Node 24 opt-in for JavaScript-based actions

- Current remaining npm advisories are confined to transitive development dependencies under `byline-feed/package-lock.json` and are being tracked separately
