$(document).ready(function() {
  const url = window.location.pathname;
  var tree = [];

  $('#update-btn').click(function() {
    showLoading();
    storeTree();

    var response = callAjax(
      url,
      'POST',
      {
        'data': tree
      },
      function (response) {
        toastSuccess();
      }
    );
  });

  function storeTree()
  {
    $('.list-group-item').each(function() {
      var child = $(this).data('id');
      var parent = $(this).closest('.list-group').data('parent');
      tree.push({child: child, parent: parent});
    });
  }
});
