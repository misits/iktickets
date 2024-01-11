# Iktickets

## Description

This is a ticketing system for Infomaniak Events. It is a Wordpress plugin that allows to sell tickets for events directly on the website.

If you find this plugin helpful, consider supporting its development via [PayPal](https://www.paypal.com/donate/?hosted_button_id=8YDDNMSELC5CS).

## Key Features

- Sell tickets for events
- Display event dates on a calendar
- Cron job to synchronize events with Infomaniak Events
- Theme colors customization

## Installation

1. Clone the repository: `git clone https://github.com/misits/iktickets.git`
2. Upload the `iktickets` folder to the `/wp-content/plugins/` directory on your WordPress installation.
3. Activate the plugin through the 'Plugins' screen in WordPress.
4. Use the `Iktickets > Settings` menu to configure the plugin.

## Usage

### Settings

1. Go to `Iktickets > Settings` and fill in the fields for the Infomaniak Events API. (See [Frequently Asked Questions](#frequently-asked-questions))
2. Change the theme colors if needed.
3. Click on `Save Changes`.

### Events in a page

A new default `Events` page is created when the plugin is activated. You can use this page to display the events.

A shortcode is also available to display the events in any page. The shortcode is `[iktickets_events]`.

### Code Snippets

You can use the following code snippets to display the events in a template file.

```php
<?php echo do_shortcode('[iktickets_events]'); ?>
```

## Frequently Asked Questions

**Q:** How to get the API key?
**A:** The API key can be found in `Infomaniak dashboard > Billetterie > Boutique / Mise en ligne > Accès API`.

**Q:** How to get the API token?
**A:** The API token can be found in `Infomaniak dashboard > Développeur > Tokens API`, then click on `Créer un token API` and select `eticket` as scopes.

**Q:** How to display events in a page?
**A:** The shortcode is `[iktickets_events]` and can be used in any page. You can also use code snippets to display the events in a template file. See [Code Snippets](#code-snippets).

## Changelog

### Beta 1.0.0

- Initial release.

## Support

For support, please visit [Iktickets Issues](https://github.com/misits/iktickets/issues).

## License

This project is licensed under the [GNU General Public License v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

## Donations

If you find this plugin helpful, consider supporting its development via [PayPal](https://www.paypal.com/donate/?hosted_button_id=8YDDNMSELC5CS).
