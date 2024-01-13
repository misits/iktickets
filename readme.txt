=== Iktickets ===
Contributors: misits
Tags: Infomaniak, Events
Donate link: https://www.paypal.com/donate/?hosted_button_id=8YDDNMSELC5CS
Requires at least: 5.0
Tested up to: 6.4.2
Requires PHP: 8.0
Stable tag: 1.0.0
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integration of Infomaniak E-tickets into WordPress

== Description ==
This is a ticketing system for Infomaniak Events. It is a Wordpress plugin that allows to sell tickets for events directly on the website.
The plugin needs to be configured with the Infomaniak Events API key and token. It also needs a cron job to synchronize the events with Infomaniak Events.
All events dates, image, description and prices zones are synchronized with Infomaniak Events. The tickets are sold on the website and the orders are synchronized with Infomaniak Events.
Payments are handled by Infomaniak Events. The plugin only query the proper API endpoints and display the form to pay the order or redirect to the payment page.

If you find this plugin helpful, consider supporting its development via PayPal (https://www.paypal.com/donate/?hosted_button_id=8YDDNMSELC5CS).

Key features include:
- Sell tickets for events
- Display event dates on a calendar
- Cron job to synchronize events with Infomaniak Events
- Theme colors customization
- Order as guest or with an account
- Registration form for new accounts
- Payment handled by Infomaniak Events (PostFinance, Twint, Visa, Mastercard, etc.)

== Installation ==
1. Download the repository: [https://github.com/misits/iktickets](https://github.com/misits/iktickets/releases)
2. Upload the `iktickets` folder to your WordPress plugin installation.
3. Activate the plugin through the `Plugins` screen in WordPress.
4. Use the `Iktickets > Settings` menu to configure the plugin.

= Usage =

= Settings =
1. Go to `Iktickets > Settings` and fill in the fields for the Infomaniak Events API. (See Frequently Asked Questions)
2. Change the theme colors if needed.
3. Click on `Save Changes`.

= Events in a page =
A new default `Events` page is created when the plugin is activated. You can use this page to display the events.
A shortcode is also available to display the events in any page. The shortcode is `[iktickets_events]`.

= Code Snippets =
You can use the following code snippets to display the events in a template file.

`echo do_shortcode(\'[iktickets_events]\');`

== Frequently Asked Questions ==
**Q:** How to get the API key?

**A:** The API key can be found in `Infomaniak dashboard > Billetterie > Boutique / Mise en ligne > Accès API`.

**Q:** How to get the API token?

**A:** The API token can be found in `Infomaniak dashboard > Développeur > Tokens API`, then click on `Créer un token API` and select `eticket` as scopes.

**Q:** How to display events in a page?

**A:** The shortcode is `[iktickets_events]` and can be used in any page. You can also use code snippets to display the events in a template file. See (Code Snippets).

== Screenshots ==
1. Shortcut in page
2. Admin events list
3. Admin settings

== Changelog ==
= 1.0.0 =
- Initial release.

== Upgrade Notice ==
Nothing
