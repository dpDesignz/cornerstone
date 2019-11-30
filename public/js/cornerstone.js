/*!
// The core JS file for running Cornerstone Framework scripts
*/

// Hide Banner Notification
function hideBanner() {
  $("#csc-banner").removeClass("visible");
}
$("[close-banner]").click(function() {
  hideBanner();
});

// Set Choice Chip Selected
// TODO: Chip actions

// Change table header direction arrow on sort
$(".csc-table-header__title").click(function() {
  if (!$(this).hasClass("csc-table-header__title--active")) {
    $(".csc-table-header__title")
      .removeClass("csc-table-header__title--active")
      .removeClass("csc-table-header__title--ascending");
    $(this).addClass("csc-table-header__title--active");
  } else {
    if (!$(this).hasClass("csc-table-header__title--ascending")) {
      $(this).addClass("csc-table-header__title--ascending");
    } else {
      $(this).removeClass("csc-table-header__title--ascending");
    }
  }
});

$(document).ready(function() {
  // Show Banner
  if ($("#csc-banner").length) {
    $("#csc-banner").addClass("visible");
  }
  // Activate Waves
  Waves.attach("[class^=csc-btn]");
  Waves.attach(".csc-icon-btn", ["waves-circle"]);
  Waves.attach(".csc-chip");
  Waves.init();

  // Hide close and add animation on all modals
  if ($.modal != undefined) {
    $.modal.defaults = { showClose: false, fadeDuration: 250 };
    // Check height of modal isn't too high, else set scrollable
    $(".modal").on($.modal.OPEN, function(event, modal) {
      var maxHeight = $(this).height();
      if ($(this).find(".csc-modal__header").length) {
        maxHeight =
          maxHeight -
          $(this)
            .find(".csc-modal__header")
            .css("height")
            .slice(0, -2);
      }
      if ($(this).find(".csc-modal__actions").length) {
        maxHeight =
          maxHeight -
          $(this)
            .find(".csc-modal__actions")
            .css("height")
            .slice(0, -2);
      }
      if (
        $(this)
          .find(".csc-modal__content")
          .css("height")
          .slice(0, -2) > maxHeight
      ) {
        $(this)
          .find(".csc-modal__content")
          .css("max-height", maxHeight);
        if (!$(this).hasClass("csc-modal--scrollable")) {
          $(this).addClass("csc-modal--scrollable");
        }
      } else {
        $(this)
          .find(".csc-modal__content")
          .css("max-height", "none");
        if (!$(this).hasClass("csc-modal--scrollable")) {
          $(this).removeClass("csc-modal--scrollable");
        }
      }
    });
  }
  // $(".modal").function({
  //   showClose: false
  // });
  // // Lock alert modals
  // $(".csc-modal--alert").modal({
  //   escapeClose: false,
  //   clickClose: false,
  //   showClose: false
  // });
  // // Show close on required modals
  // $(".csc-modal--close").modal({
  //   showClose: true
  // });
  // Add "active" class if input has value or placeholder
  $(
    "input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea"
  ).each(function() {
    if (
      $(this).val().length > 0 ||
      $(this).is(":focus") ||
      $(this).attr("placeholder") !== undefined
    ) {
      $("label[for=" + this.id + "]").addClass("active");
    }
  });
  // Activate Tooltipster
  $(".tooltip").tooltipster({ contentAsHTML: true });
  $(".form-tooltip").tooltipster({ contentAsHTML: true, side: "right" });
  // Add Tooltipster to elements dynamically added to the DOM ~ http://iamceege.github.io/tooltipster/#delegation
  $("body").on("mouseenter", ".tooltip:not(.tooltipstered)", function() {
    $(this).tooltipster({ contentAsHTML: true });
  });
});

// Bind buttons with rel="modal:close" to close the modal.
$(document).on("click.modal", 'button[rel~="modal:close"]', $.modal.close);

/*!
// FORM SCRIPTING
*/

