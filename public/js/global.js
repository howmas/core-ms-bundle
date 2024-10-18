function callAjax(url, method, data, succesFunc = null, errorFunc = null) {
  $.ajax({
    url: url,
    method: method,
    data: data,
    // cache: false,
    // contentType: false,
    // processData: false,
    success: function (response) {
      if (typeof succesFunc === 'function') {
        succesFunc(response);
      } else {
        return response;
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      if (typeof errorFunc === 'function') {
        errorFunc(jqXHR);
      } else {
        return null;
      }
    },
    complete: function(response) {
      hideLoading();
    }
  });
}

function callGet(url, dataBlock) {
  $.get(url, function (data, textStatus, jqXHR) {
    $(dataBlock).html(data);
  }, 'html');
}

function showLoading() {
  $('#njLoading').show();
}

function hideLoading() {
  $('#njLoading').hide();
}
