
$(document).ready(function() {
   
        
    // Editor page
    $('.textarea_editor').trumbowyg({
        autogrow: true
    });

     
     
        $('input[type="file"]').change(function(event) {
        let input = $(this); // Get current file input
        let file = event.target.files[0]; // Get selected file
        
        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                // Find closest PreviewContainer related to the current file input
                let previewContainer = input.siblings('#PreviewContainer');
                
                if (previewContainer.length) {
                    previewContainer.removeClass('hidden'); // Show container if hidden
                    previewContainer.find('.Preview').attr('src', e.target.result); // Update preview image
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Close button functionality to remove image preview
    $('.CloseIcon').click(function() {
        let previewContainer = $(this).closest('#PreviewContainer');
        previewContainer.find('.Preview').attr('src', ''); // Clear image source
        previewContainer.addClass('hidden'); // Hide preview container
        previewContainer.siblings('input[type="file"]').val(''); // Reset file input
    });
  
  
    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        let url = $(this).attr('action');
        let search = $(this).find('input[name="search"]').val();

     
        if (search !== '') {
    
            $('.close-icon').show();
        }

        let fullUrl = url + '?search=' + encodeURIComponent(search);
        loadTable(fullUrl);

    });

      // Reset Search icon click hone par search remove kare
      $('.close-icon').on('click', function () {
        $('#searchForm input[name="search"]').val('');
        $(this).hide();
        loadTable($('#searchForm').attr('action'));
    });

    // Intercept clicks on pagination links
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        loadTable(url);
    });

    function loadTable(url) {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json', // expecting JSON response with the full view HTML
            beforeSend: function () {
                // Optionally, show a loading message or spinner
                $('#table-container table tbody').html(`
                    <tr style="height: 190px">
                        <td style="position: absolute; height: 190px; width: 100%;">
                            <div class="loader animate-spin border-[3px] border-current border-t-transparent text-primary rounded-full" role="status" aria-label="loading">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </td> 
                    </tr>
                `);
            },
            
            success: function (response) {
                // Since the full view is returned, extract only the table container's HTML.
                var newContent = $(response.html).find('#table-container').html();
                $('#table-container').html(newContent);
                
                // Update pagination container dynamically
                var newPagination = $(response.html).find('#pagination-container').html();
                $('#pagination-container').html(newPagination);
            },
            error: function (xhr, status, error) {
                
                console.error('Error loading table:', error);
            }
        });
    }

    // Handle pagination click event dynamically
    $(document).on("click", "#pagination-container a", function (e) {
        e.preventDefault();
        let url = $(this).attr("href");
        if (url) {
            loadTable(url); // Fetch data using AJAX
        }
    });

    // Handle search form submit
    $("#search-form").on("submit", function (e) {
        e.preventDefault();
        let url = $(this).attr("action") + "?" + $(this).serialize();
        loadTable(url); // Fetch filtered results using AJAX
    });
    
    
   /* ai tool  validation */ 
