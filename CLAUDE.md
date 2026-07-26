# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

The official Bible SuperSearch WordPress plugin: a thin PHP/WordPress wrapper around the Bible SuperSearch JS client and the Bible SuperSearch REST API (default `https://api.biblesupersearch.com`, optionally self-hosted). The plugin renders shortcodes/a widget, stores plugin options, and proxies configuration to the JS client.

## Build / test / run

There is **no build system, package manager, linter, or test suite** — no `composer.json`, no `package.json`, no PHPUnit. All JS/CSS is committed pre-built or hand-written; the admin Vue app is served as native ES modules with Vue/Vuetify/axios vendored under `com_test/js/bin/`. Do not add build tooling or CDN-loaded scripts (WordPress plugin guidelines forbid CDN JS/CSS; CDN *fonts* are allowed — see `Options::displayPluginOptionsNew()`).

Verification is manual, in a real WordPress install:

1. The repo *is* the plugin directory of a live install at `/var/www/wordpress/wp-content/plugins/biblesupersearch` (`/var/www/biblesupersearch/ui-wordpress` is a symlink to the same path), so edits take effect on page reload.
2. Load a page/post containing each touched shortcode; check for PHP fatals and rendered output with both default and custom attributes.
3. Load **Admin menu → Bible SuperSearch** (settings) and → Documentation after touching option code.
4. Per-render debug output is available: set the `debug_shortcode` option to echo shortcode tracing, and `debug` for API request tracing.

## Architecture

**Bootstrap** — `biblesupersearch.php` registers a hand-rolled PSR-4 autoloader (`wp/Autoloader.php`) mapping `BibleSuperSearch\WordPress\` → `wp/php/` and `BibleSuperSearch\Common\` → `com_test/php/src/`, then registers shortcodes, the widget, admin menu, activation hook, and two REST routes.

**Options are the center of the plugin.** `BibleSuperSearch\Common\OptionsAbstract` (`com_test/php/src/OptionsAbstract.php`, ~1000 lines) is a platform-agnostic singleton holding all option logic; `BibleSuperSearch\WordPress\Options` subclasses it and supplies only the WordPress-specific storage/UI hooks (`fetchOptions`/`storeOptions` → `get_option`/`update_option` on `biblesupersearch_options`, statics cache → `biblesupersearch_statics`, admin menus, landing-page lookup via `$wpdb`). Keep platform-neutral logic in the abstract; keep `wp/php/Options.php` limited to WordPress bindings.

**Options are declarative.** `com_test/php/includes/options_list.php` is the single source of truth: it is a tab → field → settings array whose entries generate the default values, the admin UI (tabs, control types, labels, help text), and the validation/casting in `OptionsAbstract::validateOptions()` (which switches on each field's `type`: checkbox/text/select/integer/json). `com_test/php/includes/selector_options_list.php` supplies reusable dropdown item lists; a field's `items` may name a selector list *or* a method on the Options class (e.g. `getInterfaces`), resolved in `initOptions()`. **To add an option, add it to `options_list.php`** — do not hand-add defaults, form markup, or validation branches. WordPress-only options (`overrideCss`, `extraCss`) are the exception and are appended in `Options::__construct()`.

**Statics / API layer** — `getStatics()` caches the API's Bible list, book list, versions, etc. in a WP option, refreshing at most hourly via a `statics_changed` check, falling back to the cached copy on failure and reverting a broken custom `apiUrl` to the default. All API calls go through `_apiActionHelper()`, which tries `file_get_contents` then cURL. Saving options always invalidates the statics cache.

**Shortcode rendering** (`wp/php/Shortcodes.php`) — each shortcode has a static `$...Attributes` map (attribute name in `underscore_case` → `map` to the camelCase option key, plus `desc` used to auto-generate the admin documentation page). `display()` merges options + attributes, then emits a `<script>` block of globals (`biblesupersearch_config_options`, `biblesupersearch_statics`, `biblesupersearch_root_directory`, `biblesupersearch_instances`, `biblesupersearch_form_data`) followed by an empty container div that the client app mounts into. That globals contract is the PHP↔JS interface — changing key names breaks the bundled client. The Enyo client only supports **one instance per page**; the "multiple shortcode" HTML comment and `suppress_instance_error` attribute exist for that reason.

Registered shortcodes: `[biblesupersearch]`, `[biblesupersearch_demo]`, `[biblesupersearch_bible_list]`, `[biblesupersearch_downloads]`. `displayNew()` (Vue app path) exists but its shortcode registration is commented out.

**Admin settings app** — the settings page is a Vue 3 + Vuetify SPA. `Options::displayPluginOptionsNew()` builds a `$bootstrap` object (options, defaults, tabs, option props, statics, REST URL, `X-WP-Nonce`) into `wp/templates/template.options.new.php`; `wp/js/Config.vue.js` is the entry module and mounts `com_test/js/configs/source/ConfigTabs.vue.js`. It reads/writes options through the REST routes `GET|POST /wp-json/biblesupersearch/v1/config` (both gated on `manage_options`), *not* through the WP Settings API form post.

**Widget** (`wp/php/Widget.php`) renders a small search form that submits to a landing page — a published page/post containing the `[biblesupersearch]` shortcode, discovered by SQL scan in `Options::fetchLandingPageOptions()`.

### Directories

- `wp/` — everything WordPress-specific (PHP classes, templates, admin CSS/JS, download page, translations).
- `com_test/` — shared/upstream Bible SuperSearch code (the name is a legacy temp dir name). `com_test/php/` is the platform-agnostic option library, shared with other Bible SuperSearch clients.
- **Do not edit** `com_test/js/app/` (minified legacy Enyo client), `com_test/js/vue-app/` (built next-gen Vue client), or `com_test/js/bin/` (vendored Vue/Vuetify/axios). All are maintained in other repositories and overwritten wholesale on update.

## Conventions and guardrails

`AGENT.md`, `.github/copilot-instructions.md`, and `.github/instructions/*.instructions.md` (scoped by path glob to shortcodes, frontend assets, and wp-admin assets) carry the authoritative rules. In short:

- Backward compatibility is the top priority: never rename shortcode tags, attributes, option keys, or emitted JS globals without an explicit request and a migration path. Legacy parameters stay.
- Treat shortcode attributes and request values as untrusted: `shortcode_atts` with explicit defaults, sanitize on input, escape at output by context (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
- Preserve nonce and `current_user_can('manage_options')` checks on every admin/REST path.
- Small, surgical diffs; match the surrounding PHP style (the codebase predates PSR-12 in places — follow the touched file). No new dependencies.
- Minimum supported: WordPress 5.0, PHP 7.3 — avoid arrow functions, `match`, named arguments, and other 7.4+ syntax (see the commented-out `fn()` permission callbacks in `biblesupersearch.php`).

## Releases and git

- Version lives in two places and must be bumped together: the `Version:` header in `biblesupersearch.php` and `readme.txt` (WordPress.org format — `Stable tag`, `Tested up to`, and a new `== Changelog ==` entry). `com_test/changelog.txt` tracks the client.
- Commit messages: the recent history uses `BSS-###: description` (Jira ticket); `AGENT.md` also documents a `type(scope): description` form.
- Four remotes are configured (github, github-ai, bitbucket, sourceforge); `./pushall` pushes all branches to bitbucket, sourceforge, and github.
