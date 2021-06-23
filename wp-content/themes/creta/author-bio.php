<div class="author-info">
    <h2 class="author-heading"><?php esc_attr_e('Published by', 'creta'); ?></h2>

    <div class="author-avatar">
        <?php

        $author_bio_avatar_size = 56;

        echo get_avatar(get_the_author_meta('user_email'), $author_bio_avatar_size);
        ?>
    </div>
    <!-- .author-avatar -->

    <div class="author-description">
        <h3 class="author-title"><?php echo esc_html(get_the_author()); ?></h3>

        <p class="author-bio">
            <?php the_author_meta('description'); ?>
            <a class="author-link" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"
               rel="author">
                <?php printf(esc_html__('View all posts by %s', 'creta'), get_the_author()); ?>
            </a>
        </p><!-- .author-bio -->

    </div>
    <!-- .author-description -->
</div><!-- .author-info -->
