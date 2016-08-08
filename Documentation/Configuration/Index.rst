.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _configuration:

Configuration Reference
=======================
| The main configuration can be done in the SEO settings module.
| To use the page and image sitemap you have to setup a redirect in your htaccess and entries in your robots.txt.

.htaccess
---------
Just copy the following to the end of your .htaccess file.
::

	<ifModule mod_rewrite.c>
	  # MindshapeSeo google sitemap
	  RewriteRule sitemap.xml$ /index.php?type=10000 [L,R=301]
	  RewriteRule sitemap_(.*).xml$ /index.php?type=10000&pageuid=$1 [L,R=301]

	  # MindshapeSeo google image sitemap
	  RewriteRule sitemap-image.xml$ /index.php?type=10001 [L,R=301]
	</ifModule>

robots.txt
----------
| Add the following entries to your robots.txt file.
| Don't forget to replace the URL with your domain!
|
::

	Sitemap: http://yourdomain.url/index.php?type=10000
	Sitemap: http://yourdomain.url/index.php?type=10001

.. toctree::

	:maxdepth: 5
	:titlesonly:
	:glob:

	Reference/Index
	ExampleTypoScriptSetup/Index
