import AjaxRequest from '@typo3/core/ajax/ajax-request.js';
import {SeverityEnum} from '@typo3/backend/enum/severity.js';
import Modal from '@typo3/backend/modal.js';
import Utils from "./Utils.js";

class SettingsModule {
  selectors = {
    configurationForm: '#mindshape-seo-configuration',
    saveButton: '.mindshape-seo-savebutton',
    deleteButton: '.mindshape-seo-deletebutton',
    jsonldCheckbox: '#addJsonld',
    jsonld: '#jsonld',
    jsonldTypeSelect: '.type-select'
  }

  constructor () {
    let saveButton = document.querySelector(this.selectors.saveButton);
    let deleteButton = document.querySelector(this.selectors.deleteButton);

    if (saveButton !== null) {
      saveButton.addEventListener('click', this.saveConfig);
    }

    if (deleteButton !== null) {
      deleteButton.addEventListener('click', this.deleteConfig);
    }

    this.initJsonldLogic();
  };

  initJsonldLogic = () => {
    let jsonld = document.querySelector(this.selectors.jsonld);
    let addJsonldCheckbox = document.querySelector(this.selectors.jsonldCheckbox);
    let jsonldTypeSelect = document.querySelector(this.selectors.jsonldTypeSelect);

    if (jsonld !== null && addJsonldCheckbox !== null && jsonldTypeSelect !== null) {
      if (addJsonldCheckbox.checked) {
        jsonld.style.display = 'block';
      }

      addJsonldCheckbox.addEventListener('click', () => {
        Utils.slideToggle(jsonld);
      })
    }
  }

  /**
   *
   * @param {PointerEvent} e
   */
  saveConfig = (e) => {
    e.preventDefault();
    let form = document.querySelector(this.selectors.configurationForm);
    if (form !== null) form.submit();
  }

  /**
   *
   * @param {PointerEvent} e
   */
  deleteConfig = (e) => {
    e.preventDefault();

    let deleteButton = e.currentTarget;
    let title = deleteButton.hasAttribute('title') ? deleteButton.getAttribute('title') : '';
    let message = deleteButton.hasAttribute('data-message') ? deleteButton.getAttribute('data-message') : '';
    let abortLabel = deleteButton.hasAttribute('data-label-abort') ? deleteButton.getAttribute('data-label-abort') : '';
    let deleteLabel = deleteButton.hasAttribute('data-label-delete') ? deleteButton.getAttribute('data-label-delete') : '';

    Modal.advanced({
      severity: SeverityEnum.warning,
      title: title,
      content: message,
      buttons: [
        {
          text: abortLabel,
          trigger: (event, modal) => modal.hideModal()
        },
        {
          text: deleteLabel,
          trigger: (event, modal) => {
            modal.hideModal()

            if (deleteButton.hasAttribute('data-uid')) {
              let request = new AjaxRequest(TYPO3.settings.ajaxUrls['MindshapeSeoAjaxHandler::deleteConfiguration']);
              let data = { configurationUid: deleteButton.getAttribute('data-uid') };

              let promise = request.post(data, {
                headers: {
                  'Content-Type': 'application/json; charset=utf-8'
                }
              });

              promise.then(async (response) => {
                const responseData = await response.resolve();
                if (responseData.deleted === true) {
                  if (deleteButton.hasAttribute('data-redirect-url')) {
                    self.location.href = deleteButton.getAttribute('data-redirect-url');
                  }
                } else {
                  console.error('Failed to delete configuration: ' + data.configurationUid);
                }
              }, (error) => {
                console.error('Failed to delete configuration: ' + data.configurationUid);
                console.error(`The request failed with ${error.response.status}: ${error.response.statusText}`);
              });
            }
          }
        }
      ]
    });
  }
}

export default new SettingsModule();
