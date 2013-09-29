<?php
/**
 * A study in stuctural functionalism at HTIF
 *
 * @package WordPress
 * @subpackage HTIF
 * @since HTIF 0.2
 */


/**
 * Setup defaults, register taxonomies/post types and other WordPress features.
 * This function is hooked into the after_setup_theme hook.
 *
 * @since HTIF 0.2
 */

add_action('after_setup_theme', 'htif_theme_setup');

function htif_theme_setup()
{
    //add basic features
    add_theme_support('automatic-feed-links');
    add_theme_support('post-formats', array('aside', 'gallery'));

    //add custom scripts
    add_action('wp_enqueue_scripts', 'htif_enqueue_scripts');

    //add custom widgets/sidebars
    add_action('init', 'htif_widgets_init');

    // add custom menus
    add_action('init', 'htif_register_menus');


    // add various other custom actions/filters
    add_filter('body_class', 'htif_better_body_classes');
    add_filter('wp_nav_menu', 'htif_add_slug_class_to_menu_item');


    //print template file in footer — remove for production.
    add_action('wp_footer', 'htif_show_template');

}


/**
 * Loads theme-specific JavaScript files.
 *
 * @since 0.2
 */

function htif_enqueue_scripts()
{
    wp_enqueue_script('jquery');

    wp_register_script('htif', get_template_directory_uri() . '/js/htif.js');
    wp_enqueue_script('htif');

}


/**
 * Include the page slug in the body class attribute.
 *
 * @since 0.2
 *
 * @param array $classes The existing classes for the body element
 * @return array The amended class array for the body element
 */

function htif_better_body_classes($classes)
{
    global $post;
    if (isset($post)) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    }
    return $classes;
}


/**
 * Print out the current template file to the footer.
 * Obviously to be removed in production
 *
 * @since 0.2
 */

function htif_show_template()
{
    global $template;
    echo '<strong>Template file:</strong>';
    print_r($template);
}


/**
 * Add slug to menu li classes
 *
 * @since 0.2
 */

function htif_add_slug_class_to_menu_item($output)
{
    $ps = get_option('permalink_structure');
    if (!empty($ps)) {
        $idstr = preg_match_all('/<li id="menu-item-(\d+)/', $output, $matches);
        foreach ($matches[1] as $mid) {
            $id = get_post_meta($mid, '_menu_item_object_id', true);
            $slug = basename(get_permalink($id));
            $output = preg_replace(
                '/menu-item-' . $mid . '">/',
                'menu-item-' . $mid . ' menu-item-' . $slug . '">',
                $output,
                1
            );
        }
    }
    return $output;
}

/**
 * This theme uses wp_nav_menu() in one location.
 *
 * @since 0.2
 */


function htif_register_menus()
{
    register_nav_menus(
        array(
            'primary' => __('Primary Menu', 'htif'),
        )
    );

}


/**
 * Modify the Posted on output
 *
 * @since 0.2
 */

function htif_posted_on()
{
    printf(
        __(
            '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="byline">   </span></span>',
            'toolbox'
        ),
        esc_url(get_permalink()),
        esc_attr(get_the_time()),
        esc_attr(get_the_date('c')),
        esc_html(get_the_date()),
        esc_url(get_author_posts_url(get_the_author_meta('ID'))),
        esc_attr(sprintf(__('View all posts by %s', 'toolbox'), get_the_author())),
        esc_html(get_the_author())
    );
}


/**
 * Register widgetized area and update sidebar with default widgets
 */
function htif_widgets_init()
{
    register_sidebar(
        array(
            'name' => __('Sidebar 1', 'htif'),
            'id' => 'sidebar-1',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => "</aside>",
            'before_title' => '<h1 class="widget-title">',
            'after_title' => '</h1>',
        )
    );

    register_sidebar(
        array(
            'name' => __('Sidebar 2', 'htif'),
            'id' => 'sidebar-2',
            'description' => __('An optional second sidebar area', 'htif'),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => "</aside>",
            'before_title' => '<h1 class="widget-title">',
            'after_title' => '</h1>',
        )
    );
}


if (!function_exists('htif_content_nav')):
    /**
     * Display navigation to next/previous pages when applicable
     *
     * @since htif 1.2
     */
    function htif_content_nav($nav_id)
    {
        global $wp_query;

        ?>
        <nav id="<?php echo $nav_id; ?>">
            <h1 class="assistive-text section-heading"><?php _e('Post navigation', 'htif'); ?></h1>

            <?php if (is_single()) : // navigation links for single posts ?>

                <?php previous_post_link(
                    '<div class="nav-previous">%link</div>',
                    '<span class="meta-nav">' . _x('&larr;', 'Previous post link', 'htif') . '</span> %title'
                ); ?>
                <?php next_post_link(
                    '<div class="nav-next">%link</div>',
                    '%title <span class="meta-nav">' . _x('&rarr;', 'Next post link', 'htif') . '</span>'
                ); ?>

            <?php elseif ($wp_query->max_num_pages > 1 && (is_home() || is_archive() || is_search())
            ) : // navigation links for home, archive, and search pages ?>

                <?php if (get_next_posts_link()) : ?>
                    <div class="nav-previous"><?php next_posts_link(
                            __('<span class="meta-nav">&larr;</span> Older posts', 'htif')
                        ); ?></div>
                <?php endif; ?>

                <?php if (get_previous_posts_link()) : ?>
                    <div class="nav-next"><?php previous_posts_link(
                            __('Newer posts <span class="meta-nav">&rarr;</span>', 'htif')
                        ); ?></div>
                <?php endif; ?>

            <?php endif; ?>

        </nav><!-- #<?php echo $nav_id; ?> -->
    <?php
    }
endif; // htif_content_nav


function htif_categorized_blog()
{
    if (false === ($all_the_cool_cats = get_transient('all_the_cool_cats'))) {
        // Create an array of all the categories that are attached to posts
        $all_the_cool_cats = get_categories(
            array(
                'hide_empty' => 1,
            )
        );

        // Count the number of categories that are attached to the posts
        $all_the_cool_cats = count($all_the_cool_cats);

        set_transient('all_the_cool_cats', $all_the_cool_cats);
    }

    if ('1' != $all_the_cool_cats) {
        // This blog has more than 1 category so toolbox_categorized_blog should return true
        return true;
    } else {
        // This blog has only 1 category so toolbox_categorized_blog should return false
        return false;

    }
}

 