$(document).ready(function() {
  var fieldCollectIndex = [];

  // check total items of each fieldCollection after render
  $(hcoreFieldCollectionCls).each(function() {
    var namePrefix = $(this).data('name');
    if (!fieldCollectIndex.hasOwnProperty(namePrefix)) {
      fieldCollectIndex[namePrefix] = $(hcoreFieldCollectionCls + '[data-name="' + namePrefix + '"] .field-collection-items').length;
    }
  });

  // hide button clear option (x) in select div if disabled
  $("select.js-select2-custom.custom-select[disabled]").each(function() {
    var clearButton = $(this).parent().find('.select2-selection__clear').first();
    if (clearButton) {
      clearButton.remove();
    }
  });

  $('body').delegate(hcoreFieldCollectionCls + ' .remove-button', 'click', function() {
    var fieldItem = $(this).closest('.field-collection-items');
    $(fieldItem).remove();
  });

  $('body').delegate(hcoreFieldCollectionCls + ' .add-button', 'click', function() {
    var namePrefix = $(this).data('collapse');

    // increase to get new index
    var currentIndex = fieldCollectIndex[namePrefix];
    var nextIndex = currentIndex + 1;
    fieldCollectIndex[namePrefix] = nextIndex;

    var noneItem = $(this).parent().prev();
    var noneContent = $(noneItem).first().html().replaceAll(hcoreReplaceIndex, nextIndex);

    var addContent = '<div class="field-collection-items">' + noneContent + '</div>';

    $(addContent).insertBefore(noneItem);

    var fieldItems = $(noneItem).closest(hcoreFieldCollectionCls); // find parent
    var newAddItem = $(fieldItems).find('.field-collection-items').last();

    // select2 refresh
    $(newAddItem).find('.js-select2-custom').each(function() {
      var select2 = $.HSCore.components.HSSelect2.init($(this));
    });

    // init ckeditor
    $(newAddItem).find(hcoreWysiwygCls).each(function() {
      createWysiwyg(this);
    });
  });
})
