$(document).ready(function(){


    // open modal and load form
    var attachModalHandlers = function(){
        $(document).off('click', '.showModalButton, #create-category-button');
        $(document).on('click', '.showModalButton, #create-category-button', function(e){
            e.preventDefault();
            var url = $(this).data('url');
            $('#modal-product-category .modal-content').html('Loading...');
            $('#modal-product-category').modal('show');
            $.get(url).done(function(data){
                $('#modal-product-category .modal-content').html(data);
            });
        });
    };

    attachModalHandlers();

    // Handle delete via direct AJAX POST (Yii's data-method may not work after DOM replacement)
    var attachDeleteHandlers = function(){
        $('#pjax-product-category').off('click.deleteHandler', 'a[data-method="post"]').on('click.deleteHandler', 'a[data-method="post"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            
            var $link = $(this);
            var url = $link.attr('href');
            var msg = $link.attr('data-confirm') || 'Are you sure?';
            
            if(!confirm(msg)){
                return false;
            }
            
            $.ajax({
                url: url,
                type: 'post',
                data: {'_csrf': yii.getCsrfToken ? yii.getCsrfToken() : $('meta[name="csrf-token"]').attr('content')},
                success: function(res){
                    // Refresh grid after successful delete
                    $.get(window.location.href, function(html){
                        var newGridHtml = $(html).find('#pjax-product-category').html();
                        $('#pjax-product-category').html(newGridHtml);
                        attachModalHandlers();
                        attachDeleteHandlers();
                        var alert = '<div class="toast align-items-center text-white bg-success border-0" role="alert">'
                            + '<div class="d-flex">'
                            + '<div class="toast-body">'
                            + '<i class="las la-trash-alt me-2"></i>'
                            + 'Category deleted successfully'
                            + '</div>'
                            + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
                            + '</div>'
                            + '</div>';
                        $('#category-alert').html(alert);
                        var toastElement = document.querySelector('#category-alert .toast');
                        if(toastElement){
                            var toast = new bootstrap.Toast(toastElement, {delay: 5000});
                            toast.show();
                        }
                    });
                },
                error: function(xhr){
                    alert('Error deleting category: ' + xhr.status);
                }
            });
            return false;
        });
    };
    attachDeleteHandlers();

    // handle ajax form submit inside modal
    var submitProductCategoryForm = function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var form = $(this);
        var action = form.attr('action') || window.location.href;
        $.ajax({
            url: action,
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            success: function(res){
                if(res && res.success){
                    $('#modal-product-category').modal('hide');
                    var alert = '<div class="toast align-items-center text-white bg-success border-0" role="alert">'
                        + '<div class="d-flex">'
                        + '<div class="toast-body">'
                        + '<i class="las la-check-circle me-2"></i>'
                        + res.message
                        + '</div>'
                        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
                        + '</div>'
                        + '</div>';
                    $('#category-alert').html(alert);
                    var toastElement = document.querySelector('#category-alert .toast');
                    if(toastElement){
                        var toast = new bootstrap.Toast(toastElement, {delay: 5000});
                        toast.show();
                    }
                    
                    // Fetch fresh grid data without full page reload
                    $.get(window.location.href, function(html){
                        // Extract grid container from response
                        var newGridHtml = $(html).find('#pjax-product-category').html();
                        $('#pjax-product-category').html(newGridHtml);
                        
                        // Re-attach handlers for new grid rows
                        attachModalHandlers();
                        attachDeleteHandlers();
                    });
                } else if(res && res.html){
                    $('#modal-product-category .modal-content').html(res.html);
                } else {
                    $('#category-alert').html('<div class="alert alert-danger">Save failed.</div>');
                }
            },
            error: function(xhr){
                var ct = xhr.getResponseHeader('Content-Type') || '';
                if(ct.indexOf('text/html') !== -1){
                    $('#modal-product-category .modal-content').html(xhr.responseText);
                } else {
                    alert('An error occurred.');
                }
            }
        });
        return false;
    };

    $(document).on('beforeSubmit', '#product-category-form', submitProductCategoryForm);
    $(document).on('submit', '#product-category-form', submitProductCategoryForm);

    // also handle click on custom ajax-submit buttons inside the modal
    $(document).on('click', '.ajax-submit', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var form = $(this).closest('form');
        submitProductCategoryForm.call(form[0], e);
    });
})