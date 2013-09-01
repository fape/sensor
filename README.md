Sensor
======

Sensor portal

Install
------
* Download/checkout everything from this repo
* Create database tables with [database.sql](database.sql)
* Copy [config-default.inc.php](config-default.inc.php) and rename to `config.inc.php`
* Set database connection info in `config.inc.php`

First steps
------
1. Create new Unit in phpmyadmin
  * id: default blank
  * name: unit name eg °C
2. Create new Sensor in phpmyadmin
  * id: default blank
  * name: sensor name, eg living room, thermo sensor
  * unit_id: id of the unit, what you created in 1. step.
  * uuid: unique identifier. Use [online uuid generator tool](http://www.guidgenerator.com/) or any random string



