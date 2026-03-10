# Test Coverage Matrix

## How to read

- **Covered:** Reliable automated tests exist and assert expected behavior.
- **Partial:** Some tests exist, but edge cases or contracts are missing.
- **Gap:** No meaningful automated coverage yet.
- **Blocked:** Tests cannot run due to missing infrastructure.

## Infrastructure status

| Area | Status | Notes |
| --- | --- | --- |
| PHPUnit configuration | **Blocked** | No `phpunit.xml.dist` exists. Test suite cannot execute. |
| WordPress test harness | **Blocked** | No `bin/install-wp-tests.sh` or test bootstrap file. |
| CI pipeline | **Gap** | No `.github/workflows/ci.yml`. No automation of any kind. |
| Node build | **Gap** | `npm install` / `npm run build` never executed. No `build/` directory. `perspective-panel.tsx` cannot load in a real WordPress installation. |
| PHPCS automation | **Partial** | `composer.json` has WPCS dependency and `lint` script. Never verified against codebase. |
| Composer test script | **Partial** | `composer test` defined but would fail without `phpunit.xml.dist`. |

## Core domains — MVP (WP-01/02/03)

| Domain | Status | Test file | Notes |
| --- | --- | --- | --- |
| Core adapter — single author resolution | **Covered** | `test-adapter-core.php` | Happy path, role mapping, zero-value fields. |
| Core adapter — invalid/missing author | **Covered** | `test-adapter-core.php` | Returns empty array for user ID 0. |
| Core adapter — role mapping | **Covered** | `test-adapter-core.php` | Editor → staff, Author → contributor. |
| CAP adapter — mixed user/guest authors | **Gap** | Missing file | `test-adapter-cap.php` does not exist. |
| CAP adapter — guest author detection | **Gap** | Missing file | No coverage. |
| CAP adapter — author ordering | **Gap** | Missing file | No coverage. |
| PPA adapter — term meta resolution | **Gap** | Missing file | `test-adapter-ppa.php` does not exist. |
| PPA adapter — linked user fallback | **Gap** | Missing file | No coverage. |
| PPA adapter — guest author handling | **Gap** | Missing file | No coverage. |
| Adapter contract validation | **Gap** | No file | No enforcement of normalized object shape. |
| RSS2 namespace declaration | **Covered** | `test-feed-rss2.php` | Verifies `xmlns:byline` present. |
| RSS2 contributors block | **Covered** | `test-feed-rss2.php` | Verifies `<byline:person>` in channel head. |
| RSS2 per-item author refs | **Covered** | `test-feed-rss2.php` | Verifies `<byline:author ref>` matches contributor. |
| RSS2 perspective output | **Covered** | `test-feed-rss2.php` | Present when set, absent when unset. |
| RSS2 well-formed XML | **Covered** | `test-feed-rss2.php` | XML parse succeeds. |
| RSS2 profile/now/uses elements | **Gap** | `test-feed-rss2.php` | Elements not implemented in `output_person()`. |
| RSS2 multi-author per item | **Gap** | `test-feed-rss2.php` | No test for multiple `<byline:author>` on one item. |
| RSS2 standard elements preserved | **Gap** | `test-feed-rss2.php` | No test that `<author>` / `<dc:creator>` survive. |
| RSS2 empty-field omission | **Gap** | `test-feed-rss2.php` | No test that empty optional fields produce no elements. |
| Atom namespace declaration | **Gap** | Missing file | `test-feed-atom.php` does not exist. |
| Atom contributors block | **Gap** | Missing file | No coverage. |
| Atom per-entry author refs | **Gap** | Missing file | No coverage. |
| Atom filter parity with RSS2 | **Gap** | Missing file | Atom layer has no extensibility hooks. |
| Perspective — valid value accepted | **Covered** | `test-perspective.php` | All 12 allowed values pass. |
| Perspective — invalid value rejected | **Covered** | `test-perspective.php` | Returns empty string. |
| Perspective — filter override | **Covered** | `test-perspective.php` | Filter can replace value. |
| Perspective — empty when unset | **Covered** | `test-perspective.php` | No meta returns empty. |
| Perspective — block editor panel | **Gap** | No test | TSX never built; no browser or integration test. |

## Core domains — Post-MVP (WP-04/05/06)

| Domain | Status | Test file | Notes |
| --- | --- | --- | --- |
| fediverse:creator meta tag output | **Gap** | Missing file | `inc/fediverse.php` and tests do not exist. |
| fediverse handle normalization | **Gap** | Missing file | No code exists. |
| fediverse user profile field | **Gap** | Missing file | No UI exists. |
| JSON-LD Article + Person schema | **Gap** | Missing file | `inc/schema.php` and tests do not exist. |
| JSON-LD sameAs from profiles | **Gap** | Missing file | No code exists. |
| JSON-LD Yoast/Rank Math detection | **Gap** | Missing file | No code exists. |
| AI consent resolution logic | **Gap** | Missing file | `inc/rights.php` and tests do not exist. |
| AI consent HTML meta output | **Gap** | Missing file | No code exists. |
| AI consent TDM headers | **Gap** | Missing file | No code exists. |
| ai.txt generation | **Gap** | Missing file | No code exists. |
| Consent audit logging | **Gap** | Missing file | No code exists. |

## Priority backlog (highest impact first)

1. **Create `phpunit.xml.dist` and test bootstrap.** Unblocks all existing and future tests.
2. **Create `.github/workflows/ci.yml`.** Enables automated verification on every commit.
3. **Write `test-adapter-cap.php`.** The CAP adapter is the most widely-used adapter path and has zero coverage.
4. **Write `test-adapter-ppa.php`.** Same rationale — second most common adapter path.
5. **Write `test-feed-atom.php`.** Atom output has zero coverage.
6. **Add RSS2 tests for multi-author, standard-element preservation, and empty-field omission.** These are spec-required scenarios with no test.
7. **Add adapter contract validation tests.** Verify that malformed objects are caught before reaching output.
8. **Run `npm run build` and verify perspective panel loads.** The block editor UI has never been compiled.

## Quality target

- No **Blocked** items in infrastructure.
- No **Gap** items in security-critical or adapter domains before Gate A.
- CI green on PHPCS + PHPUnit matrix for all PRs.
- Every spec divergence fix includes a test proving the corrected behavior.

## Related documents

- [ASSESSMENT.md](ASSESSMENT.md) — Project assessment
- [Implementation Strategy/gap-analysis.md](Implementation%20Strategy/gap-analysis.md) — Detailed gap audit
- [TDD_TESTING_STANDARD.md](TDD_TESTING_STANDARD.md) — Testing protocol
- [Implementation Strategy/implementation-spec.md](Implementation%20Strategy/implementation-spec.md) — Test strategy and testing matrix