if ($.validator != undefined) {
  // Set default jQuery validator settings
  jQuery.validator.setDefaults({
    errorClass: "invalid",
    errorElement: "span",
    errorPlacement: function(error, element) {
      // Add the `csc-helper-text` class to the error element
      error.addClass("csc-helper-text");
      if (element.prop("type") === "checkbox") {
        error.insertAfter(element.parent("label"));
      } else {
        error.insertAfter(element);
      }
    },
    submitHandler: function(form) {
      // Add loader
      $(".cs-page").prepend(
        '<div class="csc-loader--full-page"><div class="csc-loader"><div class="text">Loading</div><div class="csc-loader--dots"><div></div><div></div><div></div><div></div></div></div></div>'
      );
      // do other things for a valid form
      form.submit();
    }
  });

  // Add custom Validation Methods
  $.validator.addMethod(
    "pattern",
    function(value, element, param) {
      if (this.optional(element)) {
        return true;
      }
      if (typeof param === "string") {
        param = new RegExp("^(?:" + param + ")$");
      }
      return param.test(value);
    },
    "Invalid format."
  );
}

// Close loader on click
$(document).on("click", ".csc-loader--full-page", function() {
  $(this).fadeOut(500, function() {
    // now that the fade completed
    $(this).remove();
  });
});

// Add "active" class when input focused
$(
  "input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea"
).on("change focus", function(e) {
  $("label[for=" + e.target.id + "]").addClass("active");
});
// Remove "active" class when input blurred if no value
$(
  "input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea"
).blur(function(e) {
  if (e.target.value.length == 0) {
    $("label[for=" + e.target.id + "]").removeClass("active");
  }
});

// Auto resizing textarea ~ Based on https://stephanwagner.me/auto-resizing-textarea-with-vanilla-javascript
function addAutoResize() {
  document.querySelectorAll("[data-autoresize]").forEach(function(element) {
    element.style.boxSizing = "border-box";
    var offset = element.offsetHeight - element.clientHeight;
    document
      .getElementById(element.id)
      .addEventListener("input", function(event) {
        event.target.style.height = "auto";
        event.target.style.height = event.target.scrollHeight + offset + "px";
      });
    element.removeAttribute("data-autoresize");
  });
}
addAutoResize();

// Character Counter ~ Based on https://www.jqueryscript.net/form/character-countdown-text-field.html
function characterCounter() {
  document.querySelectorAll("[data-counter]").forEach(function(element) {
    // Get/Set the maxlength
    var maxlength = element.getAttribute("maxlength");
    maxlength =
      typeof maxlength !== typeof undefined &&
      maxlength !== false &&
      maxlength !== null
        ? maxlength
        : 100;

    // Get/Set the opacity
    var opacity = element.getAttribute("counter-opacity");
    opacity =
      typeof opacity !== typeof undefined &&
      opacity !== false &&
      opacity !== null
        ? opacity
        : "0.8";

    // Get/Set the colour
    var color = element.getAttribute("counter-color");
    color =
      typeof color !== typeof undefined && color !== false && color !== null
        ? color
        : "#363642";

    // Check if textarea
    var textarea = element.nodeName === "TEXTAREA" ? true : false;

    // Settings
    var settings = {
      max: maxlength,
      opacity: opacity,
      color: color,
      textArea: textarea
    };

    // Create elements
    $(element).wrap('<div class="character-wrap"></div>');
    $(element).after(
      '<span class="remaining tooltip" title="Characters remaining"></span>'
    );

    // This will write the input's value on database
    var value = $(element).val().length;
    var result = settings.max - value;
    $(element)
      .next(".remaining")
      .text(result);

    // This is counter
    $(element).keyup(function() {
      var value = $(element).val().length;
      var result = settings.max - value;
      $(element)
        .next(".remaining")
        .text(result);
    });

    // Css
    $(element).css("padding-right", "35px");
    $(element)
      .parent(".character-wrap")
      .css("position", "relative");
    $(element)
      .next(".remaining")
      .css({
        position: "absolute",
        opacity: settings.opacity,
        color: settings.color,
        right: "10px"
      });

    // textArea
    if (settings.textArea == false) {
      $(element)
        .next(".remaining")
        .css({
          top: "50%",
          transform: "translateY(-50%)"
        });
    } else {
      $(element)
        .next(".remaining")
        .css({
          bottom: "10px"
        });
    }
  });
}
characterCounter();

// Auto Load Trumbowyg Editor ~ https://alex-d.github.io/Trumbowyg/documentation/
if ($.trumbowyg != undefined) {
  $("[data-editor]").trumbowyg({
    btns: [
      ["strong", "em", "underline"],
      ["unorderedList", "orderedList"],
      ["viewHTML"]
    ],
    autogrow: true,
    resetCss: true
  });
}
