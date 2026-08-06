# Omnipoint AI Provider for OpenAI Compatible

WordPress plugin that registers any OpenAI-compatible API endpoint as an AI provider for the WordPress AI Client.

## Requirements

- WordPress 7.0+
- PHP 7.4+
- An OpenAI-compatible API endpoint (API key required unless the provider is local)

## Installation

1. Install and activate the plugin.
2. Go to **Settings → Omnipoint AI** and set the API Endpoint URL.
3. Set the API key under **Settings → Connectors**.
4. Configure the AI Client under **Settings → AI**.

## Configuration

By default the plugin connects to `https://api.openai.com/v1`. Override it via the settings screen, or via environment variables:

```
OMNIPOINT_AI_BASE_URL=https://your-endpoint.example.com/v1
OMNIPOINT_AI_API_KEY=your-api-key
```

## Contributing

1. Install dev dependencies: `composer install`
2. Run linting before submitting a PR: `composer lint` (use `composer format` to auto-fix)
3. Open a pull request with a clear description of the change.

## License

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
