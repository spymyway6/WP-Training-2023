<?php

function university_files(){
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('our-main-styles-vendor', get_theme_file_uri('/build/index.css'));
    wp_enqueue_style('sweet-alert-css', get_theme_file_uri('/sweetalert/sweetalert.min.css'));
    wp_enqueue_style('toastr-css', get_theme_file_uri('/toastr/toastr.min.css'));
    wp_enqueue_style('parsley-css', get_theme_file_uri('/parsley/parsley.min.css'));
    wp_enqueue_style('font-awesome-icons-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css');
    wp_enqueue_style('our-main-styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('musics-styles', get_theme_file_uri('/build/musics-styles.css'));
    wp_enqueue_style('rhythm-scss-styles', get_theme_file_uri('/build/rhythm.css'));

    wp_enqueue_script('sweet-alert-jquery', 'https://code.jquery.com/jquery-3.6.3.min.js', NULL, '1.0', true);
    wp_enqueue_script('sweet-alert-js', get_theme_file_uri('/sweetalert/sweetalert.min.js'), array('jquery'), '1.0', true);
    wp_enqueue_script('toastr-js', get_theme_file_uri('/toastr/toastr.min.js'), array('jquery'), '1.0', true);
    wp_enqueue_script('parsley-js', get_theme_file_uri('/parsley/parsley.min.js'), array('jquery'), '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_script('main-fjs', get_theme_file_uri('/js/function.js'), array('jquery'), '1.0', true);
    wp_enqueue_script('music-jss', get_theme_file_uri('/js/music.js'), array('jquery'), '1.0', true);

    wp_localize_script('main-university-js', 'universityData', 
    array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}

add_action('wp_enqueue_scripts', 'university_files');

function university_features() {
    add_theme_support('title-tag');
}

add_action('after_setup_theme', 'university_features');

function relative_date($time) {
    $time    = strtotime($time);
    $today   = strtotime(date('M j, Y'));
    $hrs     = date("h:i A", $time);
    $reldays = ($time - $today)/86400;

    if ($reldays >= 0 && $reldays < 1) {
        return 'Today, '.$hrs;
    } else if ($reldays >= 1 && $reldays < 2) {
        return 'Tomorrow, '.$hrs;
    } else if ($reldays >= -1 && $reldays < 0) {
        return 'Yesterday, '.$hrs;
    }
        
    if (abs($reldays) < 7) {
        if ($reldays > 0) {
            $reldays = floor($reldays);
            return 'In ' . $reldays . ' day' . ($reldays != 1 ? 's' : '');
        } else {
            $reldays = abs(floor($reldays));
            return $reldays . ' day' . ($reldays != 1 ? 's' : '') . ' ago';
        }
    }
        
    if (abs($reldays) < 182) {
        return date('l, j F',$time ? $time : time());
    } else {
        return date('l, j F, Y',$time ? $time : time());
    }
}

// Example CaTCH

add_action( 'wp_ajax_nopriv_delete_post', 'delete_post' );
add_action( 'wp_ajax_delete_post', 'delete_post' );
function delete_post() {
    /* 
        * Extract the data in from this code "$_POST"
        * Ajax or Javascript sends you this data from the form in an array
    */ 

    wp_delete_post($_POST['post_id']); 

    // Return data to Javascript
    echo json_encode([
        'code'     => 200,
        'status'    => true,
        'data'      => [], 
        'msg'      => 'Post created successfully.',
    ]);
}

add_action( 'wp_ajax_nopriv_insert_new_post', 'insert_new_post' );
add_action( 'wp_ajax_insert_new_post', 'insert_new_post' );
function insert_new_post() {
    /* 
        * This code is for Insert and Update Post
        * Update the Post if the "$_POST['post_id']" is not 0
    */
    $new_post = array(
        'post_title' => $_POST['post_title'],
        'post_content' => $_POST['post_description'],
        'post_type' => 'post',
        'post_status' => 'publish'
    );

    // Update the Post if the "$_POST['post_id']" is not 0
    if($_POST['post_id']){
        $new_post['ID'] = $_POST['post_id'];
        wp_update_post( $new_post );
        $post_id = $_POST['post_id'];
    }else{
        $post_id = wp_insert_post( $new_post );
    }

    if( $post_id ){
        // Upload galleries
        $post_img_id = upload_files_and_save($post_id, '');
        if($post_img_id){
            update_field('featured_image', $post_img_id, $post_id);
        }
        update_field('featured_post', $_POST['featured_post'], $post_id);
    }

    // Return data to Javascript
    echo json_encode([
        'code'     => 200,
        'status'    => ($post_id) ? true : false,
        'data'      => [], 
        'msg'      => ($post_id) ? 'Post saved successfully' : 'A problem occured. Please try again.',
    ]);
}

// Upload Files
function upload_files_and_save($post_id, $attach_id) {
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    // for multiple file upload.
    $upload_overrides = array( 'test_form' => false );
    $files = $_FILES['post_image'];
    if($_FILES['post_image']['name']){
        if ( $files['name'] ) {
            $file = array(
                'name' 		=> $files['name'],
                'type' 		=> $files['type'],
                'tmp_name' 	=> $files['tmp_name'],
                'error' 	=> $files['error'],
                'size'	 	=> $files['size']
            );
    
            $movefile = wp_handle_upload( $file, $upload_overrides );

            if ( $movefile && !isset($movefile['error']) ) {
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                    'guid' 			 => $wp_upload_dir['url'] . '/' . basename($movefile['file']),
                    'post_mime_type' => $movefile['type'],
                    'post_title' 	 => preg_replace( '/\.[^.]+$/','', basename($movefile['file'])),
                    'post_content' 	 => '',
                    'post_status' 	 => 'inherit'
                );
                $attach_id = wp_insert_attachment($attachment, $movefile['file']);
                if($attach_id){
                    set_post_thumbnail( $post_id, $attach_id );
                }else{
                    set_post_thumbnail( $post_id, $post_img_id );
                }
            }
        }
    }
    return $attach_id;
}

add_action( 'wp_ajax_nopriv_set_as_featured_post', 'set_as_featured_post' );
add_action( 'wp_ajax_set_as_featured_post', 'set_as_featured_post' );
function set_as_featured_post() {

    update_field('featured_post', $_POST['is_featured'], $_POST['post_id']);

    // Return data to Javascript
    echo json_encode([
        'code'     => 200,
        'status'    => true,
        'data'      => [], 
        'msg'      => 'Post set to featured or not featured successfully.',
    ]);
}

// Fetch Post
add_action( 'wp_ajax_nopriv_fetch_this_post', 'fetch_this_post' );
add_action( 'wp_ajax_fetch_this_post', 'fetch_this_post' );
function fetch_this_post() {
    $args = array(
        'post_type'      => array('post'),
        'p'             => $_POST['post_id'], 
    );
    $query = new WP_Query( $args );
    $post = $query->posts[0];

    // Return data to Javascript
    echo json_encode([
        'code' => 200,
        'status' => true,
        'data' => array(
            'post_id'          => $post->ID,
            'post_title'       => $post->post_title,
            'post_description' => $post->post_content,
            'featured_post'    => (get_field('featured_post', $post->ID) == 'Yes') ? 'Yes' : 'No',
            'featured_image'   => (get_field('featured_image', $post->ID)) ? get_field('featured_image', $post->ID) : '/wp-content/uploads/2023/02/undraw_Upload_image_re_svxx.png',
        ),
        'msg' => 'Post fetched successfully.',
    ]);
}

// =======================================================================================================================
// Music Functions
// =======================================================================================================================

add_action( 'wp_ajax_nopriv_insert_new_music', 'insert_new_music' );
add_action( 'wp_ajax_insert_new_music', 'insert_new_music' );
function insert_new_music() {
    $new_post = array(
        'post_title' => $_POST['music_title'],
        'post_content' => $_POST['music_description'],
        'post_type' => 'music',
        'post_status' => 'publish'
    );

    if($_POST['music_id']){
        $new_post['ID'] = $_POST['music_id'];
        wp_update_post( $new_post );
        $post_id = $_POST['music_id'];
    }else{
        $post_id = wp_insert_post( $new_post );
    }

    if( $post_id ){
        // Upload Image
        $music_img_id = upload_featured_image($post_id, '');
        if($music_img_id){
            update_field('featured_image', $music_img_id, $post_id);
        }

        // Upload Image
        $music_file_id = upload_music_file($post_id, '');
        if($music_file_id){
            update_field('music_file', $music_file_id, $post_id);
        }
        update_field('vocalist', $_POST['vocalist'], $post_id);
        update_field('duration', $_POST['music_duration'], $post_id);
        update_field('file_size', $_POST['file_size'], $post_id);
        update_field('song_lyrics', $_POST['song_lyrics'], $post_id);
    }

    // Return data to Javascript
    echo json_encode([
        'code'     => 200,
        'status'    => ($post_id) ? true : false,
        'data'      => [], 
        'msg'      => ($post_id) ? 'Music saved successfully' : 'A problem occured. Please try again.',
    ]);
}

// Upload Featured Image
function upload_featured_image($post_id, $attach_id) {
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    // for multiple file upload.
    $upload_overrides = array( 'test_form' => false );
    $files = $_FILES['featured_image'];
    if($_FILES['featured_image']['name']){
        if ( $files['name'] ) {
            $file = array(
                'name' 		=> $files['name'],
                'type' 		=> $files['type'],
                'tmp_name' 	=> $files['tmp_name'],
                'error' 	=> $files['error'],
                'size'	 	=> $files['size']
            );
    
            $movefile = wp_handle_upload( $file, $upload_overrides );

            if ( $movefile && !isset($movefile['error']) ) {
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                    'guid' 			 => $wp_upload_dir['url'] . '/' . basename($movefile['file']),
                    'post_mime_type' => $movefile['type'],
                    'post_title' 	 => preg_replace( '/\.[^.]+$/','', basename($movefile['file'])),
                    'post_content' 	 => '',
                    'post_status' 	 => 'inherit'
                );
                $attach_id = wp_insert_attachment($attachment, $movefile['file']);
                if($attach_id){
                    set_post_thumbnail( $post_id, $attach_id );
                }else{
                    set_post_thumbnail( $post_id, $post_img_id );
                }
            }
        }
    }
    return $attach_id;
}

// Upload Music File
function upload_music_file($post_id, $attach_id) {
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    // for multiple file upload.
    $upload_overrides = array( 'test_form' => false );
    $files = $_FILES['music_file'];
    if($_FILES['music_file']['name']){
        if ( $files['name'] ) {
            $file = array(
                'name' 		=> $files['name'],
                'type' 		=> $files['type'],
                'tmp_name' 	=> $files['tmp_name'],
                'error' 	=> $files['error'],
                'size'	 	=> $files['size']
            );
    
            $movefile = wp_handle_upload( $file, $upload_overrides );

            if ( $movefile && !isset($movefile['error']) ) {
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                    'guid' 			 => $wp_upload_dir['url'] . '/' . basename($movefile['file']),
                    'post_mime_type' => $movefile['type'],
                    'post_title' 	 => preg_replace( '/\.[^.]+$/','', basename($movefile['file'])),
                    'post_content' 	 => '',
                    'post_status' 	 => 'inherit'
                );
                $attach_id = wp_insert_attachment($attachment, $movefile['file']);
                if($attach_id){
                    set_post_thumbnail( $post_id, $attach_id );
                }else{
                    set_post_thumbnail( $post_id, $post_img_id );
                }
            }
        }
    }
    return $attach_id;
}

