# Custom API WordPress Plugin

A WordPress plugin that provides encrypted API key functionality.

## Description

This plugin blueprint offers a simple way to manage encrypted API keys within your WordPress site. It also provides a custom REST endpoint for fetching external API data. The code is based on the article: [How to Safely Store API Keys and Access Protected External APIs in WordPress](https://fullstackdigital.io/blog/how-to-safely-store-api-keys-and-access-protected-external-apis-in-wordpress/).

## Features

- Admin settings page for API key management.
- Data encryption for added security.
- Custom REST endpoint for external API data fetching.

## Installation

1. Download the plugin from this repository.
2. Upload the plugin to your WordPress site via the Plugins > Add New > Upload Plugin interface.
3. Activate the plugin.
4. Navigate to the API Keys submenu under the Tools menu to configure your API key.

## Usage

1. Set your API key in the provided settings page.
2. Use the custom REST endpoint (`/wp-json/webshr/v1/fetch-external-api`) to fetch data from an external API.

## Development

This plugin requires at least WordPress 5.9 and PHP 7.4.

### Directory Structure

- `includes/`: Contains the PHP classes for data encryption, REST endpoint, and settings page functionalities.

## Contributing

Contributions are welcome! Please read the [contributing guidelines](https://chat.openai.com/c/CONTRIBUTING.md) to get started.

## License

This project is licensed under the GPL-2.0 License - see the [LICENSE.md](https://chat.openai.com/c/LICENSE.md) file for details.