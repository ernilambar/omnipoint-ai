# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dev dependencies
composer install

# Lint (PHP syntax + PHPCS)
composer lint

# Auto-fix PHPCS violations
composer format

# PHPCS only
composer phpcs

# i18n: generate .pot template
composer pot

# i18n: update .po files
composer po

# i18n: compile .mo files
composer mo

# Build production package (strips dev deps, outputs to deploy/)
pnpm run deploy
```

## Quality Gate

Every task must end with:
- `composer lint` — 0 errors, 0 warnings (run `composer format` to auto-fix first)

## Architecture

**Provider adapter** for the [WordPress AI Client](https://github.com/WordPress/ai-client) library. Registers any OpenAI-compatible API endpoint (OpenAI itself, or a third-party/self-hosted service) in the AI Client's provider registry.

### Bootstrap flow

1. `omnipoint-ai.php` loads on `plugins_loaded`, instantiates `Bootstrap`
2. `Bootstrap::register_provider()` registers `OmnipointAiProvider` with `AiClient::defaultRegistry()`
3. `Bootstrap::register_fallback_auth()` sets an API key from `OMNIPOINT_AI_API_KEY` env var if none is set via WordPress's AI credentials screen
4. `Bootstrap` also instantiates `Settings`

### Provider layer (`src/Provider/`, `src/Metadata/`, `src/Models/`)

- **`OmnipointAiProvider`** extends `AbstractApiProvider`. Base URL: `OMNIPOINT_AI_BASE_URL` env var → `Settings::get_endpoint_url()` → `https://api.openai.com/v1` default.
- **`OmnipointAiModelMetadataDirectory`** extends `AbstractOpenAiCompatibleModelMetadataDirectory`. Fetches `GET /models` (accepts `data` or `models` key), skips IDs containing "embed". Generic `/models` responses carry no per-model capability data, so every remaining model is declared text-generation + chat-history, and every model unconditionally declares vision (`text`+`image` input modality) — an incompatible model fails at request time rather than being filtered out upfront.
- **`OmnipointAiTextGenerationModel`** extends `AbstractOpenAiCompatibleTextGenerationModel`. Overrides `prepareGenerateTextParams()` to inject the saved default model, and `parseResponseToGenerativeAiResult()` to report back the actual model ID served.

### Settings (`src/Settings.php`)

Admin page under **Settings → Omnipoint AI**: API Endpoint URL and an optional Default Text Model override, populated via AJAX (`assets/js/settings.js`). API key entry is out of scope — configured via WordPress core's AI credentials UI.
