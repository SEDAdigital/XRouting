## How it works

XRouting is a simple plugin that handles requests for different contexts. It
automatically switches the context based on a (sub)domain AND/OR subfolder.

It's like the Gateway plugin from the MODX docs, except you don't have to
manually edit the plugin: it takes the `http_host` and `base_url` settings you
have already configured in your context and routes based on that. It caches the
`http_host`/`base_url`-to-context relation, so it doesn't perform excessive
database lookups.

You can also fill the `http_host_aliases` context setting with a comma separated
list to route multiple domains to one context.

## Instructions

All you need to do is to install this plugin and make sure your contexts have
`http_host`, `base_url`, `site_url`, `site_start` and optional
`http_host_aliases` context settings set.

To easily edit these context settings side by side, you can use the
[CrossContextsSettings]( https://modx.com/extras/package/crosscontextssettings)
extra.

!!! caution
    Please make sure to add your `http_host` and `site_url` without `www.` when the 
    `xrouting.include_www` setting is enabled (default!)

## System Settings

XRouting uses the following system settings in the namespace `xrouting`:

| Key                          | Name                  | Description                                                                                                                                                                                                | Default |
|------------------------------|-----------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------|
| xrouting.allow_debug_info    | Allow debug output    | Enable this setting to output debug info if the &xrouting-debug=1 GET parameter is set. ATTENTION: disable it again after you debugged your installation, since this exposes a lot information of project! | No      |
| xrouting.default_context     | Default context       | The default context to redirect to if no matches were found and xrouting.show_no_match_error is set to No.                                                                                                 | web     |
| xrouting.include_www         | Include WWW-subdomain | Specifies if the www-subdomain should automatically be included when matching against the base domain, ie. www.example.com should return the same context as example.com.                                  | Yes     |
| xrouting.show_no_match_error | Return error messages | If set to yes, XRouting will return an error instead of redirecting to the default context.                                                                                                                | Yes     |

## Troubleshooting

If your context routing isn't working as expected you can activate the
`xrouting.allow_debug_info` system setting and add `&xrouting-debug=1` to your
URL to get a handy debug output. If you can't find any issue in your debug
output feel free to [open an issue](https://github.com/Jako/XRouting/issues) and
paste your debug output into the issue.
