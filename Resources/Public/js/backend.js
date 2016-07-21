(function ($, TYPO3, MSH) {
  MSH = MSH || {};
  MSH = {
    googleTitleLength: 50,
    googleDescriptionLength: 180,
    $previewContainers: {},
    $currentPreviewContainer: {},
    init: function () {
      var that = this;

      this.$previewContainers = $('.google-preview');

      this.registerEvents();

      // Initial description rendering (kills whitespace etc.)
      this.$previewContainers.each(function () {
        that.$currentPreviewContainer = $(this);
        that.renderPreviewDescription();
        that.updateProgressBar('title', that.googleTitleLength);
        that.updateProgressBar('description', that.googleDescriptionLength);
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
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.openCurrentEditPanel();
      });

      // Abort click on edit panel
      this.$previewContainers.on('click', '.abort', function (e) {
        e.preventDefault();
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.closeCurrentEditPanel();
        that.restoreOriginalData();
        that.checkSaveState();
        that.updateProgressBar('title', that.googleTitleLength);
        that.updateProgressBar('description', that.googleDescriptionLength);
      });

      // Save click on edit panel
      this.$previewContainers.on('click', '.save', function (e) {
        e.preventDefault();
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.saveCurrentEditPanel();
      });

      // Change preview title when editing title
      this.$previewContainers.on('keyup', '.edit-panel .title', function () {
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.$currentPreviewContainer.find('.preview-box .title').html($(this).val());
        that.updateProgressBar('title', that.googleTitleLength);
        that.checkSaveState();
      });

      // Change preview description when editing description
      this.$previewContainers.on('keyup', '.edit-panel .description', function () {
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.$currentPreviewContainer.find('.preview-box .description').html($(this).val());
        that.renderPreviewDescription();
        that.updateProgressBar('description', that.googleDescriptionLength);
        that.checkSaveState();
      });

      // Re-render description on change
      this.$previewContainers.on('change', '.preview-box .description', function () {
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.renderPreviewDescription();
      });
    },
    restoreOriginalData: function () {
      this.$currentPreviewContainer.find('.preview-box .title').html($(this.$currentPreviewContainer).attr('data-original-title'));
      this.$currentPreviewContainer.find('.preview-box .description').html($(this.$currentPreviewContainer).attr('data-original-description'));
      this.$currentPreviewContainer.find('.edit-panel .title').val($(this.$currentPreviewContainer).attr('data-original-title'));
      this.$currentPreviewContainer.find('.edit-panel .description').val($(this.$currentPreviewContainer).attr('data-original-description'));
      this.renderPreviewDescription();
    },
    renderPreviewDescription: function () {
      var description = this.$currentPreviewContainer.find('.preview-box .description').html();

      description = description.trim();

      if (this.googleDescriptionLength < description.length) {
        description = description.substring(0, this.googleDescriptionLength) + ' ...';
      }

      this.$currentPreviewContainer.find('.preview-box .description').html(description);
    },
    checkSaveState: function () {
      var title = this.$currentPreviewContainer.find('.edit-panel .title').val();
      var description = this.$currentPreviewContainer.find('.edit-panel .description').val();

      if (
        0 < title.length &&
        (
          this.$currentPreviewContainer.attr('data-original-title') !== title.trim() ||
          this.$currentPreviewContainer.attr('data-original-description') !== description.trim()
        )
      ) {
        this.$currentPreviewContainer.find('button.save').prop('disabled', false);
        this.$currentPreviewContainer.find('.edit-panel .title-container').removeClass('has-error');
      } else {
        this.$currentPreviewContainer.find('button.save').prop('disabled', true);

        if (0 === title.length) {
          this.$currentPreviewContainer.find('.edit-panel .title-container').addClass('has-error')
        }
      }
    },
    updateProgressBar: function (fieldName, maxLength) {
      var fieldText = this.$currentPreviewContainer.find('.edit-panel .' + fieldName).val();
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

      this.$currentPreviewContainer
        .find('.edit-panel .progress-' + fieldName + ' .progress-bar')
        .css('width', percent + '%')
        .removeClass('progress-bar-danger')
        .removeClass('progress-bar-warning')
        .removeClass('progress-bar-success')
        .addClass(progressbarStatusClass);
    },
    closeCurrentEditPanel: function (callback) {
      var that = this;
      this.$currentPreviewContainer.find('.edit-panel').slideUp();
      this.$currentPreviewContainer.find('button.save, button.abort').fadeOut(function () {
        that.$currentPreviewContainer.find('button.edit').fadeIn(function () {
          if (typeof callback === 'function')
          callback();
        });
      });
    },
    openCurrentEditPanel: function () {
      var that = this;

      this.$currentPreviewContainer.find('.icon-provider-fontawesome-check').hide();
      this.$currentPreviewContainer.find('.edit-panel').slideDown();
      this.$currentPreviewContainer.find('button.edit').fadeOut(function () {
        that.$currentPreviewContainer.find('button.save, button.abort').fadeIn();
      });
    },
    saveCurrentEditPanel: function () {
      var that = this;

      $.ajax({
        type: "POST",
        url: TYPO3.settings.ajaxUrls['MindshapeSeoAjaxHandler::savePage'],
        data: this.$currentPreviewContainer.find('.edit-panel form').serialize(),
        success: function () {
          that.closeCurrentEditPanel(function () {
            that.$currentPreviewContainer.find('.icon-provider-fontawesome-check').show();
          });
        },
        error: function () {
          that.$currentPreviewContainer.find('.icon-provider-fontawesome-error').show();
        }
      });
    }
  };

  $(document).ready(function () {
    MSH.init();
  });
})(TYPO3.jQuery || jQuery, TYPO3, MSH = null);