//   $("#aiToolForm").validate({
//         rules: {
//             name: {
//                 required: true,
//                 minlength: 3
//             },
//             slug: {
//                 required: true,
//                 minlength: 3
//             },
//             "category[]": {
//                 required: true
//             },
//             tagline: {
//                 required: true,
//                 minlength: 5
//             },
//             short_description_heading: {
//                 required: true
//             },
//             short_description: {
//                 required: true,
//                 minlength: 10
//             },
//             verified_status: {
//                 required: true
//             },
//             payment_text: {
//                 required: true
//             },
//             website_link: {
//                 required: true,
//                 url: true
//             },
//             description_heading: {
//                 required: true
//             },
//             description: {
//                 required: true,
//                 minlength: 15
//             },
//             key_features: {
//                 required: true
//             },
//             pros: {
//                 required: true
//             },
//             cons: {
//                 required: true
//             },
//             long_description: {
//                 required: true,
//                 minlength: 20
//             },
//             aitool_filter: {
//                 required: true
//             },
//             logo: {
//                 required: function() {
//                     return $("input[name=logo]").get(0).files.length === 0;
//                 },
//                 extension: "jpg|jpeg|png|gif"
//             },
//             cover: {
//                 required: function() {
//                     return $("input[name=cover]").get(0).files.length === 0;
//                 },
//                 extension: "jpg|jpeg|png|gif"
//             }
//         },
//         messages: {
//             name: {
//                 required: "Please enter the tool name",
//                 minlength: "Tool name must be at least 3 characters long"
//             },
//             slug: {
//                 required: "Please enter a slug",
//                 minlength: "Slug must be at least 3 characters long"
//             },
//             "category[]": {
//                 required: "Please select at least one category"
//             },
//             tagline: {
//                 required: "Please enter a tagline",
//                 minlength: "Tagline must be at least 5 characters long"
//             },
//             short_description_heading: {
//                 required: "Please enter a short description heading"
//             },
//             short_description: {
//                 required: "Please enter a short description",
//                 minlength: "Short description must be at least 10 characters long"
//             },
//             verified_status: {
//                 required: "Please select verified status"
//             },
//             payment_text: {
//                 required: "Please enter payment text"
//             },
//             website_link: {
//                 required: "Please enter website link",
//                 url: "Please enter a valid URL"
//             },
//             description_heading: {
//                 required: "Please enter description heading"
//             },
//             description: {
//                 required: "Please enter description",
//                 minlength: "Description must be at least 15 characters long"
//             },
//             key_features: {
//                 required: "Please enter key features"
//             },
//             pros: {
//                 required: "Please enter pros"
//             },
//             cons: {
//                 required: "Please enter cons"
//             },
//             long_description: {
//                 required: "Please enter a long description",
//                 minlength: "Long description must be at least 20 characters long"
//             },
//             aitool_filter: {
//                 required: "Please enter AI tool filter"
//             },
//             logo: {
//                 required: "Please upload a logo",
//                 extension: "Only image files (jpg, jpeg, png, gif) are allowed"
//             },
//             cover: {
//                 required: "Please upload a cover image",
//                 extension: "Only image files (jpg, jpeg, png, gif) are allowed"
//             }
//         },
//         errorPlacement: function (error, element) {
//             error.addClass("text-red-600 text-sm");
//             error.insertAfter(element);
//         },
//         submitHandler: function (form) {
//             form.submit();
//         }
//     });
    
    
    /* ai tool Category validation */
    //   $("#aiToolCategory").validate({
    //     rules: {
    //         name: {
    //             required: true,
    //             minlength: 3
    //         },
    //         slug: {
    //             required: true,
    //             minlength: "Slug must be at least 3 characters long"
    //         },
    //         description: {
    //                 required: true,
    //                 minlength: 10
    //         },
    //         icon: {
    //             required: function() {
    //                 return $("input[name=icon]").get(0).files.length === 0;
    //             },
    //             extension: "jpg|jpeg|png|gif"
    //         },
    //         parent_category_id: {
    //             required: true
    //         }
            
    //     },
    //     messages: {
    //         name: {
    //             required: "Please enter the category name",
    //             minlength: "Tool name must be at least 3 characters long"
    //         },
    //         slug: {
    //             required: "Please enter a slug",
    //             minlength: "Slug must be at least 3 characters long"
    //         },
    //          description: {
    //                 required: "Description is required",
    //                 minlength: "Description must be at least 10 characters long"
    //         },
    //         icon: {
    //             required: "Please upload a icon",
    //             extension: "Only image files (jpg, jpeg, png, gif) are allowed"
    //         },
    //         parent_category_id: {
    //             required: "Please select a parent category"
    //         }
    //     },
    //     errorPlacement: function (error, element) {
    //         error.addClass("text-red-600 text-sm");
    //         error.insertAfter(element);
    //     },
    //     submitHandler: function (form) {
    //         form.submit();
    //     }
    // });
    
    /* Blog Category validation */
    //  $("#blogCategory").validate({
    //     rules: {
    //         name: {
    //             required: true,
    //             minlength: 3
    //         },
    //         slug: {
    //             required: true,
    //             minlength: "Slug must be at least 3 characters long"
    //         },
    //         description: {
    //                 required: true,
    //                 minlength: 10
    //         },
    //         icon: {
    //             required: function() {
    //                 return $("input[name=icon]").get(0).files.length === 0;
    //             },
    //             extension: "jpg|jpeg|png|gif"
    //         }
            
    //     },
    //     messages: {
    //         name: {
    //             required: "Please enter the category name",
    //             minlength: "Tool name must be at least 3 characters long"
    //         },
    //         slug: {
    //             required: "Please enter a slug",
    //             minlength: "Slug must be at least 3 characters long"
    //         },
    //          description: {
    //                 required: "Description is required",
    //                 minlength: "Description must be at least 10 characters long"
    //         },
    //         icon: {
    //             required: "Please upload a icon",
    //             extension: "Only image files (jpg, jpeg, png, gif) are allowed"
    //         }
    //     },
    //     errorPlacement: function (error, element) {
    //         error.addClass("text-red-600 text-sm");
    //         error.insertAfter(element);
    //     },
    //     submitHandler: function (form) {
    //         form.submit();
    //     }
    // });
    
    /* Course Category validation */
    // $("#courseCategory").validate({
    //     rules: {
    //         name: {
    //             required: true,
    //             minlength: 3
    //         },
    //         slug: {
    //             required: true,
    //             minlength: "Slug must be at least 3 characters long"
    //         },
    //         description: {
    //                 required: true,
    //                 minlength: 10
    //         },
    //         icon: {
    //             required: function() {
    //                 return $("input[name=icon]").get(0).files.length === 0;
    //             },
    //             extension: "jpg|jpeg|png|gif"
    //         }
            
    //     },
    //     messages: {
    //         name: {
    //             required: "Please enter the category name",
    //             minlength: "Tool name must be at least 3 characters long"
    //         },
    //         slug: {
    //             required: "Please enter a slug",
    //             minlength: "Slug must be at least 3 characters long"
    //         },
    //          description: {
    //                 required: "Description is required",
    //                 minlength: "Description must be at least 10 characters long"
    //         },
    //         icon: {
    //             required: "Please upload a icon",
    //             extension: "Only image files (jpg, jpeg, png, gif) are allowed"
    //         }
    //     },
    //     errorPlacement: function (error, element) {
    //         error.addClass("text-red-600 text-sm");
    //         error.insertAfter(element);
    //     },
    //     submitHandler: function (form) {
    //         form.submit();
    //     }
    // });
    
    
    /* Courses validation */
    //  $("#coursesForm").validate({
    //         rules: {
    //             name: {
    //                 required: true,
    //                 minlength: 3
    //             },
    //             slug: {
    //                 required: true,
    //                 minlength: 3
    //             },
    //             "categoryCourses[]": {
    //                 required: true,
    //                 minlength: 1
    //             },
    //             description: {
    //                 required: true,
    //                 minlength: 10
    //             },
    //             affiliate_link: {
    //                 url: true
    //             },
    //             logo: {
    //                 required: function() {
    //                     return $("input[name=logo]").get(0).files.length === 0;
    //                 },
    //                 extension: "jpg|jpeg|png|gif"
    //             },
    //             cover: {
    //                 required: function() {
    //                     return $("input[name=cover]").get(0).files.length === 0;
    //                 },
    //                 extension: "jpg|jpeg|png|gif"
    //             }
    //         },
    //         messages: {
    //             name: {
    //                 required: "Please enter the course name",
    //                 minlength: "Course name must be at least 3 characters long"
    //             },
    //             slug: {
    //                 required: "Please enter a slug",
    //                 minlength: "Slug must be at least 3 characters long"
    //             },
    //             "categoryCourses[]": {
    //                 required: "Please select at least one category"
    //             },
    //             description: {
    //                 required: "Please provide a description",
    //                 minlength: "Description must be at least 10 characters"
    //             },
    //             affiliate_link: {
    //                 url: "Please enter a valid URL"
    //             },
    //             logo: {
    //                 required: "Please upload a logo",
    //                 extension: "Only image files (jpg, jpeg, png, gif) are allowed"
    //             },
    //             cover: {
    //                 required: "Please upload a cover image",
    //                 extension: "Only image files (jpg, jpeg, png, gif) are allowed"
    //             }
    //         },
    //         errorPlacement: function (error, element) {
    //             error.addClass("text-red-600 text-sm");
    //             error.insertAfter(element);
    //         },
    //         submitHandler: function (form) {
    //             form.submit();
    //         }
    //     });
  
  /* blog validation */
