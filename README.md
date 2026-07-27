# Omnipoint AI Provider for OpenAI Compatible

WordPress plugin that registers any OpenAI-compatible API endpoint as an AI provider for the [WordPress AI Client](https://make.wordpress.org/core/2025/03/13/ai-client-for-wordpress/).

## Requirements

- WordPress 7.0+
- PHP 7.4+
- An OpenAI-compatible API endpoint and API key

## Installation

1. Install and activate the plugin.
2. Go to **Settings → Omnipoint AI** and set the API Endpoint URL.
3. Set the API key under **Settings → AI → Connectors**.
4. Configure the AI Client under **Settings → AI**.

## Configuration

By default the plugin connects to `https://api.openai.com/v1`. Override it via the settings screen, or via environment variables:

```
OMNIPOINT_AI_BASE_URL=https://your-endpoint.example.com/v1
OMNIPOINT_AI_API_KEY=your-api-key
```

## License

GPL-2.0-or-later — see [LICENSE](https://spdx.org/licenses/GPL-2.0-or-later.html).
