---
applyTo: "wp/*.php,wp/*.css,wp/*.js,templates/*.php"
---

# WordPress Admin Asset Guardrails

Use these rules when editing WordPress admin pages, option templates, and admin assets.

## Scope and Compatibility

- Preserve existing admin page structure, form field names, and option keys.
- Keep backward compatibility for saved options and legacy defaults.
- Prefer minimal, targeted edits that do not alter unrelated admin flows.

## Security

- Treat all request and form values as untrusted.
- Sanitize before persistence with appropriate WordPress helpers.
- Escape all output by context (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
- Preserve nonce and capability checks in admin save/render paths.

## WordPress Conventions

- Prefer WordPress APIs for settings/forms/messages where applicable.
- Keep hooks and callback signatures unchanged unless explicitly requested.
- Maintain expected option array shapes used by existing runtime code.

## UI and Assets

- Preserve current admin UI structure unless a redesign is requested.
- Avoid global CSS/JS behavior that can affect unrelated wp-admin screens.
- Keep selectors and script hooks stable if referenced by existing code.

## Performance and Stability

- Avoid repeated expensive operations in admin render loops.
- Avoid adding new dependencies unless explicitly requested.

## Do Not

- Do not rename option keys without a migration path.
- Do not remove legacy fields/paths unless explicitly requested.
- Do not mix unrelated refactors into admin-focused changes.
