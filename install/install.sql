CREATE TABLE IF NOT EXISTS hdio2c_sync (
OXID varchar(32) NOT NULL,
object text NOT NULL, 
type varchar(50),
state TINYINT DEFAULT 0, 
added TIMESTAMP DEFAULT NOW(),
modified TIMESTAMP(8),
PRIMARY KEY (OXID)

)