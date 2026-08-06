=== Omnipoint AI Provider for OpenAI Compatible ===

Contributors: nilambar
Tags: ai, openai, ai client, connector, api
Requires at least: 7.0
Tested up to: 7.0
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI provider for OpenAI-compatible endpoints.

== Description ==

Omnipoint AI registers any OpenAI-compatible API endpoint as a provider for the WordPress AI Client.

Set an API endpoint URL and, then use it directly from the AI Client wherever supported.

= Features =

* Works with any OpenAI-compatible chat completions API
* Configurable API endpoint URL
* Shows a live model count once connected

= Requirements =

* WordPress 7.0 or later
* PHP 7.4 or later
* An OpenAI-compatible API endpoint and API key (optional for local provider)

== Installation ==

= Using FTP =

1. Extract 'omnipoint-ai.zip' to your computer
1. Upload the 'omnipoint-ai' directory to your '/wp-content/plugins/' directory
1. Activate the plugin on the WordPress Plugins dashboard
1. Go to Settings → Omnipoint AI and set the API Endpoint URL
1. Set the API key under Settings → Connectors
1. Configure the AI Client under Settings → Omnipoint AI

== Frequently Asked Questions ==

= Do I need an API key? =

Yes, unless the endpoint you configure does not require one. Set it under Settings → Connectors.

= What is the default endpoint? =

`https://api.openai.com/v1` — override it with any OpenAI-compatible endpoint under Settings → Omnipoint AI.

== Changelog ==

= 1.0.1 - 2026-08-06 =
* Added: updater implementation from GitHub

= 1.0.0 - 2026-07-30 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release.
