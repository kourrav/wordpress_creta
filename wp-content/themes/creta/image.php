<?php

get_header(); ?>

<div class="main-container col1-layout">
    <div class="main container">
        <div class="col-main">

            <?php
            // Start the loop.
            while (have_posts()) : the_post();
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="postimg-slider">
                        <div id="image-navigation" class="navigation image-navigation">
                            <div class="nav-links">
                                <div
                                    class="nav-previous"><?php previous_image_link(false, '<span>'.esc_html__('Previous Image', 'creta').'</span>'); ?></div>
                                <div class="nav-next">
                                    <span><?php next_image_link(false,'<span>'. esc_html__('Next Image', 'creta').'</span>'); ?></span>
                                </div>
                            </div>
                            <!-- .nav-links -->
                        </div>
                        <!-- .image-navigation -->

                        <div class="page-title">
                            <?php the_title('<h2 class="entry-title">', '</h2>'); ?>
                        </div>
                        <!-- .entry-header -->

                        <div class="entry-content">

                            <div class="entry-attachment">
                                <?php


                                echo wp_get_attachment_image(get_the_ID(), 'large');
                                ?>

                                <?php if (has_excerpt()) : ?>
                                    <div class="entry-caption">
                                        <?php the_excerpt(); ?>
                                    </div><!-- .entry-caption -->
                                <?php endif; ?>

                            </div>
                            <!-- .entry-attachment -->

                            <?php
                            the_content();
                            $defaults = array(
                                'before' => '<p>' . esc_html__('Pages:', 'creta'),
                                'after' => '</p>',
                                'link_before' => '',
                                'link_after' => '',
                                'next_or_number' => 'number',
                                'separator' => ' ',
                                'nextpagelink' => esc_html__('Next page', 'creta'),
                                'previouspagelink' => esc_html__('Previous page', 'creta'),
                                'pagelink' => '%',
                                'echo' => 1
                            );

                            wp_link_pages($defaults);

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
                    </div>
                    <footer class="entry-footer">
                 
                        <?php edit_post_link(esc_html__('Edit', 'creta'), '<span class="edit-link">', '</span>'); ?>
                    </footer>
                    <!-- .entry-footer -->

                </article><!-- #post-## -->

                <?php
                // If comments are open or we have at least one comment, load up the comment template
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

                // Previous/next post navigation.


                // End the loop.
            endwhile;
            ?>

        </div>
    </div>
</div>
<?php get_footer(); ?>
