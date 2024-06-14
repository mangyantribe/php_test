<?php

class NewsManager
{
	private static $instance = null;

	private function __construct()
	{
		require_once(ROOT . '/utils/DB.php');
		require_once(ROOT . '/utils/CommentManager.php');
		require_once(ROOT . '/class/News.php');
	}

	public static function getInstance()
	{
		if (null === self::$instance) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	* list all news
	*/
	public function listNews()
	{
		$db = DB::getInstance();
		//old version 
		//$rows = $db->select('SELECT * FROM `news`');

		//my version
		// join the news table to comment to avoid multiple requestion in server
		// so we can add pagination(limit and offset) here to metigate the request of the data is too large like hundred thousans of records
		// avoid selecting all columns(*) when not needed

		$rows = $db->select('SELECT a.id,a.title,a.`body`,a.created_at,b.`body` AS `news_comment`,b.id as comment_id FROM `news` a JOIN `comment` b ON a.`id` = b.`news_id` ORDER BY a.`id`');

		$news = [];
		foreach($rows as $row) {
			//old version 

			// $n = new News();
			// $c = new Comment();
			// $news[$row['title']][] = $n->setId($row['id'])
			// 	->setTitle($row['title'])
			//   ->setBody($row['body'])
			//   ->setCreatedAt($row['created_at']);

			//my version
			$news[$row['title']][] = array(
				'title' => $row['title'],
				'body' => $row['body'],	
				'comment_id' => $row['comment_id'],
				'news_comment' => $row['news_comment'],

			);
  
		}

		return $news;
	}

	/**
	* add a record in news table
	*/
	public function addNews($title, $body)
	{
		$db = DB::getInstance();
		$sql = "INSERT INTO `news` (`title`, `body`, `created_at`) VALUES('". $title . "','" . $body . "','" . date('Y-m-d') . "')";
		$db->exec($sql);
		return $db->lastInsertId($sql);
	}

	/**
	* deletes a news, and also linked comments
	*/
	public function deleteNews($id)
	{
		$comments = CommentManager::getInstance()->listComments();
		$idsToDelete = [];

		foreach ($comments as $comment) {
			if ($comment->getNewsId() == $id) {
				$idsToDelete[] = $comment->getId();
			}
		}

		foreach($idsToDelete as $id) {
			CommentManager::getInstance()->deleteComment($id);
		}

		$db = DB::getInstance();
		$sql = "DELETE FROM `news` WHERE `id`=" . $id;
		return $db->exec($sql);
	}
}