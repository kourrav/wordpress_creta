<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="page-title">
        <?php the_title('<h2>', '</h2>'); ?>
    </div>
    <!-- .entry-header -->

    <div class="page-content">
        <?php the_content(); ?>
        <?php
        wp_link_pages(array(
            'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'creta') . '</span>',
            'after' => '</div>',
            'link_before' => '<span>',
            'link_after' => '</span>',
            'pagelink' => '<span class="screen-reader-text">' . esc_html__('Page', 'creta') . ' </span>%',
            'separator' => '<span class="screen-reader-text">, </span>',
        ));
        ?>
    </div>
    <!-- .entry-content -->

    <?php edit_post_link(esc_html__('Edit', 'creta'), '<footer class="entry-footer"><span class="edit-link">', '</span></footer><!-- .entry-footer -->'); ?>

</article><!-- #post-## -->
