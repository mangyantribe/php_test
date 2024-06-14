<?php

define('ROOT', __DIR__);
require_once(ROOT . '/utils/NewsManager.php');
require_once(ROOT . '/utils/CommentManager.php');

//old version 

// foreach (NewsManager::getInstance()->listNews() as $news) {
// 	echo("############ NEWS " . $news->getTitle() . " ############\n");
// 	echo($news->getBody() . "\n");
// 	foreach (CommentManager::getInstance()->listComments() as $comment) {
// 		if ($comment->getNewsId() == $news->getId()) {
// 			echo("Comment " . $comment->getId() . " : " . $comment->getBody() . "\n");
// 		}
// 	}
// }


//my version
//initialize the instance of a class and pass it to variable for dynamically use of variable
$newsManager = NewsManager::getInstance();

// for this we only use 1 query to fetch the records
// we avoid multiple request in server because if cause loading if the data is too big

foreach ($newsManager->listNews() as $key => $news) {
	echo("############ NEWS " . $key . " ############\n");

	 if(count($news) > 0){ //display only if there is 1 or more comments
		foreach ($news as $k => $val) {
			if($k == 0) echo($val['body'] . "\n"); //echo the first body only
			echo("Comment " . $val['comment_id'] . " : " . $val['news_comment'] . "\n");	
		}
	}
	
}

//this initialization has no use. we can comment it for the meantime

//$commentManager = CommentManager::getInstance();
//$c = $commentManager->listComments();