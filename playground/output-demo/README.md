# Output Demo Blueprint

This bundle is the primary WordPress Playground target for `byline-feed`.

Purpose:
- demonstrate shipped output channels, not adapter integration
- keep author data deterministic through a small demo mu-plugin
- make RSS2, Atom, JSON Feed, `fediverse:creator`, and JSON-LD easy to inspect in one disposable site

What it installs:
- `byline-feed` from the `main` branch of this repository via `git:directory`
- a small mu-plugin that injects two normalized authors for the default WordPress sample post
- no third-party multi-author plugin dependencies

Local usage:

```bash
npx @wp-playground/cli@latest server --blueprint=playground/output-demo/blueprint.json
```

Expected demo URLs:
- `/?p=1`
- `/feed/`
- `/feed/atom/`
- `/feed/json/`

Notes:
- This is the source-of-truth bundle for the public Playground demo.
- A later public CTA should point to a snapshot ZIP built from this bundle, not directly to a mutable branch install.
- A separate adapter-demo blueprint for Co-Authors Plus and PublishPress Authors is intentionally deferred.
