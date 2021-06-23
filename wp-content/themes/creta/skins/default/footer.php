<?php 

$MagikCreta = new MagikCreta();?>
<?php magikCreta_footer_brand_logo();?>
 <?php magikCreta_header_service();?>
<footer>
    <!-- BEGIN INFORMATIVE FOOTER -->
    <div class="footer-inner">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-xs-12 col-lg-8">
            <div class="footer-column pull-left">
                        <?php if (is_active_sidebar('footer-sidebar-1')) : ?>
                            <?php dynamic_sidebar('footer-sidebar-1'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="footer-column pull-left">
                        <?php if (is_active_sidebar('footer-sidebar-2')) : ?>
                            <?php dynamic_sidebar('footer-sidebar-2'); ?>
                        <?php endif; ?>
                    </div>
                     <div class="footer-column pull-left">
                        <?php if (is_active_sidebar('footer-sidebar-3')) : ?>
                            <?php dynamic_sidebar('footer-sidebar-3'); ?>
                        <?php endif; ?>
                    </div>
              </div>

        <div class="col-xs-12 col-lg-4">
            <div class="footer-column-last">
             
                   
               <?php magikCreta_footer_signupform();?>
                 <?php magikCreta_social_media_links(); ?>
               <?php if (is_active_sidebar('footer-sidebar-4')) : ?>
                 <?php dynamic_sidebar('footer-sidebar-4'); ?>
                <?php endif; ?>
               
      
              
            </div>
          </div>
                  
        </div>
        <!--row-->

      </div>
      <!--container-->

  </div>  
  <!--footer-middle-->
    <div class="footer-middle">
      <div class="container">
        <div class="row">
             <?php magikCreta_footer_middle();?>
             </div>
      </div>
    </div>
               
   
    <!--footer-bottom-->
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
   
    <?php wp_footer(); ?>
    </body></html>
