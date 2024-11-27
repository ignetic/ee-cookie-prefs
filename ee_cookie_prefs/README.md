EE Cookie Prefs
========================

This addon allows you to change EE cookie variables as well as add SameSite attribute to ExpressionEngine cookies.

For ExpressionEngine versions 3-5

All or individual SameSite cookies can be set as Lax, Strict or None. Simply enter which cookies you would like to apply SameSite cookie attribute to.

### Available Options: ###


#### SameSite=None Cookie Fix ####
Not all browsers are compatible with SameSite=None.
Enable this if you experience issues where these cookies are lost when returning back to the site.
See: https://www.chromium.org/updates/same-site/incompatible-clients


#### Allow 'Cookie Consent' to remain as client side cookies ####
The Cookie Content module normally stores the preferences in the database or cookies inaccessible by javascript.
This forces the Cookie Consent to store it's setting within the cookie.
This will allow you to access this cookie via javascript; for example in cases when using static caching.


#### Override individual cookie settings with entered values. ####
Change any of ExpressionEngine cookie settings to your preferred setting. Each cookie can have the folowing settings applied to it:

- Cookie name: enter the name of the cookie that you would like to change (example exp_last_visit)
- Expires: time in seconds
- Domain: use for system-wide cookies
- Path: path to apply to the cookie
- Secure: cookie will only be transmitted over a secure HTTPS connection
- HttpOnly: when enabled, cookies will not be accessible through JavaScript
- SameSite: SameSite cookies can be set as Lax, Strict or None
See: https://docs.expressionengine.com/latest/control-panel/settings/security-privacy.html#security--privacy

### About SameSite cookies and Chrome: ###

With recent changes to Google Chrome, cookies are now defaulted to SameSite=Lax. This may cause issues when attempting to send cookies to an external site.

This addon can resolve issues where offsite cookies are required, such as offsite payment gateways and tracking that requires cross-site cookies.

Note that not all browsers are compatible with SameSite=None. This addon will handle those that are not compatible and leave the parameter blank.
https://www.chromium.org/updates/same-site/incompatible-clients

