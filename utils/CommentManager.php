<?php

class CommentManager
{
	private static $instance = null;

	private function __construct()
	{
		require_once(ROOT . '/utils/DB.php');
		require_once(ROOT . '/class/Comment.php');
	}

	public static function getInstance()
	{
		if (null === self::$instance) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	//we can remove this function since we already join this on news table
	public function listComments()
	{
		$db = DB::getInstance();
		//avoid selecting all columns(*) when not needed
		// do not select all records..use pagination to limit the request
		$rows = $db->select('SELECT * FROM `comment`');  
		$comments = [];
		foreach($rows as $row) {
			$n = new Comment();
			$comments[$row['id']] = $n->setId($row['id'])
			  ->setBody($row['body'])
			  ->setCreatedAt($row['created_at'])
			  ->setNewsId($row['news_id']);
		}

		return $comments;
	}

	public function addCommentForNews($body, $newsId)
	{
		$db = DB::getInstance();
		$sql = "INSERT INTO `comment` (`body`, `created_at`, `news_id`) VALUES('". $body . "','" . date('Y-m-d') . "','" . $newsId . "')";
		$db->exec($sql);
		return $db->lastInsertId($sql);
	}

	public function deleteComment($id)
	{
		$db = DB::getInstance();
		$sql = "DELETE FROM `comment` WHERE `id`=" . $id;
		return $db->exec($sql);
	}
}