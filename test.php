<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 25.04.2017
 * Time: 17:17
 */
include __DIR__.'/vendor/autoload.php';

$db = new \ActiveRecord\db\Connection();
$db->dsn = 'mysql:host=sanddb.gtflixtv.com;dbname=shared';
$db->username = 'develop';
$db->password = 'develop@box';
$db->charset = 'utf8';

\ActiveRecord\db\ActiveRecord::setDb($db);
\ActiveRecord\db\Query::setDb($db);

$sharedDatabase = new \ActiveRecord\GeneratorDatabase('shared');
$sharedDatabase->addTable('website');
$sharedDatabase->addTable('rest_query');
$sharedDatabase->addTable('script_log');

$geoDatabase = new \ActiveRecord\GeneratorDatabase('geoip');
$geoDatabase->addTable('geo_zone');

$generator = new \ActiveRecord\Generator($db);
$generator->addDatabase($sharedDatabase);
$generator->addDatabase($geoDatabase);


$generator->generate('model',__DIR__.'/model');