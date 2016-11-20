.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _developer-manual:

Entwickler Anleitung
====================

Zielgruppe: **Entwickler**

.. _developer-hooks:

Verfügbare Hooks
----------------

Sitemap Hooks
^^^^^^^^^^^^^

**Vor-rendering der Sitemap**
::
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemap_preRendering']

**Nach-rendering der Sitemap**
::
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemap_postRendering']

**Vor-rendering der Index Sitemap**
::
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapIndex_preRendering']

**Nach-rendering der Index Sitemap**
::
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapIndex_postRendering']

**Vor-rendering der Bilder Sitemap**
::
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapImage_preRendering']

**Nach-rendering der Bilder Sitemap**
::
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['sitemapImage_postRendering']
