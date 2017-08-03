<?php

/**
 * SEO and header functions.  Not all things in this file are strictly for search engine optimization.  Many 
 * of the functions handle basic <meta> elements for the <head> area of the site.  This file is a catchall file 
 * for adding these types of things to themes.
 *
 *
 * @package    EssentialSEO
 * @subpackage Inc
 * @author     James Geiger <james@seamlessthemes.com>
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, James Geiger and Justin Tadlock
 * @link       http://seamlessthemes.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
/* Add <meta> elements to the <head> area. */
add_action('wp_head', 'essential_seo_start', 1);
add_action('wp_head', 'rel_canonical', 1);
add_action('wp_head', 'essential_seo_author_publisher', 1);
add_action('wp_head', 'essential_seo_meta_robots', 1);
add_action('wp_head', 'essential_seo_meta_description', 1);
add_action('wp_head', 'essential_seo_verifications', 1);
add_action('wp_head', 'essential_seo_end', 1);
add_filter('wp_title', 'essential_seo_title', 25, 3);
add_action('admin_head', 'check_for_wp_title', 1);
remove_action('wp_head', 'rel_canonical');

function extra_contact_info($contactmethods) {

    $contactmethods['googleplus'] = 'Google+';


    return $contactmethods;
}

add_filter('user_contactmethods', 'extra_contact_info');

function essential_seo_start() {

    echo "\n" . '<!-- Start Essential SEO -->' . "\n";
}

function essential_seo_author_publisher() {
    global $essential_settings;

    $gplus = '';

    if (is_home() || is_front_page()) {
        $gplus = get_the_author_meta('googleplus');
    } else if (is_singular()) {
        global $post;
        $gplus = get_the_author_meta('googleplus', $post->post_author);
    }

    $gplus = apply_filters('author', $gplus);

    if ($gplus)
        $gplus = '<link rel="author" href="' . $gplus . '"/>' . "\n";

    echo apply_filters('author', $gplus);
    if (isset($essential_settings['google-plus-publisher']) && !empty($essential_settings['google-plus-publisher'])) {
        echo '<link rel="publisher" href="' . esc_url($essential_settings['google-plus-publisher']) . '" />' . "\n";
    }
}

/**
 * Sets the default meta robots setting.  If private, don't send meta info to the header.  Runs the 
 * essential_seo_meta_robots filter hook at the end.
 *
 * @since 0.1.0
 * @access public
 * @return void
 */
function essential_seo_meta_robots() {

    /* Do not display index and follow tags on the following. It is the browser default. */
    if ((is_home() && ($paged < 2 )) || is_front_page() || is_single() || is_page() || is_attachment()) {
        return;
    }

    /* If viewing a search page, display noindex and nofollow. */ elseif (is_search()) {
        $robots = '<meta name="robots" content="noindex,nofollow" />' . "\n";

        /* If viewing any other page display noidex and follow. */
    } else {
        $robots = '<meta name="robots" content="noindex,follow" />' . "\n";
    }

    echo apply_filters('meta_robots', $robots);
}

/**
 * Generates the meta description based on either metadata or the description for the object.
 *
 * @since 0.1.0
 * @access public
 * @return void
 */
function essential_seo_meta_description() {

    /* Set an empty $description variable. */
    $description = '';

    /* If viewing the home/posts page, get the site's description. */
    if (is_home()) {
        $description = get_bloginfo('description');
    }

    /* If viewing a singular post. */ elseif (is_singular()) {

        /* Get the meta value for the 'Description' meta key. */
        $description = get_post_meta(get_queried_object_id(), 'Description', true);

        /* If no description was found and viewing the site's front page, use the site's description. */
        if (empty($description) && is_front_page())
            $description = get_bloginfo('description');

        /* For all other singular views, get the post excerpt. */
        elseif (empty($description))
            $description = get_post_field('post_excerpt', get_queried_object_id());
    }

    /* If viewing an archive page. */
    elseif (is_archive()) {

        /* If viewing a user/author archive. */
        if (is_author()) {

            /* Get the meta value for the 'Description' user meta key. */
            $description = get_user_meta(get_query_var('author'), 'Description', true);

            /* If no description was found, get the user's description (biographical info). */
            if (empty($description))
                $description = get_the_author_meta('description', get_query_var('author'));
        }

        /* If viewing a taxonomy term archive, get the term's description. */
        elseif (is_category() || is_tag() || is_tax())
            $description = term_description('', get_query_var('taxonomy'));

        /* If viewing a custom post type archive. */
        elseif (is_post_type_archive()) {

            /* Get the post type object. */
            $post_type = get_post_type_object(get_query_var('post_type'));

            /* If a description was set for the post type, use it. */
            if (isset($post_type->description))
                $description = $post_type->description;
        }
    }

    /* Format the meta description. */
    if (!empty($description))
        $description = '<meta name="description" content="' . str_replace(array("\r", "\n", "\t"), '', esc_attr(strip_tags($description))) . '" />' . "\n";

    echo apply_filters('meta_description', $description);
}

/**
 * Adds verification for Google Bing and Pinterest
 * @return void
 */
function essential_seo_verifications() {
    global $essential_settings;
    foreach ($essential_settings['verifications'] as $verification) {
        if (!empty($verification)) {
            echo $verification . "\n";
        }
    }
}

function essential_seo_end() {

    echo '<!-- End Essential SEO -->' . "\n\n";
}

/**
 * Main title function.
 *
 * @param string $title       Title that might have already been set.
 * @param string $sepinput    Separator determined in theme.
 * @param string $seplocation Position of seperator left or right.
 *
 * @return string
 */
function essential_seo_title($title, $sepinput = '-', $seplocation = '') {
    global $post;

    $set_title = get_post_meta($post->ID, 'Title', true);
    if (!$set_title) {
        return $title;
    }
    return $set_title;
}

/**
 * Checks if theme uses wp_title
 *
 * @since 0.2.1
 * @return boolean
 */
function check_for_wp_title() {
    global $post;
    if (get_class($post) == "WP_Post") {
        ob_start();
        get_header();
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(ob_get_contents());
        ob_end_clean();
        $title = $dom->getElementsByTagName('title')->item(0)->nodeValue;
        $set_title = get_post_meta($post->ID, 'Title', true);
        if ($set_title && $title != $set_title) {
            add_action('admin_notices', 'no_wp_title');
        }
        return;
    } else {
        global $wpdb;
        $a_titled_post = $wpdb->get_results($wpdb->prepare("SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key = '%s' AND meta_value != '' LIMIT 1", "Title"), ARRAY_A);
        //print_r($a_titled_post);
        if ($wpdb->num_rows > 0) {
            $set_title = $a_titled_post[0]['meta_value'];
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTMLFile(get_permalink($a_titled_post[0]['post_id']));
            $title = $dom->getElementsByTagName('title')->item(0)->nodeValue;
            if ($set_title != $title) {
                add_action('admin_notices', 'no_wp_title');
            }
        }
    }
}

function no_wp_title() {
    echo '<div class="updated">
       <p>Essential SEO Notice: Your theme does not use wp_title properly, this means we cannot use your chosen &lt;title&gt; for your posts.</p>
    </div>';
}

?>