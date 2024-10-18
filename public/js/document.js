$(document).ready(function() {
  const url = window.location.pathname;
  $(hcoreBtnSaveId).on('click', function() {
    showLoading();
    post();
  });

  function post()
  {
    var data = {};
    var constructData;

    var construct = {
      'input': hcoreInputCls,
      'textarea': hcoreTextareaCls,
      'wysiwyg': hcoreWysiwygCls,
      // 'password': hcorePasswordCls,
      'number': hcoreNumberCls,
      'select': hcoreSelectCls,
      'multiselect': hcoreMultiSelectCls,
      'checkbox': hcoreCheckboxCls,
      'date': hcoreDateCls,
      'datetime': hcoreDatetimeCls,
      'dateRange': hcoreDaterangeCls,
      'image': hcoreImageCls,
      'imageGallery': hcoreImageGalleryCls,
      'video': hcoreVideoCls,
      'manyToOneRelation': hcoreRelationCls,
      'manyToManyObjectRelation': hcoreRelationsCls
    };
    $.each(construct, function(type, cls) {
      constructData = {};
      $(cls).each(function () {
        var name = $(this).attr('name');
        if (name) {
          // is array field
          if (name.indexOf('[]') != -1) {
            var mainName = name.replace("[]", "");

            // not push to constructData
            if (!constructData.hasOwnProperty(mainName)) {
              var mainValue = [];
              $(cls + '[name="'+name+'"]').each(function() {
                mainValue.push($(this).val());
              });

              constructData[mainName] = mainValue;
            }
          } else {
            if (type == 'wysiwyg') {
              var editor = hcoreWysiwygContent[name];
              constructData[name] = editor == undefined ? '' : editor.getData();
            } else if (type == 'checkbox') {
              constructData[name] = $(this).is(':checked');
            } else {
              constructData[name] = $(this).val();
            }
          }
        } 
      });
      data[type] = constructData;
    });

    console.log(data);

    var response = callAjax(url, 'POST', {'data': JSON.stringify(data)});
  }
})