//   $("#blogForm").validate({
//             rules: {
//                 name: {
//                     required: true,
//                     minlength: 3
//                 },
//                 slug: {
//                     required: true,
//                     minlength: 3
//                 },
//                 content: {
//                     required: true,
//                     minlength: 10
//                 },
//                 "category[]": {
//                     required: true,
//                     minlength: 1
//                 },
//                 reading_time: {
//                     required: true,
//                     digits: true
//                 },
//                 featured_image: {
//                     required: function() {
//                         return $("input[name=featured_image]").get(0).files.length === 0;
//                     },
//                     extension: "jpg|jpeg|png|gif"
//                 },
//                 left_image: {
//                     required: function() {
//                         return $("input[name=left_image]").get(0).files.length === 0;
//                     },
//                     extension: "jpg|jpeg|png|gif"
//                 },
//                 middle_image: {
//                     required: function() {
//                         return $("input[name=middle_image]").get(0).files.length === 0;
//                     },
//                     extension: "jpg|jpeg|png|gif"
//                 },
//                 sub_image: {
//                     required: function() {
//                         return $("input[name=sub_image]").get(0).files.length === 0;
//                     },
//                     extension: "jpg|jpeg|png|gif"
//                 },
//                 sub_title: {
//                     required: true,
//                     minlength: 3
//                 },
//                 sub_content: {
//                     required: true,
//                     minlength: 10
//                 }
//             },
//             messages: {
//                 name: {
//                     required: "Please enter the title",
//                     minlength: "Title must be at least 3 characters long"
//                 },
//                 slug: {
//                     required: "Please enter a slug",
//                     minlength: "Slug must be at least 3 characters long"
//                 },
//                 content: {
//                     required: "Please enter content for the blog",
//                     minlength: "Content must be at least 10 characters"
//                 },
//                 "category[]": {
//                     required: "Please select at least one category"
//                 },
//                 reading_time: {
//                     required: "Please enter the reading time",
//                     digits: "Please enter a valid number"
//                 },
//                 featured_image: {
//                     required: "Please upload a Featured Image",
//                     extension: "Only image files (jpg, jpeg, png, gif) are allowed"
//                 },
//                 left_image: {
//                     required: "Please upload a Left Image",
//                     extension: "Only image files (jpg, jpeg, png, gif) are allowed"
//                 },
//                 middle_image: {
//                     required: "Please upload a Middle Image",
//                     extension: "Only image files (jpg, jpeg, png, gif) are allowed"
//                 },
//                 sub_image: {
//                     required: "Please upload a Sub Image",
//                     extension: "Only image files (jpg, jpeg, png, gif) are allowed"
//                 },
//                 sub_title: {
//                     required: "Please enter a sub title",
//                     minlength: "Sub title must be at least 3 characters long"
//                 },
//                 sub_content: {
//                     required: "Please enter sub content",
//                     minlength: "Sub content must be at least 10 characters long"
//                 }
//             },
//             errorPlacement: function (error, element) {
//                 error.addClass("text-red-600 text-sm");
//                 error.insertAfter(element);
//               },
//             submitHandler: function (form) {
//                 form.submit();
//             },
//         });
  
  
  $(document).on("click", ".add_sub_btn", function () {
    let element = $(this);
    let element_type = element.data("field_type");

    if (element_type === "pros") {
        if (element.hasClass("add_field")) {
            let prosContainer = element.closest(".setting_fields").find(".pros_container").first();
            let prosCount = prosContainer.find('input[name="pros[title][]"]').length;

            let html = `
            <div class="flex items-center gap-4 mt-2">
                <input type="text" name="pros[title][]" id="pros_title_${prosCount}"
                    class="form-input"
                    value="">
                <textarea name="pros[content][]" id="pros_content_${prosCount}"
                    class="form-input"></textarea>
                <div class="col-1">
                    <a href="javascript:void(0)" class="add_sub_btn add_field" data-field_type="pros">
                         <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                            <svg x="11" y="11" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.7809 18.4199H7.86689V10.6354H0.332115V8.01279H7.86689V0.35313H10.7809V8.01279H18.3157V10.6354H10.7809V18.4199Z" fill="white"></path>
                            </svg>
                        </svg>
                    </a>
                    <a href="javascript:void(0)" class="add_sub_btn sub_field" data-field_type="pros">
                        <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                            <svg x="13.5" y="19" width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.957458 3.29962V0.552137H13.6126V3.29962H0.957458Z" fill="white"></path>
                            </svg>
                        </svg>
                    </a>
                </div>
            </div>`;

            prosContainer.append(html);
        } else if (element.hasClass("sub_field")) {
            element.closest(".flex").remove();
        }
    }
});

     $(document).on("click", ".add_sub_cons_btn", function () {
        let element = $(this);
        let element_type = element.data("field_type");

        if (element_type === "cons") {
            if (element.hasClass("add_cons_field")) {
                let consContainer = element.closest(".setting_fields").find(".cons_container").first();
                let consCount = consContainer.find('input[name="cons[title][]"]').length;

                let html = `
                <div class="flex items-center gap-4 mt-2">
                    <input type="text" name="cons[title][]" id="cons_title_${consCount}"
                        class="form-input"
                        value="">
                    <textarea name="cons[content][]" id="cons_content_${consCount}"
                        class="form-input"></textarea>
                    <div class="col-1">
                        <a href="javascript:void(0)" class="add_sub_cons_btn add_cons_field" data-field_type="cons">
                            <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                                <svg x="11" y="11" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.7809 18.4199H7.86689V10.6354H0.332115V8.01279H7.86689V0.35313H10.7809V8.01279H18.3157V10.6354H10.7809V18.4199Z" fill="white"></path>
                                </svg>
                            </svg>
                        </a>
                        <a href="javascript:void(0)" class="add_sub_cons_btn sub_cons_field" data-field_type="cons">
                            <svg width="25" height="25" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20.5 0C31.775 0 41 9.225 41 20.5C41 31.775 31.775 41 20.5 41C9.225 41 0 31.775 0 20.5C0 9.225 9.225 0 20.5 0Z" fill="#3fa872"></path>
                                <svg x="13.5" y="19" width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.957458 3.29962V0.552137H13.6126V3.29962H0.957458Z" fill="white"></path>
                                </svg>
                            </svg>
                        </a>
                    </div>
                </div>`;

                consContainer.append(html);
            } else if (element.hasClass("sub_cons_field")) {
                element.closest(".flex").remove();
            }
        }
    });
  
  
  
});