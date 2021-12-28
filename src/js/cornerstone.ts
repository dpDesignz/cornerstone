/*!
// The core JS file for running Cornerstone Framework scripts
*/

// Enable JS debugging
const debug: boolean = false;

// Vanilla JS ready function
const ready = (callback: any) => {
  if (document.readyState !== 'loading') callback();
  else document.addEventListener('DOMContentLoaded', callback);
};

// Get closest element
const getClosest = function (elem: HTMLElement, selector: string): HTMLElement | null {
  // Element.matches() polyfill
  if (!Element.prototype.matches) {
    Element.prototype.matches =
      // @ts-ignore
      Element.prototype.matchesSelector ||
      // @ts-ignore
      Element.prototype.mozMatchesSelector ||
      // @ts-ignore
      Element.prototype.msMatchesSelector ||
      // @ts-ignore
      Element.prototype.oMatchesSelector ||
      Element.prototype.webkitMatchesSelector ||
      function (s) {
        const matches = (document || elem.ownerDocument).querySelectorAll(
          s
        );
        let i = matches.length;
        while (--i >= 0 && matches.item(i) !== elem) { }
        return i > -1;
      };
  }

  // Get the closest matching element
  // @ts-ignore
  for (; elem && elem !== document; elem = elem.parentNode) {
    if (elem.matches(selector)) return elem;
  }
  return null;
};

// Debounce function ~ https://gist.github.com/nmsdvid/8807205
interface DebouncedFunction {
  (): any;
  cancel: () => void;
}

