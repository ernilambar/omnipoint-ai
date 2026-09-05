# AGENTS.md

## Project Overview

WordPress plugin that registers any OpenAI-compatible API endpoint as an AI
provider for the WordPress AI Client. Users configure a base URL and API key
via settings or environment variables (`OMNIPOINT_AI_BASE_URL`,
`OMNIPOINT_AI_API_KEY`).

## Setup

```bash
composer install
pnpm install
```

## Commands

```bash
composer lint        # Run PHP lint + PHPCS
composer format      # Auto-fix with PHPCBF
composer pot         # Generate POT file
pnpm run ready       # Install optimized vendor for deploy
```

## Code Style

- PHP 7.4+ minimum. WordPress 7.0+ target.
- PHP: tabs for indent, LF line endings, short array syntax (`[]`).
- Follows WordPress Coding Standards + NilambarCodingStandard + Slevomat.
- PSR-4: `Nilambar\OmnipointAi\` maps to `src/`.
- Always `declare( strict_types=1 );` and `ABSPATH` guard in PHP files.
- Text domain: `omnipoint-ai`. Use `esc_html__()`, `esc_attr__()`, etc.
- Import classes via `use` statements; avoid fully-qualified names.
- PHPCS config: `.phpcs.xml.dist`.

## Quality Gate

Run `composer lint` before submitting changes. Fix all PHPCS warnings.
