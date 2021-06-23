<?php get_header();

 $design = get_post_meta($post->ID, 'magikCreta_post_layout', true);


$leftbar = $rightbar = $main = '';

switch ((int)$design) {
    case 1:
        $rightbar ='hidesidebar';
        $main = 'col2-left-layout';
        $col = 'col-sm-9 col-md-9 col-lg-9 col-xs-12';
        break;
    case 3:
        $leftbar = $rightbar = 'hidesidebar';
        $main = 'col1-layout';
        $col = 'col-sm-12 col-md-12 col-lg-12 col-xs-12';
        break;

    default:
        $leftbar = 'hidesidebar';
        $main = 'col2-right-layout';
        $col = 'col-sm-9 col-md-9 col-lg-9 col-xs-12';
        break;

}

?>
 <?php magikCreta_singlepage_breadcrumb();?>

    <div class="blog-wrapper <?php echo esc_html($main) ?>">
        <div class="container">
            <div class="row">

    <?php if (empty($leftbar) && $leftbar != 'hidesidebar') { ?>
    <aside id="column-left" class="col-left sidebar col-lg-3 col-sm-3 col-xs-12  <?php echo esc_html($leftbar) ?>">
     <?php get_sidebar('content'); ?>
    </aside>
    <?php } ?>

            <div class="<?php echo esc_html($col); ?>">
             <div class="col-main">   
            <?php  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                    get_query_var('page') ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="blog-post container-paper singlepost">
                           
                            <div class="post-container">
                                  
                              <div class="post-img <?php if (has_post_thumbnail()){?> has-img <?php } ?>">
                                      <?php if (has_post_thumbnail()) : ?>
                                    <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail'); ?>
                                    <a href="<?php the_permalink(); ?>"> 
                                     <figure>     
                     <img src="<?php echo esc_url($image[0]); ?>"
                                alt="<?php the_title_attribute(); ?>">  </figure>  
                                 </a>
                            <?php endif; ?>
                            <?php 
                              if (isset($creta_Options))
                            {
                            if (isset($creta_Options['blog_full_date']) && $creta_Options['blog_full_date'] == 1) { ?>
                          <div class="blogdate"><time class="entry-date updated">
                            <?php echo esc_html(get_the_date()); ?></time>
                          </div>
                         <?php }
                       }
                         else{
                         ?>
                          <div class="blogdate"><time class="entry-date updated">
                            <?php echo esc_html(get_the_date()); ?></time>
                          </div>
                       <?php  
                        }
                        ?>
                              
                                    </div>
                              
                                <div class="post-detail-container">
                                 <div class="title">
                                <h2>
                                    <?php the_title(); ?>
                                </h2>
                                
                            </div>
                    <ul class="list-info">
                    <?php if (isset($creta_Options))
                    {
 
                     if(isset($creta_Options['blog_show_post_by']) && $creta_Options['blog_show_post_by'] == 1 ) { ?>
                      <li><i class="fa fa-user"></i><?php esc_attr_e('By', 'creta'); ?>  <?php the_author(); ?> </li>
                      <li class="divider">|</li>
                    <?php 
                      }
                      }
                       else{ 
                        ?>
                      <li><i class="fa fa-user"></i><?php esc_attr_e('By', 'creta'); ?>  <?php the_author(); ?> </li>
                      <li class="divider">|</li>
                    <?php

                                        }
                     ?>
                    <?php if (isset($creta_Options))
                    {
 
                     if(isset($creta_Options['blog_display_category']) && $creta_Options['blog_display_category']==1) {  
                      if(has_category()){
                        $cats_list = get_the_category_list(', ');
                      ?>
                      <li class="under-category">
                      <i class="fa fa-tag"></i>
                       <?php echo wp_specialchars_decode($cats_list);?>
                      </li>
                      <li class="divider">|</li>
                       <?php 
                      } 
                        }
                        }
                          else
                          {
                           if(has_category()){
                            $cats_list = get_the_category_list(', ');
                      ?>
                      <li class="under-category">
                      <i class="fa fa-tag"></i>
                       <?php echo wp_specialchars_decode($cats_list);?>
                      </li>
                      <li class="divider">|</li>
                       <?php }

                      }

                        ?>

                      <?php if (isset($creta_Options))
                        {
 
                     if(isset($creta_Options['blog_display_comments_count']) && $creta_Options['blog_display_comments_count'] == 1) { ?>
                          <li><i class="fa fa-comment"></i><a href="<?php comments_link(); ?>"><?php comments_number('0 Comment', '1 Comment', '% Comments'); ?>
                          </a></li>
                           
                        <?php 
                      }
                       }
                           else
                           {
                            ?>
                          <li><i class="fa fa-comment"></i><a href="<?php comments_link(); ?>"><?php comments_number('0 Comment', '1 Comment', '% Comments'); ?>
                          </a></li>
                           
                        <?php
                          }
                         ?>
                      </ul>
                                <div class="detaildesc">
                                <?php the_content(); ?>
                            </div>

                                <?php
                                 $tag = get_the_tags();
                                 if ($tag) 
                                {
                                 if (isset($creta_Options))
                                {
                                 if(isset($creta_Options['blog_display_tags']) && $creta_Options['blog_display_tags'] == 1) 
                                 { 
                                    ?>
                                 <footer class="entry-meta clearfix">
                                    <div class="line-divider"></div>

                                    <?php  the_tags('<ul class="post-tags"><li>', '</li><li>', '</li></ul>');
                                     ?>

                                </footer>
                                <?php 
                                 }

                                  } 
                                else
                                 {
                                ?>
                                 <footer class="entry-meta clearfix">
                                    <div class="line-divider"></div>

                                    <?php  the_tags('<ul class="post-tags"><li>', '</li><li>', '</li></ul>');
                                     ?>

                                </footer>
                                <?php 

                                 }
                               }
                                 ?>
                              </div>  
                            </div>
                        </div>
                        <?php
                        // Author bio.
                    if (isset($creta_Options))
                        {
 
                     if(isset($creta_Options['blog_show_authors_bio']) && $creta_Options['blog_show_authors_bio'] == 1) {
                            get_template_part('author-bio');
                        }
                      }
                        else
                        {
                         get_template_part('author-bio');   
                        }
                        ?>
                    <?php endwhile; ?>
                    <?php
                
                    // Don't print empty markup if there's nowhere to navigate.
                    $previous = (is_attachment()) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
                    $next = get_adjacent_post(false, '', false);

                    ?>
                    <div class="post-navigation">
                        <?php if ($previous) {
                            ?>
                            <div class="pull-left btn-mega1">
                                <?php previous_post_link('%link', _x('<span class="fa fa-arrow-left"></span> &nbsp; Prev POST', 'Previous post link', 'creta')); ?>
                            </div>
                        <?php } ?>
                        <?php if ($next) {
                            ?>
                            <div class="pull-right btn-mega1">
                                <?php next_post_link('%link', _x('Next Post &nbsp; <span class="fa fa-arrow-right"></span>', 'Next post link', 'creta')); ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                    <?php comments_template('', true); ?>
                </div>
                 </div>
    <?php if (empty($rightbar) && $rightbar != 'hidesidebar') { ?>
     <aside id="column-right" class="col-right sidebar col-lg-3 col-sm-3 col-xs-12 <?php echo esc_html($rightbar) ?>">
    <?php get_sidebar('content'); ?>
    </aside>
    <?php } ?>
        </div>
    </div>
    </div>
<?php get_footer(); ?>