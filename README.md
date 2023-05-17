# PostageApp for Craft CMS 4

This plugin provides a [PostageApp](http://www.postageapp.com/) integration for [Craft CMS](https://craftcms.com/).

## Requirements

This plugin requires Craft CMS 4.0 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “PostageApp”. Then click on the “Install” button in its modal window.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require convergine/craft-postageapp

# tell Craft to install the plugin
./craft plugin/install craft-postageapp
```

## Setup

Once PostageApp is installed:

1. Go to **Settings** → **Email**.
2. Change the **Transport Type** setting to **PostageApp**. Also, ensure that your System Email Address in your Craft Email Settings is verified and configured in your [senders list in PostageApp](https://secure.postageapp.com/login).
3. Enter your **Project's API Key** (which you can get from your specific project in PostageApp page).
4. Click **Save**.

> **Tip:** The API Key can be set using environment variables. See [Environmental Configuration](https://craftcms.com/docs/4.x/config/#environmental-configuration) in the Craft docs to learn more about that.

## Support

For any issues or questions, you can reach us by email info@convergine.com or by opening an issue on GitHub.
