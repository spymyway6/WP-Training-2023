// Progress
const progress = document.getElementById("progress");
const progressContainer = document.getElementById("progress-container");
progressContainer.addEventListener("click", setProgress);

// Volume
const volumeContainer = document.getElementById("volumeContainer");
const volumeWidth = document.getElementById("volumeWidth");
volumeContainer.addEventListener("click", setVolume);

const audio = document.getElementById("music_audio");

// Audio Functions
audio.addEventListener("timeupdate", updateProgress);
audio.addEventListener("ended", playNextSong);

// Autoplay the music
$(document).ready(function(){
    setTimeout(() => {
        audio.play();
        $('#play-btn-play').html('<i class="fa fa-pause"></i>');
        $('#play-btn-play').attr('onclick', "playSong(this, 'pause')");
        $('#rhythm-animation').removeClass('d-none');
        $('#audio_duration').html(numToTime(audio.duration));
    }, "2000");
});

function playSong(e, type) {
    if(type=='play'){
        audio.play();
        $(e).html('<i class="fa fa-pause"></i>');
        $(e).attr('onclick', "playSong(this, 'pause')");
        $('#rhythm-animation').removeClass('d-none');
    }else{
        audio.pause();
        $(e).html('<i class="fa fa-play"></i>');
        $(e).attr('onclick', "playSong(this, 'play')");
        $('#rhythm-animation').addClass('d-none');
    }
    $('#audio_duration').html(numToTime(audio.duration));
}

function stopSong(){
    audio.currentTime = 0;
    $('#tracktime').html('0:00');
    $('.play-btn-play').html('<i class="fa fa-play"></i>');
    $('.play-btn-play').attr('onclick', "playSong(this, 'play')");
}

function playNextSong(){
    $('#play-btn-prev-next').trigger('click');
}

function updateProgress(e) {
    const { duration, currentTime } = e.srcElement;
    const progressPercent = (currentTime / duration) * 100;
    progress.style.width = `${progressPercent}%`;
    $('#tracktime').html(numToTime(audio.currentTime));
}

function numToTime(num) { 
    var hours = Math.floor(num / 60);  
    var minutes = num % 60;
    if (minutes + ''.length < 2) {
      minutes = '0' + minutes; 
    }
    var floor_mins = Math.floor(minutes);
    var mins = (floor_mins < 10) ? '0'+floor_mins : floor_mins;
    return hours + ":" + mins;
}

function setProgress(e) {
    const width = this.clientWidth;
    const clickX = e.offsetX;
    const duration = audio.duration;
    audio.currentTime = Math.floor((clickX / width) * duration);
}

function setVolume(e) {
    const volWidth = this.clientWidth;
    const volClickX = e.offsetX;
    const progressPercent = (volClickX / volWidth) * 100;
    volumeWidth.style.width = `${progressPercent}%`;
    audio.volume = progressPercent / 100; // 50%
}

// Music Modal

function inserMusicModal(modal){
    // Show Loader and Open modal
    $('#post-loader').addClass('d-none');
    $('#featured-img-view').removeClass('d-none');
    $('#post-form-wrapper').removeClass('d-none');
    openModal(modal);

    // Edit Modal Title and Button
    $('#modal_title').html('<i class="fa fa-plus"></i> Add New Music');
    $('#submit-button').html('<i class="fa fa-check"></i> Save Music');

    // Reset the Form and Return back the background image default
    $('#music_id').val(0); // Set back the post ID to 0
    $('#addMusicForm')[0].reset();
    $('#featured-img-view').css('background-image', `url('/wp-content/themes/sciences-university-theme/images/music-default.jpg')`);
}

function inserNewMusic(e){
    $('#addMusicForm').parsley().validate();
    if ($('#addMusicForm').parsley().isValid()) {
        $(e).html('<i class="fa fa-spin fa-spinner"></i> Saving please wait...');
        $(e).attr('disabled', 'disabled');
        var formData = new FormData(document.getElementById('addMusicForm'));
        $.ajax({
            type: 'POST',
            url: $('#ajaxUrl').val(), // insert_new_music
            data: formData,
            contentType: false,
            processData: false,
            success:(resp)=>{
                var res = JSON.parse(resp.slice(0, -1));
                if(res.status==true){
                    swal({
                        title: "Music Saved!",
                        text: res.msg,
                        type: "success",
                    },
                    () => {
                        window.location.href='/my-musics/'
                    });
                }else{
                    // Error message
                    swal('Oops!', 'We found out that you have an issue with your system', 'error');
                }
            },
            error:(err)=>{
                // Error message
                swal('Oops!', 'We found out that you have an issue with your system', 'error');
            }
        });
    }
}

function editMusic(modal, id){
    // Show Loader and Open modal
    $('#post-loader').removeClass('d-none');
    $('#featured-img-view').addClass('d-none');
    $('#post-form-wrapper').addClass('d-none');
    openModal(modal);

    // Edit Modal Title and Button
    $('#modal_title').html('<i class="fa fa-pencil"></i> Edit Music');
    $('#submit-button').html('<i class="fa fa-check"></i> Save Music');

    $.ajax({
        type: 'POST',
        url: $('#ajaxUrl').val(), 
        data: {
            music_id: id,
            action: 'fetch_this_music'
        },
        success:(resp)=>{
            var res = JSON.parse(resp.slice(0, -1));
            if(res.status==true){
                console.log(res.data)

                // Update Field Values
                $('#music_id').val(res.data.music_id);
                $('#music_title').val(res.data.music_title);
                $('#music_description').val(res.data.music_description);
                $('#vocalist').val(res.data.vocalist);
                $('#music_duration').val(res.data.duration);
                $('#file_size').val(res.data.file_size);
                $('#song_lyrics').val(res.data.song_lyrics);
                $('#featured-img-view').css('background-image', `url(${res.data.featured_image})`);

                // Hide Loader and show the forms
                $('#post-loader').addClass('d-none');
                $('#featured-img-view').removeClass('d-none');
                $('#post-form-wrapper').removeClass('d-none');
            }else{
                // Error message
                toastr.error('We found out that you have an issue with your system');
            }
        },
        error: (e)=>{
            toastr.error('We found out that you have an issue with your system');
        }
    });
}

function deleteMusic(musicTitle, id){
    swal(
        {
            title: 'Delete music: "'+ musicTitle + '"?',
            text: "Please note, you will not be able to recover this. Continue?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false,
            confirmButtonColor: "#e11641",
        },
        () => {
            $.ajax({
                type: 'POST',
                url: $('#ajaxUrl').val(),
                data: {
                    post_id: id,
                    action: 'delete_music'
                },
                success:(resp)=>{
                    var res = JSON.parse(resp.slice(0, -1)); // We need this code to remoge the number 1 on the return
                    if(res.status==true){
                        // Success Message
                        $('#music-items-'+id).remove();
                        swal('Deleted', 'Deleted, Succefully.', 'success');
                    }else{
                        // Error message
                        swal('Oops!', 'We found out that you have an issue with your system', 'error');
                    }
                },
                error: (e)=>{
                    swal('Oops!', 'We found out that you have an issue with your system', 'error');
                }
            });
        }
    );
}