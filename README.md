# Publicize With Hashtags

A small WordPress plugin that turns the tags of your posts into hashtags and appends them to the message that
**Jetpack Social** (formerly Jetpack Publicize) shares on your social networks.

> **Maintenance mode.** The plugin receives compatibility and security fixes, but no new features. Jetpack Social now
> has a built-in `{tags}` placeholder for its message templates: if your Jetpack plan includes message templates, you
> can use it instead of this plugin. Issues and pull requests are still welcome.

## Features

- Works with the full **Jetpack** plugin (with the Social module active) and with the standalone **Jetpack Social**
  plugin, in both the block editor and the classic editor. Without Jetpack Social it does nothing.
- Each post tag becomes a hashtag, with its spaces removed: `Open Source` becomes `#OpenSource`.
- Hashtags you write yourself are kept, and the plugin never adds a hashtag that is already in the message.
- If the post has no custom message, the hashtags are appended to the post title.
- Works with every post type that Jetpack Social can share, as long as it uses tags.
- Posts that are already published are never changed.
- Jetpack message templates, and messages that already use `{tags}`, are left untouched.

The hashtags are added when you publish or schedule the post, right before Jetpack shares it. If you have written a
custom sharing message, they are also added every time you save a draft or a pending post, so you can see them and
move them around before publishing.

## Filters

- `publicize_with_hashtags_hashtags` (`string[] $hashtags, WP_Post $post`): the hashtags, with the leading `#`, before
  they are appended.
- `publicize_with_hashtags_max_length` (`int $max_length, WP_Post $post`): maximum message length in characters;
  hashtags that don't fit are not added. Default `0` (no limit).

```php
add_filter( 'publicize_with_hashtags_max_length', function () {
	return 280;
} );
```

## Installation

Install it from the [WordPress.org plugin directory](https://wordpress.org/plugins/publicize-with-hashtags/)
(**Plugins > Add New Plugin**, search for "Publicize With Hashtags"), or download the ZIP attached to a
[GitHub release](https://github.com/Darkseal/publicize-with-hashtags/releases) and upload it from
**Plugins > Add New Plugin > Upload Plugin**.

Requires WordPress 5.6 or later, PHP 7.2 or later, and Jetpack Social or Jetpack.

## Releases

Publishing a GitHub release deploys the plugin to WordPress.org through
[10up/action-wordpress-plugin-deploy](https://github.com/10up/action-wordpress-plugin-deploy)
(see [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml)); the WordPress.org assets live in
[`.wordpress-org/`](.wordpress-org/). The full changelog is in [`readme.txt`](readme.txt).

## Links

- [Plugin page](https://wordpress.org/plugins/publicize-with-hashtags/) on WordPress.org.
- [Installation and usage guide](https://www.ryadel.com/en/add-hashtags-to-jetpack-publicize-shared-posts/) at
  Ryadel.com (written for the classic editor).

## Author

**Darkseal** of [Ryadel.com](https://www.ryadel.com/).

You can support this project by clicking the button below. Thanks a lot!

[<img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate">](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JSTHA4HMVSQ8J)
