$('#importForm').on('submit', function(e) {
	e.preventDefault();

	// Reset messages
	$('#uploadSuccess').addClass('d-none');
	$('#uploadError').addClass('d-none');

	const formData = new FormData(this);

	// Show spinner
	$('#uploadSpinner').removeClass('d-none');

	$.ajax({
		url: importUrl,
		type: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		success: function(responseJSON) {
			setTimeout(() => {
				$('#uploadSpinner').addClass('d-none');
				$('#importForm')[0].reset();
                if (responseJSON.failed_import_file) {
                    $('#uploadWarning').removeClass('d-none');
                    // trigger download
                    window.location.href = '/storage/failed_imports/' + responseJSON.failed_import_file;
                }else {
                    $('#uploadSuccess').removeClass('d-none');
                }
			}, 1000);
		},
		error: function(xhr) {
            setTimeout(() => {
                $('#uploadSpinner').addClass('d-none');
        
                if (xhr.status === 422 && xhr.responseJSON.validation_errors) {
                    let messages = '<ul>';
        
                    xhr.responseJSON.validation_errors.forEach(function(error) {
                        messages += `<li>Row ${parseInt(error.row) - 1}: ${error.errors.join(', ')} <p>${error.attribute}: ${error.value}</p></li>`;
                    });
        
                    messages += '</ul>';
        
                    $('#uploadError').html('❌ Import failed due to validation errors:' + messages).removeClass('d-none');
                } else {
                    $('#uploadError').html('❌ Something went wrong. Please try again.').removeClass('d-none');
                }
            }, 1000);
        }
	});
});

$('#exportButton').on('click', function() {
    
    let filteredData = table.rows({ search: 'applied' }).data().toArray()
    const filteredEmails = filteredData.map(row => row[1]);
    //console.log(filteredEmails)
    const query = $.param({ q: filteredEmails });
    window.location.href = `${exportUrl}?${query}`;

});
$(document).ready(function() {
    // When promotion changes
    $('.promotion-select').change(function() {
        var promotion_id = $(this).val();
        if (promotion_id) {
            $.get('/admin/get_semesters/' + promotion_id, function(data) {
                $('.semester-select').empty().append('<option value="">Select Semester</option>');
                $('.section-select').empty().append('<option value="">Select Section</option>');
                $('.group-select').empty().append('<option value="">Select Group</option>');
                $.each(data, function(key, semester) {
                    $('.semester-select').append('<option value="' + semester.id + '">' + semester.name + '</option>');
                });
            });
        }
    });

    // When semester changes
    $('.semester-select').change(function() {
        var semester_id = $(this).val();
        var isModuleOpt = $('#module-options').is(':visible');
        if (semester_id) {
            $.get('/admin/get_sections/' + semester_id, function(data) {
                $('.section-select').empty().append('<option value="">Select Section</option>');
                $('.group-select').empty().append('<option value="">Select Group</option>');
                $.each(data, function(key, section) {
                    $('.section-select').append('<option value="' + section.id + '">' + section.name + '</option>');
                });
            });

            if(isModuleOpt) {
                $('#module-optins').empty();
                $.get('/admin/get_modules/' + semester_id, function (modules) {
                    let html = ``;
                    if(modules.length > 0) {
                        modules.forEach(module => {
                            html += `
                                <div class="form-check" style="padding-left: 1.25rem !important;">
                                    <input class="form-check-input" type="radio" name="module_id" id="module_${module.id}" value="${module.id}">
                                    <label class="form-check-label ml-0" for="module_${module.id}">
                                        ${module.name}
                                    </label>
                                </div>
                            `;
                        });

                        $('#module-options').html(html);
                        $('#module-column').slideDown();
                    } else {
                        $('#module-options').html('<p class="text-muted">No modules found for this semester.</p>');
                        $('#module-column').slideDown();
                    }
                });
            }
        } else {
            if(isModuleOpt) {
                $('#group-options').empty();
                $('#module-column').slideDown();
            }
        }
    });

    // When section changes
    $('.section-select').change(function() {
        var section_id = $(this).val();
        var isModuleOpt = $('#group-options').is(':visible');
        if (section_id) {
            $.get('/admin/get_groups/' + section_id, function(data) {
                $('.group-select').empty().append('<option value="">Select Group</option>');
                $.each(data, function(key, group) {
                    $('.group-select').append('<option value="' + group.id + '">' + group.name + '</option>');
                });

                if(isModuleOpt) {
                    $('#group-options').empty();
                    let html = ``;
                    if(data.length > 0) {
                        $.each(data, function(key, group) {
                            html += `
                                <div class="form-check" style="padding-left: 1.25rem !important;">
                                    <input class="form-check-input" type="checkbox" name="group_id" value="${group.id}" id="group_${group.id}">
                                    <label class="form-check-label ml-0" for="group_${group.id}">
                                        ${group.name}
                                    </label>
                                </div>
                            `;
                        });
                        $('#group-options').html(html);
                        $('#group-column').slideDown();
                    }else {
                        $('#group-options').html('<p class="text-muted">No modules found for this semester.</p>');
                        $('#group-column').slideDown();
                    }
                }
            });

        } else {
            if(isModuleOpt) {
                $('#module-options').empty();
                $('#group-column').slideDown();
            }
        }
    });
});

