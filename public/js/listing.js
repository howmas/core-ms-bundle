$(document).ready(function() {
  var createUrl = $('#addNewModal').data('url');

  // ------------------------------------------------

  var newName = null;
  $('#listing-add-new-input').on('keyup', function() {
    newName = $(this).val();
    $('#listing-add-new').prop('disabled', !newName);
  });

  $('#listing-add-new').on('click', function() {
    showLoading();

    var data = {
      type: $('#listing-add-new-type').val(),
      key: newName
    };

    callAjax(
      createUrl,
      "POST",
      data,
      function (response) {
        showLoading();
        toastSuccess();
        location.href = response.url;
      },
      function (response) {
        console.log(response);
        toastError(response.responseJSON.error);
      },
    );
  });

  // ------------------------------------------------
});
