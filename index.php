<?php

require_once dirname(__FILE__) . '/lib/vendor/Albino/DatabaseManager.php';
require_once dirname(__FILE__) . '/lib/vendor/Albino/Connection.php';

$manager = new DatabaseManager();
$manager->addConnection('default', new Connection(array(
  'host' => 'localhost',
  'name' => 'gijsn_symfony2_test',
  'username' => 'freshheads',
  'password' => 'ypo73mpv'
)));

require_once dirname(__FILE__) . '/lib/table/PageTable.php';
require_once dirname(__FILE__) . '/lib/model/Page.php';

/* @var PageTable $table */
$table = $manager->getTable('Page');

// $pages = $table->getAll();
$pages = $table->getOneByName('b7a46e4498eac20c79315caec8a69f73');

echo "<pre>";
print_r($pages);
echo "</pre>";
die();