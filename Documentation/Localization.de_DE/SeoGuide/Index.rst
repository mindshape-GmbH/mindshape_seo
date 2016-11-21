.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _seo-manual:

=============
SEO Anleitung
=============

Zielgruppe: **Editoren**

Meta-Daten
==========

Hreflang-Tag
------------

Das hreflang-Tag wird bei einer mehrsprachigen Website eingesetzt.
Der Tag verweist jeweils auf die verschiedenen anderen Sprachversionen einer
URL und ermöglicht Suchmaschinen damit die Orientierung in mehrsprachigen Websites.

Weitere Informationen finden Sie hier: https://support.google.com/webmasters/answer/189077?hl=de&ref_topic=2370587

Breadcrumb
----------

Die Brotkrumennavigation ist eine Navigation innerhalb der Website
und wird meistens direkt über dem Seiteninhalt platziert. Ausgezeichnet
wird diese beispielsweise über das Markup JSON-LD, um der Suchmaschine das
Verständnis der Seitenstruktur zu erleichtern. Die Breadcrumb bietet jedoch
hauptsächlich dem User eine Orientierung über die aktuelle Position in der
Informationsarchitektur der Website. Zudem hat es den Vorteil, dass der User
über wenige Klicks sich in obere Seitenebenen navigieren kann.

Weitere Informationen finden Sie hier: https://developers.google.com/search/docs/data-types/breadcrumbs

Noindex
-------

Noindex teilt der Suchmaschine mit, die Seite nicht in den Index aufzunehmen.
Hierüber lässt sich die Indizierung Ihrer Seiten gezielt steuern.
Hauptsächlich wird das Noindex Tag zur Vermeidung von doppelten Inhalten eingesetzt.
Zusätzlich findet es Anwendung bei Seiten, die beispielsweise über die interne Suche
generiert werden und nicht im Index der Suchmaschine erscheinen sollen.

Weitere Informationen finden Sie hier: https://support.google.com/webmasters/answer/93710?hl=de

Nofollow
--------

Mit dem nofollow-Attribut wird der Suchmaschine mitgeteilt, Link-Verweisen auf der
Seite nicht zu folgen. Der Verweis bleibt für den Nutzer erhalten, jedoch wird der
Linkjuice der Website nicht weitergegeben und der Crawler verfolgt nicht die
verlinkten URLs. Soll der Crawler den Links jedoch weiter folgen, die Seite aber
nicht indexieren, dann wird ein Meta-Tag noindex/follow verwendet. Die Auszeichnung
hierfür sieht folgendermaßen aus::

    <meta name="robots" content="noindex,follow" />

Weitere Informationen finden Sie hier: https://support.google.com/webmasters/answer/96569?hl=de

Title-Tag
---------

Der Title-Tag gehört zu einem der wichtigsten Faktoren der OnPage Optimierung.
Dieser wird von Google zur Berechnung der Rankings herangezogen.
Daher ist es wichtig die Hauptüberschrift bzw. das Haupt-Keyword der
Seite in den Title aufzunehmen.

Meta-Descriptions
-----------------

Die Meta-Description ist eine Kurzbeschreibung des Seiteninhalts und gibt dem
Nutzer einen ersten Hinweis darauf, was ihn erwartet. Diese Kurzbeschreibung
erscheint als Beschreibungstext in den Suchergebnissen und hat keinen direkten
Einfluss auf das Ranking. Indirekt kann eine ansprechende Description jedoch die
Klickrate und somit die Besuchermenge positiv beeinflussen.

Weitere Informationen finden Sie hier: https://webmaster-de.googleblog.com/2007/10/wie-ihr-snippets-durch-ein-makeover-der.html

Canonical
---------

Das Canonical kommt zum Einsatz, wenn es mehrere URLs mit gleichem oder stark
ähnlichem Inhalt gibt. Hierüber wird der Suchmaschine die bevorzugte URL
mitgeteilt, die verweisenden URLs werden aus dem Google-Index genommen
und somit nicht mehr als Duplicate Content gewertet.

Mehr Informationen zum Canonical finden Sie hier: https://support.google.com/webmasters/answer/139066?hl=de

Strukturiertes Daten-Markup /Website-Informationen als JSON-LD einfügen
-----------------------------------------------------------------------

JSON-LD ist eine Möglichkeit Ihre strukturierten Daten unter Verwendung
des schema.org Vokabulars mit Markup zu versehen.  Suchmaschinen lesen diese Markups
und spielen diese in den Suchergebnisse in Form von Rich Snippets aus. Beispielsweise
erscheinen diese dann als Breadcrumb oder Bewertungssterne.

Weitere Informationen finden Sie hier: https://support.google.com/webmasters/answer/3069489?hl=de

XML-Sitemap
===========

Eine XML-Sitemap stellt Suchmaschinen eine strukturierte Übersicht des
Websitesaufbaus und verfügbarer Inhalte zur Verfügung. So können auch
Unterseiten gefunden werden, welche andernfalls unter Umständen übersehen
werden. Über die XML-Sitemap können zudem Crawl-Prioritäten gesetzt werden,
indem bestimmten URLs eine größere Bedeutung durch Zahlen von 0-1 vergeben werden.
Besonders hilfreich ist eine XML-Sitemap bei umfangreichen Websites mit vielen Unterseiten.

Weitere Informationen finden Sie hier: https://support.google.com/webmasters/answer/156184?hl=de
