---
applyTo: "wp/class.shortcodes.php,wp/*.php,biblesupersearch.php"
---

# WordPress Shortcode Guardrails

Use these rules when editing shortcode or WordPress integration code.

## Shortcode Safety

- Define attributes with `shortcode_atts` and explicit defaults.
- Sanitize and normalize attributes before branching logic.
- Escape all rendered output for the appropriate context.
- Keep shortcode output deterministic for identical inputs.

## WordPress Plugin Conventions

- Prefer WordPress APIs over custom helpers when equivalent.
- Preserve hooks, action/filter signatures, and public shortcode tags.
- Return rendered HTML strings from shortcode handlers; do not echo unexpectedly.

## Performance

- Avoid repeated expensive computation inside shortcode rendering paths.
- Reuse parsed defaults and avoid unnecessary database reads per render.

## Do Not

- Do not introduce breaking output changes unless explicitly requested.
- Do not weaken sanitization/escaping to preserve malformed input.
- Do not add unrelated refactors in shortcode-focused tasks.
