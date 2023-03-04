    <?php
        /* 
            Template Name: My blog post
        */
        get_header();
        while(have_posts()) { the_post();
    ?>
        <div class="page-banner" >
            <div class="page-banner__bg-image" style="background-image: url(<?=get_theme_file_uri('/images/library-hero.jpg');?>)"></div>
                <div class="page-banner__content container container--narrow">
                    <h1 class="page-banner__title-center"><?php the_title(); ?></h1>
                    <p class="page-banner_tagline">"Educating all students to achieve today and tomorrow in a global community and economy."</p>
                <div class="page-banner__intro">
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
?>

   <?php if ( $query->have_posts() ) : ?>
    <div class="button-wrapper">
        <button onclick="inserPostModal()" class="submit-button"><i class="fa fa-plus"></i> Create New Post</button>
    </div>
    <div class="blog-wrapper">
        <ul>
            <?php foreach ( $posts as $post ) { ?>
                <?php 
                    $featured_img_value = get_field('featured_image', $post->ID);
                    $is_featured = (get_field('featured_post', $post->ID) == 'Yes') ? 'is-featured' : '';
                ?>
                <li id="post-items-<?=$post->ID?>">
                    <span class="featured-post <?=$is_featured;?>" onclick="setAsFeatured(<?=$post->ID?>, '<?=$is_featured;?>', this)"><i class="fa fa-heart"></i></span>
                    <a href="<?php echo get_permalink(); ?>"><div class="blog-image-style" style="background-image: url(<?=$featured_img_value ? $featured_img_value : '/wp-content/uploads/2023/02/undraw_Upload_image_re_svxx.png';?>)"></div></a>
                    <div class="content-wrapper">
                        <div class="blog-content">
                                <a href="<?php echo get_permalink();?>"><h3 class="blog-title"><?=$post->post_title;?></h3></a>
                                <p class="blog-date"><?=get_the_author_meta( 'display_name', $post->post_author); ?> | <?=relative_date($post->post_date); ?></p>
                            <div class="blog-descript"><?=($post->post_content) ? ((strlen($post->post_content) <= 150) ? $post->post_content : substr(strip_tags($post->post_content), 0, 150).'...') : 'No Content'; ?></div>
                        </div>
                        <div class="blog-button">
                            <a href="javascript:;" class="blog-btn edit-btn" onclick="editPost(<?=$post->ID?>)">Edit</a> 
                            <a href="javascript:;" class="blog-btn delete-btn" onclick="deletePost(<?=$post->ID?>, '<?=$post->post_title;?>')">Delete</a>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>

    
   <?php wp_reset_postdata(); ?>
        
    <!-- Post Modal -------------------------------------------------------------------------------->
    <div id="createpostModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal_title"> <i class="fa fa-plus"></i> Create New Post</h6>
                <span class="close" onclick="closeModal('createpostModal')">&times;</span>
            </div>
            <!-- Waiting Loader -->
            <div class="waiting-loader d-none" id="post-loader"><i class="fa fa-spin fa-spinner"></i> <span>Loading post. Please wait...</span></div>
            <!-- Featured Image -->
            <div class="main-post-image" id="featured-img-view" style="background-image: url(/wp-content/uploads/2023/02/undraw_Upload_image_re_svxx.png);"></div>
            <!-- Post Form Content -->
            <div class="modal-form" id="post-form-wrapper">
                <form action="javascript:;" method="post" class="form-container" id="post-form">
                    <div class="form-wrapper">
                        <label for="post_title">Post Title</label>
                        <div class="form-group">
                            <input type="text" class="form-input" id="post_title" name="post_title" placeholder="Write your post title..">
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="subject">Post Description</label>
                        <div class="form-group">
                            <textarea class="form-input" id="post_description" name="post_description" placeholder="Write something.." rows="10"></textarea>
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="subject">Make it Featured?</label>
                        <div class="form-group">
                            <select name="featured_post" id="featured_post" class="form-input">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="subject">Post Image</label>
                        <div class="form-group">
                            <input type="file" class="form-input" id="image" name="post_image" accept=".png, .jpg" >
                            <input type="hidden" name="action" value="insert_new_post">
                            <input type="hidden" name="post_id" id="post_id" value="0">
                        </div>
                    </div>
                    <div class="form-button">
                        <button onclick="insertNewPost(this)" class="submit-button" id="submit-button" type="button"><i class="fa fa-check"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Close Post Modal -------------------------------------------------------------------------->

<?php endif; 
    }
    get_footer();
?>
<script src="/js/function.js"></script>
