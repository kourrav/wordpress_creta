<div id="nav-panel" class="">
    <?php
    // show top navigation and mobile menu
         $MagikCreta = new MagikCreta();
     
        echo magikCreta_mobile_search();
        echo '<div class="menu-wrap">';
      
        echo magikCreta_mobile_menu().'</div>';

   
        echo '<div class="menu-wrap">'.magikCreta_mobile_top_navigation().'</div>';
    ?>
</div>