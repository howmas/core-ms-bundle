$(document).ready(function() {
  var createUrl = $('#uploadGroupDropdown').data('url');
  var createParent = $('#uploadGroupDropdown').data('parent');

  // ------------------------------------------------

  var newFolderName = null;
  $('#create-new-folder-input').on('keyup', function() {
    newFolderName = $(this).val();
    $('#create-new-folder').prop('disabled', !newFolderName);
  });

  $('#create-new-folder').on('click', function() {
    showLoading();

    var data = {
      type: 'folder',
      name: newFolderName,
      parent: createParent
    };

    callAjax(
      createUrl,
      "POST",
      data,
      function (response) {
        $('#cardFolders').append(response);
        $('#create-new-folder-input').val('');
        $('#assetCreateNewFolder').modal('hide');
      },
      function (response) {
        showLoading();
        location.reload(true);
      },
    );
  });

  // ------------------------------------------------

  var newFiles = null;
  $('#create-new-from-file-input').on('change', function() {
    newFiles = $(this)[0].files;
    var total = $(newFiles).length;
    var previewFileNames = '';
    if (total > 0) {
      formFiles = {};

      $(newFiles).each(function() {
        if (previewFileNames) {
          previewFileNames += '<br>' + $(this)[0].name;
        } else {
          previewFileNames = $(this)[0].name;
        }
      });
    }

    $('#create-new-from-file-preview').html(previewFileNames);
    $('#create-new-from-file').prop('disabled', total == 0);
  });

  $('#create-new-from-file').on('click', function() {
    showLoading();

    var data = new FormData();
    data.append('parent', createParent);

    $(newFiles).each(function() {
      data.append('files[]', $(this)[0]);
    });

    callAjax(
      createUrl,
      "POST",
      data,
      function (response) {
        showLoading();
        location.reload(true);
      },
      function (response) {
        showLoading();
        location.reload(true);
      },
    );
  });

  // ------------------------------------------------

  var newFileUrl = null;
  $('#create-new-from-url-input').on('keyup', function() {
    newFileUrl = $(this).val();
    $('#create-new-from-url').prop('disabled', !newFileUrl);
  });

  $('#create-new-from-url').on('click', function() {
    showLoading();

    var data = {
      url: newFileUrl,
      parent: createParent
    };

    callAjax(
      createUrl,
      "POST",
      data,
      function (response) {
        showLoading();
        location.reload(true);
      },
      function (response) {
        showLoading();
        location.reload(true);
      },
    );
  });

  // ------------------------------------------------

  $('body').delegate('.asset-listing-delete', 'click', function() {
    var url = $(this).parent().data('url');
    var name = $(this).data('name');
    var message = `Bạn chắc chắn muốn xóa <b class="text-danger">${name}</b>?`;

    messageShow(message, function () {
      showLoading();
      callAjax(url, "DELETE", {}, function (response) {
        $('#cardAsset' + response.id).remove();
      });
    })
    
  });

  $('#reloadButton').on('click', function() {
    showLoading();
    location.reload(true);
  });
});
