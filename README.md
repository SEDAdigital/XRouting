XRouting
==================

XRouting is a simple plugin that handles requests for different contexts. It automatically switches the context based on a (sub)domain AND/OR subfolder.

It's like the Gateway plugin from the docs, except you don't have to manually edit the plugin: it takes
the http_host and base_url settings you have already configured in your context and routes based on that. It caches the http_host/base_url => context relation so it doesn't perform excessive database lookups.


Instructions
------------------

All you need to do is to install this plugin and make sure your contexts have http_host and base_url context settings set.


System Settings
------------------
You can add the following system settings:
+ ```xrouting.include_www``` - automatically include www subdomain
+ ```xrouting.show_no_match_error``` - set to true to show the error page if no matching context has been found (false will show the default context instead)
+ ```xrouting.default_context``` - define the default context if no matching context has been found and ```xrouting.show_no_match_error``` is false
+ ```xrouting.reset_after_contextswitch_settings``` - commaseperated list of system-settings, if some system-settings shouldn't be touched on contextSwitch, for example ```cultureKey,base_url```, which was set by another plugin at onInitCulture
