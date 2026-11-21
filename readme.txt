=== Publicize With Hashtags ===
Contributors: neilgaiman
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JSTHA4HMVSQ8J
Tags: jetpack, jetpack social, publicize, hashtags, social
Requires at least: 5.6
Tested up to: 7.1
Requires PHP: 7.2
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Appends the post tags, as hashtags, to the message that Jetpack Social shares on your social networks.

== Description ==

**Maintenance mode.** This plugin receives compatibility and security fixes, but no new features. Jetpack Social now has a built-in `{tags}` placeholder for its message templates: if your Jetpack plan includes message templates, you can use it instead of this plugin.

Publicize With Hashtags turns the tags of your posts into hashtags and appends them to the message that **Jetpack Social** (formerly Jetpack Publicize) shares on Facebook, Threads, Bluesky, LinkedIn, Mastodon and the other supported networks.

It works with the full **Jetpack** plugin (with the Social module active) and with the standalone **Jetpack Social** plugin, in both the block editor and the classic editor. Without Jetpack Social it does nothing.

**Features**

* Each post tag becomes a hashtag, with its spaces removed: `Open Source` becomes `#OpenSource`.
* Hashtags you write yourself are kept, and the plugin never adds a hashtag that is already in the message.
* If the post has no custom message, the hashtags are appended to the post title.
* Works with every post type that Jetpack Social can share, as long as it uses tags.
* Posts that are already published are never changed.

**Useful links**

* [GitHub repository](https://github.com/Darkseal/publicize-with-hashtags) (issues and pull requests).
* [Installation and usage guide](https://www.ryadel.com/en/add-hashtags-to-jetpack-publicize-shared-posts/) at Ryadel.com (written for the classic editor).

== Installation ==

1. Install and activate **Jetpack Social** (or **Jetpack**, with the Social module active) and connect your social accounts.
2. In **Plugins > Add New Plugin**, search for "Publicize With Hashtags", then install and activate it.
3. Add tags to your posts: their hashtags are added to the sharing message when you save or publish the post.

== Frequently Asked Questions ==

= When are the hashtags added? =

When you publish or schedule the post, right before Jetpack shares it. If you have written a custom sharing message, they are also added every time you save a draft or a pending post, so you can see them and move them around before publishing. Posts that are already published are left untouched.

= What if I write some hashtags myself? =

They stay where you put them, and the plugin won't add them a second time (the check ignores case).

= I use a Jetpack message template. Does the plugin change it? =

No. When the post uses your site's message template, the plugin leaves it alone, so that Jetpack can fill in its placeholders: add `{tags}` to the template to get the hashtags. The plugin also does nothing when the message already contains `{tags}`.

= Does it work with per-network custom messages? =

No: it only changes the main sharing message. Messages customized for a single network are left as they are.

= Can I change the hashtags or limit the message length? =

Yes, with two filters:

* `publicize_with_hashtags_hashtags` filters the array of hashtags (with the leading `#`) before they are appended, for example to add the categories too;
* `publicize_with_hashtags_max_length` sets the maximum length of the message, in characters: hashtags that don't fit are not added. The default, `0`, means no limit.

Example:

`add_filter( 'publicize_with_hashtags_max_length', function () { return 280; } );`

== Screenshots ==

1. The post tags become hashtags in the Jetpack sharing message (classic editor).

== Changelog ==

= 0.2.0 =
* Works again with the block editor and with the standalone Jetpack Social plugin.
* Fixed the `Jetpack_Options` notice shown when the plugin loaded before Jetpack (GitHub issue #2).
* Supports every post type that Jetpack Social can share.
* Leaves Jetpack message templates, and messages that use `{tags}`, untouched.
* Removed the 118-character limit, designed for Twitter: there is no limit by default (see the `publicize_with_hashtags_max_length` filter).
* New filter `publicize_with_hashtags_hashtags`.
* Fixed the duplicate check, which skipped a tag when the message contained a longer hashtag starting with it (`#Word` and `#WordPress`).
* Fixed messages containing backslashes.
* Without a custom message, the hashtags are added when the post is published or scheduled, so that a draft no longer keeps an outdated title in the sharing message.
* The plugin is now in maintenance mode.
* Requires WordPress 5.6 and PHP 7.2.

= 0.1.4 =
* Fixed a bug for some auto-saved posts missing their title in their social text.

= 0.1.3 =
* Added WordPress 4.3 compatibility notice.

= 0.1.2 =
* Fixed a bug in hashtags generation.

= 0.1.1 =
* Added an explicative screenshot.
* Added some Frequently Asked Questions.
* Small changes to readme.txt.

= 0.1 =
* First release.

== Upgrade Notice ==

= 0.2.0 =
Works again with the block editor and with Jetpack Social. The 118-character limit is gone: use the publicize_with_hashtags_max_length filter if you need one.
