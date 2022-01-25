/**
 * Module: TYPO3/CMS/MindshapeSeo/SettingsModule
 *
 * JavaScript logic for the SEO settings module
 *
 * @exports TYPO3/CMS/MindshapeSeo/SettingsModule
 */
define([
    'TYPO3/CMS/Backend/Severity',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/Core/Ajax/AjaxRequest',
    'TYPO3/CMS/MindshapeSeo/Utils'
], function (Severity, Modal, AjaxRequest, Utils) {
    'use strict';

    let SettingsModule = {
        selectors: {
            configurationForm: '#mindshape-seo-configuration',
            saveButton: '.mindshape-seo-savebutton',
            deleteButton: '.mindshape-seo-deletebutton',
            jsonldCheckbox: '#addJsonld',
            jsonld: '#jsonld',
            jsonldTypeSelect: '.type-select',
            jsonldLogo: 'fieldset.logo',
            jsonldLogoDelete: '.mindshape-seo-delete',
            jsonldLogoContainer: '.mindshape-seo-upload'
        }
    };

    SettingsModule.init = function () {
        let saveButton = document.querySelector(SettingsModule.selectors.saveButton);
        let deleteButton = document.querySelector(SettingsModule.selectors.deleteButton);

        if (saveButton !== null) {
            saveButton.addEventListener('click', SettingsModule.saveConfig);
        }

        if (deleteButton !== null) {
            deleteButton.addEventListener('click', SettingsModule.deleteConfig);
        }

        SettingsModule.initJsonldLogic();
    };

    SettingsModule.initJsonldLogic = function () {
        let jsonld = document.querySelector(SettingsModule.selectors.jsonld);
        let addJsonldCheckbox = document.querySelector(SettingsModule.selectors.jsonldCheckbox);
        let jsonldTypeSelect = document.querySelector(SettingsModule.selectors.jsonldTypeSelect);
        let jsonldLogo = document.querySelector(SettingsModule.selectors.jsonldLogo);
        let jsonldLogoDelete = document.querySelector(SettingsModule.selectors.jsonldLogoDelete);

        if (jsonldLogoDelete !== null) {
            jsonldLogoDelete.addEventListener('click', function (e) {
                e.preventDefault();

                let uploadContainer =jsonld.querySelector(SettingsModule.selectors.jsonldLogoContainer);
                if (uploadContainer !== null) {
                    uploadContainer.querySelector('input[type="hidden"]').remove();
                    uploadContainer.querySelector('.image').remove();
                }
            });
        }

        if (jsonld !== null && addJsonldCheckbox !== null && jsonldTypeSelect !== null) {
            if (jsonldTypeSelect.value === 'Organization') {
                jsonldLogo.style.display = 'block';
            }

            jsonldTypeSelect.addEventListener('change', function (e) {
                if (jsonldTypeSelect.value === 'Organization') {
                    Utils.slideDown(jsonldLogo);
                } else {
                    Utils.slideUp(jsonldLogo);
                }
            });

            if (addJsonldCheckbox.checked) {
                jsonld.style.display = 'block';
            }

            addJsonldCheckbox.addEventListener('click', function (e) {
                Utils.slideToggle(jsonld);
            })
        }
    }

    /**
     *
     * @param {PointerEvent} e
     */
    SettingsModule.saveConfig = function (e) {
        e.preventDefault();
        let form = document.querySelector(SettingsModule.selectors.configurationForm);
        if (form !== null) form.submit();
    }

    /**
     *
     * @param {PointerEvent} e
     */
    SettingsModule.deleteConfig = function (e) {
        e.preventDefault();

        let deleteButton = e.currentTarget;
        let title = deleteButton.hasAttribute('title') ? deleteButton.getAttribute('title') : '';
        let message = deleteButton.hasAttribute('data-message') ? deleteButton.getAttribute('data-message') : '';
        let abortLabel = deleteButton.hasAttribute('data-label-abort') ? deleteButton.getAttribute('data-label-abort') : '';
        let deleteLabel = deleteButton.hasAttribute('data-label-delete') ? deleteButton.getAttribute('data-label-delete') : '';

        Modal.confirm(title, message, Severity.warning, [
                {
                    text: abortLabel,
                    trigger: function () {
                        Modal.dismiss();
                    }
                },
                {
                    text: deleteLabel,
                    trigger: function () {
                        Modal.dismiss();

                        if (deleteButton.hasAttribute('data-uid')) {
                            let request = new AjaxRequest(TYPO3.settings.ajaxUrls['MindshapeSeoAjaxHandler::deleteConfiguration']);
                            let data =  {configurationUid: deleteButton.getAttribute('data-uid')};

                            let promise = request.post(data, {
                                headers: {
                                    'Content-Type': 'application/json; charset=utf-8'
                                }
                            });

                            promise.then(async function (response) {
                                const responseData = await response.resolve();
                                if (responseData.deleted === true) {
                                    if (deleteButton.hasAttribute('data-redirect-url')) {
                                        self.location.href = deleteButton.getAttribute('data-redirect-url');
                                    }
                                } else {
                                    console.error('Failed to delete configuration: ' + data.configurationUid);
                                }
                            }, function (error) {
                                console.error('Failed to delete configuration: ' + data.configurationUid);
                                console.error(`The request failed with ${error.response.status}: ${error.response.statusText}`);
                            });
                        }
                    }
                }
            ]
        )
    }

    // To let the module be a dependency of another module, we return our object
    return SettingsModule;
});