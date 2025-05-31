$(document).ready(function() {
    $('.justify-btn').on('click', function() {
        let absence_id = $(this).data('id');
        $('#justifyModal').find('input[name="attendance_id"]').val(absence_id);
    })

    $('#justifyForm').submit(function(e) {
        e.preventDefault();

        $('#justifyForm .form-group').hide();
        $('#loadingSpinner').removeClass('d-none');

        let form = $('#justifyForm')[0];
        let formData = new FormData(form);

        $.ajax({
            url: storeJustification, 
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#justifyModal').modal('hide');
                $('#justifyForm')[0].reset();
                location.reload(); 
            },
            error: function (xhr) {
                $('#loadingSpinner').addClass('d-none');
                $('#justifyForm .form-group').show();
            
                if (xhr.status === 422) {
                    
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = Object.values(errors).map(messages => messages.join('\n')).join('\n');
                    alert("Validation Error:\n" + errorMessages);
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    
                    alert("Error: " + xhr.responseJSON.error);
                } else {
                    alert("An unknown error occurred. Please try again.");
                }
            }            
        });
    });

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }    

    $('.view-btn').on('click', function () {
        let absence_id = $(this).data('id');

        // Reset modal content
        $('#viewLoadingSpinner').removeClass('d-none');
        $('#justificationDetails').addClass('d-none');
        $('#justificationMessage').text('');
        $('#justificationStatus').text('').removeClass('bg-success bg-warning bg-danger');
        $('#justificationFiles').html('');

        // AJAX call to fetch justification data
        $.ajax({
            url: '/student/justifications/view/' + absence_id,
            type: 'GET',
            success: function (response) {
                // Status
                $('#justificationStatus').text(capitalize(response.status)).removeClass().addClass('badge');
                if (response.status === 'pending') $('#justificationStatus').addClass('bg-warning text-dark');
                if (response.status === 'approved') $('#justificationStatus').addClass('bg-success');
                if (response.status === 'refused') $('#justificationStatus').addClass('bg-danger');

                // Teacher decision
                let teacherLabel = 'Not Reviewed';
                let teacherClass = 'bg-secondary';

                if (response.teacher_desition === '1') {
                    teacherLabel = 'Approved';
                    teacherClass = 'bg-success';
                } else if (response.teacher_desition === '0') {
                    teacherLabel = 'Rejected';
                    teacherClass = 'bg-danger';
                } else if (response.teacher_desition === '2') {
                    teacherLabel = 'Under Review';
                    teacherClass = 'bg-warning text-dark';
                }
                $('#teacherDecision').text(teacherLabel).removeClass().addClass('badge ' + teacherClass);

                // Admin decision
                let adminLabel = 'Not Reviewed';
                let adminClass = 'bg-secondary';

                if (response.admin_decision === '1') {
                    adminLabel = 'Approved';
                    adminClass = 'bg-success';
                } else if (response.admin_decision === '0') {
                    adminLabel = 'Rejected';
                    adminClass = 'bg-danger';
                } else if (response.admin_decision === '2') {
                    adminLabel = 'Under Review';
                    adminClass = 'bg-warning text-dark';
                }
                $('#adminDecision').text(adminLabel).removeClass().addClass('badge ' + adminClass);


                // Message
                $('#justificationMessage').text(response.message || '—');

                // Files
                if (response.files && response.files.length > 0) {
                    response.files.forEach(file => {
                        $('#justificationFiles').append(
                            `<a href="/storage/${file}" target="_blank" class="d-block mb-1">📎 ${file.split('/').pop()}</a>`
                        );
                    });
                } else {
                    $('#justificationFiles').html('<em>No files uploaded.</em>');
                }

                $('#viewLoadingSpinner').addClass('d-none');
                $('#justificationDetails').removeClass('d-none');

            },
            error: function () {
                $('#viewLoadingSpinner').addClass('d-none');
                alert('Failed to load justification details. Please try again.');
            }
        });
    });

    function isHexColor(str) {
        return /^#([0-9A-F]{3}){1,2}$/i.test(str);
    }

    function hexToRgba(hex, opacity) {
        let r = 0, g = 0, b = 0;
        hex = hex.replace('#', '');

        if (hex.length === 3) {
            r = parseInt(hex[0] + hex[0], 16);
            g = parseInt(hex[1] + hex[1], 16);
            b = parseInt(hex[2] + hex[2], 16);
        } else if (hex.length === 6) {
            r = parseInt(hex.substring(0, 2), 16);
            g = parseInt(hex.substring(2, 4), 16);
            b = parseInt(hex.substring(4, 6), 16);
        }

        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    $('.schedule-cell').each(function () {
        const color = $(this).data('color');

        if (color) {
            let background;

            if (isHexColor(color)) {
                background = hexToRgba(color, 0.3); // 30% opacity
            } else {
                background = color; // fallback to plain named color
            }

            $(this).css('background-color', background);
        }
    });
});