// Fetch Music
add_action( 'wp_ajax_nopriv_fetch_this_music', 'fetch_this_music' );
add_action( 'wp_ajax_fetch_this_music', 'fetch_this_music' );
function fetch_this_music() {
    $args = array(
        'post_type'      => array('music'),
        'p'             => $_POST['music_id'], 
    );
    $query = new WP_Query( $args );
    $post = $query->posts[0];

    // Return data to Javascript
    echo json_encode([
        'code' => 200,
        'status' => true,
        'data' => array(
            'music_id'          => $post->ID,
            'music_title'       => $post->post_title,
            'music_description' => $post->post_content,
            'vocalist' => get_field('vocalist', $post->ID),
            'duration' => get_field('duration', $post->ID),
            'file_size' => get_field('file_size', $post->ID),
            'song_lyrics' => get_field('song_lyrics', $post->ID),
            'featured_image'   => (get_field('featured_image', $post->ID)) ? get_field('featured_image', $post->ID) : '/wp-content/themes/sciences-university-theme/images/music-default.jpg',
        ),
        'msg' => 'Music fetched successfully.',
    ]);
}

add_action( 'wp_ajax_nopriv_delete_music', 'delete_music' );
add_action( 'wp_ajax_delete_music', 'delete_music' );
function delete_music() {

    wp_delete_post($_POST['post_id']); 
    
    // Return data to Javascript
    echo json_encode([
        'code'     => 200,
        'status'    => true,
        'data'      => [], 
        'msg'      => 'Post created successfully.',
    ]);
}