<?php
class Settings
{
	private $setting = array();
	public function getSettings() {
		
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "select * from adminsettings";
		$q = $pdo->prepare($sql);
		$q->execute();
		$q->setFetchMode(PDO::FETCH_ASSOC);
		$setting = $q->fetch();
		return $setting;

	}

}
