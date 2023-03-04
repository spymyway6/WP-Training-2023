<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <input type="hidden" id="ajaxUrl" value="<?=admin_url('admin-ajax.php');?>">
</head>
<body>
    <section class="section-music-wrappers">
        <!--  -->
        <section class="main-section-wrapper" style="background-image: url(<?=get_theme_file_uri('images/music-bg.jpg')?>)">
            <div class="bg-overlay"></div>
            <div class="section-wrapper">
                <h1 class="section-header"><span>MJ</span> MUSICS</h1>
                <h5 class="slogan">Where my heart beats faster than the rhythm</h5>
            </div>
        </section>
        <?php
            $paged = 1;
            $paginate_url = explode("/", $_SERVER['REQUEST_URI']);
            // Search Field
            $keyword = isset($_GET['search']) ? $_GET['search'] : '';
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            if(count($paginate_url) == 5){
                if($paginate_url[2] == 'page'){
                    $paged = $paginate_url[3];
                }
            }
            function pagination( $paged = '', $max_page = '' )
            {
                if( !$paged )
                    $paged = get_query_var('paged');
                if( !$max_page )
                    $max_page = $wp_query->max_num_pages;
                return paginate_links( array(
                    'current'    => max(1, $paged),
                    'total'      => $max_page,
                    'mid_size'   => 1,
                    'prev_text'  => __('<i class="fa fa-backward"></i>'),
                    'next_text'  => __('<i class="fa fa-forward"></i>'),
                    'type'       => 'block'
                ) );
            }
            $args = array(
                'post_type'      => array('music'),
                'post_status'    => 'publish',
                'posts_per_page' => 10,
                'orderby'        => 'date',
                'paged'          => $paged,
                'sort_order'     => 'ASC',
                's'              => $keyword,
            );
            $query = new WP_Query( $args );
            $get_musics = $query->posts;
        ?>
        <section class="body-section">
            <div class="music-section-wrapper">
                <div class="music-lists">
                    <div class="heading-music-title">
                        <h2>Today's Hits</h2>
                        <button onclick="inserMusicModal('addMusicModal')" class="add-new-btn"><i class="fa fa-plus"></i> Add Music</button>
                    </div>
                    <div class="search-container">
                        <?php if(pagination($paged, $query->max_num_pages)){ ?>
                            <div class="pagination-list">
                                <?=pagination($paged, $query->max_num_pages); ?>
                            </div>
                        <?php }else{ ?>
                            <h5 class="slogan" style="font-size: 16px;">Search results</h5>
                        <?php } ?>
                        <div class="custom-search-wrapper">
                            <form action="/my-musics" method="get">
                                <i class="fa fa-search"></i>
                                <input type="search" name="search" id="search-input" class="input-search" value="<?=$keyword;?>" placeholder="Search codes...">
                                <button type="submit">Search</button>
                            </form>
                        </div>
                    </div>
                    <!-- Search -->
                    <!-- Music Lists -->
                    <?php if($get_musics) { ?>
                    <ul>
                        <?php foreach($get_musics as $music){ ?>
                            <?php
                                $music_id = $music->ID; 
                                $featured_img = (get_field('featured_image', $music_id)) ? get_field('featured_image', $music_id) : '/wp-content/themes/sciences-university-theme/images/music-default.jpg';  
                            ?>
                            <li id="music-items-<?=$music_id;?>">
                                <div class="music-img-wrapper">
                                    <div class="music-img" style="background-image: url(<?=$featured_img;?>)">
                                        <div class="play-button"><a href="/<?=$music->post_type;?>/<?=$music->post_name;?>"><i class="fa fa-play-circle"></i></a></div>
                                    </div>
                                </div>
                                <div class="music-content">
                                    <h3><a href="/<?=$music->post_type;?>/<?=$music->post_name;?>"><?=$music->post_title;?></a></h3>
                                    <p class="music-artists"><?=get_field('vocalist', $music_id); ?></p>
                                    <p class="music-desc"><?=($music->post_content) ? ((strlen($music->post_content) <= 35) ? $music->post_content : substr(strip_tags($music->post_content), 0, 35).'...') : 'No Description'; ?></p>
                                </div>
                                <div class="edit-buttons">
                                    <a href="javascript:;" onclick="editMusic('addMusicModal', <?=$music_id;?>)"><i class="fa fa-pencil"></i></a>
                                    <a href="javascript:;" onclick="deleteMusic(`<?=$music->post_title;?>`, <?=$music_id;?>)"><i class="fa fa-times"></i></a>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                    <?php }else{ ?>
                        <div class="empty-msg">
                            <i class="fa fa-music"></i>
                            <h5 class="slogan">No musics found</h5>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </section>

    <!-- Modal Section -->
    <!-- Post Modal -------------------------------------------------------------------------------->
    <div id="addMusicModal" class="modal music-modal">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal_title"> <i class="fa fa-plus"></i> Add New Music </h6>
                <span class="close" onclick="closeModal('addMusicModal')">&times;</span>
            </div>
            <!-- Waiting Loader -->
            <div class="waiting-loader d-none" id="post-loader"><i class="fa fa-spin fa-spinner"></i> <span>Loading post. Please wait...</span></div>
            <!-- Featured Image -->
            <div class="main-post-image" id="featured-img-view" style="background-image: url('/wp-content/themes/sciences-university-theme/images/music-default.jpg');"></div>
            <!-- Post Form Content -->
            <div class="modal-form" id="post-form-wrapper">
                <form action="javascript:;" method="post" class="form-container" id="addMusicForm">
                    <div class="form-wrapper">
                        <label for="music_title">Music Title *</label>
                        <div class="form-group">
                            <input type="text" class="form-input" id="music_title" name="music_title" placeholder="The title of your music.." required>
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="music_title">Vocalist *</label>
                        <div class="form-group">
                            <input type="text" class="form-input" id="vocalist" name="vocalist" placeholder="Name of the Vocalist" required>
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="music_description">Music Description *</label>
                        <div class="form-group">
                            <textarea class="form-input" id="music_description" name="music_description" placeholder="Write description.." rows="10" required></textarea>
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="music_title">Duration *</label>
                        <div class="form-group">
                            <input type="text" class="form-input" id="music_duration" name="music_duration" placeholder="Duration" required>
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="file_size">File Size *</label>
                        <div class="form-group">
                            <input type="text" class="form-input" id="file_size" name="file_size" placeholder="File Size" required>
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="song_lyrics">Song Lyrics *</label>
                        <div class="form-group">
                            <textarea class="form-input" id="song_lyrics" name="song_lyrics" placeholder="Write the lyrics here.." rows="10" required></textarea>
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="music_file">Music File *</label>
                        <div class="form-group">
                            <input type="file" class="form-input" id="music_file" name="music_file" accept=".mp3">
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <label for="featured_image">Featured Image *</label>
                        <div class="form-group">
                            <input type="file" class="form-input" id="featured_image" name="featured_image" accept=".png, .jpg">
                            <input type="hidden" name="action" value="insert_new_music">
                            <input type="hidden" name="music_id" id="music_id" value="0">
                        </div>
                    </div>
                    <div class="form-button">
                        <button onclick="inserNewMusic(this)" class="submit-button" id="submit-button" type="button"><i class="fa fa-check"></i> Save Music</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Close Post Modal -------------------------------------------------------------------------->

</body>
<!-- Footer -->
<?php wp_footer(); ?>
</html>