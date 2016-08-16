.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _configuration:

Konfigurationsreferenz
======================
| Alle primären Einstellungen können im "SEO Einstellungen" Modul vorgenommen werden.
| Um die Seiten und Bilder Sitemap zu verwenden ist es nötig einen Redirect in deiner htaccess und Einträge in die robots.txt hinzuzufügen.

.htaccess
---------
| Füge folgende Einträge zu deiner .htaccess Datei hinzu.
| Die Einträge müssen direkt nach "RewriteEngine On" (Zeile: ~263) eingesetzt werden.
|
::

	...
	RewriteEngine On

	# MindshapeSeo google sitemap
	RewriteRule sitemap.xml$ /index.php?type=10000 [L,R=301]
	RewriteRule sitemap_(.*).xml$ /index.php?type=10000&pageuid=$1 [L,R=301]

	# MindshapeSeo google image sitemap
	RewriteRule sitemap-image.xml$ /index.php?type=10001 [L,R=301]
	...

robots.txt
----------
| Füge folgende Einträge zu deiner robots.txt Datei hinzu.
| Vergiss nicht deine eigene Domain in die URL einzusetzen!
|
::

	Sitemap: http://yourdomain.url/index.php?type=10000
	Sitemap: http://yourdomain.url/index.php?type=10001
	Sitemap: http://yourdomain.url/index.php?type=10002

.. toctree::

	:maxdepth: 5
	:titlesonly:
	:glob:

	Reference/Index
	ExampleTypoScriptSetup/Index