function updateBreadcrumb(label, value) {
    $('.breadcrumb-item').each(function() {
        const currentLabel = $(this).find('strong').text().trim();
        if (currentLabel.startsWith(label + ':')) {
            const newHtml = `<strong>${label}:</strong> ${value}`;
            $(this).html(newHtml);
        }
    });
}

function resetBreadcrumb(label) {
    $('.breadcrumb-item').each(function() {
        const currentLabel = $(this).find('strong').text().trim();
        if (currentLabel.startsWith(label + ':')) {
            const resetHtml = `<strong>${label}:</strong>`;
            $(this).html(resetHtml);
        }
    });
}

function switchTab(tabId) {
    $(`#${tabId}-tab`).trigger('click');
}

function updateSemesterListGroup(id) {
    $('.structureListGroupItem').each(function() {
        const promotionId = $(this).data('promotion');
        
        if(promotionId != id) {
            $(this).addClass('d-none');
        }else {
            $(this).addClass('d-block');
        }
    });
}

function updateSectionListGroup(id) {
    $('.structureListGroupItem').each(function() {
        const semesterId = $(this).data('semester');
        
        if(semesterId != id) {
            $(this).addClass('d-none');
        }else {
            $(this).addClass('d-block');
        }
    });
}

function updateGroupListGroup(id) {
    $('.structureListGroupItem').each(function() {
        const sectionId = $(this).data('section');
        
        if(sectionId != id) {
            $(this).addClass('d-none');
        }else {
            $(this).addClass('d-block');
        }
    });
}

function resetListGroupVisibility() {
    $('.structureListGroupItem').each(function() {
        $(this).removeClass('d-none').addClass('d-block');
    });
}

function updateSelectInput(label, id, name, targetTabId = null) {
    let selector = `.${label}-select`;

    if (targetTabId) {
        selector = `#${targetTabId} ${selector}`;
    }

    $(selector).val(id).trigger('change');
}


function resetSelectInput(label) {
    $(`.${label}-select`).val('').trigger('change');
}

$(document).ready(function() {
    const isCurrentTab = sessionStorage.getItem('currentTab');
    if(isCurrentTab) {
        switchTab(isCurrentTab);
    }

    $('.select-promotion-btn').on('click', function(e) {
        e.preventDefault();

        const promoId = $(this).data('id');
        const promoName = $(this).data('name');

        //console.log(promoId, promoName);
        updateBreadcrumb('Promotion', promoName);
        resetBreadcrumb('Semester');
        resetBreadcrumb('Section');
        resetBreadcrumb('Group');
        switchTab('semesters');
        updateSemesterListGroup(promoId);
        updateSelectInput('promotion', promoId, promoName);
    });
    
    $('.select-semester-btn').on('click', function(e) {
        e.preventDefault()

        const semesterId = $(this).data('id');
        const semesterName = $(this).data('name');
        const promotion_name = $(this).data('promotion');

        updateBreadcrumb('Semester', semesterName);
        updateBreadcrumb('Promotion', promotion_name);
        resetBreadcrumb('Section');
        resetBreadcrumb('Group');
        switchTab('sections');
        updateSectionListGroup(semesterId);
        updateSelectInput('semester', semesterId, semesterName);
    });

    $('.select-section-btn').on('click', function(e) {
        e.preventDefault()

        const sectionId = $(this).data('id');
        const sectionName = $(this).data('name');
        const semester_name = $(this).data('semester');
        const promotion_name = $(this).data('promotion');

        updateBreadcrumb('Section', sectionName);
        updateBreadcrumb('Semester', semester_name);
        updateBreadcrumb('Promotion', promotion_name);
        resetBreadcrumb('Group');
        switchTab('groups');
        updateGroupListGroup(sectionId);
        updateSelectInput('section', sectionId, sectionName);
    });

    $('.select-group-btn').on('click', function(e) {
        e.preventDefault()

        const groupId = $(this).data('id');
        const groupName = $(this).data('name');
        const section_name = $(this).data('section');
        const semester_name = $(this).data('semester');
        const promotion_name = $(this).data('promotion');
        const promoId = $(this).data('promotion-id');
        const semester_id = $(this).data('semester-id');
        console.log(semester_id, promoId)

        updateBreadcrumb('Group', groupName);
        updateBreadcrumb('Section', section_name);
        updateBreadcrumb('Semester', semester_name);
        updateBreadcrumb('Promotion', promotion_name);
        resetListGroupVisibility();
        switchTab('modules');
        
        //updateGroupListGroup(groupId);
    });

    resetListGroupVisibility();

    $('.ac-tab').on('click', function() {
        
        const currentTab = $(this).data('tabname');
        sessionStorage.setItem('currentTab', currentTab);
    })

    // function filterModules() {
    //     const selectedPromotion = $('#promotion').val();
    //     const selectedSemester = $('#semester').val();

    //     $('.moduleListGroup').each(function () {
    //         const promoId = $(this).data('promotion-id');
    //         const semesterId = $(this).data('semester-id');

    //         if (
    //             (!selectedPromotion || promoId == selectedPromotion) &&
    //             (!selectedSemester || semesterId == selectedSemester)
    //         ) {
    //             $(this).removeClass('d-none').addClass('d-block');
    //         } else {
    //             $(this).removeClass('d-block').addClass('d-none');
    //         }
    //     });
    // }

    // // Trigger filter when either dropdown changes
    // $('#promotion, #semester').on('change', filterModules);

});

