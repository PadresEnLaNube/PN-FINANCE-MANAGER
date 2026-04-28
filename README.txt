=== Personal Finance Manager - PN ===
Contributors: felixmartinez, hamlet237
Donate link: https://padresenlanube.com/
Tags: finance, portfolio, stocks, cryptocurrency, asset manager
Requires at least: 3.0
Tested up to: 6.9
Stable tag: 1.1.4
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Personal finance manager with asset and liability tracking, real-time stock and cryptocurrency portfolio monitoring, interactive charts, multi-currency support, and portfolio export/import.

== Description ==

Personal Finance Manager - PN is a comprehensive personal finance plugin for WordPress that lets users track their complete financial picture: assets, liabilities, investments, and net worth, all from the front-end of your site.

= Asset Tracking (13 Categories) =

Track any type of asset with category-specific fields:

* **Stocks** - Real-time prices via Twelve Data API, share count, purchase price (auto-fetched from historical data), sector classification, country diversification.
* **Cryptocurrencies** - Real-time prices via CoinGecko API, coin amount with high decimal precision, 24h price change tracking.
* **Real Estate** - Purchase price, current market value, ownership percentage (0-100%).
* **Bonds** - Face value, coupon rate, maturity tracking.
* **Commodities** - Name, amount, unit, and current valuation.
* **Precious Metals** - Type (Gold, Silver, Platinum, Palladium), weight, unit (troy ounce, gram, kilogram).
* **Art & Collectibles** - Category, purchase price, estimated current value.
* **Vehicles** - Purchase and current value tracking.
* **Business Equity** - Ownership stake valuation.
* **Intellectual Property** - IP asset tracking with valuations.
* **Retirement Accounts** - Balance and contribution tracking.
* **Insurance Policies** - Policy value tracking.
* **Other** - Flexible fields for any asset type.

Every asset supports a title, description, purchase date, and the ability to mark it as **Sold** with a sale date and sale price, which freezes the profit/loss calculation and stops API calls.

= Liability Tracking (9 Categories) =

* **Mortgage** - Original amount, remaining balance, interest rate, monthly payment, maturity date.
* **Car Loan** - Balance, interest rate, monthly payment.
* **Student Loan** - Balance, interest rate, monthly payment.
* **Credit Card Debt** - Credit limit, current balance, interest rate, minimum payment.
* **Personal Loan** - Balance, interest rate, monthly payment.
* **Medical Debt** - Balance and provider information.
* **Business Loan** - Flexible debt tracking.
* **Tax Obligations** - Tax debt tracking.
* **Other** - Custom liability fields.

= Real-Time Stock & Crypto Monitoring =

* **Twelve Data API** integration for US stocks (free tier: 800 calls/day).
* **CoinGecko API** integration for cryptocurrencies (free, no API key required).
* Automatic daily price recording via WordPress cron.
* 365-day price history retention.
* Configurable API cache duration (5 minutes to 24 hours).
* Historical purchase price auto-retrieval.

= Portfolio Overview & Interactive Charts =

A full financial dashboard powered by Chart.js with collapsible sections:

* **Financial Overview** - Total assets, total invested, total liabilities, net worth, profit/loss, and total return percentage.
* **Asset & Liability Distribution** - Horizontal bar chart showing all categories with net worth.
* **Stock Portfolio Performance** - Line chart with historical value and invested baseline.
* **Crypto Portfolio Performance** - Separate line chart for cryptocurrency holdings.
* **Stock Holdings Breakdown** - Doughnut chart of individual stock positions.
* **Sector Diversification** - Doughnut chart by industry sector (Technology, Healthcare, Financials, etc.).
* **Country Diversification** - Doughnut chart by country (27 countries supported).
* **Crypto Holdings Breakdown** - Doughnut chart of cryptocurrency positions.
* **Real Estate Breakdown** - Doughnut chart of property holdings.

All charts are responsive, interactive with tooltips, and display values in the user's selected currency.

= Watchlist with Price Alerts =

* Add stocks and cryptocurrencies to a watchlist without owning them.
* Enable price alerts per item with configurable thresholds (1-50%).
* Automatic price monitoring via daily cron.
* Email notifications when thresholds are triggered.

= Multi-Currency Support (27 Currencies) =

EUR, USD, GBP, JPY, CHF, CAD, AUD, CNY, INR, BRL, RUB, KRW, MXN, SGD, HKD, NOK, SEK, DKK, NZD, ZAR, TRY, PLN, THB, IDR, MYR, PHP, CZK.

