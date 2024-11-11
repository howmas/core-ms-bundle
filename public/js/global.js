$(document).ready(function() {
  hideLoading();

  // click to a class then show loading
  $('body').delegate(hcoreLoadingCls, 'click', function() {
    showLoading();
  });

  // logout
  $('#logout').click(function() {
    messageShow("Bạn chắc chắn muốn <b class='text-danger'>Đăng xuất</b> khỏi hệ thống?", function() {
      showLoading();
      $('#logout_form').submit();
    });
  });
});

function callAjax(url, method, data, succesFunc = null, errorFunc = null) {
  if (method !== "GET") {
    if (data instanceof FormData) {
      data.append('csrfToken', hcoreUserToken);
    } else {
      data.csrfToken = hcoreUserToken;
    }
    startCallAjax(url, method, data, succesFunc, errorFunc);
  } else {
    startCallAjax(url, method, data, succesFunc, errorFunc);
  }
}

function startCallAjax(url, method, data, succesFunc = null, errorFunc = null) {
  $.ajax({
    url: url,
    method: method,
    data: data,
    // cache: false,
    contentType: data instanceof FormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
    processData: !(data instanceof FormData),
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
      if ($('#njLoading').is(":visible")) {
        hideLoading();
      }
    }
  });
}

function callGet(url, dataBlock) {
  $.get(url, function (data, textStatus, jqXHR) {
    $(dataBlock).html(data);
  }, 'html');
}

// loading
function showLoading() {
  $('#njLoading').show();
}

function hideLoading() {
  $('#njLoading').hide();
}

// toast alert
let toastProgressRun;
const toastStep = 10;

function toastSuccess(message = 'Thành công!', delay = 1000)
{
  toastShow(message, 'text-success', delay);
}

function toastError(message, delay = 3000)
{
  toastShow(message, 'text-danger', delay);
}

function toastShow(message, messageClass = 'text-success', delay = 3000)
{
  $('#toast').css({'z-index': 9999});

  var processLength = 100;
  var progessMinusPercent = (toastStep / delay) * 100;
  processLength -= progessMinusPercent;
  $('#toast-progess').css({'width': processLength + '%'});

  toastProgressRun = setInterval(function() {
    processLength -= progessMinusPercent;

    if (processLength < 0) {
      $('#toast').css({'z-index': 0});
      clearInterval(toastProgressRun);
    }

    $('#toast-progess').css({'width': processLength + '%'});
  }, toastStep);

  $("#toast-message").removeClass();
  $("#toast-message").addClass(messageClass);
  $('#toast-message').html(message);

  $('#toast').toast({
    delay: delay
  });

  $('#toast').toast('show');
}

// modal message
let messageContinueFunction = null;

function messageShow(message, continueFunc)
{
  messageContinueFunction = continueFunc;
  $('#modal-message-content').html(message);
  $('#modalMessageModal').modal('show');
}

// run function while click continue button in message modal
$('body').delegate('#modal-message-action', 'click', function() {
  if (messageContinueFunction) {
    // hide modal
    $('#modalMessageModal').modal('hide');

    // reset to run function only one time
    var continueFunc = messageContinueFunction;
    messageContinueFunction = null;

    continueFunc();
  }
});
