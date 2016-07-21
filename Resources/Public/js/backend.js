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
      this.$previewContainers.on('click', '.edit', function () {
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.openCurrentEditPanel();
      });

      // Abort click on edit panel
      this.$previewContainers.on('click', '.abort', function () {
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.closeCurrentEditPanel();
        that.setOriginalData();
        that.checkSaveState();
        that.updateProgressBar('title', that.googleTitleLength);
        that.updateProgressBar('description', that.googleDescriptionLength);
      });

      // Save click on edit panel
      this.$previewContainers.on('click', '.save', function () {
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.saveCurrentEditPanel();
        that.closeCurrentEditPanel();
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
      });

      // Re-render description on change
      this.$previewContainers.on('change', '.preview-box .description', function () {
        that.$currentPreviewContainer = $(this).parents('.google-preview');
        that.renderPreviewDescription();
      });
    },
    setOriginalData: function () {
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
      var input = this.$currentPreviewContainer.find('.edit-panel .title').val();

      if (0 < input.length) {
        this.$currentPreviewContainer.find('button.save').prop('disabled', false);
        this.$currentPreviewContainer.find('.edit-panel .title-container').removeClass('has-error');
      } else {
        this.$currentPreviewContainer.find('button.save').prop('disabled', true);
        this.$currentPreviewContainer.find('.edit-panel .title-container').addClass('has-error');
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
    closeCurrentEditPanel: function () {
      this.$currentPreviewContainer.find('.edit-panel').hide();
      this.$currentPreviewContainer.find('button.save, button.abort').hide();
      this.$currentPreviewContainer.find('button.edit').show();
    },
    openCurrentEditPanel: function () {
      this.$currentPreviewContainer.find('.edit-panel').show();
      this.$currentPreviewContainer.find('button.save, button.abort').show();
      this.$currentPreviewContainer.find('button.edit').hide();
    },
    saveCurrentEditPanel: function () {
      $.post(
        TYPO3.settings.ajaxUrls['MindshapeSeoAjaxHandler::savePage'],
        this.$currentPreviewContainer.find('.edit-panel form').serialize()
      );
    }
  };

  $(document).ready(function () {
    MSH.init();
  });
})(TYPO3.jQuery || jQuery, TYPO3, MSH = null);
