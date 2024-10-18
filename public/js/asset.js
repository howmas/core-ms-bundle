$(document).ready(function() {
  const assetOptionVar = 'data-asset-options';
  var assetPreview, assetFileInput;

  $('body').delegate(hCoreAssetListing + "[" + assetOptionVar + "]", 'click', function() {
    listing(this);
  });

  $('body').delegate(hCoreAssetOpenModal, 'click', function() {
    preUpdate(this);
  });

  // video type
  $('body').delegate(hCoreAssetVideoOpenModal, 'click', function() {
    preUpdate(this, 'iframe');
  });

  function preUpdate(currentTag, assetTag = 'img')
  {
    var parentCard = $(currentTag).closest('.card');
    assetPreview = $(parentCard).find(assetTag).first();
    assetFileInput = $(parentCard).find('input').first();

    // remove "." in class name, then check and open library modal
    if (
      $(currentTag).hasClass(hCoreAssetOpenModal.substring(1)) ||
      $(currentTag).hasClass(hCoreAssetVideoOpenModal.substring(1))
    ) {
      if (assetPreview.data('id') !== assetFileInput.data('id')) {
        
      }
      listing(currentTag);
      openAssetModal();
    }
  }

  $('body').delegate('#assetLibraryModal:not(.ckeditor) .asset-modal-select-button', 'click', function() {
    postUpdate();
    closeAssetModal();
  });

  // insert Youtube video
  $('body').delegate('.insert-youtube-input', 'keyup', function() {
    var modal = $(this).closest('.modal');
    var button = $(modal).find('.insert-youtube-button').first();
    var url = $(this).val();

    $(button).prop('disabled', !url);
  });

  $('body').delegate('.insert-youtube-button', 'click', function() {
    preUpdate(this, 'iframe');

    var modal = $(this).closest('.modal');
    var input = $(modal).find('.insert-youtube-input').first();
    var url = $(input).val();

    if (url) {
      var videoID = getYouTubeVideoID(url);
      // var videoImgUrl = `https://i.ytimg.com/vi/${videoID}/hqdefault.jpg`;
      var embedUrl = `https://www.youtube.com/embed/${videoID}`;

      if (assetPreview) {
        $(assetPreview).attr('src', embedUrl);
      }

      if (assetFileInput) {
        $(assetFileInput).val($(input).data('prefix') + videoID);
      }
    }
  });

  function getYouTubeVideoID(url) {
    var videoID = 'howmas';

    // Define a regular expression to match YouTube video URLs
    var regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;

    // Execute the regular expression on the URL
    var match = url.match(regex);

    // If a match is found, extract the video ID (first capture group)
    if (match && match[1]) {
        videoID = match[1];
    }

    return videoID;
  }
  // end: insert Youtube video

  function openAssetModal()
  {
    $('#assetLibraryModal').removeClass('ckeditor');
    $('#assetLibraryModal').modal('show');
  }

  function closeAssetModal()
  {
    $('#assetLibraryModal').modal('hide');
  }

  function listing(currentTag)
  {
    url = $('#assetLibraryModal').data('url');

    var assetOptions = $(currentTag).attr(assetOptionVar);
    
    if (assetOptions) {
      assetOptions = $.parseJSON(assetOptions);
      $.each(assetOptions, function(key, value) {
        url += `&${key}=${value}`;
      });
    }

    callGet(url, '#assetLibraryModal .modal-content');
  }

  function postUpdate()
  {
    var assetKey = $('#assetLibraryModalBody input.js-custom-checkbox-card-input:checked').data('id');
    var assetSelected ='#' + assetKey;
    if (assetPreview) {
      $(assetPreview).attr('src', $(assetSelected).data('src'));
    }

    if (assetFileInput) {
      var defaultPrefix = $(assetFileInput).attr('data-default-prefix');
      var prefix = typeof defaultPrefix !== typeof undefined && defaultPrefix !== false ? defaultPrefix : '';

      $(assetFileInput).val(prefix + $(assetSelected).data('id'));
    }
  }

  // image + imagegallery action
  $('body').delegate('.image-action .delete-button', 'click', function() {
    replaceAsset(this);
  });
  $('body').delegate('.video-action .delete-button', 'click', function() {
    replaceAsset(this);
  });

  $('body').delegate('.gallery-action .delete-button', 'click', function() {
    $(this).closest('.card').parent().remove();
  });

  $('body').delegate('.image-action .undo-button', 'click', function() {
    replaceAsset(this);
  });

  $('body').delegate('.gallery-action .undo-button', 'click', function() {
    replaceAsset(this);
  });

  $('body').delegate('.video-action .undo-button', 'click', function() {
    replaceAsset(this, 'iframe');
  });

  $('body').delegate('.gallery-add-button', 'click', function() {
    var galleryAddButton = $(this);
    var galleryItem = $(galleryAddButton).find('.empty-gallery-item').first();
    $(galleryAddButton).before($(galleryItem).html());
    $('body').tooltip({selector: '[data-toggle="tooltip"]'});
  });

  function replaceAsset(button, assetTag = 'img')
  {
    console.log(1)
    var value = $(button).data('value');
    var src = $(button).data('src');
    var card = $(button).closest('.card');

    $(card).find('input').first().val(value);
    $(card).find(assetTag).first().attr('src', src);
  }
})