All API prices are fetched in USD and automatically converted for display in the user's chosen currency.

= User Preferences =

Each user can configure:

* **Display Currency** - Independent from the site-wide default.
* **Number Format** - Decimal and thousands separator style (1,234.56 / 1.234,56 / 1 234,56).
* **Comparison Period** - Daily, Weekly, Monthly, Yearly, or Since Purchase for price change badges.
* **Alert Preferences** - Default threshold, enable/disable for assets and watchlist.

= Portfolio Export & Import =

* Export your complete portfolio (assets, liabilities, watchlist, and preferences) as a JSON file.
* Import from a previously exported file with duplicate prevention.
* Accessible from the user profile popup (requires UsersPn plugin) or via shortcode.

= User Roles & Permissions =

* Custom **Personal Finance Manager - PN** role with dedicated capabilities.
* Admin can assign/remove the role from the Settings page.
* Users only see their own assets and liabilities.
* Role-based access control on all CRUD operations.

= Shortcodes =

* `[pn-personal-finance-manager-asset-list]` - Front-end asset management interface.
* `[pn-personal-finance-manager-liability-list]` - Front-end liability management interface.
* `[pn_personal_finance_manager_user_assets]` - Portfolio overview with all charts and metrics.
* `[pn-personal-finance-manager-watchlist]` - Watchlist interface with alerts.

= Admin Tools =

* **API Status Check** - Verify Twelve Data API connectivity.
* **Manual Symbol Update** - Force-refresh available stock symbols.
* **Cache Statistics** - View symbol count and cache size.
* **Required Pages Setup** - One-click page creation for each shortcode.
* **Primary Color Customization** - Set the accent color used across the plugin UI.
* **Diagnostic Logging** - Debug information for troubleshooting.

= Internationalization =

Fully translation-ready with 200+ translatable strings. Text domain: `pn-personal-finance-manager`. Compatible with Loco Translate and Polylang.

= Compatibility =

* Works with any WordPress theme (classic and block themes).
* Polylang multi-language support.
* UsersPn plugin integration for profile popup tabs.
* Responsive design for mobile and tablet.


== Installation ==

1. Upload the `pn-personal-finance-manager` folder to the `/wp-content/plugins/` directory, or install directly through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Settings > Personal Finance Manager** to configure your API keys and preferences.
4. Create the required pages using the one-click setup buttons in the Settings page, or manually create pages with the shortcodes listed above.
5. (Optional) Configure the Twelve Data API key for real-time stock data.

== Frequently Asked Questions ==

= Do I need an API key? =

For stock tracking, you need a free Twelve Data API key (800 calls/day on the free plan). Cryptocurrency tracking uses CoinGecko's free API and requires no key.

= How do I track my investments? =

Navigate to the Assets page on your site, click "Add New", choose the asset type (Stocks, Crypto, Real Estate, etc.), and fill in the details. The plugin will automatically track prices and calculate profit/loss.

= Can each user have their own portfolio? =

Yes. Each user only sees their own assets and liabilities. Admins can view all portfolios.

= What happens when I sell an asset? =

Edit the asset and check the "Asset Sold" checkbox. Enter the sale date and sale price. The plugin will freeze the profit/loss at the sale price and stop making API calls for that asset.

= Can I change the display currency? =

Yes. Each user can choose their display currency from 27 supported currencies in their profile preferences. All values are automatically converted.

= How do I back up my portfolio? =

Use the Export feature in your profile to download a complete JSON backup of your assets, liabilities, watchlist, and preferences. You can import it again at any time.

= Is the plugin compatible with my theme? =

Yes, the plugin is designed to work with any WordPress theme, including block themes. Some themes may require minor CSS adjustments.

= Can I use this plugin with any WordPress theme? =

Yes, the Personal Finance Manager - PN plugin is designed to be compatible with any WordPress theme. However, some themes may require additional customization to ensure the plugin's styles integrate seamlessly.

= Is the plugin translation-ready? =

Yes, the Personal Finance Manager - PN plugin is fully translation-ready. You can use translation plugins such as Loco Translate to translate the plugin into your desired language.

= How do I get support? =

Visit the plugin's support forum on WordPress.org or contact us at info@padresenlanube.com.


== Screenshots ==

1. Portfolio overview with financial metrics and distribution chart.
2. Stock holdings with real-time prices and profit/loss.
3. Cryptocurrency tracking with CoinGecko integration.
4. Sector and country diversification doughnut charts.
5. Asset edit form with stock symbol search.
6. Liability management interface.
7. Watchlist with price alert configuration.
8. Admin settings page with API configuration.


