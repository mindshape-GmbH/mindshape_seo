.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _seo-manual:

==========
SEO Manual
==========

Target group: **Editors**

Metadata
========

Breadcrumb
----------

Mostly the breadcrumb trail is placed directly above the website content.
To integrate a correct breadcrumb to your website you can use the JSON-LD markup for instance,
in order to give Google a better understanding of your website structure.
By using the breadcrumb, users have the option to navigate all the way up in the site hierarchy.
Additionally it indicates the current position on the website.

Further information can be found here: https://developers.google.com/search/docs/data-types/breadcrumbs

Noindex
-------

Noindex is used to tell search engines to drop the page entirely from search results.
It's a useful tool to control your pages from getting indexed. Generally it's used
to prevent duplicate content as well as keeping internal generated pages out of getting indexed by search engines.

Further information can be found here: https://support.google.com/webmasters/answer/93710?hl=en

Nofollow
--------

The nofollow attribute is a way to tell search engines to not follow links on a certain page.
So the link is still persisting for users but the crawler is not following any
outgoing link from this page anymore. This means the link juice of the website
is not relayed to other pages.  If the crawler should follow but shouldn't index the page,
than you have to use following Meta-Tag noindex/follow::

    <meta name="robots" content="noindex,follow" />

Further information can be found here: https://support.google.com/webmasters/answer/96569?hl=en

Title-Tag
---------

The title tag is the most important on-page SEO element. Google is considering title tags
as an important ranking factor, therefore it`s important to place keywords
to the front of title tags to improve your position in search results.

Further information can be found here: https://support.google.com/webmasters/answer/35624?visit_id=1-636153175105126499-721612477&rd=1

Meta-Descriptions
-----------------

Meta descriptions accurately represent the content of a certain URL,
so the user gets a clear idea of the expected content.
The description has no influence on the rankings, but an attractive description
can influence the click rate and so the traffic on your website.

Further information can be found here: https://webmasters.googleblog.com/2007/09/improve-snippets-with-meta-description.html

Canonical
---------

The canonical tag is used for pages with same or nearly same content.
It´s a way to show search engines which version is he original and should appear in search results.
Thus, all other versions are no duplicate content anymore and should remove by search engines from the index.

Further information can be found here: https://support.google.com/webmasters/answer/139066?hl=en

Structured Data-Markup / Website-Information as JSON-LD
-------------------------------------------------------

JSON-LD is one different way to mark up your structured data by using schema.org vocabulary.
Search engines will understand the data and can present search results more attractively
in terms of rich snippets. For example stars for product reviews or the appearance of a breadcrumb.

Further information can be found here: https://support.google.com/webmasters/answer/3069489?hl=en
