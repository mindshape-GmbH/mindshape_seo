#
# Table structure for table 'pages'
#
CREATE TABLE pages (
  mindshapeseo_ogtitle                       varchar(255) DEFAULT ''       NOT NULL,
  mindshapeseo_ogurl                         varchar(255) DEFAULT ''       NOT NULL,
  mindshapeseo_ogimage                       int(11) unsigned              NOT NULL DEFAULT '0',
  mindshapeseo_ogdescription                 text                          NOT NULL,
  mindshapeseo_priority                      double(11, 2) DEFAULT '0.50'  NOT NULL,
  mindshapeseo_change_frequency              varchar(255) DEFAULT ''       NOT NULL,
  mindshapeseo_no_index                      tinyint(4) DEFAULT '0'        NOT NULL,
  mindshapeseo_no_follow                     tinyint(4) DEFAULT '0'        NOT NULL,
  mindshapeseo_exclude_from_sitemap          tinyint(4) DEFAULT '0'        NOT NULL,
  mindshapeseo_exclude_suppages_from_sitemap tinyint(4) DEFAULT '0'        NOT NULL,
  mindshapeseo_sub_sitemap                   tinyint(4) DEFAULT '0'        NOT NULL
);

#
# Table structure for table 'pages_language_overlay'
#
CREATE TABLE pages_language_overlay (
  mindshapeseo_ogtitle                       varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_ogurl                         varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_ogimage                       int(11) unsigned             NOT NULL DEFAULT '0',
  mindshapeseo_ogdescription                 text                         NOT NULL,
  mindshapeseo_priority                      double(11, 2) DEFAULT '0.50' NOT NULL,
  mindshapeseo_change_frequency              varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_no_index                      tinyint(4) DEFAULT '0'       NOT NULL,
  mindshapeseo_no_follow                     tinyint(4) DEFAULT '0'       NOT NULL,
  mindshapeseo_exclude_from_sitemap          tinyint(4) DEFAULT '0'       NOT NULL,
  mindshapeseo_exclude_suppages_from_sitemap tinyint(4) DEFAULT '0'       NOT NULL,
  mindshapeseo_sub_sitemap                   tinyint(4) DEFAULT '0'       NOT NULL
);