== Credits ==

This plugin stands on the shoulders of giants

Chart.js v4.5.1
Licensed under MIT
Copyright 2014-2025 Chart.js contributors
https://www.chartjs.org/

Leaflet v1.9.4
Licensed under BSD-2-Clause
Copyright 2010-2023 Vladimir Agafonkin
https://leafletjs.com/

Select2 4.0.13
License MIT - https://github.com/select2/select2/blob/master/LICENSE.md
https://github.com/select2/select2/tree/master

Trumbowyg v2.27.3 - A lightweight WYSIWYG editor
alex-d.github.io/Trumbowyg/
License MIT - Author : Alexandre Demode (Alex-D)
https://github.com/Alex-D/Trumbowyg


== External Services ==

This plugin relies on the following third-party services to provide real-time financial data and mapping functionality. No personal user data is sent to any of these services.

= Twelve Data API =

Used to retrieve real-time and historical stock prices, search stock symbols, and list available US stocks. API calls are made from the server when users view their stock assets, when the daily cron job runs to record prices, and when the site administrator tests the API connection from the settings page. The data sent is limited to stock ticker symbols and the administrator's API key. A free API key is required (800 calls/day on the free plan).

* Service: [Twelve Data](https://twelvedata.com/)
* Terms of Service: [https://twelvedata.com/terms](https://twelvedata.com/terms)
* Privacy Policy: [https://twelvedata.com/privacy](https://twelvedata.com/privacy)

= CoinGecko API =

Used to retrieve real-time cryptocurrency prices, market data, historical price charts, and search for available coins. API calls are made from the server when users view their cryptocurrency assets and when the daily cron job runs. The data sent is limited to cryptocurrency identifiers (e.g., "bitcoin"). No API key is required.

* Service: [CoinGecko](https://www.coingecko.com/)
* Terms of Service: [https://www.coingecko.com/en/terms](https://www.coingecko.com/en/terms)
* Privacy Policy: [https://www.coingecko.com/en/privacy](https://www.coingecko.com/en/privacy)

= ExchangeRate API =

Used to retrieve current currency exchange rates for converting asset values between 27 supported currencies. API calls are made from the server and cached locally. The only data sent is the base currency code (USD). No API key is required.

* Service: [ExchangeRate-API](https://www.exchangerate-api.com/)
* Terms of Service: [https://www.exchangerate-api.com/terms](https://www.exchangerate-api.com/terms)
* Privacy Policy: [https://www.exchangerate-api.com/terms](https://www.exchangerate-api.com/terms)

= OpenStreetMap =

Used for the optional map field in asset forms. Map tiles are loaded from OpenStreetMap tile servers to display interactive maps via the bundled Leaflet library. Address search (geocoding) uses the Nominatim service. The data sent for geocoding is the search query typed by the user. Map tile requests send standard HTTP headers (including the user's IP address) to OpenStreetMap servers.

* Service: [OpenStreetMap](https://www.openstreetmap.org/)
* Tile Usage Policy: [https://operations.osmfoundation.org/policies/tiles/](https://operations.osmfoundation.org/policies/tiles/)
* Nominatim Usage Policy: [https://operations.osmfoundation.org/policies/nominatim/](https://operations.osmfoundation.org/policies/nominatim/)
* Privacy Policy: [https://wiki.osmfoundation.org/wiki/Privacy_Policy](https://wiki.osmfoundation.org/wiki/Privacy_Policy)


== Changelog ==

= 1.1.4 =
* Added sold asset functionality with frozen P/L calculations.
* Added stock sector and country diversification charts.
* Added global asset/liability distribution bar chart with net worth.
* Added collapsible sections in portfolio overview.
* Added stock and crypto portfolio performance charts under their categories.
* Moved breakdown charts to their respective asset subcategories.
* WordPress Plugin Check compliance: output escaping, i18n translators comments, direct file access protection, gmdate usage, local Chart.js bundling.

= 1.1.0 =
* Added cryptocurrency tracking via CoinGecko API.
* Added watchlist with price alert system.
* Added portfolio export/import functionality.
* Added user preference settings (currency, number format, comparison period).
* Added real estate asset tracking with ownership percentage.
* Added 27 currency support with automatic conversion.
* Added daily cron jobs for automated price recording.

= 1.0.0 =
* Initial release.
* Asset and liability tracking with custom post types.
* Stock tracking via Twelve Data API.
* Portfolio overview with basic charts.
* User role management.
* Shortcode-based front-end interface.
