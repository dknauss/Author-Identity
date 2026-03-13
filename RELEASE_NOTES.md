# Release Notes Convention

Use release notes to disclose substantial AI assistance without confusing authorship or accountability.

## Release checklist

For each real release:

1. Create or update the matching version section in [CHANGELOG.md](CHANGELOG.md).
2. Summarize only the externally meaningful changes in the GitHub release notes.
3. Reuse the changelog categories where practical (`Added`, `Changed`, `Fixed`, `Security`, `Docs`).
4. Include the AI-assistance note below when AI materially affected the shipped release.
5. Do not imply AI ownership; keep accountability with the human maintainer.

## Recommended note

When a release includes material AI-assisted work, add a short note like:

> Portions of this release were developed with OpenAI Codex assistance. Repository ownership, review, and merge accountability remain with the human maintainer.

## When to include it

Include the note when AI assistance materially affected:

- implementation
- documentation
- test coverage
- CI or tooling changes

## When it can be omitted

You can omit the note when AI assistance was trivial or purely editorial.

## Relationship to commit history

Release notes complement, but do not replace:

- `Assisted-by: Codex` commit trailers
- the AI assistance disclosure in `README.md`
- the contributor/process guidance in `CONTRIBUTING.md`
- the dated version entries in `CHANGELOG.md`
