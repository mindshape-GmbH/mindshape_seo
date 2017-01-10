.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _changelog:

ChangeLog
---------

v1.0.14
  - Exclude hidden pages in image sitemap generator
  - Decode image url

v1.0.13
  - Sitemap fix for multidomain sites
  - Remove hidden pages from sitemap
  - Fix unsafe condition in image sitemap generator
  - Fix url schema in settings module
  - Apply variable protocol schema to analytics url

v1.0.12
  - Always add title tag

v1.0.11
  - Fixed wrong rootpage in base href on nested siteroots

v1.0.10
  - Fixed wrong base href path

v1.0.9
  - Fixed multilingual sitemaps

v1.0.8
  - Fixed german label for description in page properties
  - Fixed translating issues

v1.0.7
  - Refactored the update script to a service
  - Added an option to be able to manually control the adding of analytics scripts in frontend

v1.0.6
  - Fixed header title replacement

v1.0.5
  - Fixed illegal classname in namespace of configuration model
  - Replaced myspace field with xing for social media in json-ld
  - Added hreflang x-default

v1.0.4
  - Updated and cleaned up templates
  - Bugfix in hreflang finding of languages

v1.0.3
  - Usage of proper baseurl rendering
  - Don't respect storagepid on news hook
  - Added data-ignore attribute to analytics script tags
  - Added missing default language to hreflang

v1.0.2
  - Added filename sanitizing in image upload
  - Fixed strict argument type on extended sitemap hook
  - Usage of standard title, description etc. for facebook metadata if not explicity given

v1.0.1
  - Fixed missing frontend controller in page service

v1.0.0
  - First release
