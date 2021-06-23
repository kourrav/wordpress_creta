<?php

get_header(); ?>
 <?php if (isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
      {?>
 <div class="page-heading">   
      <div class="container">
     
          <div class="breadcrumbs">
            <?php $MagikCreta->magikCreta_breadcrumbs(); ?>
      </div>
 
        <div class="page-title">
      <h1 class="entry-title">
        <?php $MagikCreta->magikCreta_page_title(); ?>
      </h1>
        </div>
</div>
</div>
    <?php }
    else { ?>
 <div class="breadcrumbs">
      <div class="container">
        <div class="row">
      <div class="col-xs-12">
       <?php $MagikCreta->magikCreta_breadcrumbs(); ?>
      </div>       
     </div>
     </div>
      </div>
<?php } ?>

<div class="content-wrapper">
    <div class="container">
      
      <?php if(isset($creta_Options['theme_layout']) && $creta_Options['theme_layout']=='version2')
      {
        ?>
        <?php } 
       else{
       ?>
        <div class="page-title">
      <h2 class="entry-title">
        <?php $MagikCreta->magikCreta_page_title(); ?>
      </h2>
        </div>
        <?php

       }
        ?>
        <div class="std">
            <div class="page-not-found wow bounceInRight animated">
                <h2><?php  esc_attr_e('404','creta') ;?></h2>

                <h3><img src="<?php echo esc_url(MAGIKCRETA_THEME_URI) . '/images/signal.png'; ?>"
                         alt="<?php  esc_attr_e('404! Page Not Found','creta') ;?>">
                         <?php  esc_attr_e('Oops! The Page you requested was not found!','creta') ;?></h3>

                <div><a href="<?php echo esc_url(get_home_url()); ?>" type="button"
                        class="btn-home"><span><?php  esc_attr_e('Back To Home','creta') ;?></span></a></div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

