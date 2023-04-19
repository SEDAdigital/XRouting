# XRouting

Simple routing plugin that handles requests for different contexts.

### Requirements

* MODX Revolution 2.8+
* PHP 7.2+

### Features

- Switch the context based on a (sub)domain AND/OR subfolder.
- Define the routing based on the http_host and base_url context settings.
- Cache the http_host/base_url => context settings to avoid database lookups on page load.
