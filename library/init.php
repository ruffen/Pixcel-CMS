<?php
/*** include implementation of several php magic functions, such as __autoload and errorhandler ***/
require_once('library/php_settings.php');

/*** include logger class ***/
require_once('library/inkMVC/INKLogger.php');

/*** include PHPLinq - Linq for php ***/
require_once('classes/PHPLinq.php');
require_once('classes/PHPLinq/LinqToObjects.php');

/*** include database interface file **/
require_once('model/interface.db.php');

/*** include database file, use MySQL for now ***/
require_once('model/mysql.db.php');

/*** include session handler class and shared memory handler ***/
require_once('model/session.db.php');
require_once('library/inkMVC/memoryhandler.class.php');

/*** include config file ***/
require_once('library/config.php');

/*** include data handler **/
require_once('model/repository.model.php');

/*** include assets abstract class **/
require_once('model/assets/abstract.asset.php');

/*** include spots abstract classes **/
require_once('spots/staticspot.abstract.php');
require_once('spots/adminspot.abstract.php');

/*** include header classes **/
require_once('library/headerclasses/js.class.php');
require_once('library/headerclasses/css.class.php');

/** create logger instance **/
$logger = new INKLogger();

/** create a new repository **/
$dRep = new Repository();
/** create a new variable checker **/
$varChecker = new HttpVars($_GET, $_POST);
?>
