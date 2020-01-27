<?php
class PageContent
{
	public $content;
	public $pagename;

	public function showPage($pagename) {
		
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "select htmlcode from pages where name=? or slug=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($pagename, $pagename));
		$q->setFetchMode(PDO::FETCH_ASSOC);
		$data = $q->fetch();
		$content = $data['htmlcode'];
$content = <<<HEREDOC
$content
HEREDOC;
		Database::disconnect();
		
		return $content;
	}
	
}