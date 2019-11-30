(function($) {
  $.fn.characterCounter = function(options) {
    var maxlength = $(this).attr("maxlength");
    if (typeof maxlength !== typeof undefined && maxlength !== false) {
      maxlength = maxlength;
    } else {
      maxlength = 100;
    }
    var settings = $.extend(
      {
        max: maxlength,
        opacity: ".8",
        color: "#363642",
        textArea: $(this).is("textarea")
      },
      options
    );

    return this.each(function() {
      $(this).wrap('<div class="character-wrap"></div>');
      $(this).after('<span class="remaining tooltip" title="Characters remaining"></span>');

      // This will write the input's value on database
      var value = $(this).val().length;
      var result = settings.max - value;
      $(this)
        .next(".remaining")
        .text(result);

      // This is counter
      $(this).keyup(function() {
        var value = $(this).val().length;
        var result = settings.max - value;
        $(this)
          .next(".remaining")
          .text(result);
      });

      // Css
      $(this).css("padding-right", "35px");
      $(this)
        .parent(".character-wrap")
        .css("position", "relative");
      $(this)
        .next(".remaining")
        .css({
          position: "absolute",
          opacity: settings.opacity,
          color: settings.color,
          right: "10px"
        });

      // textArea
      if (settings.textArea == false) {
        $(this)
          .next(".remaining")
          .css({
            top: "50%",
            transform: "translateY(-50%)"
          });
      } else {
        $(this)
          .next(".remaining")
          .css({
            bottom: "10px"
          });
      }
    });
  };
})(jQuery);