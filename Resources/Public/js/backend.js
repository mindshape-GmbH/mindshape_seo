(function ($, TYPO3, MSH) {
  MSH = MSH || {};
  MSH = {
    googleTitleLength: 50,
    googleDescriptionLength: 180,
    $previewContainers: {},
    $robotForms: {},
    init: function () {
      var that = this;

      this.$previewContainers = $('.google-preview');
      this.$robotForms = $('.robots-form');

      this.registerEvents();

      // Initial description rendering (kills whitespace etc.)
      this.$previewContainers.each(function () {
        that.renderPreviewDescription($(this));
        that.updatePreviewEditPanelProgressBar($(this), 'title', that.googleTitleLength);
        that.updatePreviewEditPanelProgressBar($(this), 'description', that.googleDescriptionLength);
      });
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

        that.closePreviewEditPanel($currentPreview);
        that.restorePreviewOriginalData($currentPreview);
        that.checkPreviewEditPanelSaveState($currentPreview);
        that.updatePreviewEditPanelProgressBar($currentPreview, 'title', that.googleTitleLength);
        that.updatePreviewEditPanelProgressBar($currentPreview, 'description', that.googleDescriptionLength);
      });

      // Save click on edit panel
      this.$previewContainers.on('click', '.save', function (e) {
        e.preventDefault();

        that.savePreviewEditPanel($(this).parents('.google-preview'));
      });

      // Change preview title when editing title
      this.$previewContainers.on('keyup', '.edit-panel .title', function () {
        var $currentPreview = $(this).parents('.google-preview');

        $currentPreview.find('.preview-box .title').html($(this).val());
        that.updatePreviewEditPanelProgressBar($currentPreview, 'title', that.googleTitleLength);
        that.checkPreviewEditPanelSaveState($currentPreview);
      });

      // Change preview description when editing description
      this.$previewContainers.on('keyup', '.edit-panel .description', function () {
        var $currentPreview = $(this).parents('.google-preview');

        $currentPreview.find('.preview-box .description').html($(this).val());
        that.renderPreviewDescription($currentPreview);
        that.updatePreviewEditPanelProgressBar($currentPreview, 'description', that.googleDescriptionLength);
        that.checkPreviewEditPanelSaveState($currentPreview);
      });

      // Update focus keyword check
      this.$previewContainers.on('keyup', '.focus-keyword input', function () {
        that.checkFocusKeyword($(this).parents('.google-preview'), $(this).val())
      });

      // Re-render description on change
      this.$previewContainers.on('change', '.preview-box .description', function () {
        that.renderPreviewDescription($(this).parents('.google-preview'));
      });

      // Save robots data on un-/check
      this.$robotForms.on('change', 'input', function () {
        that.saveRobotsData($(this).parents('.robots-form'), $(this));
      })
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

      if (
        0 < title.length &&
        (
          $previewContainer.attr('data-original-title') !== title.trim() ||
          $previewContainer.attr('data-original-description') !== description.trim()
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
      } else if(percent >= 70) {
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

      $.ajax({
        type: "POST",
        url: TYPO3.settings.ajaxUrls['MindshapeSeoAjaxHandler::savePage'],
        data: $previewContainer.find('.edit-panel form').serialize(),
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
          var $successIcon = $input.parents('.checkbox').find('.icon-provider-fontawesome-check');

          $successIcon.css('display', 'inline-block');
          $input.prop('disabled', true);

          setTimeout(function () {
            $successIcon.fadeOut(function () {
              $input.prop('disabled', false);
            });
          }, 2000);
        },
        error: function () {
          var $errorIcon = $input.parents('.checkbox').find('.icon-provider-fontawesome-error');

          $errorIcon.css('display', 'inline-block');
          $input.prop('disabled', true);
        }
      });
    },
    checkFocusKeyword: function ($previewContainer, fokusKeyword) {
      var title = $previewContainer.find('.preview-box .title').text();
      var descriptionEdit = $previewContainer.find('.edit-panel .description').text();
      var descriptionPreview = $previewContainer.find('.preview-box .description').text();
      var regex = new RegExp('(^|\\s)(' + fokusKeyword.trim() + ')(\\s|$)', 'igm');
      var titleMatches = title.match(regex);
      var descriptionMatches = descriptionEdit.match(regex);

      this.renderPreviewDescription($previewContainer);
      this.clearPreviewTitle($previewContainer);

      if (null !== titleMatches) {
        $previewContainer.find('.preview-box .title').html(
          title.replace(regex, function (match) {
            return ' <span class="focus-keyword">' + match.trim() + '</span> ';
          })
        );

        $previewContainer.attr('data-keyword-title-matches', titleMatches.length);
      } else {
        $previewContainer.attr('data-keyword-title-matches', 0);
      }

      if (null !== descriptionMatches) {
        $previewContainer.find('.preview-box .description').html(
          descriptionPreview.replace(regex, function (match) {
            return ' <span class="focus-keyword">' + match.trim() + '</span> ';
          })
        );

        $previewContainer.attr('data-keyword-description-matches', descriptionMatches.length);
      } else {
        $previewContainer.attr('data-keyword-description-matches', 0);
      }
    },
    clearPreviewTitle: function ($previewContainer) {
      $previewContainer.find('.preview-box .title').html(
        $previewContainer.find('.edit-panel .title').val().trim()
      );

    }
  };

  $(document).ready(function () {
    MSH.init();
  });
})(TYPO3.jQuery || jQuery, TYPO3, MSH = null);
