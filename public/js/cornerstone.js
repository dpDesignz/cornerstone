/*!
// The core JS file for running Cornerstone Framework scripts
*/

// Vanilla JS ready function
const ready = callback => {
  if (document.readyState !== 'loading') callback();
  else document.addEventListener('DOMContentLoaded', callback);
};

// Hide Banner Notification
function hideBanner() {
  $('#csc-banner').removeClass('visible');
}
$('[close-banner]').click(function() {
  hideBanner();
});

// Hide alerts on click
document.querySelectorAll('.csc-alert').forEach(alert =>
  alert.addEventListener('click', function() {
    // Check if closable
    if (
      !this.hasAttribute('data-closable') ||
      this.dataset.closable === 'true'
    ) {
      this.style.display = 'none';
    }
  })
);

// Set Choice Chip Selected
// TODO: Chip actions

// Change table header direction arrow on sort
$('.csc-table-header__title').click(function() {
  if (!$(this).hasClass('csc-table-header__title--active')) {
    $('.csc-table-header__title')
      .removeClass('csc-table-header__title--active')
      .removeClass('csc-table-header__title--desc');
    $(this).addClass('csc-table-header__title--active');
  } else if (!$(this).hasClass('csc-table-header__title--desc')) {
    $(this).addClass('csc-table-header__title--desc');
  } else {
    $(this).removeClass('csc-table-header__title--desc');
  }
});

// Bind buttons with rel="modal:close" to close the modal.
$(document).on('click.modal', 'button[rel~="modal:close"]', $.modal.close);

/*!
// FORM SCRIPTING
*/

// Add active class to label
function addLabelActive(e) {
  if (document.querySelector(`label[for=${e.id.trim()}]`)) {
    document.querySelector(`label[for=${e.id.trim()}]`).classList.add('active');
  }
}
// Remove active class from label
function removeLabelActive(e) {
  if (document.querySelector(`label[for=${e.id.trim()}]`)) {
    if (e.value.length === 0) {
      document
        .querySelector(`label[for=${e.id.trim()}]`)
        .classList.remove('active');
    }
  }
}
// Add active class listeners
function addLabelListeners(t) {
  ['change', 'focus'].forEach(evt =>
    t.addEventListener(evt, function () {
      addLabelActive(this);
    })
  );
  t.addEventListener('blur', function () {
    removeLabelActive(this);
  });
  // Add "active" class if input has value or placeholder
  if (t.value !== '') {
    addLabelActive(t);
  }
}

// Add Jquery Validator Defaults
if ($.validator !== undefined) {
  // Set default jQuery validator settings
  jQuery.validator.setDefaults({
    ignore: ':hidden:not(select)',
    errorClass: 'invalid',
    errorElement: 'span',
    errorPlacement(error, element) {
      // Add the `csc-helper-text` class to the error element
      error.addClass('csc-helper-text');
      if (element.prop('type') === 'checkbox') {
        error.insertAfter(element.parent('label'));
      } else {
        error.insertAfter(element);
      }
    },
    submitHandler(form) {
      const submit = form
        .querySelectorAll('button[type=submit]')[0]
        .querySelector('i.fas');
      if (submit.classList.contains('fa-save')) {
        submit.classList.remove('fa-save');
        submit.classList.add('fa-circle-notch');
        submit.classList.add('fa-spin');
      } else {
        // Add loader
        $('.cs-page').prepend(
          '<div class="csc-loader--full-page"><div class="csc-loader"><div class="text">Loading</div><div class="csc-loader--dots"><div></div><div></div><div></div><div></div></div></div></div>'
        );
      }
      // do other things for a valid form
      form.submit();
    },
  });

  // Add custom Validation Methods
  $.validator.addMethod(
    'pattern',
    function(value, element, param) {
      if (this.optional(element)) {
        return true;
      }
      if (typeof param === 'string') {
        param = new RegExp(`^(?:${param})$`);
      }
      return param.test(value);
    },
    'Invalid format.'
  );
}

// Add Jquery Modal Defaults
// Hide close and add animation on all modals
if ($.modal !== undefined) {
  $.modal.defaults = {
    showClose: false,
    fadeDuration: 250,
    showSpinner: true,
  };
  // Check height of modal isn't too high, else set scrollable
  $('.modal').on($.modal.OPEN, function(event, modal) {
    let maxHeight = $(this).height();
    if ($(this).find('.csc-modal__header').length) {
      maxHeight -= $(this)
        .find('.csc-modal__header')
        .css('height')
        .slice(0, -2);
    }
    if ($(this).find('.csc-modal__actions').length) {
      maxHeight -= $(this)
        .find('.csc-modal__actions')
        .css('height')
        .slice(0, -2);
    }
    if ($(this).find('.csc-modal__content').length > 0) {
      if (
        $(this)
          .find('.csc-modal__content')
          .css('height')
          .slice(0, -2) > maxHeight
      ) {
        $(this)
          .find('.csc-modal__content')
          .css('max-height', maxHeight);
        if (!$(this).hasClass('csc-modal--scrollable')) {
          $(this).addClass('csc-modal--scrollable');
        }
      } else {
        $(this)
          .find('.csc-modal__content')
          .css('max-height', 'none');
        if (!$(this).hasClass('csc-modal--scrollable')) {
          $(this).removeClass('csc-modal--scrollable');
        }
      }
    }
  });
  // Remove manual ajax added modals from DOM
  $(document).on($.modal.CLOSE, function(event, m) {
    if (m.elm[0].id === '') {
      m.elm.remove();
    }
  });
}

