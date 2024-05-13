jQuery(document).ready(function ($) {
  // Show search popup when trigger div is clicked
  $("#search-trigger").on("click", function () {
    $("#search-popup").fadeIn();
  });

  // Close search popup on close button click
  $(".close-btn").on("click", function () {
    $("#search-popup").fadeOut();
  });

  var ajaxurl = ajax_object.ajaxurl;
  // AJAX request to load post titles from "faq" post type
  $(".search-field").on("input", function () {
    var searchQuery = $(this).val();
    $.ajax({
      url: ajaxurl, // WordPress AJAX URL
      type: "POST",
      data: {
        action: "load_faq_post_titles",
        search_query: searchQuery,
      },
      success: function (response) {
        $("#search-results").html(response);
      },
    });
  });
});
