<?php 

$MagikCreta = new MagikCreta();?>

<?php if ((is_front_page() && is_home()) || is_front_page()) { ?>
 <div class="footer">
    <div class="footer-bottom">
      <div class="container">
        <div class="row">
 <?php magikCreta_footer_text1(); ?>

              
        <!--row-->
<div class="col-lg-4 col-md-4 col-xs-12 col-sm-6 social">
 <?php magikCreta_social_media_links(); ?>
 </div>
   
      </div>
      </div>
      <!--container-->
    </div>
   
 </div>
    </div>
        
<div class="menu-overlay"></div>
<?php // navigation panel
require_once(MAGIKCRETA_THEME_PATH .'/menu-panel.php');?>
    <?php wp_footer(); ?>

    </body>
    </html>
<?php
} else {
    ?>

 <?php magikCreta_footer_brand_logo();?>
  
<footer>

    <!-- BEGIN INFORMATIVE FOOTER -->
    <div class="footer-inner">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-xs-12 col-lg-12">
            <div class="footer-column footer-first">
                        <?php if (is_active_sidebar('footer-sidebar-1')) : ?>
                            <?php dynamic_sidebar('footer-sidebar-1'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="footer-column">
                        <?php if (is_active_sidebar('footer-sidebar-2')) : ?>
                            <?php dynamic_sidebar('footer-sidebar-2'); ?>
                        <?php endif; ?>
                    </div>
                     <div class="footer-column">
                        <?php if (is_active_sidebar('footer-sidebar-3')) : ?>
                            <?php dynamic_sidebar('footer-sidebar-3'); ?>
                        <?php endif; ?>
                    </div>
                      <div class="co-info">
                        <?php if (is_active_sidebar('footer-sidebar-4')) : ?>
                            <?php dynamic_sidebar('footer-sidebar-4'); ?>
                        <?php endif; ?>

                        <div class="social">
                            
                                <?php magikCreta_social_media_links(); ?>
                            
                        </div>
                    </div>
                    
                </div>
          
        </div>
      </div>
      
    </div>
  

<div class="footer-middle">
      
         <?php magikCreta_footer_middle();?>     
</div>
        
     
   
     
      <div class="footer-bottom">
      <div class="container">
        <div class="row">
                    <?php $MagikCreta->magikCreta_footer_text(); ?>
               </div>
     
      </div>
    
    </div>
   
 
  </footer>

    </div>
 
     
<div class="menu-overlay"></div>
<?php // navigation panel
require_once(MAGIKCRETA_THEME_PATH .'/menu-panel.php');
 ?>
    <!-- JavaScript -->
    
    <?php wp_footer(); ?>
    </body></html><?php } ?>
