CREATE TABLE pages (
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
  mindshapeseo_canonical                     int(11) unsigned             NOT NULL DEFAULT '0'
);

CREATE TABLE pages_language_overlay (
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
  mindshapeseo_canonical                     int(11) unsigned             NOT NULL DEFAULT '0'
);

CREATE TABLE tx_mindshapeseo_domain_model_configuration (
  uid                       int(11)                         NOT NULL auto_increment,
  pid                       int(11) DEFAULT '0'             NOT NULL,

  domain                    varchar(255) DEFAULT ''         NOT NULL,
  google_analytics          varchar(255) DEFAULT ''         NOT NULL,
  piwik_url                 varchar(255) DEFAULT ''         NOT NULL,
  piwik_idsite              varchar(255) DEFAULT ''         NOT NULL,
  title_attachment          varchar(255) DEFAULT ''         NOT NULL,
  generate_sitemap          tinyint(1) unsigned DEFAULT '1' NOT NULL,
  add_hreflang              tinyint(1) unsigned DEFAULT '1' NOT NULL,
  add_jsonld                tinyint(1) unsigned DEFAULT '1' NOT NULL,
  facebook_default_image    int(11) unsigned                NOT NULL DEFAULT '0',
  jsonld_custom_url         varchar(255) DEFAULT ''         NOT NULL,
  jsonld_type               varchar(255) DEFAULT ''         NOT NULL,
  jsonld_telephone          varchar(255) DEFAULT ''         NOT NULL,
  jsonld_fax                varchar(255) DEFAULT ''         NOT NULL,
  jsonld_email              varchar(255) DEFAULT ''         NOT NULL,
  jsonld_same_as            text                            NOT NULL,
  jsonld_logo               int(11) unsigned                NOT NULL DEFAULT '0',
  jsonld_address_locality   varchar(255) DEFAULT ''         NOT NULL,
  jsonld_address_postalcode varchar(255) DEFAULT ''         NOT NULL,
  jsonld_address_street     varchar(255) DEFAULT ''         NOT NULL,

  tstamp                    int(11) unsigned DEFAULT '0'    NOT NULL,
  crdate                    int(11) unsigned DEFAULT '0'    NOT NULL,
  cruser_id                 int(11) unsigned DEFAULT '0'    NOT NULL,
  deleted                   tinyint(4) unsigned DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);
