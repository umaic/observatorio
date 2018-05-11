<?php

use Phinx\Migration\AbstractMigration;

class PointLimitTableInfoTrigger extends AbstractMigration
{

    public function up()
    {
        $this->execute("
            ALTER TABLE post_point ADD COLUMN admin_boundary_id INTEGER(11);
            DROP TABLE IF EXISTS admin_boundaries;
            CREATE TABLE admin_boundaries (
              id INTEGER(11) NOT NULL,
              parent_id INTEGER(11) DEFAULT NULL,
              name VARCHAR(75) COLLATE utf8_general_ci NOT NULL,
              geom MULTIPOLYGON NOT NULL,
              SPATIAL INDEX(geom)
            );
            DROP TRIGGER IF EXISTS insert_post_point;
            DELIMITER $$
            CREATE TRIGGER insert_post_point BEFORE INSERT ON post_point
              FOR EACH ROW
            BEGIN
                  SET NEW.admin_boundary_id = (
                    SELECT admin_boundaries.id
                    FROM admin_boundaries
                    WHERE ST_Contains(admin_boundaries.geom, NEW.value)
                    AND admin_boundaries.parent_id>0
                  );
            END;$$
            DELIMITER;
            DROP TRIGGER IF EXISTS update_post_point;
            DELIMITER $$
            CREATE TRIGGER update_post_point BEFORE UPDATE ON post_point
              FOR EACH ROW
            BEGIN
                  SET NEW.admin_boundary_id = (
                    SELECT admin_boundaries.id
                    FROM admin_boundaries
                    WHERE ST_Contains(admin_boundaries.geom, NEW.value)
                    AND admin_boundaries.parent_id>0
                  );
            END;$$
            DELIMITER ;
        ");
     }
    public function down()
    {
    }
}
