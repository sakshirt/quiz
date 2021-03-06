(function ($) {
  "use strict";

  var table;

  //datatables 
  table = $("#blogcategorytable").DataTable({
    language: {
      info: table_showing +
        " _START_ " +
        table_to +
        " _END_ " +
        table_of +
        " _TOTAL_ " +
        table_entries,
      sLengthMenu: table_show + " _MENU_ " + table_entries,
      sSearch: table_search,
      paginate: {
        previous: table_previous,
        next: table_next,
      },
    },

    processing: true, //Feature control the processing indicator.
    serverSide: true, //Feature control DataTables' server-side processing mode.
    order: [], //Initial no order.
    ajax: {
      url: BASE_URL + "admin/blog/blog_category_list",
      type: "POST",
    },

    //Set column definition initialisation properties.
    columnDefs: [{
      targets: [0], //first column / numbering column
      orderable: false, //set not orderable
    }, ],
  });

  table = $("#blogposttable").DataTable({
    language: {
      info: table_showing +
        " _START_ " +
        table_to +
        " _END_ " +
        table_of +
        " _TOTAL_ " +
        table_entries,
      sLengthMenu: table_show + " _MENU_ " + table_entries,
      sSearch: table_search,
      paginate: {
        previous: table_previous,
        next: table_next,
      },
    },

    processing: true, //Feature control the processing indicator.
    serverSide: true, //Feature control DataTables' server-side processing mode.
    order: [], //Initial no order.
    ajax: {
      url: BASE_URL + "admin/blog/blog_post_list",
      type: "POST",
    },

    //Set column definition initialisation properties.
    columnDefs: [{
      targets: [0], //first column / numbering column
      orderable: false, //set not orderable
    }, ],
  });  

  $("body").on("click", ".blog_cat_delete", function (e) {
    var link = $(this).attr("href");

    e.preventDefault(false);
    swal({
        title: are_you_sure,
        text: permanently_deleted,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: yes_delere_it,
      },
      function (isConfirm) {
        if (isConfirm == true) {
          window.location.href = link;
        }
      }
    );
  });

  //select2 tool jquery
  $(document).ready(function () {
    $(".select_dropdown").select2();
  });

  $(".popup").on("click", function () {
    $("#imagepreview").attr("src", $(this).attr("src"));
    $("#imagemodal").modal("show");
  });
  
})(jQuery);