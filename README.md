XRouting
==================

XRouting is a simple plugin that handles requests for different contexts. It automatically switches the context based on a (sub)domain AND/OR subfolder.

It's like the Gateway plugin from the docs, except you don't have to manually edit the plugin: it takes the `http_host` and `base_url` settings you have already configured in your context and routes based on that. It caches the `http_host`/`base_url`-to-context relation so it doesn't perform excessive database lookups.

You can also use `http_host_aliases` to route multiple domains to one context.

Instructions
------------------

All you need to do is to [install](http://modx.com/extras/package/xrouting) this plugin and make sure your contexts have `http_host`, `base_url`, `site_url` and `site_start` context settings set.

/!\ Please make sure to add your `http_host` and `site_url` without `www.` if the ```xrouting.include_www``` setting is enabled (default!)

System Settings
------------------
You can set the following system settings:

+ ```xrouting.include_www``` - automatically include www subdomain (default: yes)
+ ```xrouting.show_no_match_error``` - set to true to show the error page if no matching context has been found (false will show the default context instead)
+ ```xrouting.default_context``` - define the default context if no matching context has been found and ```xrouting.show_no_match_error``` is false
+ `xrouting.allow_debug_info` - activate this setting and add `&xrouting-debug=1` to your URL and you will get a handy debug output if your routing isn't working as expected

Troubleshooting
------------------
If your context routing is not working as expected you can active the `xrouting.allow_debug_info` system setting and add `&xrouting-debug=1` to your URL to get a handy debug output. If you can't find any issue in your debug output feel free to open an issue and paste your debug output into the issue.
