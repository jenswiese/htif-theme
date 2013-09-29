<?php
/**
 * @package WordPress
 * @subpackage HTIF
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf(
                esc_attr__('Permalink to %s', 'htif'),
                the_title_attribute('echo=0')
            ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

        <?php if ('post' == get_post_type()) : ?>
            <div class="entry-meta">
                <?php htif_posted_on(); ?>
            </div><!-- .entry-meta -->
        <?php endif; ?>
    </header>
    <!-- .entry-header -->

    <?php if (is_search()) : // Only display Excerpts for Search ?>
        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div><!-- .entry-summary -->
    <?php else : ?>
        <div class="entry-content">
            <?php the_content(__('Continue reading <span class="meta-nav">&rarr;</span>', 'htif')); ?>
            <?php wp_link_pages(
                array('before' => '<div class="page-link">' . __('Pages:', 'htif'), 'after' => '</div>')
            ); ?>
        </div><!-- .entry-content -->
    <?php endif; ?>

    <footer class="entry-meta">
        <?php if ('post' == get_post_type()) : // Hide category and tag text for pages on Search ?>
            <?php
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(__(', ', 'htif'));
            if ($categories_list) :
                ?>
                <span class="cat-links">
				<?php printf(__('Posted in %1$s', 'htif'), $categories_list); ?>
			</span>
                <span class="sep"> | </span>
            <?php endif; // End if categories ?>

            <?php
            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', __(', ', 'htif'));
            if ($tags_list) :
                ?>
                <span class="tag-links">
				<?php printf(__('Tagged %1$s', 'htif'), $tags_list); ?>
			</span>
                <span class="sep"> | </span>
            <?php endif; // End if $tags_list ?>
        <?php endif; // End if 'post' == get_post_type() ?>



        <?php edit_post_link(__('Edit', 'htif'), '<span class="edit-link">', '</span>'); ?>
    </footer>
    <!-- #entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