const debounce = <F extends (...args: any[]) => ReturnType<F>>(
  func: F,
  wait: number = 250,
  immediate?: boolean
) => {
  let timeout: number = 0;

  const debounced: DebouncedFunction = function (this: void) {
    const context: any = this;
    const args = arguments;

    const later = function () {
      timeout = 0;
      if (!immediate) func.call(context, ...args);
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = window.setTimeout(later, wait);
    if (callNow) func.call(context, ...args);
  };

  debounced.cancel = function () {
    clearTimeout(timeout);
    timeout = 0;
  };

  return debounced as (...args: Parameters<F>) => ReturnType<F>;
};

// Vanilla JS Show/Hide ~ https://gomakethings.com/how-to-a-fade-in-to-vanilla-javascript-show-and-hide-methods/
// Vanilla JS show element
const jsShow = function (elem: HTMLElement): void {
  // Get the natural height of the element
  const getHeight = function (): string {
    elem.style.display = 'block'; // Make it visible
    const elemHeight = `${elem.scrollHeight}px`; // Get it's height
    elem.style.display = ''; //  Hide it again
    return elemHeight;
  };

  const thisHeight = getHeight(); // Get the natural height
  elem.classList.add('show'); // Make the element visible
  elem.style.height = thisHeight; // Update the max-height

  // Once the transition is complete, remove the inline max-height so the content can scale responsively
  window.setTimeout(function (): void {
    elem.style.height = '';
  }, 350);
};
// Vanilla JS hide element
const jsHide = function (elem: HTMLElement): void {
  // Give the element a height to change from
  elem.style.height = `${elem.scrollHeight}px`;

  // Set the height back to 0
  window.setTimeout(function (): void {
    elem.style.height = '0';
  }, 1);

  // When the transition is complete, hide it
  window.setTimeout(function (): void {
    elem.classList.remove('show');
  }, 350);
};

// Vanilla JS Animations Helper Function ~ https://vanillajstoolkit.com/helpers/animate/
const animateElm = function (elem: HTMLElement, animation: string, speed: string | null, hide: boolean): void {

  // If there's no element or animation, do nothing
  if (!elem || !animation) return;

  // Remove the [hidden] attribute
  elem.removeAttribute('hidden');

  // Check for the speed
  if (speed && (speed === 'slow' || speed === 'slower' || speed === 'fast' || speed === 'faster')) {
    // Add the animation speed
    elem.classList.add(`animate__${speed}`);
  }

  // Apply the animation
  elem.classList.add(`animate__${animation}`);

  // Detect when the animation ends
  elem.addEventListener('animationend', function endAnimation(event) {

    // Remove the animation class
    elem.classList.remove(`animate__${animation}`);

    // Check for the speed
    if (speed && (speed === 'slow' || speed === 'slower' || speed === 'fast' || speed === 'faster')) {
      // Remove the animation speed
      elem.classList.remove(`animate__${speed}`);
    }

    // If the element should be hidden, hide it
    if (hide) {
      elem.setAttribute('hidden', 'true');
    }

    // Remove this event listener
    elem.removeEventListener('animationend', endAnimation, false);

  }, false);

};

// Hide alerts on click
const siteAlerts = document.querySelectorAll<HTMLElement>('.csc-alert');
// Check if there are any alerts
if (siteAlerts) {
  // Loop through the alerts
  siteAlerts.forEach(alert =>
    alert.addEventListener('click', () => { // Add click event listeners
      // Check if closable
      if (
        !alert.hasAttribute('data-closable') ||
        alert.dataset.closable === 'true'
      ) {
        // Hide the alert
        alert.style.display = 'none';
      }
    })
  );
}

// Hide Banner Notification
const siteBanner = document.querySelector('#csc-banner');
// Check if there are any banners
if (siteBanner) {
  // Add click event listener
  siteBanner.addEventListener('click', (): void => {
    // Check if the banner has the visible class
    if (siteBanner.classList.contains('visible'))
      // Remove the visible class
      siteBanner.classList.remove('visible');
  });
}

// Change table header direction arrow on sort
const tableHeaders = document.querySelectorAll<HTMLElement>('.csc-table-header__title');
// Check if there are any table headers
if (tableHeaders) {
  // Loop through the table headers
  tableHeaders.forEach(headerItem => headerItem.addEventListener('click', (): void => {
    // Check if the current header is active
    if (!headerItem.classList.contains('csc-table-header__title--active')) {
      // Remove the active and descending class
      headerItem.classList.remove('csc-table-header__title--active', 'csc-table-header__title--desc');
    } else if (!headerItem.classList.contains('csc-table-header__title--desc')) {
      // Add the class descending
      headerItem.classList.add('csc-table-header__title--desc');
    } else {
      // Remove the class descending
      headerItem.classList.remove('csc-table-header__title--desc');
    }
  }));
}

// Set Choice Chip Selected
// TODO: Chip actions

// Toggle collapsible
function toggleCollapsible(this: HTMLElement) {
  // Get collapsible
  const collapsible: HTMLElement | null = this.parentElement;
  // Check if the collapsible exists
  if (collapsible) {
    // Get collapsible body
    const collapsibleBody: HTMLElement | null = collapsible.querySelector(`.csc-collapsible__body`);
    // Check if the collapsible body exists
    if (collapsibleBody) {
      // Check if classList contains open
      if (collapsible.classList.contains('open')) {
        // Check for collapsible body
        if (collapsibleBody) {
          // Remove show class
          jsHide(collapsibleBody);
        }
        // Remove open class
        collapsible.classList.remove('open');
        // Change aria-expanded state
        collapsible.setAttribute('aria-expanded', 'false');
      } else {
        // Add open class
        collapsible.classList.add('open');
        // Check for collapsible body
        if (collapsibleBody) {
          // Add show class
          jsShow(collapsibleBody);
        }
        // Change aria-expanded state
        collapsible.setAttribute('aria-expanded', 'true');
      }
    }
  }
}

// Toggle FAQ Collapsible
function toggleFAQCollapsible(this: HTMLElement) {
  // Get collapsible header
  const collapsibleHeader: HTMLElement | null = this;
  // Get data list
  const dlElm: HTMLElement | null = collapsibleHeader.parentElement;
  // Check if the collapsible header element exists
  if (collapsibleHeader) {
    // Get aria-controls
    const ariaControls: string | null = collapsibleHeader.getAttribute('aria-controls');
    // Check if the aria controls and data list element exists
    if (ariaControls && dlElm) {
      // Get collapsible body
      const collapsibleBody: HTMLElement | null = dlElm.querySelector(`#${ariaControls}`);
      // Check if the collapsible body element exists
      if (collapsibleBody) {
        // Check if classList contains open
        if (collapsibleHeader.classList.contains('open')) {
          // Check for collapsible body
          if (collapsibleBody) {
            // Remove show class
            jsHide(collapsibleBody);
          }
          // Remove open class
          collapsibleHeader.classList.remove('open');
          // Change aria-expanded state
          collapsibleHeader.setAttribute('aria-expanded', 'false');
        } else {
          // Add open class
          collapsibleHeader.classList.add('open');
          // Check for collapsible body
          if (collapsibleBody) {
            // Add show class
            jsShow(collapsibleBody);
          }
          // Change aria-expanded state
          collapsibleHeader.setAttribute('aria-expanded', 'true');
        }
      }
    }
  }
}

// Toggle Tabs
const tabLinks = document.querySelectorAll<HTMLElement>('.csc-tab');
if (tabLinks) {
  tabLinks.forEach(tabLink => {
    tabLink.addEventListener('click', function (e: MouseEvent | TouchEvent): void {
      // Hide all tab contents
      const tabContents = document.querySelectorAll<HTMLElement>('.csc-tab__content');
      tabContents.forEach(tabContent => {
        tabContent.style.display = "none";
      });

      // Remove the active button class
      tabLinks.forEach(activeLink => {
        activeLink.classList.remove("csc-tab--active");
      });

      // Show active tab
      const activeTab: HTMLElement | null = document.querySelector(`#tab__${this.dataset.ref}`);
      if (activeTab) {
        activeTab.style.display = "block";
        tabLink.classList.add("csc-tab--active");
      }
    });
  });

  // Load initial tab on document load
  ready(() => {
    // Check for tab with active class
    const activeTab = [...tabLinks].filter(el => el.classList.contains('csc-tab--active') !== false);
    if (activeTab && activeTab.length > 0) { // Load active tab
      activeTab[0].click();
    } else { // Load initial tab
      tabLinks[0].click();
    }
  });
}

// Scroll to element from hash
ready(() => {
  // Get the window location hash
  const { hash } = window.location;
  // Check if the hash exists
  if (hash) {
    // Get element
    const elm: HTMLElement | null = document.querySelector(hash);
    // Check if the element exists
    if (elm) {
      // Scroll to element
      elm.scrollIntoView({
        behavior: 'smooth',
      });
    }
  }
});

/*!
// FORM SCRIPTING
*/

// Add active class to label
function addLabelActive(e: HTMLInputElement) {
  // Check if the input ID is set
  if (e.id) {
    // Get the label element
    const label: HTMLElement | null = document.querySelector(`label[for=${e.id}]`);
    // Check if the input ID is set and the label exists
    if (label) {
      // Add the active class to the label
      label.classList.add('active');
    }
  }
}
// Remove active class from label
function removeLabelActive(e: HTMLInputElement) {
  // Check if the input ID is set
  if (e.id) {
    // Get the label element
    const label: HTMLElement | null = document.querySelector(`label[for=${e.id}]`);
    // Check if the input ID is set and the label exists
    if (label) {
      // Check if the input is empty
      if (e.value.length === 0) {
        // Remove the active class from the label
        label.classList.remove('active');
      }
    }
  }
}
// Add active class listeners
function addLabelListeners(input: HTMLInputElement) {
  // Check the input exists
  if (input) {
    // Loop through each event type
    ['change', 'focus'].forEach(evt =>
      input.addEventListener(evt, (): void => { // Add event listener
        addLabelActive(input); // Trigger the `addLabelActive()` function
      })
    );
    // Add blur event listener to the input
    input.addEventListener('blur', () => {
      removeLabelActive(input); // Trigger the `removeLabelActive()` function
    });
    // Add "active" class if input has value or placeholder
    if (input.value !== '') {
      addLabelActive(input); // Manually trigger the `addLabelActive()` function
    }
  }
}

// Add Jquery Validator Defaults
// @ts-ignore
if ($.validator !== undefined) {
  // Set default jQuery validator settings
  // @ts-ignore
  $.validator.setDefaults({
    ignore: ':hidden:not(select)',
    errorClass: 'invalid',
    errorElement: 'span',
    errorPlacement(errorGroup: object, elementGroup: object) {
      // Loop through the errors
      Object.keys(errorGroup).forEach((errorKey) => {
        // Check the error is an element
        // @ts-ignore
        if (typeof errorGroup[errorKey] === 'object')
          // Add the `csc-helper-text` class to the error element
          // @ts-ignore
          errorGroup[errorKey].classList.add('csc-helper-text');
      });
      // Loop through the elements
      Object.keys(elementGroup).forEach((elmKey) => {
        // Check the data is an element
        // @ts-ignore
        if (typeof elementGroup[elmKey] === 'object') {
          // @ts-ignore
          if (elementGroup[elmKey].type === 'checkbox') {
            // Get the label
            // @ts-ignore
            const label = getClosest(elementGroup[elmKey], 'label');
            // Check there is a label
            if (label) {
              // Get the parent element
              const parentElement = label.parentNode;
              // Check the parent element exists
              if (parentElement)
                // Insert the error after the checkbox
                // @ts-ignore
                parentElement.insertBefore(errorGroup[elmKey], label.nextSibling);
            }
          } else {
            // Check the element exists
            // @ts-ignore
            if (elementGroup[elmKey]) {
              // Get the parent element
              // @ts-ignore
              const parentElement = elementGroup[elmKey].parentNode;
              // Check the parent element exists
              if (parentElement)
                // Insert the error after the element
                // @ts-ignore
                parentElement.insertBefore(errorGroup[elmKey], elementGroup[elmKey].nextSibling);
            }
          }
        }
      });
    },
    submitHandler(form: HTMLFormElement) {
      // Get the submit/save button
      const submitSaveBtn: HTMLElement | null = form
        .querySelectorAll('button[type=submit]')[0]
        .querySelector('i[class^="fa"]');
      // Check if the submit/save button exists
      if (submitSaveBtn) {
        // Check if the classList contains a save icon
        if (submitSaveBtn.classList.contains('fa-save')) {
          // Remove the save icon
          submitSaveBtn.classList.remove('fa-save');
          // Add the circle notch icon and spin class
          submitSaveBtn.classList.add('fa-circle-notch', 'fa-spin');
        }
      } else {
        // Add loader
        // @ts-ignore
        $('.cs-page').prepend(
          '<div class="csc-loader--full-page"><div class="csc-loader"><div class="text">Loading</div><div class="csc-loader--dots"><div></div><div></div><div></div><div></div></div></div></div>'
        );
      }
      // do other things for a valid form
      form.submit();
    },
  });

  // Add custom Validation Methods
  // @ts-ignore
  $.validator.addMethod(
    'pattern',
    // @ts-ignore
    function (value, element, param) {
      // @ts-ignore
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
// @ts-ignore
if ($.modal !== undefined) {
  // @ts-ignore
  $.modal.defaults = {
    showClose: false,
    fadeDuration: 250,
    showSpinner: true,
  };
  // Check height of modal isn't too high, else set as scrollable
  // @ts-ignore
  $('.modal').on($.modal.OPEN, function (event, modal) {
    // Get the modal
    const modalElm: HTMLElement = event.target;
    // Get the height of the modal element
    let maxHeight: number = +modalElm.style.height.slice(0, -2);
    // Get the modal header
    const modalElmHeader: HTMLElement | null = modalElm.querySelector('.csc-modal__header');
    // Check the modal header exists
    if (modalElmHeader) {
      // Get the height of the header
      maxHeight -= +modalElmHeader.style.height.slice(0, -2);
    }
    // Get the modal actions
    const modalElmActions: HTMLElement | null = modalElm.querySelector('.csc-modal__actions');
    // Check the modal actions exists
    if (modalElmActions) {
      // Get the height of the actions
      maxHeight -= +modalElmActions.style.height.slice(0, -2);
    }
    // Get the modal content
    const modalElmContent: HTMLElement | null = modalElm.querySelector('.csc-modal__content');
    // Check the modal content exists
    if (modalElmContent) {
      // Get the height of the content
      const modalElmContentHeight: number = +modalElmContent.style.height.slice(0, -2);
      // Check if the modal content height is greater than the max height
      if (
        modalElmContentHeight > maxHeight
      ) {
        // Set the max height
        modalElmContent.style.maxHeight = `${maxHeight}px`;
        // Check for the scrollable class
        if (!modalElmContent.classList.contains('csc-modal--scrollable'))
          // Add the scrollable class
          modalElmContent.classList.add('csc-modal--scrollable')
      } else {
        // Remove the max height
        modalElmContent.style.maxHeight = `none`;
        // Check for the scrollable class
        if (!modalElmContent.classList.contains('csc-modal--scrollable'))
          // Remove the scrollable class
          modalElmContent.classList.remove('csc-modal--scrollable')
      }
    }
  });
  // Remove manual ajax added modals from DOM
  // @ts-ignore
  $(document).on($.modal.CLOSE, function (event, m) {
    if (m.elm[0].id === '') {
      m.elm.remove();
    }
  });

  // Bind buttons with rel="modal:close" to close the modal.
  // @ts-ignore
  $(document).on('click.modal', 'button[rel~="modal:close"]', $.modal.close);
}

// Close loader on click
// @ts-ignore
$(document).on('click', '.csc-loader--full-page', function () {
  // @ts-ignore
  $(this).fadeOut(500, function () {
    // now that the fade completed
    // @ts-ignore
    $(this).remove();
  });
});

// Add "active" class when input focused or changed
['change', 'focus'].forEach(evt =>
  document
    .querySelectorAll<HTMLInputElement>(
      'input[type=text]:not(.chosen-search-input), input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea'
    )
    .forEach(elem =>
      elem.addEventListener(evt, function () {
        addLabelActive(elem);
      })
    )
);
// Remove "active" class when input blurred if no value
document
  .querySelectorAll<HTMLInputElement>(
    'input[type=text]:not(.chosen-search-input), input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea'
  )
  .forEach(elem =>
    elem.addEventListener('blur', function () {
      removeLabelActive(elem);
    })
  );

// Auto resizing textarea ~ Based on https://stephanwagner.me/auto-resizing-textarea-with-vanilla-javascript
function addAutoResize(): void {
  document.querySelectorAll<HTMLInputElement>('[data-autoresize]').forEach(element => {
    // Check the element exists
    if (element) {
      element.style.boxSizing = 'border-box';
      const offset = element.offsetHeight - element.clientHeight;
      // Add event listener to the element
      element.addEventListener('input', (event) => {
        // Get the target element
        const targetElm: HTMLInputElement | null = event.target as HTMLInputElement;
        // Check the target element exists
        if (targetElm) {
          // Set the target element height
          targetElm.style.height = 'auto';
          targetElm.style.height = `${targetElm.scrollHeight + offset}px`;
        }
      });
      element.removeAttribute('data-autoresize');
    }
  });
}
addAutoResize();

// Character Counter ~ Based on https://www.jqueryscript.net/form/character-countdown-text-field.html
function characterCounter() {
  document.querySelectorAll<HTMLInputElement>('[data-counter]').forEach(element => {
    // Check for the element
    if (element) {
      // Get/Set the max length
      let maxLengthValue: string | null = element.getAttribute('maxlength');
      let maxLength: number =
        typeof maxLengthValue !== typeof undefined &&
          maxLengthValue !== null
          ? +maxLengthValue
          : 100;

      // Get/Set the opacity
      let opacityValue: string | null = element.getAttribute('counter-opacity');
      let opacity: number =
        typeof opacityValue !== typeof undefined &&
          opacityValue !== null
          ? +opacityValue
          : 0.8;

      // Get/Set the colour
      let colorValue: string | null = element.getAttribute('counter-color');
      let color: string =
        typeof colorValue !== typeof undefined && colorValue !== null
          ? colorValue
          : '#363642';

      // Check if textarea
      const textarea = element.nodeName === 'TEXTAREA';

      // Settings
      const settings = {
        max: maxLength,
        opacity,
        color,
        textArea: textarea,
      };

      // Create elements
      const characterWrapper = document.createElement('div');
      characterWrapper.classList.add('character-wrap');
      const elmParentNode = element.parentNode;
      if (elmParentNode)
        elmParentNode.insertBefore(characterWrapper, element);
      characterWrapper.appendChild(element);
      characterWrapper.insertAdjacentHTML('beforeend', '<span class="remaining tooltip" title="Characters remaining"></span>');

      // Get the remaining span
      const remainingSpan: HTMLElement | null = characterWrapper.querySelector('.remaining');

      // This will write the input's value on the element
      const updateCountValue = () => {
        const value: number = element.value.length;
        const result = settings.max - value;
        // Check if the remaining span exists
        if (remainingSpan)
          // Update the remaining count
          remainingSpan.innerText = result.toString();
      }

      // Trigger init update
      updateCountValue();

      // Add event listener for the counter and update
      element.addEventListener('keyup', updateCountValue);

      // Css
      element.style.paddingRight = '35px';
      characterWrapper.style.position = 'relative';
      if (remainingSpan) {
        remainingSpan.style.position = 'absolute';
        remainingSpan.style.opacity = settings.opacity.toString();
        remainingSpan.style.color = settings.color.toString();
        remainingSpan.style.right = '10px';
      }

      // Check if the element is a text area
      if (settings.textArea === false) {
        // Element is not a text area. Position in the middle
        if (remainingSpan) {
          remainingSpan.style.top = '50%';
          remainingSpan.style.transform = 'translateY(-50%)';
        }
      } else {
        // Element is a text area. Position 10px from the bottom
        if (remainingSpan) {
          remainingSpan.style.bottom = '10px';
        }
      }
    }
  });
}
// Init character counter
characterCounter();

// Auto Load Trumbowyg Editor ~ https://alex-d.github.io/Trumbowyg/documentation/
// @ts-ignore
if ($.trumbowyg !== undefined) {
  // @ts-ignore
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
document.querySelectorAll<HTMLInputElement>('.cs-pagitems').forEach(select =>
  select.addEventListener('change', function () {
    window.location.href = select.value;
  })
);

// @ts-ignore
$(document).ready(function () {
  // Show Banner
  if (siteBanner) {
    if (!siteBanner.classList.contains('visible'))
      siteBanner.classList.add('visible');
  }
  // Activate Waves
  // @ts-ignore
  if (typeof Waves !== typeof undefined) {
    // @ts-ignore
    Waves.attach('[class^=csc-btn]');
    // @ts-ignore
    Waves.attach('.csc-icon-btn', ['waves-circle']);
    // @ts-ignore
    Waves.attach('.csc-chip');
    // @ts-ignore
    Waves.init();
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
  document
    .querySelectorAll<HTMLInputElement>(
      'input[type=text]:not(.chosen-search-input):not(.swal-content__input), input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea'
    )
    .forEach((e) => {
      if (e.value !== '' || e.placeholder !== '') {
        addLabelActive(e);
      }
    });

  // @ts-ignore
  if ($.fn.tooltipster !== undefined) {
    // Activate Tooltipster
    // @ts-ignore
    $('.tooltip').tooltipster({ contentAsHTML: true });

    // Add Tooltipster to elements dynamically added to the DOM ~ http://iamceege.github.io/tooltipster/#delegation
    // @ts-ignore
    $('body').on('mouseenter', '.tooltip:not(.tooltipstered)', function () {
      // @ts-ignore
      $(this).tooltipster({ contentAsHTML: true });
    });
  } else if (debug) {
    console.error(
      'Tooltipster is not loaded. Please load Tooltipster to enable.'
    );
  }

  // Activate Tippy
  try {
    // Activate Tippy
    // @ts-ignore
    tippy('[data-tippy-content]', {
      allowHTML: true,
      delay: 200,
    });
  } catch (error) {
    if (debug)
      console.error('Tippy is not loaded. Please load Tippy.js to enable.');
  }
});
