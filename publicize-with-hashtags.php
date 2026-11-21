<?php
/**
 * Plugin Name:       Publicize With Hashtags
 * Plugin URI:        https://github.com/Darkseal/publicize-with-hashtags
 * Description:       Appends the post tags, as hashtags, to the message that Jetpack Social (formerly Publicize) shares on your social networks.
 * Version:           0.2.0
 * Requires at least: 5.6
 * Requires PHP:      7.2
 * Author:            Darkseal
 * Author URI:        https://www.ryadel.com/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post meta where Jetpack Social stores the custom sharing message
 * (exposed to the block editor as `jetpack_publicize_message`).
 */
const PUBLICIZE_WITH_HASHTAGS_META = '_wpas_mess';

/*
 * `wp_after_insert_post` fires once the post, its terms and its REST meta are all saved, in both the block editor and
 * the classic editor. Priority 10 runs before Jetpack Sync (priority 11), which queues the post for sharing.
 */
add_action( 'wp_after_insert_post', 'publicize_with_hashtags', 10, 4 );

/**
 * Appends the post tags, as hashtags, to the Jetpack Social message of the post.
 *
 * Runs while the post is a draft, pending or scheduled, and when it gets published; it never touches a post that was
 * already published, because Jetpack has already shared it.
 *
 * @param int          $post_id     Post ID.
 * @param WP_Post      $post        Post object.
 * @param bool         $update      Whether this is an existing post being updated.
 * @param WP_Post|null $post_before Post object before the update, null for new posts.
 */
function publicize_with_hashtags( $post_id, $post, $update = false, $post_before = null ) {
	if ( ! $post instanceof WP_Post || wp_is_post_revision( $post ) || wp_is_post_autosave( $post ) ) {
		return;
	}
	if ( ! in_array( $post->post_status, array( 'draft', 'pending', 'future', 'publish' ), true ) ) {
		return;
	}
	if ( $post_before instanceof WP_Post && 'publish' === $post_before->post_status ) {
		return;
	}
	if ( ! post_type_supports( $post->post_type, 'publicize' ) || ! publicize_with_hashtags_is_publicize_active() ) {
		return;
	}

	$meta     = PUBLICIZE_WITH_HASHTAGS_META;
	$template = function_exists( 'get_metadata_default' ) ? (string) get_metadata_default( 'post', $post->ID, $meta, true ) : '';
	$message  = metadata_exists( 'post', $post->ID, $meta ) ? (string) get_post_meta( $post->ID, $meta, true ) : '';

	// The site uses a Jetpack message template (paid plans): leave it alone, so that Jetpack fills its placeholders.
	// Add {tags} to the template to get the hashtags.
	if ( '' !== trim( $template ) && ( '' === trim( $message ) || $message === $template ) ) {
		return;
	}
	// Jetpack already turns the tags into hashtags.
	if ( false !== stripos( $message, '{tags}' ) ) {
		return;
	}
	// Without a custom message the plugin starts from the title: wait until the post is published or scheduled, when
	// the title is final, so that a draft doesn't freeze an old title in the message.
	if ( '' === trim( $message ) && ! in_array( $post->post_status, array( 'future', 'publish' ), true ) ) {
		return;
	}

	$hashtags = array();
	$tags     = get_the_terms( $post, 'post_tag' );
	if ( is_array( $tags ) ) {
		foreach ( $tags as $tag ) {
			$hashtag = preg_replace( '/\s+/u', '', wp_specialchars_decode( $tag->name, ENT_QUOTES ) );
			if ( '' !== $hashtag ) {
				$hashtags[] = '#' . $hashtag;
			}
		}
	}

	/**
	 * Filters the hashtags to append to the Jetpack Social message.
	 *
	 * @param string[] $hashtags Hashtags, including the leading '#', in tag order.
	 * @param WP_Post  $post     The post being saved.
	 */
	$hashtags = (array) apply_filters( 'publicize_with_hashtags_hashtags', $hashtags, $post );
	if ( empty( $hashtags ) ) {
		return;
	}

	/**
	 * Filters the maximum length of the message: hashtags that would exceed it are not added. 0 means no limit.
	 *
	 * @param int     $max_length Maximum message length, in characters. Default 0.
	 * @param WP_Post $post       The post being saved.
	 */
	$max_length = (int) apply_filters( 'publicize_with_hashtags_max_length', 0, $post );

	$new_message = publicize_with_hashtags_append( $message, $post->post_title, $hashtags, $max_length );
	if ( $new_message !== $message ) {
		update_post_meta( $post->ID, $meta, wp_slash( $new_message ) );
	}
}

/**
 * Builds the new message: the current one (or the post title, if empty) followed by the hashtags it doesn't contain yet.
 *
 * @param string   $message    Current message.
 * @param string   $title      Post title.
 * @param string[] $hashtags   Hashtags to append.
 * @param int      $max_length Maximum message length; 0 means no limit.
 * @return string
 */
function publicize_with_hashtags_append( $message, $title, $hashtags, $max_length = 0 ) {
	$message = trim( $message );

	// No message, or a message made of hashtags only (e.g. a draft saved before it had a title): start from the title.
	if ( '' === $message || preg_match( '/^#\S+(\s+#\S+)*$/u', $message ) ) {
		$message = trim( $title . ' ' . $message );
	}

	foreach ( $hashtags as $hashtag ) {
		$hashtag = (string) $hashtag;
		if ( '' === $hashtag || '#' === $hashtag ) {
			continue;
		}
		// Skip hashtags that are already in the message, as a whole word and ignoring case (#Word doesn't match #WordPress).
		if ( preg_match( '/(?<![\p{L}\p{N}_#])' . preg_quote( $hashtag, '/' ) . '(?![\p{L}\p{N}_])/iu', $message ) ) {
			continue;
		}
		$candidate = '' === $message ? $hashtag : $message . ' ' . $hashtag;
		// Too long: skip it, a shorter hashtag may still fit.
		if ( $max_length > 0 && publicize_with_hashtags_strlen( $candidate ) > $max_length ) {
			continue;
		}
		$message = $candidate;
	}

	return $message;
}

/**
 * Whether the Jetpack Social (Publicize) module is active, with the full Jetpack plugin or the standalone Jetpack Social.
 *
 * @return bool
 */
function publicize_with_hashtags_is_publicize_active() {
	if ( class_exists( '\Automattic\Jetpack\Modules' ) ) {
		return ( new \Automattic\Jetpack\Modules() )->is_active( 'publicize' );
	}
	// Older Jetpack versions.
	if ( class_exists( 'Jetpack' ) && method_exists( 'Jetpack', 'is_module_active' ) ) {
		return Jetpack::is_module_active( 'publicize' );
	}
	return false;
}

/**
 * String length in characters, with a fallback for sites without mbstring.
 *
 * @param string $text Text.
 * @return int
 */
function publicize_with_hashtags_strlen( $text ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( $text, 'UTF-8' ) : (int) preg_match_all( '/./us', $text );
}
