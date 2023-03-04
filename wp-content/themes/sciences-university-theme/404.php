<!DOCTYPE html>
<html>
    <head>
        <?php wp_head(); ?>
    </head>
    <body class="page-bannerr" style="background-image: url(<?php echo get_theme_file_uri('images/simple404.png')?>)" >
       <div>
        <div class="page-banner__bg-color"></div>
        <div class="container t-center c-white t-top">
            <h2 class="headline headline--larger ">OOPS! Page not Found</h2>
             <?php echo "<br> <br>" ?>
        <a href="<?php echo site_url() ?>" class="btn btn--slarge btn--black">Back to Home Page</a>
      </div>
    </div>

    </body>
</html>
   
