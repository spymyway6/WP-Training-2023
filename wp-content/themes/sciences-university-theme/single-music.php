<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <input type="hidden" id="ajaxUrl" value="<?=admin_url('admin-ajax.php');?>">
    <style>
        html{
            margin: 0 !important;
        }
    </style>
</head>
<?php while(have_posts()) { the_post(); ?>
    <?php 
        $music_id = get_the_ID(); 
        $featured_img = (get_field('featured_image', $music_id)) ? get_field('featured_image', $music_id) : get_theme_file_uri('images/artists/music-img-5.png');
    ?>
    <body>
        <section class="single-music-section" style="background-image: url(<?=get_theme_file_uri('images/single-bg.jpg');?>)">
            <div class="single-music-section-wrapper">
                <div class="music-details-content-wrapper">
                    <div class="music-contents-wrapper">
                        <div class="music-content-img" style="background-image: url(<?=$featured_img;?>)"></div>
                        <div class="m-content">
                            <h1><?=get_the_title(); ?></h1>
                            <h5><?=get_field('vocalist', $music_id); ?></h5>
                            <p class="desc">
                                <?=nl2br(get_the_content()); ?>
                            </p>
                            <div class="more-music-details">
                                <ul>
                                    <li><i class="fa fa-clock"></i> Duration: <?=get_field('duration', $music_id); ?></li>
                                    <li><i class="fa fa-music"></i> File Size: <?=get_field('file_size', $music_id); ?></li>
                                </ul>
                            </div>
                            <!-- <button class="play-btn-content"><i class="fa fa-play-circle"></i> Play Music</button> -->
                        </div>
                    </div>
                    <div class="music-lyrics-wrapper">
                        <div class="lyrics">
                            <!-- <marquee direction="up" height="600px" scrollamount="1"> -->
                                <?=nl2br(get_field('song_lyrics', $music_id)); ?>
                            <!-- </marquee> -->
                        </div>
                    </div>
                    <div class="music-suggestions">
                        <h2>More Musics To Play</h2>
                        <div class="latest-music-lists">
                            <?php
                                $args = array(
                                    'post_type'      => array('music'),
                                    'post_status'    => 'publish',
                                    'posts_per_page' => 6,
                                    'orderby'        => 'rand',
                                    'sort_order'     => 'ASC',
                                    'post__not_in' => array($music_id)
                                );
                                $query = new WP_Query( $args );
                                $get_musics = $query->posts;
                            ?>
                            <ul>
                                <?php foreach($get_musics as $music){ ?>
                                    <?php
                                        $_music_id = $music->ID; 
                                        $_featured_img = (get_field('featured_image', $_music_id)) ? get_field('featured_image', $_music_id) : '/wp-content/themes/sciences-university-theme/images/music-default.jpg';  
                                    ?>
                                    <li>
                                        <div class="lists-music-img-wrapper">
                                            <div class="lists-music-img" style="background-image: url(<?=$_featured_img;?>)">
                                                <div class="lists-play-button"><a href="/<?=$music->post_type;?>/<?=$music->post_name;?>"><i class="fa fa-play-circle"></i></a></div>
                                            </div>
                                        </div>
                                        <div class="lists-music-content">
                                            <h3><a href="/<?=$music->post_type;?>/<?=$music->post_name;?>"><?=$music->post_title;?></a></h3>
                                            <p class="music-artists"><?=get_field('vocalist', $_music_id); ?></p>
                                            <p class="music-duration">Duration: <?=get_field('duration', $_music_id); ?> | File Size: <?=get_field('file_size', $_music_id); ?></p>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                            <a href="/my-musics" class="play-btn-content"><i class="fa fa-play-circle"></i> View More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Player Controls -->
        <?php 
            $previous_music = $get_musics[5];
            $next_music = $get_musics[0];
        ?>
        <section class="music-player">
            <audio id="music_audio" src="<?=get_field('music_file', $music_id); ?>" title="<?=get_the_title(); ?>">
                <p>Your browser does not support the audio element</p>
            </audio>
            <div class="music-player-wrapper">
                <!-- Left -->
                <div class="music-content-artist">
                    <div class="player-img" style="background-image: url(<?=$featured_img;?>)">
                        <div class="rhythm-animation d-none" id="rhythm-animation">
                            <svg xmlns="http://www.w3.org/2000/svg" class="equilizer" viewBox="0 0 128 128">
                                <g>
                                    <title>Audio Equilizer</title>
                                    <rect class="bar" transform="translate(0,0)" y="15"></rect>
                                    <rect class="bar" transform="translate(25,0)" y="15"></rect>
                                    <rect class="bar" transform="translate(50,0)" y="15"></rect>
                                    <rect class="bar" transform="translate(75,0)" y="15"></rect>
                                    <rect class="bar" transform="translate(100,0)" y="15"></rect>
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div class="player-artist-cont">
                        <h5 class="player-music-title"><?=get_the_title(); ?></h5>
                        <p class="player-artist"><?=get_field('vocalist', $music_id); ?></p>
                    </div>
                </div>
                <!-- Center -->
                <div class="player-tools">
                    <div class="player-buttons">
                        <div class="play-btn-prev-next" onclick="window.location.href='/<?=$previous_music->post_type;?>/<?=$previous_music->post_name;?>/'"><i class="fa fa-backward"></i></div>
                        <div id="play-btn-play" class="play-btn-play" onclick="playSong(this, 'play')"><i class="fa fa-play"></i></div>
                        <div id="play-btn-prev-next" class="play-btn-prev-next" onclick="window.location.href='/<?=$next_music->post_type;?>/<?=$next_music->post_name;?>/'"><i class="fa fa-forward"></i></div>
                    </div>
                </div>
                <!-- Right -->
                <div class="music-player-volume">
                    <div class="volume-wrapper">
                        <i class="fa fa-volume-up"></i> 
                        <div class="progress-container" id="volumeContainer">
                            <div class="progress volume" id="volumeWidth"></div>
                        </div> 
                    </div>
                </div>
            </div>
            <!-- Player -->
            <div class="player-timer">
                <span id="tracktime">0:00</span> 
                <div class="progress-container" id="progress-container">
                    <div class="progress" id="progress"></div>
                </div> 
                <span id="audio_duration">0:00</span>
            </div>
        </section>
    </body>
<?php } ?>

<!-- Footer -->
<?php wp_footer(); ?>

</html>