let currentPage = 1;
let typingTimer;
const debounceDelay = 300; // ms

function fetchFilteredClasses(reset = true) {
    if (reset) {
        $('#resultsContainer').empty();
        currentPage = 1;
    }

    $('#loadingSpinner').show();
    $('#loadMoreBtn').addClass('d-none');

    $.ajax({
        url: `/admin/classes/search?page=${currentPage}`,
        method: 'GET',
        data: {
            query: $('#searchBar').val(),
            promotion_id: $('#promotion').val(),
            semester_id: $('#semester').val(),
            section_id: $('#section').val(),
            group_id: $('#group').val(),
        },
        success: function(data) {
            //console.log('Result: ', data)
            $('#loadingSpinner').hide();
            $('#resultsContainer').html(data.html);

            if (data.hasMore) {
                $('#loadMoreBtn').removeClass('d-none');
            }
        },
        error: () => alert("Error loading classes")
    });
}

function setupclasseSessionModal(classeId) {
    $.ajax({
        url: `/admin/classes/classe_sessions`,
        method: 'GET',
        data: {
            classe_id: classeId,

        },
        success: function(data) {
            $('.template-form').html(data.template);
            $('.sessions-table').html(data.table);
            $('#classeSessionModalSpinner').addClass('d-none');
        },
        error: () => alert("Error loading classe information")
    });
}

$(document).ready(function() {

    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    // Debounced search input
    $('#searchBar').on('input', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => fetchFilteredClasses(true), debounceDelay);
    });

    // Filters change
    $('#promotion, #semester, #section, #group')
        .on('change select2:select', () => fetchFilteredClasses(true));

    $('#loadMoreBtn').on('click', () => {
        currentPage++;
        fetchFilteredClasses(false);
    });

    fetchFilteredClasses(); // initial load

    $(document).on('click', '.classe-card', function() {
        let title = $(this).find('h5.fw-semibold').text().trim();
        let cleanedTitle = title.replace(/^[\p{Emoji_Presentation}\p{Extended_Pictographic}\p{So}]+/gu, '').trim();

        let classeId = $(this).data('id');

        $('#classeSessionModalLabel').html('📅 '+cleanedTitle);
        $('#classeSessionModal').modal('show');
        setupclasseSessionModal(classeId);
    });
    
});

$(document).ready(function (){
    function showToast(type, message, delay = 3000) {
        // build the toast HTML
        const toastId = `toast-${Date.now()}`;
        const $toast = $(`
            <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                ${message}
                </div>
            </div>
            </div>
        `);

        // append to container
        $('#toast-container').append($toast);

        // initialize and show
        const toast = new bootstrap.Toast(document.getElementById(toastId), { delay });
        toast.show();

        // remove from DOM when hidden
        document.getElementById(toastId)
            .addEventListener('hidden.bs.toast', () => $toast.remove());
    }

    $('#groupChangeToggle').on('change', function() {
        let isChecked = $(this).is(':checked');
        let token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/admin/toggle_group_change',
            method: 'POST',
            data: {
                '_token': token,
                'group_change_open': isChecked ? 1 : 0
            },
            success: function (responseJSON) {
                if(responseJSON.success) {
                    showToast('success', '✅ Group change request status updated successfully!');
                }else {
                    showToast('danger', '⚠️ Failed to update status.');
                }
            },
        });
    });
});