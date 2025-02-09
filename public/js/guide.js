$(document).ready(function() {
    $('#guideContentId img').each(function() {
        var src = $(this).attr('src');
        var newTag = '<a class="js-fancybox" href="javascript:;"\
           data-src="' + src + '"><img src="' + src + '"></a>';

        var parent = $(this).parent();
        $(parent).addClass('guide-image');

        $(this).remove();
        $(parent).append(newTag);
    });

    $('.js-fancybox').each(function() {
        var fancybox = $.HSCore.components.HSFancyBox.init($(this));
    });

    $('#bieutuong_thaotac').on('click', function() {
        setTimeout(function() {
            $('#globalGuideButton')[0].click();
        }, 100);
    });
});
