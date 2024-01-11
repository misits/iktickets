# Iktickets
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-1-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->

## Description

This is a ticketing system for Infomaniak Events. It is a Wordpress plugin that allows to sell tickets for events directly on the website.

The plugin needs to be configured with the Infomaniak Events API key and token. It also needs a cron job to synchronize the events with Infomaniak Events.

All events dates, image, description and prices zones are synchronized with Infomaniak Events. The tickets are sold on the website and the orders are synchronized with Infomaniak Events.

Payments are handled by Infomaniak Events. The plugin only query the proper API endpoints and display the form to pay the order or redirect to the payment page.

If you find this plugin helpful, consider supporting its development via [PayPal](https://www.paypal.com/donate/?hosted_button_id=8YDDNMSELC5CS).

## Key Features

- Sell tickets for events
- Display event dates on a calendar
- Cron job to synchronize events with Infomaniak Events
- Theme colors customization
- Order as guest or with an account
- Registration form for new accounts
- Payment handled by Infomaniak Events (PostFinance, Twint, Visa, Mastercard, etc.)

## Installation

1. Clone the repository: `git clone https://github.com/misits/iktickets.git`
2. Upload the `iktickets` folder to the `/wp-content/plugins/` directory on your WordPress installation.
3. Activate the plugin through the `Plugins` screen in WordPress.
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

## Todo

- [ ] Add a shortcode to display a specific event with its tickets
- [ ] Allow users to reset their password
- [ ] Style my account page
- [ ] Style single event page
- [ ] Display banner on top of the page when action is done or error
- [ ] Clean vue components & scss double code
- [ ] Translate plugin
- [ ] ... and more

## Changelog

### 1.0.0

- Initial release.

## Support

For support, please visit [Iktickets Issues](https://github.com/misits/iktickets/issues).

## License

This project is licensed under the [GNU General Public License v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

## Donations

If you find this plugin helpful, consider supporting its development via [PayPal](https://www.paypal.com/donate/?hosted_button_id=8YDDNMSELC5CS).

## Contributors

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tbody>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="http://jean-christio.ch"><img src="https://avatars.githubusercontent.com/u/27312102?v=4?s=100" width="100px;" alt="Martin Jean-Christio"/><br /><sub><b>Martin Jean-Christio</b></sub></a><br /><a href="#infra-MarJC5" title="Infrastructure (Hosting, Build-Tools, etc)">🚇</a> <a href="https://github.com/misits/iktickets/commits?author=MarJC5" title="Tests">⚠️</a> <a href="https://github.com/misits/iktickets/commits?author=MarJC5" title="Code">💻</a></td>
    </tr>
  </tbody>
</table>

<!-- markdownlint-restore -->
<!-- prettier-ignore-end -->

<!-- ALL-CONTRIBUTORS-LIST:END -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->

<!-- markdownlint-restore -->
<!-- prettier-ignore-end -->

<!-- ALL-CONTRIBUTORS-LIST:END -->