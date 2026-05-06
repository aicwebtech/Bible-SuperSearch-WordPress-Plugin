# Copilot Instructions

This repository is a WordPress plugin using shortcodes.

## What To Optimize For

- Backward-compatible shortcode behavior.
- WordPress security best practices (sanitize input, escape output).
- Minimal changes that preserve existing structure and APIs.

## Implementation Preferences

- For shortcode changes, edit `wp/class.shortcodes.php` unless told otherwise.
- Keep shortcode parsing deterministic and explicit.
- Use `shortcode_atts` with stable defaults.
- Avoid adding new dependencies unless necessary.

## Security Requirements

- Treat all shortcode attributes and request values as untrusted input.
- Sanitize before use, validate against expected formats, and escape at output.
- Never output raw user-provided values without the correct escaping function.

## Compatibility Requirements

- Support existing shortcode names and common attribute patterns.
- Avoid changing output HTML structure unless required.
- Do not remove legacy parameters unless explicitly requested.

## Style

- Match existing PHP style in touched files.
- Prefer straightforward, readable conditionals over clever abstractions.
- Add brief comments only where logic is non-obvious.
