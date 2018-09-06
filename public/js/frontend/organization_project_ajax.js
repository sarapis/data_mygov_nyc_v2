$(document).ready(function()
{
    $('.project-link').on('click', function() {

        var project_id = $(this).attr('id');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        
        $.ajax({
            type: 'POST',
            url: '/organizationproject_'+project_id,
            contentType: false,
            cache: false, // To unable request pages to be cached
            processData: false,
            success: function(data) {
                $('#project_content').html(data);
            },
            error: function(errResponse) {

            }
        });
        
    });
});