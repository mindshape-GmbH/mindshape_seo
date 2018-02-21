CREATE TABLE pages (
  mindshapeseo_focus_keyword                 varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_ogtitle                       varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_ogurl                         varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_ogimage                       int(11) unsigned             NOT NULL DEFAULT '0',
  mindshapeseo_ogdescription                 text                         NOT NULL,
  mindshapeseo_priority                      double(11,2) DEFAULT '0.50'  NOT NULL,
  mindshapeseo_change_frequency              varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_no_index                      tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_no_index_recursive            tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_no_follow                     tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_no_follow_recursive           tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_exclude_from_sitemap          tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_exclude_suppages_from_sitemap tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_sub_sitemap                   tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_disable_title_attachment      tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_canonical                     varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_alternative_title             varchar(255) DEFAULT ''      NOT NULL
);

CREATE TABLE pages_language_overlay (
  mindshapeseo_focus_keyword                 varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_ogtitle                       varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_ogurl                         varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_ogimage                       int(11) unsigned             NOT NULL DEFAULT '0',
  mindshapeseo_ogdescription                 text                         NOT NULL,
  mindshapeseo_priority                      double(11,2) DEFAULT '0.50'  NOT NULL,
  mindshapeseo_change_frequency              varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_no_index                      tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_no_index_recursive            tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_no_follow                     tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_no_follow_recursive           tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_exclude_from_sitemap          tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_exclude_suppages_from_sitemap tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_sub_sitemap                   tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_disable_title_attachment      tinyint(1) DEFAULT '0'       NOT NULL,
  mindshapeseo_canonical                     varchar(255) DEFAULT ''      NOT NULL,
  mindshapeseo_alternative_title             varchar(255) DEFAULT ''      NOT NULL
);

CREATE TABLE tx_mindshapeseo_domain_model_configuration (
  uid                        int(11)                         NOT NULL auto_increment,
  pid                        int(11) DEFAULT '0'             NOT NULL,

  domain                     varchar(255) DEFAULT ''         NOT NULL,
  sitename                   varchar(255) DEFAULT ''         NOT NULL,
  google_analytics           varchar(255) DEFAULT ''         NOT NULL,
  google_tagmanager          varchar(255) DEFAULT ''         NOT NULL,
  piwik_url                  varchar(255) DEFAULT ''         NOT NULL,
  piwik_idsite               varchar(255) DEFAULT ''         NOT NULL,
  title_attachment           varchar(255) DEFAULT ''         NOT NULL,
  title_attachment_seperator varchar(255) DEFAULT ''         NOT NULL,
  title_attachment_position  varchar(255) DEFAULT ''         NOT NULL,
  add_analytics              tinyint(1) unsigned DEFAULT '0' NOT NULL,
  add_hreflang               tinyint(1) unsigned DEFAULT '0' NOT NULL,
  add_jsonld                 tinyint(1) unsigned DEFAULT '0' NOT NULL,
  add_jsonld_breadcrumb      tinyint(1) unsigned DEFAULT '0' NOT NULL,
  facebook_default_image     int(11) unsigned                NOT NULL DEFAULT '0',
  image_sitemap_min_height   int(11) unsigned                NOT NULL DEFAULT '50',
  image_sitemap_min_width    int(11) unsigned                NOT NULL DEFAULT '50',
  jsonld_custom_url          varchar(255) DEFAULT ''         NOT NULL,
  jsonld_type                varchar(255) DEFAULT ''         NOT NULL,
  jsonld_name                varchar(255) DEFAULT ''         NOT NULL,
  jsonld_telephone           varchar(255) DEFAULT ''         NOT NULL,
  jsonld_fax                 varchar(255) DEFAULT ''         NOT NULL,
  jsonld_email               varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_facebook    varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_twitter     varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_googleplus  varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_instagram   varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_youtube     varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_linkedin    varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_xing        varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_printerest  varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_soundcloud  varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as_tumblr      varchar(255) DEFAULT ''         NOT NULL,
  jsonld_logo                int(11) unsigned                NOT NULL DEFAULT '0',
  jsonld_address_locality    varchar(255) DEFAULT ''         NOT NULL,
  jsonld_address_postalcode  varchar(255) DEFAULT ''         NOT NULL,
  jsonld_address_street      varchar(255) DEFAULT ''         NOT NULL,

  tstamp                     int(11) unsigned DEFAULT '0'    NOT NULL,
  crdate                     int(11) unsigned DEFAULT '0'    NOT NULL,
  cruser_id                  int(11) unsigned DEFAULT '0'    NOT NULL,
  deleted                    tinyint(4) unsigned DEFAULT '0' NOT NULL,

  t3ver_oid                  int(11) DEFAULT '0'             NOT NULL,
  t3ver_id                   int(11) DEFAULT '0'             NOT NULL,
  t3ver_wsid                 int(11) DEFAULT '0'             NOT NULL,
  t3ver_label                varchar(255) DEFAULT ''         NOT NULL,
  t3ver_state                tinyint(4) DEFAULT '0'          NOT NULL,
  t3ver_stage                int(11) DEFAULT '0'             NOT NULL,
  t3ver_count                int(11) DEFAULT '0'             NOT NULL,
  t3ver_tstamp               int(11) DEFAULT '0'             NOT NULL,
  t3ver_move_id              int(11) DEFAULT '0'             NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY t3ver_oid (t3ver_oid,t3ver_wsid)
);

CREATE TABLE tx_mindshapeseo_domain_model_redirect (
  uid                        int(11)                            NOT NULL auto_increment,
  pid                        int(11) DEFAULT '0'                NOT NULL,

  source_domain              int(11) unsigned DEFAULT '0'       NOT NULL,
  source_path                varchar(255) DEFAULT ''            NOT NULL,
  target                     varchar(255) DEFAULT ''            NOT NULL,
  http_statuscode            varchar(255) DEFAULT ''            NOT NULL,
  hits                       int(11) DEFAULT '0'                NOT NULL,
  last_hit_on                int(11) unsigned DEFAULT '0'       NOT NULL,
  edited                     int(11) unsigned DEFAULT '0'       NOT NULL,
  hidden                     tinyint(4) unsigned DEFAULT '0'    NOT NULL,
  regex                      tinyint(1) unsigned DEFAULT '0'    NOT NULL,

  tstamp                     int(11) unsigned DEFAULT '0'       NOT NULL,
  crdate                     int(11) unsigned DEFAULT '0'       NOT NULL,
  cruser_id                  int(11) unsigned DEFAULT '0'       NOT NULL,
  deleted                    tinyint(4) unsigned DEFAULT '0'    NOT NULL,

  PRIMARY KEY (uid),
  KEY Source_Domain_Source_Path (source_domain, source_path),
);