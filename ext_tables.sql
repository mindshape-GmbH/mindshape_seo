CREATE TABLE pages
(
    mindshapeseo_focus_keyword            varchar(255) DEFAULT '' NOT NULL,
    mindshapeseo_no_index_recursive       tinyint(1) DEFAULT '0' NOT NULL,
    mindshapeseo_no_follow_recursive      tinyint(1) DEFAULT '0' NOT NULL,
    mindshapeseo_disable_title_attachment tinyint(1) DEFAULT '0' NOT NULL,
    mindshapeseo_jsonld_breadcrumb_title  varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE tx_mindshapeseo_domain_model_configuration
(
    uid                                    int(11) NOT NULL auto_increment,
    pid                                    int(11) DEFAULT '0' NOT NULL,

    domain                                 varchar(255) DEFAULT '' NOT NULL,
    merge_with_default                     tinyint(1) unsigned DEFAULT '1' NOT NULL,
    google_analytics                       varchar(255) DEFAULT '' NOT NULL,
    google_analytics_v4                       varchar(255) DEFAULT '' NOT NULL,
    google_tagmanager                      varchar(255) DEFAULT '' NOT NULL,
    matomo_url                             varchar(255) DEFAULT '' NOT NULL,
    matomo_idsite                          varchar(255) DEFAULT '' NOT NULL,
    title_attachment                       varchar(255) DEFAULT '' NOT NULL,
    title_attachment_seperator             varchar(255) DEFAULT '' NOT NULL,
    title_attachment_position              varchar(255) DEFAULT '' NOT NULL,
    add_analytics                          tinyint(1) unsigned DEFAULT '0' NOT NULL,
    google_analytics_use_cookie_consent    tinyint(1) unsigned DEFAULT '0' NOT NULL,
    google_analytics_v4_use_cookie_consent tinyint(1) unsigned DEFAULT '0' NOT NULL,
    tagmanager_use_cookie_consent          tinyint(1) unsigned DEFAULT '0' NOT NULL,
    matomo_use_cookie_consent              tinyint(1) unsigned DEFAULT '0' NOT NULL,
    add_jsonld                             tinyint(1) unsigned DEFAULT '0' NOT NULL,
    add_jsonld_breadcrumb                  tinyint(1) unsigned DEFAULT '0' NOT NULL,
    jsonld_custom_url                      varchar(255) DEFAULT '' NOT NULL,
    jsonld_type                            varchar(255) DEFAULT '' NOT NULL,
    jsonld_name                            varchar(255) DEFAULT '' NOT NULL,
    jsonld_telephone                       varchar(255) DEFAULT '' NOT NULL,
    jsonld_fax                             varchar(255) DEFAULT '' NOT NULL,
    jsonld_email                           varchar(255) DEFAULT '' NOT NULL,
    jsonld_same_as_facebook                varchar(255) DEFAULT '' NOT NULL,
    jsonld_same_as_twitter                 varchar(255) DEFAULT '' NOT NULL,
    jsonld_same_as_instagram               varchar(255) DEFAULT '' NOT NULL,
    jsonld_same_as_youtube                 varchar(255) DEFAULT '' NOT NULL,
    jsonld_same_as_linkedin                varchar(255) DEFAULT '' NOT NULL,
    jsonld_same_as_xing                    varchar(255) DEFAULT '' NOT NULL,
    jsonld_same_as_printerest              varchar(255) DEFAULT '' NOT NULL,
    jsonld_same_as_soundcloud              varchar(255) DEFAULT '' NOT NULL,
    jsonld_same_as_tumblr                  varchar(255) DEFAULT '' NOT NULL,
    jsonld_logo                            int(11) unsigned DEFAULT null,
    jsonld_address_locality                varchar(255) DEFAULT '' NOT NULL,
    jsonld_address_postalcode              varchar(255) DEFAULT '' NOT NULL,
    jsonld_address_street                  varchar(255) DEFAULT '' NOT NULL,

    tstamp                                 int(11) unsigned DEFAULT '0' NOT NULL,
    crdate                                 int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id                              int(11) unsigned DEFAULT '0' NOT NULL,
    deleted                                tinyint(4) unsigned DEFAULT '0' NOT NULL,

    sys_language_uid                       int(11) DEFAULT '0' NOT NULL,
    l10n_source                            int(11) DEFAULT '0' NOT NULL,
    l10n_parent                            int(11) DEFAULT '0' NOT NULL,
    l10n_diffsource                        mediumblob,

    PRIMARY KEY (uid),
    KEY                                    parent (pid),
    KEY language (l10n_parent, sys_language_uid)
);
