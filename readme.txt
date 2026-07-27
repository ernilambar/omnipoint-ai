=== Omnipoint AI Provider for OpenAI Compatible ===

Contributors: nilambar
Tags: ai, openai, ai client, connector, api
Requires at least: 7.0
Tested up to: 7.0
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Omnipoint AI provider for OpenAI-compatible endpoints for the WordPress AI Client.

== Description ==

Omnipoint AI registers any OpenAI-compatible API endpoint as a provider for the WordPress AI Client (WordPress 7.0+).

Set an API endpoint URL and, optionally, a default text model, then use it directly from the AI Client wherever supported.

= Features =

* Works with any OpenAI-compatible chat completions API
* Configurable API endpoint URL
* Optional default text model override
* Shows a live model count once connected

= Requirements =

* WordPress 7.0 or later
* PHP 7.4 or later
* An OpenAI-compatible API endpoint and API key

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to Plugins → Add New Plugin
1. Search for "Omnipoint AI Provider for OpenAI Compatible"
1. Install and activate the plugin
1. Go to Settings → Omnipoint AI and set the API Endpoint URL
1. Set the API key under Settings → AI → Connectors
1. Configure the AI Client under Settings → AI

= Using FTP =

1. Extract 'omnipoint-ai.zip' to your computer
1. Upload the 'omnipoint-ai' directory to your '/wp-content/plugins/' directory
1. Activate the plugin on the WordPress Plugins dashboard
1. Go to Settings → Omnipoint AI and set the API Endpoint URL
1. Set the API key under Settings → AI → Connectors
1. Configure the AI Client under Settings → AI

== Frequently Asked Questions ==

= Do I need an API key? =

Yes, unless the endpoint you configure does not require one. Set it under Settings → AI → Connectors.

= What is the default endpoint? =

`https://api.openai.com/v1` — override it with any OpenAI-compatible endpoint under Settings → Omnipoint AI.

= Which models are supported? =

Any text generation model exposed by the configured endpoint's models listing.

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release.
