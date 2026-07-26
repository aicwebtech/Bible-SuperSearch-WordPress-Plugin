# AGENT.md

This repository is a WordPress plugin for BibleSuperSearch.
Primary stack: PHP + WordPress hooks/shortcodes + bundled JS/CSS assets.

## Goal

Make safe, minimal, production-ready changes to the plugin while preserving backward compatibility for WordPress installs.

## Project Layout

- `biblesupersearch.php`: Main plugin bootstrap and registration entry.
- `wp/`: WordPress runtime integration (shortcodes, widgets, options pages, templates).
- `com_test/`: Bible SuperSearch base/common files. 
- `com_test/js/`: Frontend app assets and Vue/Vuetify code used by plugin tooling and UI.
- `com_test/js/app/`: *Do not edit, maintained elsewhere* Legacy Enyo.js based BibleSuperSearch UI app
- `com_test/js/configs/`: Admin configs app (Vue/Vuetify)
- `com_test/js/vue-app/`: *Do not edit, maintained elsewhere* Future rebuilt BibleSuperSearch UI app (Vue/Vuetify)
- `com_test/php/`: Shared plugin PHP option/config logic.
- `wp/templates/`: Option page templates.

## High-Value Files

- `wp/php/Shortcodes.php`: Shortcode handlers and output generation.
- `wp/php/Widget.php`: Widget registration and rendering.
- `wp/php/Options.php`: WP option handling.
- `com_test/php/src/OptionsAbstract.php`: WP option handling - generic abstract.
- `biblesupersearch.php`: Plugin initialization and hooks.

## Coding Rules

1. Keep public shortcode behavior backward compatible unless explicitly asked otherwise.
2. Escape all output and sanitize all input:
   - Output: `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` as appropriate.
   - Input: `sanitize_text_field`, `absint`, `sanitize_key`, and strict allowlists.
3. For shortcode attributes, use `shortcode_atts` with explicit defaults.
4. Avoid introducing global state; prefer class methods and WordPress hooks.
5. Do not hardcode site URLs or environment-specific paths.
6. Keep diffs small and avoid refactoring unrelated code.
7. Match the existing PHP style of the file being touched; much of this codebase predates `PSR-12`, and reformatting it is an unrelated refactor. Use `PSR-12` for new files.

## Instruction Scopes

Use these scoped instruction files to determine task-specific guardrails:

- `.github/instructions/wordpress-shortcodes.instructions.md`: Applies to shortcode and core WordPress integration files (`wp/php/Shortcodes.php`, `wp/*.php`, `biblesupersearch.php`).
- `.github/instructions/frontend-assets.instructions.md`: Applies to frontend asset sources in `com_test/js/app`, `com_test/js/configs`, and `com_test/js/vue-app`.
- `.github/instructions/wp-admin-assets.instructions.md`: Applies to admin-facing PHP/CSS/JS in `wp/php/*.php`, `wp/css/*.css`, `wp/js/*.js`, and option templates in `wp/templates/*.php`.

When multiple scopes could match, prioritize the most specific file path and preserve backward compatibility.

## WordPress Shortcode Checklist

When editing shortcode logic:

1. Confirm all attributes have defaults and type handling.
2. Confirm rendering handles missing/invalid attributes safely.
3. Escape output for the destination context (HTML/text/URL/attribute).
4. Preserve existing shortcode names and aliases.
5. Avoid costly per-render work that can be cached or reused.

## Testing Expectations

At minimum after shortcode-related changes:

1. Load plugin in a local WordPress instance without fatal errors.
2. Render each touched shortcode in a test post/page.
3. Verify output for both default attributes and custom attributes.
4. Check that admin pages still load if option code was touched.

## Change Policy

- Prefer small, surgical edits.
- Do not rename existing shortcode tags unless requested.
- If behavior must change, document old vs new behavior in the PR/summary.

## Commit Message Guidance

Use clear, action-oriented messages, for example:

- `fix(shortcode): sanitize and validate attributes`
- `fix(wp): escape shortcode html output`
- `feat(options): add default for language selector`
