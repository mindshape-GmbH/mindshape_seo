(function ($, TYPO3, MSH) {
  MSH = MSH || {};
  MSH = {
    googleTitleLength: 50,
    googleDescriptionLength: 180,
    $previewContainers: {},
    $robotForms: {},
    editing: true,
    init: function () {
      var that = this;

      this.$previewContainers = $('.google-preview');
      this.$robotForms = $('.robots-form');

      // Initial description rendering (kills whitespace etc.)
      this.$previewContainers.each(function () {
        that.editing = 0 < parseInt($(this).attr('data-editing'));
        that.renderPreviewDescription($(this));
        that.checkFocusKeyword($(this), $(this).find('.focus-keyword input').val());
        that.updatePreviewAlerts($(this));

        if (this.editing) {
          that.updatePreviewEditPanelProgressBar($(this), 'title', that.googleTitleLength);
          that.updatePreviewEditPanelProgressBar($(this), 'description', that.googleDescriptionLength);
        }
      });

      this.registerEvents();
    },
    registerEvents: function () {
      var that = this;

      // Change selection of pagetree depth
      $('#depthselect').on('change', function () {
        $('#depthselect-form').submit()
      });

      // Save configuration form
      $('.mindshape-seo-savebutton').on('click', function (e) {
        e.preventDefault();

        $('#mindshape-seo-configuration').submit();
      });

      // Configuration form upload fields delete function
      $('.mindshape-seo-upload').on('click', '.mindshape-seo-delete', function (e) {
        e.preventDefault();

        var $uploadContainer = $(this).parent('.mindshape-seo-upload');

        $uploadContainer.find('input[type="hidden"]').remove();
        $uploadContainer.find('.image').remove();
      });

      // Edit click on google preview
      this.$previewContainers.on('click', '.edit', function (e) {
        e.preventDefault();

        that.openPreviewEditPanel($(this).parents('.google-preview'));
      });

      // Abort click on edit panel
      this.$previewContainers.on('click', '.abort', function (e) {
        e.preventDefault();

        var $currentPreview = $(this).parents('.google-preview');
        var fokuskeyword = $currentPreview.find('.focus-keyword input').val();

        that.closePreviewEditPanel($currentPreview);
        that.restorePreviewOriginalData($currentPreview);
        that.checkPreviewEditPanelSaveState($currentPreview);
        that.updatePreviewEditPanelProgressBar($currentPreview, 'title', that.googleTitleLength);
        that.updatePreviewEditPanelProgressBar($currentPreview, 'description', that.googleDescriptionLength);
        that.updatePreviewAlerts($currentPreview);

        if (fokuskeyword.trim().length) {
          that.checkFocusKeyword($currentPreview, fokuskeyword);
        }
      });

      // Save click on edit panel
      this.$previewContainers.on('click', '.save', function (e) {
        e.preventDefault();

        that.savePreviewEditPanel($(this).parents('.google-preview'));
      });

      // Show SEO alerts
      this.$previewContainers.on('click', '.alerts', function (e) {
        e.preventDefault();

        var $alertsContainer = $(this).parents('.google-preview').find('.alerts-container');

        if ($alertsContainer.is(':hidden')) {
          $alertsContainer.slideDown();
        } else {
          $alertsContainer.slideUp();
        }
      });

      // Change preview title when editing title
      this.$previewContainers.on('keyup', '.edit-panel .title', function () {
        var $currentPreview = $(this).parents('.google-preview');

        $currentPreview.find('.preview-box .title').html($(this).val());
        that.updatePreviewEditPanelProgressBar($currentPreview, 'title', that.googleTitleLength);
        that.updatePreviewAlerts($currentPreview);

        if (that.editing) {
          that.checkPreviewEditPanelSaveState($currentPreview);
        }
      });

      // Change preview description when editing description
      this.$previewContainers.on('keyup', '.edit-panel .description', function () {
        var $currentPreview = $(this).parents('.google-preview');
        var fokusKeyword = $currentPreview.find('.focus-keyword input').val().trim();

        $currentPreview.find('.preview-box .description').html($(this).val());
        that.renderPreviewDescription($currentPreview);
        that.updatePreviewEditPanelProgressBar($currentPreview, 'description', that.googleDescriptionLength);
        that.updatePreviewAlerts($currentPreview);

        if (0 < fokusKeyword.length) {
          that.checkFocusKeyword($currentPreview, fokusKeyword);
        }

        if (that.editing) {
          that.checkPreviewEditPanelSaveState($currentPreview);
        }
      });

      // Update focus keyword check
      this.$previewContainers.on('keyup', '.focus-keyword input', function () {
        var $currentPreview = $(this).parents('.google-preview');
        var focusKeyword = $(this).val().trim();

        that.checkFocusKeyword($currentPreview, focusKeyword);
        that.updatePreviewAlerts($currentPreview);

        if (that.editing) {
          that.checkPreviewEditPanelSaveState($currentPreview);
        }
      });

      // Re-render description on change
      this.$previewContainers.on('change', '.preview-box .description', function () {
        that.renderPreviewDescription($(this).parents('.google-preview'));
      });

      // Save robots data on un-/check
      this.$robotForms.on('change', 'input', function () {
        that.saveRobotsData($(this).parents('.robots-form'), $(this));
      });

      if (!this.editing) {
        var $tcaForm = $('form');
        var $currentPreview = $('.google-preview');
        var currentPageUid = $currentPreview.find('input[name="pageUid"]').val();
        var fokusKeyword = $currentPreview.find('.focus-keyword input').val().trim();

        $tcaForm.find('input[data-formengine-input-name="data[pages][' + currentPageUid + '][title]"]').on('keyup', function () {
          $currentPreview.find('.preview-box .title').html($(this).val());
          that.updatePreviewAlerts($currentPreview);

          if (0 < fokusKeyword.length) {
            that.checkFocusKeyword($currentPreview, fokusKeyword);
          }
        });

        $tcaForm.find('textarea[name="data[pages][' + currentPageUid + '][description]"]').on('keyup', function () {
          $currentPreview.find('.preview-box .description').html($(this).val());
          that.renderPreviewDescription($currentPreview);
          that.updatePreviewAlerts($currentPreview);

          if (0 < fokusKeyword.length) {
            that.checkFocusKeyword($currentPreview, fokusKeyword);
          }
        });
      }
    },
    restorePreviewOriginalData: function ($previewContainer) {
      $previewContainer.find('.preview-box .title').html($previewContainer.attr('data-original-title'));
      $previewContainer.find('.preview-box .description').html($previewContainer.attr('data-original-description'));
      $previewContainer.find('.edit-panel .title').val($previewContainer.attr('data-original-title'));
      $previewContainer.find('.edit-panel .description').val($previewContainer.attr('data-original-description'));
      this.renderPreviewDescription($previewContainer);
    },
    renderPreviewDescription: function ($previewContainer) {
      var description = $previewContainer.find('.preview-box .description').text();

      description = description.trim();

      if (this.googleDescriptionLength < description.length) {
        description = description.substring(0, this.googleDescriptionLength) + ' ...';
      }

      $previewContainer.find('.preview-box .description').html(description);
    },
    checkPreviewEditPanelSaveState: function ($previewContainer) {
      var title = $previewContainer.find('.edit-panel .title').val();
      var description = $previewContainer.find('.edit-panel .description').val();
      var focusKeyword = $previewContainer.find('.focus-keyword input').val();

      if (
        0 < title.length &&
        (
          $previewContainer.attr('data-original-title') !== title.trim() ||
          $previewContainer.attr('data-original-description') !== description.trim() ||
          $previewContainer.attr('data-original-focuskeyword') !== focusKeyword.trim()
        )
      ) {
        $previewContainer.find('button.save').prop('disabled', false);
        $previewContainer.find('.edit-panel .title-container').removeClass('has-error');
      } else {
        $previewContainer.find('button.save').prop('disabled', true);

        if (0 === title.length) {
          $previewContainer.find('.edit-panel .title-container').addClass('has-error')
        }
      }
    },
    updatePreviewEditPanelProgressBar: function ($previewContainer, fieldName, maxLength) {
      var fieldText = $previewContainer.find('.edit-panel .' + fieldName).val();
      var percent = 0;
      var fieldLength = fieldText.trim().length;
      var progressbarStatusClass = 'progress-bar-';

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

      $previewContainer
        .find('.edit-panel .progress-' + fieldName + ' .progress-bar')
        .css('width', percent + '%')
        .removeClass('progress-bar-danger')
        .removeClass('progress-bar-warning')
        .removeClass('progress-bar-success')
        .addClass(progressbarStatusClass);
    },
    closePreviewEditPanel: function ($previewContainer, callback) {
      $previewContainer.find('.edit-panel').slideUp();
      $previewContainer.find('button.save, button.abort').fadeOut(function () {
        $previewContainer.find('button.edit').fadeIn(function () {
          if (typeof callback === 'function')
            callback();
        });
      });
    },
    openPreviewEditPanel: function ($previewContainer) {
      $previewContainer.find('.icon-provider-fontawesome-check').hide();
      $previewContainer.find('.edit-panel').slideDown();
      $previewContainer.find('button.edit').fadeOut(function () {
        $previewContainer.find('button.save, button.abort').fadeIn();
      });
    },
    savePreviewEditPanel: function ($previewContainer) {
      var that = this;

      if (!this.editing) {
        return;
      }

      $.ajax({
        type: "POST",
        url: TYPO3.settings.ajaxUrls['MindshapeSeoAjaxHandler::savePage'],
        data: $previewContainer.find('form').serialize(),
        success: function () {
          $previewContainer.attr('data-original-title', $previewContainer.find('.edit-panel .title').val().trim());
          $previewContainer.attr('data-original-description', $previewContainer.find('.edit-panel .description').val().trim());

          that.checkPreviewEditPanelSaveState($previewContainer);
          that.closePreviewEditPanel($previewContainer, function () {
            $previewContainer.find('.icon-provider-fontawesome-check').show();
          });
        },
        error: function () {
          $previewContainer.find('.icon-provider-fontawesome-error').show();
        }
      });
    },
    saveRobotsData: function ($robotsForm, $input) {
      $.ajax({
        type: "POST",
        url: TYPO3.settings.ajaxUrls['MindshapeSeoAjaxHandler::savePageRobots'],
        data: $robotsForm.serialize(),
        success: function () {
          var $loadingIcon = $input.parents('.checkbox').find('.loader');

          $loadingIcon.css('display', 'inline');
          $input.prop('disabled', true);

          setTimeout(function () {
            $loadingIcon.fadeOut(function () {
              $input.prop('disabled', false);
            });
          }, 1000);
        },
        error: function () {
          var $errorIcon = $input.parents('.checkbox').find('.icon-provider-fontawesome-error');

          $errorIcon.css('display', 'inline-block');
          $input.prop('disabled', true);
        }
      });
    },
    checkFocusKeyword: function ($previewContainer, fokusKeyword) {
      this.renderPreviewDescription($previewContainer);
      this.clearPreviewTitle($previewContainer);
      this.clearUrlTitle($previewContainer);

      if ('' === fokusKeyword.trim()) {
        return;
      }

      var title = $previewContainer.find('.preview-box .title').text();
      var description = $previewContainer.find('.preview-box .description').text();
      var url = $previewContainer.find('.preview-box .url').text();
      var regex = new RegExp('(^|\\.|\\s)(' + fokusKeyword.trim() + ')(\\s|\\.|$)', 'igm');
      var titleMatches = title.match(regex);
      var descriptionMatches = description.match(regex);
      var urlMatches = url.match(regex);

      if (null === titleMatches) {
        $previewContainer.attr('data-keyword-title-matches', 0);
      } else {
        $previewContainer.find('.preview-box .title').html(
          title.replace(regex, '$1<span class="focus-keyword">$2</span>$3')
        );

        $previewContainer.attr('data-keyword-title-matches', titleMatches.length);
      }

      if (null === descriptionMatches) {
        $previewContainer.attr('data-keyword-description-matches', 0);
      } else {
        $previewContainer.find('.preview-box .description').html(
          description.replace(regex, '$1<span class="focus-keyword">$2</span>$3')
        );

        $previewContainer.attr('data-keyword-description-matches', descriptionMatches.length);
      }

      if (null === urlMatches) {
        $previewContainer.attr('data-keyword-url-matches', 0);
      } else {
        $previewContainer.find('.preview-box .url cite').html(
          url.replace(regex, '$1<span class="focus-keyword">$2</span>$3')
        );

        $previewContainer.attr('data-keyword-url-matches', urlMatches.length);
      }
    },
    clearPreviewTitle: function ($previewContainer) {
      $previewContainer.find('.preview-box .title').html(
        $previewContainer.find('.preview-box .title').text().trim()
      );
    },
    clearUrlTitle: function ($previewContainer) {
      $previewContainer.find('.preview-box .url cite').html(
        $previewContainer.find('.preview-box .url').text().trim()
      );
    },
    updatePreviewAlerts: function ($previewContainer) {
      var titleLength = $previewContainer.find('.preview-box .title').text().length + $previewContainer.find('.preview-box .attachment').text().length;
      var description = $previewContainer.find('.preview-box .description').text();
      var $alertsContainer = $previewContainer.find('.alerts-container');

      if (titleLength > this.googleTitleLength) {
        $alertsContainer.find('.title-length').show();
      } else {
        $alertsContainer.find('.title-length').hide();
      }

      if (description.length > this.googleDescriptionLength) {
        $alertsContainer.find('.description-length').show();
      } else {
        $alertsContainer.find('.description-length').hide();
      }

      if (0 === $previewContainer.find('.focus-keyword input').val().trim().length) {
        $alertsContainer.find('.focus-keyword').hide();
      } else {
        if (0 < parseInt($previewContainer.attr('data-keyword-title-matches'))) {
          $alertsContainer.find('.focus-keyword.missing-title').hide();
          $alertsContainer.find('.focus-keyword.found-title').show();
        } else {
          $alertsContainer.find('.focus-keyword.missing-title').show();
          $alertsContainer.find('.focus-keyword.found-title').hide();
        }

        if (0 < parseInt($previewContainer.attr('data-keyword-description-matches'))) {
          $alertsContainer.find('.focus-keyword.missing-description').hide();
          $alertsContainer.find('.focus-keyword.found-description').show();
        } else {
          $alertsContainer.find('.focus-keyword.missing-description').show();
          $alertsContainer.find('.focus-keyword.found-description').hide();
        }

        if (0 < parseInt($previewContainer.attr('data-keyword-url-matches'))) {
          $alertsContainer.find('.focus-keyword.missing-url').hide();
          $alertsContainer.find('.focus-keyword.found-url').show();
        } else {
          $alertsContainer.find('.focus-keyword.missing-url').show();
          $alertsContainer.find('.focus-keyword.found-url').hide();
        }
      }

      if (0 < $previewContainer.find('.alerts-container .alert-danger').filter(function () { return $(this).css('display') !== 'none'; }).length) {
        $previewContainer.find('.buttons .alerts').prop('disabled', false);
        $previewContainer.find('.buttons .alerts')
          .removeClass('btn-success')
          .addClass('btn-danger');
      } else {
        $previewContainer.find('.alerts-container').hide();
        $previewContainer.find('.buttons .alerts').prop('disabled', true);
        $previewContainer.find('.buttons .alerts')
          .removeClass('btn-danger')
          .addClass('btn-success');
      }
    }
  };

  $(document).ready(function () {
    MSH.init();
  });
})(TYPO3.jQuery || jQuery, TYPO3, MSH = null);
