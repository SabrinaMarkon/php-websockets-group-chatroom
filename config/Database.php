<?php
class Database
{
	private static $dbhost = "localhost";
	private static $dbname = "YOUR_DATABASE_NAME";
	private static $dbuser = "YOUR_DATABASE_USER";
	private static $dbpass = "YOUR_DATABASE_PASS";
	private static $dbconn = null;
	const BASE_URL = "http://YOURDOMAIN.COM";

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

    public static function query($sqlquery, $attributearray, $id) {
        # query the database - TODO: DRY out the database areas of the codebase.
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
        $q->execute(array($sqlvariables, $id));
    }

	public static function disconnect() {
		self::$dbconn = null;
	}

}
