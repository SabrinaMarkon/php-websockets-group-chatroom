<?php
class PageContent
{
	public $content;
	public $slug;

	public function showPage($slug) {
		
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "select htmlcode from pages where slug=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($slug));
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