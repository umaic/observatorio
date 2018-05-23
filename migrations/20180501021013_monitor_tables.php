<?php

use Phinx\Migration\AbstractMigration;

class MonitorTables extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('actors', 'actors')");
        $this->execute("INSERT INTO oauth_scopes (scope, name) VALUES ('sources', 'sources')");
        $this->execute("
            CREATE TABLE `actors` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `parent_id` int(11) DEFAULT NULL,
              `tag` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
              `slug` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
              `type` varchar(20) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'actor',
              `color` varchar(6) COLLATE utf8_spanish_ci DEFAULT NULL,
              `icon` varchar(20) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'tag',
              `description` text COLLATE utf8_spanish_ci,
              `role` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
              `priority` int(11) NOT NULL DEFAULT '99',
              `created` int(11) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `created` (`created`),
              KEY `parent_id` (`parent_id`),
              CONSTRAINT `actors_fk1` FOREIGN KEY (`parent_id`) REFERENCES `actors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='custom table in Ushahidi. Kuery development';
            ");
        $this->execute("
            CREATE TABLE `sources` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `parent_id` int(11) DEFAULT NULL,
              `tag` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
              `slug` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
              `type` varchar(20) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'source',
              `color` varchar(6) COLLATE utf8_spanish_ci DEFAULT NULL,
              `icon` varchar(20) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'tag',
              `description` text COLLATE utf8_spanish_ci,
              `role` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
              `priority` int(11) NOT NULL DEFAULT '99',
              `created` int(11) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `sources_fk1_idx` (`parent_id`),
              KEY `created` (`created`),
              CONSTRAINT `sources_fk1` FOREIGN KEY (`parent_id`) REFERENCES `sources` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
            ");
        $this->execute("
            CREATE TABLE `posts_actors` (
              `post_id` int(11) NOT NULL,
              `actor_id` int(11) NOT NULL,
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `form_attribute_id` int(11) NOT NULL,
              `created` int(11) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_post_actors_attributes_ids` (`post_id`,`actor_id`,`form_attribute_id`),
              KEY `actor_id` (`actor_id`),
              KEY `post_id` (`post_id`),
              KEY `form_attribute_id` (`form_attribute_id`),
              CONSTRAINT `posts_actors_fk1` FOREIGN KEY (`actor_id`) REFERENCES `actors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `posts_actors_fk2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `posts_actors_fk3` FOREIGN KEY (`form_attribute_id`) REFERENCES `form_attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
            ");
         $this->execute("
            CREATE TABLE `posts_sources` (
              `post_id` int(11) NOT NULL,
              `source_id` int(11) NOT NULL,
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `form_attribute_id` int(11) NOT NULL,
              `created` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `ps_fk1_idx` (`post_id`),
              KEY `ps_fk1\3_idx` (`form_attribute_id`),
              KEY `ps_fk2_idx` (`source_id`),
              CONSTRAINT `ps_fk1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `ps_fk2` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `ps_fk3` FOREIGN KEY (`form_attribute_id`) REFERENCES `form_attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
            ");
         $this->execute("
            CREATE TABLE `post_tag_actor` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `post_id` int(11) NOT NULL,
              `tag_id` int(11) NOT NULL,
              `actor_id` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `pta_fk1_idx` (`post_id`),
              KEY `pta_fk2_idx` (`tag_id`),
              KEY `pta_fk3_idx` (`actor_id`),
              CONSTRAINT `pta_fk1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `pta_fk2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `pta_fk3` FOREIGN KEY (`actor_id`) REFERENCES `actors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
            ");
         $this->execute("
            CREATE TABLE `post_source_detail` (
              `post_id` int(11) NOT NULL,
              `source_id` int(11) NOT NULL,
              `event_desc` text COLLATE utf8_spanish_ci,
              `url` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
              `event_date` date NOT NULL,
              `id` int(11) NOT NULL AUTO_INCREMENT,
              PRIMARY KEY (`id`),
              KEY `psd_fk1_idx` (`post_id`),
              KEY `psd_fk2_idx` (`source_id`),
              CONSTRAINT `psd_fk1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `psd_fk2` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
            ");
         $this->execute("
            CREATE TABLE `victim_age_group` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `age_group` varchar(100) NOT NULL,
              PRIMARY KEY (`id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Age groups of incident victims';
            ");
        $this->execute("
            CREATE TABLE `victim_age` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `age` varchar(50) NOT NULL DEFAULT '',
              `id_age_group` int(11) NOT NULL,
              PRIMARY KEY (`id`) USING BTREE,
              KEY `a_fk1_idx` (`id_age_group`),
              CONSTRAINT `a_fk1` FOREIGN KEY (`id_age_group`) REFERENCES `victim_age_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Age of incident victims';
            ");
        $this->execute("
            CREATE TABLE `victim_condition` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `condition` varchar(30) NOT NULL,
              PRIMARY KEY (`id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Condition of incident victims';
            ");
         $this->execute("
            CREATE TABLE `victim_sub_condition` (
              `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Almacena el identificado',
              `victim_condition_id` int(11) NOT NULL,
              `sub_condition` varchar(100) NOT NULL COMMENT 'Almacena el nombre de la sub condició',
              PRIMARY KEY (`id`) USING BTREE,
              KEY `fk_victim_sub_condition_victim_condition1` (`victim_condition_id`) USING BTREE,
              CONSTRAINT `vsc_fk1` FOREIGN KEY (`victim_condition_id`) REFERENCES `victim_condition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Sub Conditions of incident victims';
            ");
         $this->execute("
            CREATE TABLE `victim_ethnic_group` (
              `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Almacena el identificador de la etnia',
              `ethnic_group` varchar(50) NOT NULL,
              PRIMARY KEY (`id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='Ethnic groups of incident victims';
            ");
         $this->execute("
            CREATE TABLE `victim_sub_ethnic_group` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `victim_ethnic_group_id` int(11) NOT NULL,
              `sub_ethnic_group` varchar(100) DEFAULT NULL,
              PRIMARY KEY (`id`) USING BTREE,
              KEY `fk_victim_sub_ethnic_group_victim_ethnic_group1` (`victim_ethnic_group_id`) USING BTREE,
              CONSTRAINT `vseg_fk1` FOREIGN KEY (`victim_ethnic_group_id`) REFERENCES `victim_ethnic_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8 COMMENT='Sub Ethnic groups of incident victims';
            ");
         $this->execute("
            CREATE TABLE `victim_gender` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `gender` varchar(30) NOT NULL DEFAULT '',
              PRIMARY KEY (`id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Gender of incident victims';
            ");
         $this->execute("
            CREATE TABLE `victim_occupation` (
              `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Almacena el identificador de la Ocupació',
              `occupation` varchar(100) NOT NULL COMMENT 'Almacena el nombre de la Ocupació',
              PRIMARY KEY (`id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='Almacena la ocupación de la víctima afecta por algún evento';
            ");
         $this->execute("
            CREATE TABLE `victim_status` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `status` varchar(100) NOT NULL,
              PRIMARY KEY (`id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='Status of incident involved';
            ");
         $this->execute("
            CREATE TABLE `victims` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `amount` int(11) DEFAULT NULL,
              `id_gender` int(11) DEFAULT NULL,
              `id_ethnic_group` int(11) DEFAULT NULL,
              `id_sub_ethnic_group` int(11) DEFAULT NULL,
              `id_condition` int(11) DEFAULT NULL,
              `id_sub_condition` int(11) DEFAULT NULL,
              `id_occupation` int(11) DEFAULT NULL,
              `id_age_group` int(11) DEFAULT NULL,
              `id_age` int(11) DEFAULT NULL,
              `id_status` int(11) DEFAULT NULL,
              `post_id` int(11) DEFAULT NULL,
              `tag_id` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `v_fk1_idx` (`id_age`),
              KEY `v_fk2_idx` (`id_age_group`),
              KEY `v_fk3_idx` (`id_condition`),
              KEY `v_fk4_idx` (`id_sub_condition`),
              KEY `v_fk5_idx` (`id_ethnic_group`),
              KEY `v_fk6_idx` (`id_sub_ethnic_group`),
              KEY `v_fk7_idx` (`id_status`),
              KEY `v_fk8_idx` (`id_gender`),
              KEY `v_fk9_idx` (`id_occupation`),
              KEY `post_id` (`post_id`),
              KEY `tag_id` (`tag_id`),
              CONSTRAINT `vic_fk1` FOREIGN KEY (`id_age`) REFERENCES `victim_age` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `vic_fk2` FOREIGN KEY (`id_age_group`) REFERENCES `victim_age_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `vic_fk3` FOREIGN KEY (`id_condition`) REFERENCES `victim_condition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `vic_fk4` FOREIGN KEY (`id_sub_condition`) REFERENCES `victim_sub_condition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `vic_fk5` FOREIGN KEY (`id_ethnic_group`) REFERENCES `victim_ethnic_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `vic_fk6` FOREIGN KEY (`id_sub_ethnic_group`) REFERENCES `victim_sub_ethnic_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `vic_fk7` FOREIGN KEY (`id_gender`) REFERENCES `victim_gender` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `vic_fk8` FOREIGN KEY (`id_status`) REFERENCES `victim_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `vic_fk9` FOREIGN KEY (`id_occupation`) REFERENCES `victim_occupation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `victims_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `victims_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
            ");
         $this->execute("
            CREATE TABLE `posts_victims` (
              `post_id` int(11) NOT NULL,
              `victim_id` int(11) NOT NULL,
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `form_attribute_id` int(11) NOT NULL,
              `created` int(11) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_post_victim_attr_id` (`post_id`,`victim_id`,`form_attribute_id`),
              KEY `ptv_fk1_idx` (`victim_id`),
              KEY `ptv_fk3_idx` (`form_attribute_id`),
              CONSTRAINT `ptv_fk1` FOREIGN KEY (`victim_id`) REFERENCES `victims` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `ptv_fk2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `ptv_fk3` FOREIGN KEY (`form_attribute_id`) REFERENCES `form_attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
            ");
         $this->execute("
            CREATE TABLE `post_tag_victim` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `post_id` int(11) NOT NULL,
              `tag_id` int(11) NOT NULL,
              `victim_id` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `ptv_fk1_idx` (`post_id`),
              KEY `ptvi_fk1_idx` (`post_id`),
              KEY `ptvi_fk2_idx` (`tag_id`),
              KEY `ptvi_fk3_idx` (`victim_id`),
              CONSTRAINT `ptvi_fk1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `ptvi_fk2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `ptvi_fk3` FOREIGN KEY (`victim_id`) REFERENCES `victims` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
            ");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DROP TABLE post_tag_victim");
        $this->execute("DROP TABLE posts_victims");
        $this->execute("DROP TABLE victims");
        $this->execute("DROP TABLE victim_status");
        $this->execute("DROP TABLE victim_occupation");
        $this->execute("DROP TABLE victim_gender");
        $this->execute("DROP TABLE victim_sub_ethnic_group");
        $this->execute("DROP TABLE victim_ethnic_group");
        $this->execute("DROP TABLE victim_sub_condition");
        $this->execute("DROP TABLE victim_condition");
        $this->execute("DROP TABLE victim_age");
        $this->execute("DROP TABLE victim_age_group");
        $this->execute("DROP TABLE post_source_detail");
        $this->execute("DROP TABLE post_tag_actor");
        $this->execute("DROP TABLE posts_sources");
        $this->execute("DROP TABLE posts_actors");
        $this->execute("DROP TABLE sources");
        $this->execute("DROP TABLE actors");
    }
}
