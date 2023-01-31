/**
 /**
 * Module: TYPO3/CMS/MindshapeSeo/PreviewModule
 *
 * JavaScript logic for the SEO preview module and for the preview field in the editing form
 *
 * @exports TYPO3/CMS/MindshapeSeo/PreviewModule
 */
define([
    'TYPO3/CMS/MindshapeSeo/Utils',
    'TYPO3/CMS/MindshapeSeo/Event',
    'TYPO3/CMS/Core/Ajax/AjaxRequest',
    'bootstrap',
], function (Utils, Event, AjaxRequest, bootstrap) {
    'use strict';

    let PreviewModule = {
        numberOfSeoChecks: 6,
        googleTitleLengthPixel: 580,
        googleDescriptionLengthPixel: 920,
        googleDescriptionMinLengthPixel: 300,
        googleDescriptionFontSize: '13px',
        googleTitleFontSize: '18px',
        googleFontFamily: 'arial,sans-serif',
        googleEllipsis: ' ...',
        typo3version: '',
        previewContainers: {},
        canvasRenderingContext: {},
        editing: true
    };

    PreviewModule.init = function () {
        this.typo3version = document.querySelector('.mindshapeseo-preview')?.getAttribute('data-typo3-version') ?? '';
        this.canvasRenderingContext = document.createElement('canvas').getContext('2d');
        this.previewContainers = document.querySelectorAll('.google-preview');

        this.previewContainers.forEach(element => {
            this.editing = 0 < parseInt(element.getAttribute('data-editing'));
            this.renderPreviewDescription(element);

            if (this.editing) {
                this.checkFocusKeyword(element, element.querySelector('.focus-keyword input').value);
            } else {
                this.checkFocusKeyword(element, document.querySelector('#focusKeyword').value);
            }

            if (this.editing) {
                this.updatePreviewAlerts(element);
                this.updatePreviewEditPanelProgressBar(element, 'title', this.googleTitleLengthPixel);
                this.updatePreviewEditPanelProgressBar(element, 'description', this.googleDescriptionLengthPixel);
            } else {
                this.updatePreviewAlerts(element, document.querySelector('#focusKeyword'), document.querySelector('textarea[name="data[pages][' + element.querySelector('input[name="pageUid"]').value + '][description]"], textarea[name="data[pages_language_overlay][' + element.querySelector('input[name="pageUid"]').value + '][description]"]'));
            }
        });

        this.registerEvents();
    }

    PreviewModule.registerEvents = function () {
        let depthChange = document.querySelector('#depthselect');
        let depthChangeForm = document.querySelector('#depthselect-form');
        if (depthChange !== null && depthChangeForm !== null) {
            depthChange.addEventListener('change', function (e) {
                depthChangeForm.submit();
            });
        }

        // Edit click on google preview
        this.previewContainers.forEach(previewContainer => {
           Event.delegate('click', '.edit', function (element, e) {
               e.preventDefault();

               let currentPreview = element.parents('.google-preview');
               let currentEditPanel = currentPreview.querySelector('.edit-panel');
               let focusKeywordMetadataPreview = element.parents('.page').querySelector('.focus-keyword-container .focus-keyword');

               if (!currentEditPanel.isVisible()) {
                   PreviewModule.openPreviewEditPanel(currentPreview);
               } else {
                   PreviewModule.closePreviewEditPanel(currentPreview);
                   PreviewModule.restorePreviewOriginalData(currentPreview);
                   PreviewModule.checkAndChangeIndexPreview(currentPreview);

                   let focusKeyword = currentPreview.querySelector('.focus-keyword input').value ?? '';

                   PreviewModule.checkPreviewEditPanelSaveState(currentPreview);
                   PreviewModule.updatePreviewEditPanelProgressBar(currentPreview, 'title', PreviewModule.googleTitleLengthPixel);
                   PreviewModule.updatePreviewEditPanelProgressBar(currentPreview, 'description', PreviewModule.googleDescriptionLengthPixel);

                   if (0 < focusKeyword.trim().length) {
                       PreviewModule.checkFocusKeyword(currentPreview, focusKeyword);

                   }

                   if ('' === focusKeyword) {
                       focusKeywordMetadataPreview.innerHTML = 'n/a';
                       focusKeywordMetadataPreview.classList.add('focus-keyword-na');
                   } else {
                       focusKeywordMetadataPreview.classList.remove('focus-keyword-na');
                       focusKeywordMetadataPreview.innerHTML = focusKeyword;
                   }

                   PreviewModule.updatePreviewAlerts(currentPreview);
               }
           }, previewContainer);
        });

        // Save click on edit panel
        this.previewContainers.forEach(previewContainer => {
            Event.delegate('click', '.save', function (element, e) {
                e.preventDefault();
                PreviewModule.savePreviewEditPanel(element.parents('.google-preview'));
            }, previewContainer)
        });

        // Show SEO alerts
        this.previewContainers.forEach(previewContainer => {
            Event.delegate('click', '.alerts', function (element, e) {
                e.preventDefault();

                let alertsContainer = element.parents('.google-preview').querySelector('.alerts-container');

                if (!alertsContainer.isVisible()) {
                    Utils.slideDown(alertsContainer);
                } else {
                    Utils.slideUp(alertsContainer);
                }
            }, previewContainer)
        });

        this.previewContainers.forEach(previewContainer => {
            Event.delegate('click', 'input[type="checkbox"]', function (element) {
                let currentPreview = element.parents('.google-preview');

                PreviewModule.checkAndChangeIndexPreview(currentPreview);
                PreviewModule.checkPreviewEditPanelSaveState(currentPreview);
            }, previewContainer)
        });

        // Change preview title when editing title
        this.previewContainers.forEach(previewContainer => {
            Event.delegate('keyup', '.edit-panel .title', function (element) {
                let currentPreview = element.parents('.google-preview');
                let focusKeyword = currentPreview.querySelector('.focus-keyword input').value.trim();
                let seoTitleField = currentPreview.querySelector('.edit-panel .seo-title')

                if (seoTitleField.value.trim().length === 0) {
                    currentPreview.querySelector('.preview-box .title').innerHTML = PreviewModule.escapeHtml(element.value);
                    PreviewModule.updatePreviewEditPanelProgressBar(currentPreview, 'title', PreviewModule.googleTitleLengthPixel);
                    PreviewModule.updatePreviewAlerts(currentPreview);

                    if (0 < focusKeyword.length) {
                        PreviewModule.checkFocusKeyword(currentPreview, focusKeyword);
                    }

                    if (PreviewModule.editing) {
                        PreviewModule.checkPreviewEditPanelSaveState(currentPreview);
                    }
                }
            }, previewContainer)
        });

        // Change preview title when editing seo-title
        this.previewContainers.forEach(previewContainer => {
            Event.delegate('keyup', '.edit-panel .seo-title', function (element) {
                let currentPreview = element.parents('.google-preview');
                let focusKeyword = currentPreview.querySelector('.focus-keyword input').value.trim();
                let title = '';
                let titleContainer = previewContainer.querySelector('.edit-panel .title-container');
                let titleProgressBar = titleContainer.querySelector('.progress-title');
                let seoTitleContainer = previewContainer.querySelector('.edit-panel .seo-title-container');
                let seoTitleProgressBar = seoTitleContainer.querySelector('.progress-title');

                if (element.value.trim().length === 0) {
                    title = element.closest('.google-preview').querySelector('.edit-panel .title').value.trim();

                    if (titleProgressBar === null) {
                        titleContainer.appendChild(seoTitleProgressBar);
                    }
                } else {
                    title = element.value.trim();

                    if (seoTitleProgressBar === null) {
                        seoTitleContainer.appendChild(titleProgressBar);
                    }
                }

                currentPreview.querySelector('.preview-box .title').innerHTML = PreviewModule.escapeHtml(title);
                PreviewModule.updatePreviewEditPanelProgressBar(currentPreview, 'title', PreviewModule.googleTitleLengthPixel);
                PreviewModule.updatePreviewAlerts(currentPreview);

                if (0 < focusKeyword.length) {
                    PreviewModule.checkFocusKeyword(currentPreview, focusKeyword);
                }

                if (PreviewModule.editing) {
                    PreviewModule.checkPreviewEditPanelSaveState(currentPreview);
                }
            }, previewContainer)
        });

        // Change preview description when editing description
        this.previewContainers.forEach(previewContainer => {
            Event.delegate('keyup', '.edit-panel .description', function (element, e) {
                let currentPreview = element.parents('.google-preview');
                let focusKeyword = currentPreview.querySelector('.focus-keyword input').value.trim();

                currentPreview.querySelector('.preview-box .description').innerHTML = PreviewModule.escapeHtml(element.value);
                PreviewModule.renderPreviewDescription(currentPreview);
                PreviewModule.updatePreviewEditPanelProgressBar(currentPreview, 'description', PreviewModule.googleDescriptionLengthPixel);

                if (0 < focusKeyword.length) {
                    PreviewModule.checkFocusKeyword(currentPreview, focusKeyword);
                }

                PreviewModule.updatePreviewAlerts(currentPreview);

                if (PreviewModule.editing) {
                    PreviewModule.checkPreviewEditPanelSaveState(currentPreview);
                }
            }, previewContainer)
        });

        if (!this.editing) {
            let tcaForm = document.querySelector('form');
            let currentPreview = document.querySelector('.google-preview');
            let currentPageUid = currentPreview.querySelector('input[name="pageUid"]').value;
            let titleField = tcaForm.querySelector('input[data-formengine-input-name="data[pages][' + currentPageUid + '][title]"], input[data-formengine-input-name="data[pages_language_overlay][' + currentPageUid + '][title]"]');
            let seoTitleField = tcaForm.querySelector('input[data-formengine-input-name="data[pages][' + currentPageUid + '][seo_title]"], input[data-formengine-input-name="data[pages_language_overlay][' + currentPageUid + '][seo_title]"]');
            let descriptionField = tcaForm.querySelector('textarea[name="data[pages][' + currentPageUid + '][description]"], textarea[name="data[pages_language_overlay][' + currentPageUid + '][description]"]');

            titleField.addEventListener('keyup', function () {
                const focusKeyword = document.querySelector('#focusKeyword').value.trim();

                // Only update preview title with page title if no explicit seo title is set
                if (seoTitleField.value.trim().length === 0) {
                    currentPreview.querySelector('.preview-box .title').innerHTML = (this.value);

                    if (0 < focusKeyword.length) {
                        PreviewModule.checkFocusKeyword(currentPreview, focusKeyword);
                    }

                    PreviewModule.updatePreviewAlerts(currentPreview, document.querySelector('#focusKeyword'), descriptionField);
                }
            });

            seoTitleField.addEventListener('keyup', function () {
                const focusKeyword = document.querySelector('#focusKeyword').value.trim();
                let title = this.value.trim();

                if (this.value.trim().length === 0) {
                    title = titleField.value.trim();
                }

                currentPreview.querySelector('.preview-box .title').innerHTML = title;

                if (0 < focusKeyword.length) {
                    PreviewModule.checkFocusKeyword(currentPreview, focusKeyword);
                }

                PreviewModule.updatePreviewAlerts(currentPreview, document.querySelector('#focusKeyword'), descriptionField);
            });

            descriptionField.addEventListener('keyup', function () {
                const focusKeyword = document.querySelector('#focusKeyword').value.trim();

                currentPreview.querySelector('.preview-box .description').innerHTML = this.value;
                PreviewModule.renderPreviewDescription(currentPreview);

                if (0 < focusKeyword.length) {
                    PreviewModule.checkFocusKeyword(currentPreview, focusKeyword);
                }

                PreviewModule.updatePreviewAlerts(currentPreview, document.querySelector('#focusKeyword'), this);
            });
        }

        // Update focus keyword check
        Event.delegate('keyup', '.focus-keyword input, #focusKeyword', function (element) {
            let currentPreview = {};

            if (PreviewModule.editing) {
                currentPreview = element.parents('.google-preview');
            } else {
                currentPreview = document.querySelector('.google-preview');
            }

            let focusKeyword = element.value.trim();

            PreviewModule.checkFocusKeyword(currentPreview, focusKeyword);

            if (PreviewModule.editing) {
                let focusKeywordMetadataPreview = currentPreview.parents('.page').querySelector('.focus-keyword-container .focus-keyword');

                if ('' === focusKeyword) {
                    focusKeywordMetadataPreview.innerHTML = 'n/a';
                    focusKeywordMetadataPreview.classList.add('focus-keyword-na');
                } else {
                    focusKeywordMetadataPreview.classList.remove('focus-keyword-na');
                    focusKeywordMetadataPreview.innerHTML = focusKeyword;
                }

                PreviewModule.updatePreviewAlerts(currentPreview);
                PreviewModule.checkPreviewEditPanelSaveState(currentPreview);
            } else {
                PreviewModule.updatePreviewAlerts(
                    currentPreview,
                    document.querySelector('#focusKeyword'),
                    document.querySelector('textarea[name="data[pages][' + currentPreview.querySelector('input[name="pageUid"]').value + '][description]"], textarea[name="data[pages_language_overlay][' + currentPreview.querySelector('input[name="pageUid"]').value + '][description]"]')
                );
            }
        });

        Event.delegate('click', '.info, .progress-seo-check', function (element, e) {
            e.preventDefault();

            let parent = {};

            if (PreviewModule.editing) {
                parent = element.parents('.page');
            } else {
                parent = document.querySelector('form');
            }

            let modal = document.querySelector('#msh-modal');

            modal.querySelector('.modal-body').innerHTML = parent.querySelector('.google-preview .alerts-container').innerHTML;
            if ('10' === PreviewModule.typo3version) {
                window.$(modal).modal();
            } else {
                bootstrap.Modal.getOrCreateInstance(modal).show();
            }
        });
    }

    PreviewModule.savePreviewEditPanel = function (previewContainer) {
        if (!this.editing) {
            return;
        }

        previewContainer.querySelector('.edit-panel .save').disabled = true;

        let data = {};
        let request = new AjaxRequest(TYPO3.settings.ajaxUrls['MindshapeSeoAjaxHandler::savePage']);
        let formData = new FormData(previewContainer.querySelector('form'));

        for (let key of formData.keys()) {
            // the checkboxes of the first previewContainer has  a hidden input then wrong value is saved
            if (formData.getAll(key).length > 1) {
                let _data = formData.getAll(key).filter(el => {
                    return el !== '';
                })
                data[key] = _data[0];
            } else {
                data[key] = formData.get(key);
            }
        }

        let promise = request.post(data, {
            headers: {
                'Content-Type': 'application/json; charset=utf-8'
            }
        });

        promise.then(async function (response) {
            const responseData = await response.resolve();

            if (responseData.saved !== true) {
                previewContainer.querySelector('.icon-provider-fontawesome-error').showMe();
            } else {
                previewContainer.setAttribute('data-original-title', previewContainer.querySelector('.edit-panel .title').value.trim());
                previewContainer.setAttribute('data-original-seo-title', previewContainer.querySelector('.edit-panel .seo-title').value.trim());
                previewContainer.setAttribute('data-original-description', previewContainer.querySelector('.edit-panel .description').value.trim());
                previewContainer.setAttribute('data-original-focuskeyword', previewContainer.querySelector('.edit-panel .focus-keyword input').value.trim());

                PreviewModule.checkAndChangeIndexPreview(previewContainer, true);

                PreviewModule.checkPreviewEditPanelSaveState(previewContainer);
                PreviewModule.closePreviewEditPanel(previewContainer);
            }

        }, function (error) {
            previewContainer.querySelector('.icon-provider-fontawesome-error').showMe();
        });
    }

    PreviewModule.checkPreviewEditPanelSaveState = function (previewContainer) {
        let title = previewContainer.querySelector('.edit-panel .title').value;
        let seoTitle = previewContainer.querySelector('.edit-panel .seo-title').value;
        let description = previewContainer.querySelector('.edit-panel .description').value;
        let focusKeyword = previewContainer.querySelector('.focus-keyword input').value;
        let noindex = previewContainer.querySelector('.noindex input[type="checkbox"]').checked;
        let nofollow = previewContainer.querySelector('.nofollow input[type="checkbox"]').checked;

        if (
            0 < title.length &&
            (
                previewContainer.getAttribute('data-original-title') !== title.trim() ||
                previewContainer.getAttribute('data-original-seo-title') !== seoTitle.trim() ||
                previewContainer.getAttribute('data-original-description') !== description.trim() ||
                previewContainer.getAttribute('data-original-focuskeyword') !== focusKeyword.trim() ||
                0 < parseInt(previewContainer.getAttribute('data-original-noindex')) !== noindex ||
                0 < parseInt(previewContainer.getAttribute('data-original-nofollow')) !== nofollow
            )
        ) {
            previewContainer.querySelector('button.save').disabled = false;
            previewContainer.querySelector('.edit-panel .title-container').classList.remove('has-error');
        } else {
            previewContainer.querySelector('button.save').disabled = true;

            if (0 === title.length) {
                previewContainer.querySelector('.edit-panel .title-container').classList.add('has-error')
            }
        }
    }

    PreviewModule.checkAndChangeIndexPreview = function (previewContainer, setOriginalData) {
        let $noindexPreview = previewContainer.parents('.page').querySelector('.robots .noindex');
        let $nofollowPreview = previewContainer.parents('.page').querySelector('.robots .nofollow');

        if (previewContainer.querySelector('.edit-panel .noindex input[type="checkbox"]').checked) {
            if (setOriginalData) {
                previewContainer.setAttribute('data-original-noindex', 1);
            }

            if (this.editing) {
                $noindexPreview.innerHTML = 'noindex,';
                $noindexPreview.classList.add('danger');
            }
        } else {
            if (setOriginalData) {
                previewContainer.setAttribute('data-original-noindex', 0);
            }

            if (this.editing) {
                $noindexPreview.innerHTML = 'index,';
                $noindexPreview.classList.remove('danger');
            }
        }

        if (previewContainer.querySelector('.edit-panel .nofollow input[type="checkbox"]').checked) {
            if (setOriginalData) {
                previewContainer.setAttribute('data-original-nofollow', 1);
            }

            if (this.editing) {
                $nofollowPreview.innerHTML = 'nofollow';
                $nofollowPreview.classList.add('danger');
            }
        } else {
            if (setOriginalData) {
                previewContainer.setAttribute('data-original-nofollow', 0);
            }

            if (this.editing) {
                $nofollowPreview.innerHTML = 'follow';
                $nofollowPreview.classList.remove('danger');
            }
        }
    }

    PreviewModule.restorePreviewOriginalData = function (previewContainer) {
        const originalTitle = previewContainer.getAttribute('data-original-title');
        const originalSeoTitle = previewContainer.getAttribute('data-original-seo-title');
        const originalDescription = previewContainer.getAttribute('data-original-description');

        previewContainer.querySelector('.preview-box .title').innerHTML = originalSeoTitle.length > 0 ? originalSeoTitle : originalTitle;
        previewContainer.querySelector('.preview-box .description').innerHTML = previewContainer.getAttribute('data-original-description');
        previewContainer.querySelector('.edit-panel .title').value = originalTitle;
        previewContainer.querySelector('.edit-panel .seo-title').value = originalSeoTitle;
        previewContainer.querySelector('.edit-panel .description').value = originalDescription;

        this.renderPreviewDescription(previewContainer);

        previewContainer.querySelector('.edit-panel .noindex input[type="checkbox"]').checked = 0 < parseInt(previewContainer.getAttribute('data-original-noindex'));
        previewContainer.querySelector('.edit-panel .nofollow input[type="checkbox"]').checked = 0 < parseInt(previewContainer.getAttribute('data-original-nofollow'));
        previewContainer.querySelector('.edit-panel .focus-keyword input').value = previewContainer.getAttribute('data-original-focuskeyword');

        if (this.editing) {
            previewContainer.parents('.page')
                .querySelector('.focus-keyword-container .focus-keyword')
                .innerHTML = previewContainer.getAttribute('data-original-focuskeyword');
        }
    }

    PreviewModule.closePreviewEditPanel = function (previewContainer) {
        Utils.slideUp(previewContainer.querySelector('.edit-panel'))
        previewContainer.querySelector('button.save').showMe();
        previewContainer.querySelector('button.edit .edit-text').showMe();
        previewContainer.querySelector('button.edit .abort-text').hideMe();
    }

    PreviewModule.openPreviewEditPanel = function (previewContainer) {
        Utils.slideDown(previewContainer.querySelector('.edit-panel'));
        previewContainer.querySelector('button.save').showMe();
        previewContainer.querySelector('button.edit .edit-text').hideMe();
        previewContainer.querySelector('button.edit .abort-text').showMe();
    }

    PreviewModule.updatePreviewEditPanelProgressBar = function (previewContainer, fieldName, maxLength) {
        let fieldText = '';

        if ('title' === fieldName) {
            fieldText = previewContainer.querySelector('.preview-box h3').innerText;
            fieldText.replace(/\n/, ' ')
        } else {
            fieldText = previewContainer.querySelector('.edit-panel .description').value;
        }

        let percent = 0;
        let progressbarStatusClass = 'progress-bar-';
        let fieldLength = this.calcStringPixelLength(
            fieldText.trim(),
            this.googleFontFamily,
            fieldName === 'description' ?
                this.googleDescriptionFontSize :
                this.googleTitleFontSize
        );

        maxLength = parseInt(maxLength);

        if (fieldLength >= maxLength) {
            percent = 100;
        } else {
            percent = 100 / maxLength * fieldLength;
        }

        if (percent >= 100) {
            progressbarStatusClass += 'danger';
        } else if (percent >= 70) {
            progressbarStatusClass += 'warning';
        } else {
            progressbarStatusClass += 'success';
        }

        let progressbar = previewContainer.querySelector('.edit-panel .progress-' + fieldName + ' .progress-bar');
        if (progressbar !== null) {
            progressbar.style.width = percent + '%';
            progressbar.classList.remove('progress-bar-danger');
            progressbar.classList.remove('progress-bar-warning');
            progressbar.classList.remove('progress-bar-success');
            progressbar.classList.add(progressbarStatusClass);
        }
    }

    PreviewModule.updatePreviewAlerts = function (previewContainer, focusKeywordInput, descriptionInput) {
        let titleLengthPixel = this.calcStringPixelLength(previewContainer.querySelector('.preview-box h3').innerText, this.googleFontFamily, this.googleTitleFontSize);
        let description = '';

        if ('undefined' !== typeof descriptionInput) {
            description = descriptionInput.value;
        } else {
            description = previewContainer.querySelector('.edit-panel .description').value.trim();
        }

        let descriptionLengthPixel = this.calcStringPixelLength(description, this.googleFontFamily, this.googleDescriptionFontSize);
        let focusKeyword = 'undefined' !== typeof focusKeywordInput ? focusKeywordInput.value.trim() : previewContainer.querySelector('.focus-keyword input').value.trim();

        let alertsContainer = previewContainer.querySelector('.alerts-container');
        let alertsCounter = 0;

        if (titleLengthPixel > this.googleTitleLengthPixel) {
            alertsContainer.querySelector('.title-length').showMe();
            alertsCounter++;
        } else {
            alertsContainer.querySelector('.title-length').hideMe();
        }

        if (descriptionLengthPixel > this.googleDescriptionLengthPixel) {
            alertsContainer.querySelector('.description-length').showMe();
            alertsCounter++;
        } else {
            alertsContainer.querySelector('.description-length').hideMe();
        }

        if (descriptionLengthPixel === 0) {
            alertsContainer.querySelector('.description-empty').showMe();
            alertsContainer.querySelector('.description-min-length').hideMe();
            alertsCounter++;
        } else {
            alertsContainer.querySelector('.description-empty').hideMe();

            if (descriptionLengthPixel < this.googleDescriptionMinLengthPixel) {
                alertsContainer.querySelector('.description-min-length').showMe();
                alertsCounter++;
            } else {
                alertsContainer.querySelector('.description-min-length').hideMe();
            }
        }

        if (0 === focusKeyword.length) {
            alertsContainer.querySelector('.focus-keyword-missing').showMe();
            alertsContainer.querySelector('.focus-keyword').hideMe();
            alertsContainer.querySelector('.focus-keyword.missing-description').hideMe();
            alertsContainer.querySelector('.focus-keyword.missing-url').hideMe();
            alertsContainer.querySelector('.focus-keyword.found-title').hideMe();
            alertsContainer.querySelector('.focus-keyword.found-description').hideMe();
            alertsContainer.querySelector('.focus-keyword.found-url').hideMe();
            alertsCounter++;
        } else {
            alertsContainer.querySelector('.focus-keyword-missing').hideMe();
            if (0 < parseInt(previewContainer.getAttribute('data-keyword-title-matches'))) {
                alertsContainer.querySelector('.focus-keyword.missing-title').hideMe();
                alertsContainer.querySelector('.focus-keyword.found-title').showMe();
            } else {
                alertsContainer.querySelector('.focus-keyword.missing-title').showMe();
                alertsContainer.querySelector('.focus-keyword.found-title').hideMe();
                alertsCounter++;
            }

            if (0 < parseInt(previewContainer.getAttribute('data-keyword-description-matches'))) {
                alertsContainer.querySelector('.focus-keyword.missing-description').hideMe();
                alertsContainer.querySelector('.focus-keyword.found-description').showMe();
            } else {
                alertsContainer.querySelector('.focus-keyword.missing-description').showMe();
                alertsContainer.querySelector('.focus-keyword.found-description').hideMe();
                alertsCounter++;
            }

            if (0 < parseInt(previewContainer.getAttribute('data-keyword-url-matches'))) {
                alertsContainer.querySelector('.focus-keyword.missing-url').hideMe();
                alertsContainer.querySelector('.focus-keyword.found-url').showMe();
            } else {
                alertsContainer.querySelector('.focus-keyword.missing-url').showMe();
                alertsContainer.querySelector('.focus-keyword.found-url').hideMe();
                alertsCounter++;
            }
        }

        alertsContainer.querySelectorAll('li').forEach(_element => {
            if (_element.classList.contains('first-visible')) _element.classList.remove('first-visible');
            if (_element.classList.contains('last-visible')) _element.classList.remove('last-visible');
        });

        let visibleElements = Array.from(previewContainer.querySelectorAll('.alerts-container li')).filter(function (el) {
            return el.style.display !== 'none';
        });

        if (visibleElements.length > 0) {
            visibleElements[0].classList.add('first-visible');
            visibleElements[visibleElements.length -1 ].classList.add('last-visible');
        }


        let seoCheckParent = {};

        if (this.editing) {
            seoCheckParent = previewContainer.parents('.page');
        } else {
            seoCheckParent = document.querySelector('form');
        }

        seoCheckParent.querySelector('.seo-check .alerts').innerHTML = alertsCounter;

        if (alertsCounter > 0) {
            seoCheckParent.querySelector('.seo-check .no-error').hideMe();
            seoCheckParent.querySelector('.seo-check .error').showMe('inline-block');
        } else {
            seoCheckParent.querySelector('.seo-check .no-error').showMe('inline-block');
            seoCheckParent.querySelector('.seo-check .error').hideMe();
        }
    }

    PreviewModule.renderPreviewDescription = function (previewContainer) {
        let description = this.escapeHtml(previewContainer.querySelector('.preview-box .description').innerText.trim());

        if (this.googleDescriptionLengthPixel < this.calcStringPixelLength(description, this.googleFontFamily, this.googleDescriptionFontSize)) {
            let invalidLastChar = function (description) {
                return description.slice(-1).match(/(\s|\.)/);
            };

            while (this.googleDescriptionLengthPixel < this.calcStringPixelLength(description, this.googleFontFamily, this.googleDescriptionFontSize)) {
                description = description.split(' ');

                if (description.length > 1) {
                    description.pop();
                    description = description.join(' ');
                } else {
                    description = description.join(' ');
                    description = description.slice(0, -1);
                }
            }

            while (invalidLastChar(description)) {
                description = description.slice(0, -1);
            }

            description += this.googleEllipsis;
        }

        previewContainer.querySelector('.preview-box .description').innerHTML = description;
    }

    PreviewModule.checkFocusKeyword = function (previewContainer, focusKeyword) {
        this.renderPreviewDescription(previewContainer);
        this.clearPreviewTitle(previewContainer);
        this.clearUrlTitle(previewContainer);

        if ('' === focusKeyword) {
            return;
        }

        let title = this.escapeHtml(previewContainer.querySelector('.preview-box .title').innerText);
        let description = this.escapeHtml(previewContainer.querySelector('.preview-box .description').innerText);
        let urlPathElement = previewContainer.querySelector('.preview-box .url .path');
        let urlPath = urlPathElement instanceof HTMLElement ? urlPathElement.innerText : '';
        let regex = new RegExp('(^|\\.|\\,|\\?|\\!|\\/|\\#|\\+|\\s)(' + this.escapeHtml(focusKeyword.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/ig, '\\$&').trim()) + ')(\\s|\\.|\\,|\\?|\\!|\\/|\\#|\\+|$)', 'igm');
        let titleMatches = title.match(regex);
        let descriptionMatches = description.match(regex);
        let urlMatches = urlPath.match(regex);

        if (null === titleMatches) {
            previewContainer.setAttribute('data-keyword-title-matches', 0);
        } else {
            previewContainer.querySelector('.preview-box .title').innerHTML = title.replace(regex, '$1<span class="focus-keyword">$2</span>$3');

            previewContainer.setAttribute('data-keyword-title-matches', titleMatches.length);
        }

        if (null === descriptionMatches) {
            previewContainer.setAttribute('data-keyword-description-matches', 0);
        } else {
            previewContainer.querySelector('.preview-box .description').innerHTML = description.replace(regex, '$1<span class="focus-keyword">$2</span>$3');

            previewContainer.setAttribute('data-keyword-description-matches', descriptionMatches.length);
        }

        if (null === urlMatches) {
            previewContainer.setAttribute('data-keyword-url-matches', 0);
        } else {
            if (urlPathElement instanceof HTMLElement) {
                urlPathElement.innerHTML = urlPath.replace(regex, '$1<span class="focus-keyword">$2</span>$3');
            }

            previewContainer.setAttribute('data-keyword-url-matches', urlMatches.length);
        }
    }

    PreviewModule.clearPreviewTitle = function (previewContainer) {
        previewContainer.querySelector('.preview-box .title').innerHTML = previewContainer.querySelector('.preview-box .title').innerText.trim();
    }

    PreviewModule.clearUrlTitle = function (previewContainer) {
        let urlPathElement = previewContainer.querySelector('.preview-box .url cite .path');

        if (urlPathElement instanceof HTMLElement) {
            urlPathElement.innerHTML = urlPathElement.innerText.trim();
        }

    }

    PreviewModule.calcStringPixelLength = function (text, fontFamily, fontSize) {
        this.canvasRenderingContext.font = fontSize + ' ' + fontFamily;

        return parseInt(this.canvasRenderingContext.measureText(text).width);
    }

    PreviewModule.escapeHtml = function (text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    return PreviewModule;
});