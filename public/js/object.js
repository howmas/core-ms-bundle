$(document).ready(function() {
  const url = window.location.pathname;

  // delete
  $(hcoreBtnDeleteId).on('click', function() {
    showLoading();
    
    callAjax(
      url,
      "DELETE",
      {},
      function (response) {
        showLoading();
        toastSuccess();
        location.href = $(hcoreBtnDeleteId).data('url');
      },
      function (response) {
        showLoading();
        location.reload(true);
      },
    );
  });

  // change publish
  $(hcoreBtnChangePublishId).on('click', function() {
    showLoading();
    
    callAjax(
      url,
      "PUT",
      {},
      function (response) {
        showLoading();
        toastSuccess();
        location.reload(true);
      },
      function (response) {
        showLoading();
        location.reload(true);
      },
    );
  });

  // post data
  $(hcoreBtnSaveId).on('click', function() {
    showLoading();
    post();
  });

  function post()
  {
    var data = {};
    var constructData;

    var construct = {
      input: hcoreInputCls,
      textarea: hcoreTextareaCls,
      wysiwyg: hcoreWysiwygCls,
      // password: hcorePasswordCls,
      number: hcoreNumberCls,
      select: hcoreSelectCls,
      multiselect: hcoreMultiSelectCls,
      checkbox: hcoreCheckboxCls,
      date: hcoreDateCls,
      datetime: hcoreDatetimeCls,
      dateRange: hcoreDaterangeCls,
      image: hcoreImageCls,
      imageGallery: hcoreImageGalleryCls,
      video: hcoreVideoCls,
      manyToOneRelation: hcoreRelationCls,
      manyToManyObjectRelation: hcoreRelationsCls
    };
    $.each(construct, function(type, cls) {
      constructData = {};
      $(cls).each(function () {

        var name = $(this).attr('name');
        if (name) {
          // is array field
          if (name.indexOf('[]') != -1) {
            // is field-collection
            var fieldCollectionParent = $(this).closest(hcoreFieldCollectionCls);
            var isFieldCollection = fieldCollectionParent.length > 0;
            if (!isFieldCollection) {
              var mainName = name.replace("[]", "");

              // not push to constructData
              if (!constructData.hasOwnProperty(mainName)) {
                var mainValue = [];
                $(cls + '[name="'+name+'"]').each(function() {
                  mainValue.push($(this).val());
                });

                constructData[mainName] = mainValue;
              }
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

    // field-collection
    constructData = {};
    $.each(construct, function(type, cls) {
      var fieldClass = hcoreFieldCollectionCls + ' .field-collection-items ' + cls;
      $(fieldClass).each(function () {
        var fieldCollectionParent = $(this).closest(hcoreFieldCollectionCls);
        var fieldCollectionAllowType = $(fieldCollectionParent).eq(0).data('type');
        var name = $(this).attr('name');
        if (name) {
          if (name.indexOf('[][]') != -1) {
            var mainName = name.replace("[][]", "");
            var fieldAndName = mainName.split(hcoreFieldCollectionDelimiter);
            var fcField = fieldAndName[0];
            var fcName = fieldAndName[1];

            var mainValue = [];
            var eachFieldClass = hcoreFieldCollectionCls + '[data-type="'+fieldCollectionAllowType+'"]' + ' .field-collection-items';
            $(eachFieldClass).each(function() {
              var eachValue = [];
              $(this).find(cls + '[name="'+name+'"]').each(function() {
                if ($(this).closest('.empty-gallery-item').length == 0) {
                  var eachValueItem = $(this).val();

                  // if (type == 'wysiwyg') {
                  //   var editor = hcoreWysiwygContent[name];
                  //   eachValueItem = editor == undefined ? '' : editor.getData();
                  // } else if (type == 'checkbox') {
                  //   eachValueItem = $(this).is(':checked');
                  // }

                  eachValue.push(eachValueItem);
                }
              });
              mainValue.push(eachValue);
            });

            if (!constructData[fcField]?.[type]?.[fcName]) {
              constructData[fcField] = constructData[fcField] ?? {};
              constructData[fcField][type] = constructData[fcField][type] ?? {};
              constructData[fcField][type][fcName] = mainValue;

              constructData[fcField]['allowType'] = fieldCollectionAllowType;
            }
          } else if (name.indexOf('[]') != -1) {
            var mainName = name.replace("[]", "");
            var fieldAndName = mainName.split(hcoreFieldCollectionDelimiter);
            var fcField = fieldAndName[0];
            var fcName = fieldAndName[1];

            var mainValue = [];
            var itemCount = $(fieldClass + '[name="'+name+'"]').length;
            $(fieldClass + '[name="'+name+'"]').each(function() {
              var eachValueItem = $(this).val();

              if (type == 'wysiwyg') {
                var editor = hcoreWysiwygContent?.[mainName]?.[$(this).attr('id')];
                eachValueItem = editor == undefined || !editor ? '' : editor.getData();
              } else if (type == 'checkbox') {
                eachValueItem = $(this).is(':checked');
              }

              mainValue.push(eachValueItem);
            });

            if (!constructData[fcField]?.[type]?.[fcName]) {
              constructData[fcField] = constructData[fcField] ?? {};
              constructData[fcField][type] = constructData[fcField][type] ?? {};
              constructData[fcField][type][fcName] = mainValue;

              constructData[fcField]['allowType'] = fieldCollectionAllowType;
              constructData[fcField]['itemCount'] = itemCount;
            }
          }
        } 
      });
    });
    data['fieldCollection'] = constructData;
    console.log(data);

    var response = callAjax(
      url,
      'POST',
      {
        'data': JSON.stringify(data)
      },
      function (response) {
        toastSuccess();
      }
    );
  }
})
