<?php
        /* 
            Template Name: My blog post
        */
        get_header();
        while(have_posts()) { the_post();
        $post_id = get_the_ID(); 
        $featured_img_value = get_field('featured_image', $post_id);
    ?>
    <div class="page-banner" >
        <div class="page-banner__bg-image" style="background-image: url(<?=$featured_img_value ? $featured_img_value : get_theme_file_uri('/images/library-hero.jpg')?>)"></div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php the_title(); ?></h1>
                <div class="page-banner__intro">
                    <p>Author: <?php the_author(); ?></p>
                    <p>Date Posted: <?php the_date('F j, Y g:i A'); ?> </p>
                </div>
            </div>
        </div>
    </div>
<?php
    $args = array(
        'post_type'      => array('post'),
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'sort_order'     => 'ASC',
    );
    $query = new WP_Query( $args );
    $posts = $query->posts;

    if ( $query->have_posts() ) :
?>

    <section class="content">
        <?php the_content(); ?>
    </section>

<?php endif; }
    get_footer();
?>