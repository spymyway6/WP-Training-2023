function editPost(id){
    // Show Loader and Open modal
    $('#post-loader').removeClass('d-none');
    $('#featured-img-view').addClass('d-none');
    $('#post-form-wrapper').addClass('d-none');
    openModal('createpostModal');

    // Edit Modal Title and Button
    $('#modal_title').html('<i class="fa fa-pencil"></i> Editing Post');
    $('#submit-button').html('<i class="fa fa-check"></i> Update');

    $.ajax({
        type: 'POST',
        url: $('#ajaxUrl').val(), 
        data: {
            post_id: id,
            action: 'fetch_this_post'
        },
        success:(resp)=>{
            var res = JSON.parse(resp.slice(0, -1));
            if(res.status==true){
                console.log(res.data)

                // Update Field Values
                $('#post_id').val(res.data.post_id);
                $('#post_title').val(res.data.post_title);
                $('#post_description').val(res.data.post_description);
                $('#featured_post').val(res.data.featured_post);
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

function deletePost(id, postTitle){
    swal(
        {
            title: "Delete this post: "+ postTitle + "?",
            text: "Please note, you will not be able to recover this. Continue?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false,
            confirmButtonColor: "#e11641",
        },
        () => {
            // swal("Deleted", "Successfully deleted.", "success");
            $.ajax({
                type: 'POST',
                url: $('#ajaxUrl').val(), // Target the function name to the WordPress. Ex: "get_create_posts_form_example"
                // data: $('#post-form').serialize(),
                // data: {
                //     post_title: $('#post_title').val(),
                //     post_description: $('#post_description').val(),
                // },
                data: {
                    post_id: id,
                    action: 'delete_post'
                },
                success:(resp)=>{
                    var res = JSON.parse(resp.slice(0, -1)); // We need this code to remoge the number 1 on the return
                    if(res.status==true){
                        // Success Message
                        $('#post-items-'+id).remove();
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

function inserPostModal(){
    // Show Loader and Open modal
    $('#post-loader').addClass('d-none');
    $('#featured-img-view').removeClass('d-none');
    $('#post-form-wrapper').removeClass('d-none');
    openModal('createpostModal');

    // Edit Modal Title and Button
    $('#modal_title').html('<i class="fa fa-plus"></i> Create New Post');
    $('#submit-button').html('<i class="fa fa-check"></i> Submit');

    // Reset the Form and Return back the background image default
    $('#post_id').val(0); // Set back the post ID to 0
    $('#post-form')[0].reset();
    $('#featured-img-view').css('background-image', `url('/wp-content/uploads/2023/02/undraw_Upload_image_re_svxx.png')`);
}

function openModal(modalID){
    var modal = document.getElementById(modalID);
    modal.style.display = "block";
}

function closeModal(modalID){
    var modal = document.getElementById(modalID);
    modal.style.display = "none";
}

function insertNewPost(e){
    if($('#post_title').val() && $('#post_description').val()){
        $(e).html('<i class="fa fa-spin fa-spinner"></i> Posting...');
        $(e).attr('disabled', 'disabled');
        var formData = new FormData(document.getElementById('post-form'));
        $.ajax({
            type: 'POST',
            url: $('#ajaxUrl').val(), // insert_new_post
            data: formData,
            contentType: false,
            processData: false,
            success:(resp)=>{
                var res = JSON.parse(resp.slice(0, -1));
                if(res.status==true){
                    swal({
                        title: "Post Saved!",
                        text: res.msg,
                        type: "success",
                    },
                    () => {
                        window.location.href='/blog-list'
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
    }else{
        swal('Oops!', 'Please write a title and a description on your post...', 'error');
    }
}

function setAsFeatured(id, type, e){
    toastr.warning('Making it featured. Please wait...');
    $.ajax({
        type: 'POST',
        url: $('#ajaxUrl').val(), 
        data: {
            post_id: id,
            is_featured: (type == 'is-featured') ? 'No' : 'Yes',
            action: 'set_as_featured_post'
        },
        success:(resp)=>{
            var res = JSON.parse(resp.slice(0, -1));
            if(res.status==true){
                // Success Message
                if(type=='is-featured'){ // Remove Featured
                    toastr.success('Removed as featured');
                    $(e).removeClass('is-featured');
                    $(e).attr('onclick', `setAsFeatured(${id}, '', this)`);
                }else{ //Set to Featured
                    toastr.success('Post set as featured.');
                    $(e).addClass('is-featured');
                    $(e).attr('onclick', `setAsFeatured(${id}, 'is-featured', this)`);
                }
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