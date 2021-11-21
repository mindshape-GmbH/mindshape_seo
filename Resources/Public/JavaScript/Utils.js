/**
 * Module: TYPO3/CMS/MindshapeSeo/Utils
 *
 * JavaScript utils
 * @exports TYPO3/CMS/MindshapeSeo/Utils
 */
define([], function () {
    'use strict';

    let Utils = {};

    Utils.init = () => {
        Utils.polyfillParentsFunc();
        Utils.polyfillHide();
        Utils.polyfillShow();
        Utils.polyfillIsVisible();
    }

    Utils.polyfillIsVisible = () => {
        if (!HTMLElement.prototype.isVisible) {
            HTMLElement.prototype.isVisible = function () {
                return !!( this.offsetWidth || this.offsetHeight || this.getClientRects().length );
            }
        }
    }

    Utils.polyfillParentsFunc = () => {
        if (!Element.prototype.parents) {
            Element.prototype.parents = function (selector) {
                let elements = [];
                let el = this;
                let ishaveselector = selector !== undefined;

                while ((el = el.parentElement) !== null) {
                    if (el.nodeType !== Node.ELEMENT_NODE) {
                        continue;
                    }

                    if (!ishaveselector || el.matches(selector)) {
                        elements.push(el);
                    }
                }
                return elements.length === 1 ? elements[0] : elements;
            }
        }
    }

    Utils.polyfillHide = () => {
        if (!Element.prototype.hideMe) {
            Element.prototype.hideMe = function () {
                this.style.display = 'none';
            }
        }
    }
    Utils.polyfillShow = () => {
        if (!Element.prototype.showMe) {
            Element.prototype.showMe = function (display = 'block') {
                this.style.display = display;
            }
        }
    }

    Utils.slideUp = (target, duration=500) => {
        target.style.transitionProperty = 'height, margin, padding';
        target.style.transitionDuration = duration + 'ms';
        target.style.boxSizing = 'border-box';
        target.style.height = target.offsetHeight + 'px';
        target.offsetHeight;
        target.style.overflow = 'hidden';
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
        window.setTimeout( () => {
            target.style.display = 'none';
            target.style.removeProperty('height');
            target.style.removeProperty('padding-top');
            target.style.removeProperty('padding-bottom');
            target.style.removeProperty('margin-top');
            target.style.removeProperty('margin-bottom');
            target.style.removeProperty('overflow');
            target.style.removeProperty('transition-duration');
            target.style.removeProperty('transition-property');
            //alert("!");
        }, duration);
    }

    Utils.slideDown = (target, duration=500) => {
        target.style.removeProperty('display');
        let display = window.getComputedStyle(target).display;

        if (display === 'none')
            display = 'block';

        target.style.display = display;
        let height = target.offsetHeight;
        target.style.overflow = 'hidden';
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
        target.offsetHeight;
        target.style.boxSizing = 'border-box';
        target.style.transitionProperty = "height, margin, padding";
        target.style.transitionDuration = duration + 'ms';
        target.style.height = height + 'px';
        target.style.removeProperty('padding-top');
        target.style.removeProperty('padding-bottom');
        target.style.removeProperty('margin-top');
        target.style.removeProperty('margin-bottom');
        window.setTimeout( () => {
            target.style.removeProperty('height');
            target.style.removeProperty('overflow');
            target.style.removeProperty('transition-duration');
            target.style.removeProperty('transition-property');
        }, duration);
    }
    Utils.slideToggle = (target, duration = 500) => {
        if (window.getComputedStyle(target).display === 'none') {
            return Utils.slideDown(target, duration);
        } else {
            return Utils.slideUp(target, duration);
        }
    }

    Utils.init();

    return Utils;
});