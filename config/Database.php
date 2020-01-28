<?php
class Database
{
	private static $dbhost = "localhost";
	private static $dbname = "collectorsscave_collectorsscave";
	private static $dbuser = "collectorsscave_collectorsscave";
	private static $dbpass = "+u6Au(-Lm8T{";
	private static $dbconn = null;
	const BASE_URL = "http://collectorsscave.phpsitescripts.com/";

	public function __construct() {
		die('Action not allowed'); 
	}

	public static function connect() {
		# one connection for whole program
		if (null == self::$dbconn) {

			try
			{
				self::$dbconn = new \PDO("mysql:host=" . self::$dbhost . ";dbname=" . self::$dbname, self::$dbuser, self::$dbpass);
			}
			catch(PDOException $e)
			{
				echo 'Connection failed: ' . $e->getMessage();
				exit;
			}
		}
		return self::$dbconn;
	}

    public static function query($sqlquery, $attributearray) {
        # query the database
        $sqlqueryfields = '';
        $sqlvariables = '';
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        foreach ($attributearray as $attribute) {
            $sqlqueryfields .= $attribute . '=?, ';
            $sqlvariables .= $sqlvariables . ', ';
        }
        $sqlqueryfields = rtrim($sqlqueryfields, ',');
        $sqlvariables = rtrim($sqlvariables, ',');

        $sqlquery = "update members set " . $sqlqueryfields . " where id=?";
        $q = $pdo->prepare($sqlquery);
        $q->execute(array($id, $sqlvariables));
    }

	public static function disconnect() {
		self::$dbconn = null;
	}

}