// Close loader on click
$(document).on('click', '.csc-loader--full-page', function() {
  $(this).fadeOut(500, function() {
    // now that the fade completed
    $(this).remove();
  });
});

// Add "active" class when input focused or changed
['change', 'focus'].forEach(evt =>
  document
    .querySelectorAll(
      'input[type=text]:not(.chosen-search-input), input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea'
    )
    .forEach(elem =>
      elem.addEventListener(evt, function() {
        addLabelActive(elem);
      })
    )
);
// Remove "active" class when input blurred if no value
document
  .querySelectorAll(
    'input[type=text]:not(.chosen-search-input), input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea'
  )
  .forEach(elem =>
    elem.addEventListener('blur', function() {
      removeLabelActive(elem);
    })
  );

// Auto resizing textarea ~ Based on https://stephanwagner.me/auto-resizing-textarea-with-vanilla-javascript
function addAutoResize() {
  document.querySelectorAll('[data-autoresize]').forEach(function(element) {
    element.style.boxSizing = 'border-box';
    const offset = element.offsetHeight - element.clientHeight;
    document
      .getElementById(element.id)
      .addEventListener('input', function(event) {
        event.target.style.height = 'auto';
        event.target.style.height = `${event.target.scrollHeight + offset}px`;
      });
    element.removeAttribute('data-autoresize');
  });
}
addAutoResize();

// Character Counter ~ Based on https://www.jqueryscript.net/form/character-countdown-text-field.html
function characterCounter() {
  document.querySelectorAll('[data-counter]').forEach(function(element) {
    // Get/Set the maxlength
    let maxlength = element.getAttribute('maxlength');
    maxlength =
      typeof maxlength !== typeof undefined &&
      maxlength !== false &&
      maxlength !== null
        ? maxlength
        : 100;

    // Get/Set the opacity
    let opacity = element.getAttribute('counter-opacity');
    opacity =
      typeof opacity !== typeof undefined &&
      opacity !== false &&
      opacity !== null
        ? opacity
        : '0.8';

    // Get/Set the colour
    let color = element.getAttribute('counter-color');
    color =
      typeof color !== typeof undefined && color !== false && color !== null
        ? color
        : '#363642';

    // Check if textarea
    const textarea = element.nodeName === 'TEXTAREA';

    // Settings
    const settings = {
      max: maxlength,
      opacity,
      color,
      textArea: textarea,
    };

    // Create elements
    $(element).wrap('<div class="character-wrap"></div>');
    $(element).after(
      '<span class="remaining tooltip" title="Characters remaining"></span>'
    );

    // This will write the input's value on database
    const value = $(element).val().length;
    const result = settings.max - value;
    $(element)
      .next('.remaining')
      .text(result);

    // This is counter
    $(element).keyup(function() {
      const value = $(element).val().length;
      const result = settings.max - value;
      $(element)
        .next('.remaining')
        .text(result);
    });

    // Css
    $(element).css('padding-right', '35px');
    $(element)
      .parent('.character-wrap')
      .css('position', 'relative');
    $(element)
      .next('.remaining')
      .css({
        position: 'absolute',
        opacity: settings.opacity,
        color: settings.color,
        right: '10px',
      });

    // textArea
    if (settings.textArea === false) {
      $(element)
        .next('.remaining')
        .css({
          top: '50%',
          transform: 'translateY(-50%)',
        });
    } else {
      $(element)
        .next('.remaining')
        .css({
          bottom: '10px',
        });
    }
  });
}
characterCounter();

// Auto Load Trumbowyg Editor ~ https://alex-d.github.io/Trumbowyg/documentation/
if ($.trumbowyg !== undefined) {
  $('[data-editor]').trumbowyg({
    btns: [
      ['strong', 'em', 'underline'],
      ['unorderedList', 'orderedList'],
      ['link'],
      ['viewHTML'],
    ],
    autogrow: true,
    resetCss: true,
  });
}

// Reload page limit on change in pagination
document.querySelectorAll('.cs-pagitems').forEach(select =>
  select.addEventListener('change', function() {
    window.location.href = select.value;
  })
);

$(document).ready(function() {
  // Show Banner
  if ($('#csc-banner').length) {
    $('#csc-banner').addClass('visible');
  }
  // Activate Waves
  Waves.attach('[class^=csc-btn]');
  Waves.attach('.csc-icon-btn', ['waves-circle']);
  Waves.attach('.csc-chip');
  Waves.init();

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
  document
    .querySelectorAll(
      'input[type=text]:not(.chosen-search-input), input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea'
    )
    .forEach(function(e) {
      if (e.value !== '' || e.placeholder !== '') {
        addLabelActive(e);
      }
    });

  if ($.fn.tooltipster !== undefined) {
    // Activate Tooltipster
    $('.tooltip').tooltipster({ contentAsHTML: true });

    // Add Tooltipster to elements dynamically added to the DOM ~ http://iamceege.github.io/tooltipster/#delegation
    $('body').on('mouseenter', '.tooltip:not(.tooltipstered)', function() {
      $(this).tooltipster({ contentAsHTML: true });
    });
  } else {
    console.error(
      'Tooltipster is not loaded. Please load Tooltipster to enable.'
    );
  }

  // Activate Tippy
  try {
    // Activate Tippy
    tippy('[data-tippy-content]', {
      allowHTML: true,
      delay: 200,
    });
  } catch (error) {
    console.error('Tippy is not loaded. Please load Tippy.js to enable.');
  }
});
