DROP TABLE IF EXISTS ci_logs;
CREATE TABLE ci_logs (
    ip                      INT NOT NULL,
    page                    VARCHAR(255) NOT NULL,
    user_agent              VARCHAR(255) NOT NULL,
    referrer                VARCHAR(255) NOT NULL,
    logged                  TIMESTAMP NOT NULL
                            default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    username                VARCHAR(255) NOT NULL,
    memory                  INT UNSIGNED NOT NULL,
    render_elapsed          FLOAT NOT NULL,
    ci_elapsed              FLOAT NOT NULL,
    controller_elapsed      FLOAT NOT NULL,
    mysql_elapsed           FLOAT NOT NULL,
    mysql_count_queries     TINYINT UNSIGNED NOT NULL,
    mysql_queries           TEXT NOT NULL
) ENGINE=ARCHIVE;
