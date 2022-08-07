"use strict";
/*!
// The core JS file for running Cornerstone Framework scripts
*/
const debug = false;
let isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
isIOS = (!isIOS && navigator.userAgent.match(/Mac/) && navigator.maxTouchPoints && navigator.maxTouchPoints > 2) ? true : isIOS;
const touchEvent = 'ontouchstart' in window ? 'touchstart' : 'click';
const ready = (callback) => {
    if (document.readyState !== 'loading')
        callback();
    else
        document.addEventListener('DOMContentLoaded', callback);
};
const getClosest = function (elem, selector) {
    if (!Element.prototype.matches) {
        Element.prototype.matches =
            Element.prototype.matchesSelector ||
                Element.prototype.mozMatchesSelector ||
                Element.prototype.msMatchesSelector ||
                Element.prototype.oMatchesSelector ||
                function (s) {
                    const matches = (document || elem.ownerDocument).querySelectorAll(s);
                    let i = matches.length;
                    while (--i >= 0 && matches.item(i) !== elem) { }
                    return i > -1;
                };
    }
    for (; elem && elem !== document; elem = elem.parentNode) {
        if (elem.matches(selector))
            return elem;
    }
    return null;
};
const debounce = (func, wait = 250, immediate) => {
    let timeout = 0;
    const debounced = function () {
        const context = this;
        const args = arguments;
        const later = function () {
            timeout = 0;
            if (!immediate)
                func.call(context, ...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = window.setTimeout(later, wait);
        if (callNow)
            func.call(context, ...args);
    };
    debounced.cancel = function () {
        clearTimeout(timeout);
        timeout = 0;
    };
    return debounced;
};
const jsShow = function (elem) {
    const getHeight = function () {
        elem.style.display = 'block';
        const elemHeight = `${elem.scrollHeight}px`;
        elem.style.display = '';
        return elemHeight;
    };
    const thisHeight = getHeight();
    elem.classList.add('show');
    elem.style.height = thisHeight;
    window.setTimeout(function () {
        elem.style.height = '';
    }, 350);
};
const jsHide = function (elem) {
    elem.style.height = `${elem.scrollHeight}px`;
    window.setTimeout(function () {
        elem.style.height = '0';
    }, 1);
    window.setTimeout(function () {
        elem.classList.remove('show');
    }, 350);
};
const animateElm = function (elem, animation, speed, hide) {
    if (!elem || !animation)
        return;
    elem.removeAttribute('hidden');
    if (speed && (speed === 'slow' || speed === 'slower' || speed === 'fast' || speed === 'faster')) {
        elem.classList.add(`animate__${speed}`);
    }
    elem.classList.add(`animate__${animation}`);
    elem.addEventListener('animationend', function endAnimation(event) {
        elem.classList.remove(`animate__${animation}`);
        if (speed && (speed === 'slow' || speed === 'slower' || speed === 'fast' || speed === 'faster')) {
            elem.classList.remove(`animate__${speed}`);
        }
        if (hide) {
            elem.setAttribute('hidden', 'true');
        }
        elem.removeEventListener('animationend', endAnimation, false);
    }, false);
};
const siteAlerts = document.querySelectorAll('.csc-alert');
if (siteAlerts) {
    siteAlerts.forEach(alert => alert.addEventListener(touchEvent, () => {
        if (!alert.hasAttribute('data-closable') ||
            alert.dataset.closable === 'true') {
            alert.style.display = 'none';
        }
    }));
}
const siteBanner = document.querySelector('#csc-banner');
if (siteBanner) {
    siteBanner.addEventListener(touchEvent, () => {
        if (siteBanner.classList.contains('visible'))
            siteBanner.classList.remove('visible');
    });
}
const tableHeaders = document.querySelectorAll('.csc-table-header__title');
if (tableHeaders) {
    tableHeaders.forEach(headerItem => headerItem.addEventListener(touchEvent, () => {
        if (!headerItem.classList.contains('csc-table-header__title--active')) {
            headerItem.classList.remove('csc-table-header__title--active', 'csc-table-header__title--desc');
        }
        else if (!headerItem.classList.contains('csc-table-header__title--desc')) {
            headerItem.classList.add('csc-table-header__title--desc');
        }
        else {
            headerItem.classList.remove('csc-table-header__title--desc');
        }
    }));
}
function toggleCollapsible() {
    const collapsible = this.parentElement;
    if (collapsible) {
        const collapsibleBody = collapsible.querySelector(`.csc-collapsible__body`);
        if (collapsibleBody) {
            if (collapsible.classList.contains('open')) {
                if (collapsibleBody) {
                    jsHide(collapsibleBody);
                }
                collapsible.classList.remove('open');
                collapsible.setAttribute('aria-expanded', 'false');
            }
            else {
                collapsible.classList.add('open');
                if (collapsibleBody) {
                    jsShow(collapsibleBody);
                }
                collapsible.setAttribute('aria-expanded', 'true');
            }
        }
    }
}
function toggleFAQCollapsible() {
    const collapsibleHeader = this;
    const dlElm = collapsibleHeader.parentElement;
    if (collapsibleHeader) {
        const ariaControls = collapsibleHeader.getAttribute('aria-controls');
        if (ariaControls && dlElm) {
            const collapsibleBody = dlElm.querySelector(`#${ariaControls}`);
            if (collapsibleBody) {
                if (collapsibleHeader.classList.contains('open')) {
                    if (collapsibleBody) {
                        jsHide(collapsibleBody);
                    }
                    collapsibleHeader.classList.remove('open');
                    collapsibleHeader.setAttribute('aria-expanded', 'false');
                }
                else {
                    collapsibleHeader.classList.add('open');
                    if (collapsibleBody) {
                        jsShow(collapsibleBody);
                    }
                    collapsibleHeader.setAttribute('aria-expanded', 'true');
                }
            }
        }
    }
}
const tabLinks = document.querySelectorAll('.csc-tab');
let tabItemsFound = false;
if (tabLinks.length > 0) {
    tabLinks.forEach(tabLink => {
        if (tabLink.dataset.ref !== undefined) {
            tabItemsFound = true;
            tabLink.addEventListener('click', function (e) {
                const tabContents = document.querySelectorAll('.csc-tab__content');
                tabContents.forEach(tabContent => {
                    tabContent.style.display = "none";
                });
                tabLinks.forEach(activeLink => {
                    activeLink.classList.remove("csc-tab--active");
                });
                const activeTab = document.querySelector(`#tab__${this.dataset.ref}`);
                if (activeTab) {
                    activeTab.style.display = "block";
                    tabLink.classList.add("csc-tab--active");
                }
            });
        }
    });
    if (tabItemsFound) {
        ready(() => {
            const activeTab = [...tabLinks].filter(el => el.classList.contains('csc-tab--active') !== false);
            if (activeTab && activeTab.length > 0) {
                activeTab[0].click();
            }
            else {
                tabLinks[0].click();
            }
        });
    }
}
ready(() => {
    const { hash } = window.location;
    if (hash) {
        const elm = document.querySelector(hash);
        if (elm) {
            elm.scrollIntoView({
                behavior: 'smooth',
            });
        }
    }
});
function addLabelActive(e) {
    if (e.id) {
        const label = document.querySelector(`label[for=${e.id}]`);
        if (label) {
            label.classList.add('active');
        }
    }
}
function removeLabelActive(e) {
    if (e.id) {
        const label = document.querySelector(`label[for=${e.id}]`);
        if (label) {
            if (e.value.length === 0) {
                label.classList.remove('active');
            }
        }
    }
}
function addLabelListeners(input) {
    if (input) {
        ['change', 'focus'].forEach(evt => input.addEventListener(evt, () => {
            addLabelActive(input);
        }));
        input.addEventListener('blur', () => {
            removeLabelActive(input);
        });
        if (input.value !== '') {
            addLabelActive(input);
        }
    }
}
if ($.validator !== undefined) {
    $.validator.setDefaults({
        ignore: ':hidden:not(select)',
        errorClass: 'invalid',
        errorElement: 'span',
        errorPlacement(errorGroup, elementGroup) {
            Object.keys(errorGroup).forEach((errorKey) => {
                if (typeof errorGroup[errorKey] === 'object')
                    errorGroup[errorKey].classList.add('csc-helper-text');
            });
            Object.keys(elementGroup).forEach((elmKey) => {
                if (typeof elementGroup[elmKey] === 'object') {
                    if (elementGroup[elmKey].type === 'checkbox') {
                        const label = getClosest(elementGroup[elmKey], 'label');
                        if (label) {
                            const parentElement = label.parentNode;
                            if (parentElement)
                                parentElement.insertBefore(errorGroup[elmKey], label.nextSibling);
                        }
                    }
                    else {
                        if (elementGroup[elmKey]) {
                            const parentElement = elementGroup[elmKey].parentNode;
                            if (parentElement)
                                parentElement.insertBefore(errorGroup[elmKey], elementGroup[elmKey].nextSibling);
                        }
                    }
                }
            });
        },
        submitHandler(form) {
            const submitSaveBtn = form
                .querySelectorAll('button[type=submit]')[0]
                .querySelector('i[class^="fa"]');
            if (submitSaveBtn) {
                if (submitSaveBtn.classList.contains('fa-save')) {
                    submitSaveBtn.classList.remove('fa-save');
                    submitSaveBtn.classList.add('fa-circle-notch', 'fa-spin');
                }
            }
            else {
                $('.cs-page').prepend('<div class="csc-loader--full-page"><div class="csc-loader"><div class="text">Loading</div><div class="csc-loader--dots"><div></div><div></div><div></div><div></div></div></div></div>');
            }
            form.submit();
        },
    });
    $.validator.addMethod('pattern', function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
        if (typeof param === 'string') {
            param = new RegExp(`^(?:${param})$`);
        }
        return param.test(value);
    }, 'Invalid format.');
}
if ($.modal !== undefined) {
    $.modal.defaults = {
        showClose: false,
        fadeDuration: 250,
        showSpinner: true,
    };
    $('.modal').on($.modal.OPEN, function (event, modal) {
        const modalElm = event.target;
        let maxHeight = +modalElm.style.height.slice(0, -2);
        const modalElmHeader = modalElm.querySelector('.csc-modal__header');
        if (modalElmHeader) {
            maxHeight -= +modalElmHeader.style.height.slice(0, -2);
        }
        const modalElmActions = modalElm.querySelector('.csc-modal__actions');
        if (modalElmActions) {
            maxHeight -= +modalElmActions.style.height.slice(0, -2);
        }
        const modalElmContent = modalElm.querySelector('.csc-modal__content');
        if (modalElmContent) {
            const modalElmContentHeight = +modalElmContent.style.height.slice(0, -2);
            if (modalElmContentHeight > maxHeight) {
                modalElmContent.style.maxHeight = `${maxHeight}px`;
                if (!modalElmContent.classList.contains('csc-modal--scrollable'))
                    modalElmContent.classList.add('csc-modal--scrollable');
            }
            else {
                modalElmContent.style.maxHeight = `none`;
                if (!modalElmContent.classList.contains('csc-modal--scrollable'))
                    modalElmContent.classList.remove('csc-modal--scrollable');
            }
        }
    });
    $(document).on($.modal.CLOSE, function (event, m) {
        if (m.elm[0].id === '') {
            m.elm.remove();
        }
    });
    $(document).on('click.modal', 'button[rel~="modal:close"]', $.modal.close);
}
$(document).on(touchEvent, '.csc-loader--full-page', function () {
    $(this).fadeOut(500, function () {
        $(this).remove();
    });
});
['change', 'focus'].forEach(evt => document
    .querySelectorAll('input[type=text]:not(.chosen-search-input), input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea')
    .forEach(elem => elem.addEventListener(evt, function () {
    addLabelActive(elem);
})));
document
    .querySelectorAll('input[type=text]:not(.chosen-search-input), input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea')
    .forEach(elem => elem.addEventListener('blur', function () {
    removeLabelActive(elem);
}));
function addAutoResize() {
    document.querySelectorAll('[data-autoresize]').forEach(element => {
        if (element) {
            element.style.boxSizing = 'border-box';
            const offset = element.offsetHeight - element.clientHeight;
            element.addEventListener('input', (event) => {
                const targetElm = event.target;
                if (targetElm) {
                    targetElm.style.height = 'auto';
                    targetElm.style.height = `${targetElm.scrollHeight + offset}px`;
                }
            });
            element.removeAttribute('data-autoresize');
        }
    });
}
addAutoResize();
function characterCounter() {
    document.querySelectorAll('[data-counter]').forEach(element => {
        if (element) {
            let maxLengthValue = element.getAttribute('maxlength');
            let maxLength = typeof maxLengthValue !== typeof undefined &&
                maxLengthValue !== null
                ? +maxLengthValue
                : 100;
            let opacityValue = element.getAttribute('counter-opacity');
            let opacity = typeof opacityValue !== typeof undefined &&
                opacityValue !== null
                ? +opacityValue
                : 0.8;
            let colorValue = element.getAttribute('counter-color');
            let color = typeof colorValue !== typeof undefined && colorValue !== null
                ? colorValue
                : '#363642';
            const textarea = element.nodeName === 'TEXTAREA';
            const settings = {
                max: maxLength,
                opacity,
                color,
                textArea: textarea,
            };
            const characterWrapper = document.createElement('div');
            characterWrapper.classList.add('character-wrap');
            const elmParentNode = element.parentNode;
            if (elmParentNode)
                elmParentNode.insertBefore(characterWrapper, element);
            characterWrapper.appendChild(element);
            characterWrapper.insertAdjacentHTML('beforeend', '<span class="remaining tooltip" title="Characters remaining"></span>');
            const remainingSpan = characterWrapper.querySelector('.remaining');
            const updateCountValue = () => {
                const value = element.value.length;
                const result = settings.max - value;
                if (remainingSpan)
                    remainingSpan.innerText = result.toString();
            };
            updateCountValue();
            element.addEventListener('keyup', updateCountValue);
            element.style.paddingRight = '35px';
            characterWrapper.style.position = 'relative';
            if (remainingSpan) {
                remainingSpan.style.position = 'absolute';
                remainingSpan.style.opacity = settings.opacity.toString();
                remainingSpan.style.color = settings.color.toString();
                remainingSpan.style.right = '10px';
            }
            if (settings.textArea === false) {
                if (remainingSpan) {
                    remainingSpan.style.top = '50%';
                    remainingSpan.style.transform = 'translateY(-50%)';
                }
            }
            else {
                if (remainingSpan) {
                    remainingSpan.style.bottom = '10px';
                }
            }
        }
    });
}
characterCounter();
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
document.querySelectorAll('.cs-pagitems').forEach(select => select.addEventListener('change', function () {
    window.location.href = select.value;
}));
ready(() => {
    if (siteBanner) {
        if (!siteBanner.classList.contains('visible'))
            siteBanner.classList.add('visible');
    }
    if (typeof Waves !== typeof undefined) {
        Waves.attach('[class^=csc-btn]');
        Waves.attach('.csc-icon-btn', ['waves-circle']);
        Waves.attach('.csc-chip');
        Waves.init();
    }
    document
        .querySelectorAll('input[type=text]:not(.chosen-search-input):not(.swal-content__input), input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea')
        .forEach((e) => {
        if (e.value !== '' || e.placeholder !== '') {
            addLabelActive(e);
        }
    });
    if ($.fn.tooltipster !== undefined) {
        $('.tooltip').tooltipster({ contentAsHTML: true });
        $('body').on('mouseenter', '.tooltip:not(.tooltipstered)', function () {
            $(this).tooltipster({ contentAsHTML: true });
        });
    }
    else if (debug) {
        console.error('Tooltipster is not loaded. Please load Tooltipster to enable.');
    }
    try {
        tippy('[data-tippy-content]', {
            allowHTML: true,
            delay: 200,
        });
    }
    catch (error) {
        if (debug)
            console.error('Tippy is not loaded. Please load Tippy.js to enable.');
    }
});
