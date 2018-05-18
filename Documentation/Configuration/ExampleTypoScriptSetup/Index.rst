.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

Example TypoScript Setup
^^^^^^^^^^^^^^^^^^^^^^^^

The following example shows all usable settings for the extension:

::

    plugin.tx_mindshapeseo {
      settings {
        pageTree.usePagination = 0

        sitemap {
          imageSitemap.tables {
            pages = media
            tt_content = image,media,assets
          }
        }
      }
    }

    sitemap.typeNum = 19371
    sitemapIndex.typeNum = 19372
    imageSitemap.typeNum = 19373
