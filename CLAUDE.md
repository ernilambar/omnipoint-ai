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

This plugin is a **provider adapter** for the [WordPress AI Client](https://github.com/WordPress/ai-client) library. It registers any OpenAI-compatible API endpoint (OpenAI itself, or a compatible third-party/self-hosted service) as a provider in the AI Client's provider registry.

### Bootstrap flow

1. `omnipoint-ai.php` loads on `plugins_loaded`, instantiates `Bootstrap`
2. `Bootstrap::register_provider()` registers `OmnipointAiProvider` with `AiClient::defaultRegistry()`
3. `Bootstrap::register_fallback_auth()` sets an API key from the `OMNIPOINT_AI_API_KEY` env var (or empty) if none has been configured through WordPress's own AI credentials screen
4. `Bootstrap` also instantiates `Settings`

### Provider layer (`src/Provider/`, `src/Metadata/`, `src/Models/`)

- **`OmnipointAiProvider`** extends `AbstractApiProvider`. Base URL resolution order: `OMNIPOINT_AI_BASE_URL` env var, then the saved `Settings::get_endpoint_url()` option, then the `https://api.openai.com/v1` default. Factory methods:
  - `createProviderMetadata()` — slug `omnipoint_ai`, display name, icon
  - `createModelMetadataDirectory()` — returns `OmnipointAiModelMetadataDirectory`
  - `createProviderAvailability()` — `ListModelsApiBasedProviderAvailability`, which considers the provider configured if a live `/models` request succeeds

- **`OmnipointAiModelMetadataDirectory`** extends `AbstractOpenAiCompatibleModelMetadataDirectory`. Sends `GET /models`, accepts either a `data` or `models` array in the response (different OpenAI-compatible servers use either key), skips model IDs containing "embed", and treats every remaining model as text-generation + chat-history capable. Since generic `/models` endpoints don't expose per-model capability metadata the way the native OpenAI API's model catalog does, no OpenAI-specific model family detection (GPT/DALL-E/TTS) is performed.

- **`OmnipointAiTextGenerationModel`** extends `AbstractOpenAiCompatibleTextGenerationModel`. Notable overrides:
  - `prepareGenerateTextParams()` — injects the user's saved default model from `wp_options` only when the caller didn't already specify one
  - `parseResponseToGenerativeAiResult()` — when the default-model override was used, replaces the reported model ID with the one the endpoint actually served
  - Does **not** override `prepareResponseFormatParam()` — the parent's default `{ type: json_schema, json_schema: <schema> }` shape already matches the real OpenAI Chat Completions spec

### Settings (`src/Settings.php`)

Admin page under **Settings → Omnipoint AI**. Two options: the API Endpoint URL (`ai_provider_omnipoint_ai_endpoint_url`) and an optional Default Text Model override (`ai_provider_omnipoint_ai_default_model`). The model dropdown is populated via AJAX (`assets/js/settings.js`), which also displays a "N models loaded." (or connection error) status message once the endpoint responds. API key entry is intentionally out of scope for this settings screen — it's configured through WordPress core's own AI credentials UI (Settings → AI → Connectors).

### Key design constraints

- **Generic OpenAI-compatible** — all generation goes through `AbstractOpenAiCompatibleTextGenerationModel`; endpoint is `chat/completions` relative to the configured base URL
- **No image generation** — this provider covers text generation only
- **No persistent object cache assumed** — avoid adding transient/cache logic that depends on cross-process persistence
