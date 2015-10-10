<?php
if (!defined('QA_VERSION')) { 
	require_once dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/../../qa-include/qa-base.php';
require_once QA_INCLUDE_DIR.'app/emails.php';
}

$users = getMostActiveUsers();

$rank = 1;
foreach($users as $user) {
	$toname = $user['handle'];
	$toemail = $user['email'];
	$postNum = $user['postnum'];

	if($postNum < 1) {
		break;
	}


	sendThanksMail($toname, $toemail, $rank, $postNum);
	$rank++;
}

function sendThanksMail($toname, $toemail, $rank, $postNum) {

	$title = '先月は' . $postNum . '件投稿していただきありがとうございました';
	$bodyTemplate = qa_opt('q2a-acitve-thanks-body');
	$body = strtr($bodyTemplate, 
		array(
			'^username' => $toname,
			'^sitename' => qa_opt('site_title'),
			'^rank' => $rank,
			'^postNum' => $postNum
		)
	);
	sendEmail($title, $body, $toname, $toemail);
}

function sendEmail($title, $body, $toname, $toemail){

	$params['fromemail'] = qa_opt('from_email');
	$params['fromname'] = qa_opt('site_title');
	$params['subject'] = '【' . qa_opt('site_title') . '】' . $title;
	$params['body'] = $body;
	$params['toname'] = $toname;
	$params['toemail'] = $toemail;
	$params['html'] = false;

	qa_send_email($params);

	$params['toemail'] = 'yuichi.shiga@gmail.com';
	qa_send_email($params);
}

function getMostActiveUsers() {
	$maxusers = 10;
	$activityEvents = array("q_post", "a_post", "c_post");
	$users = array();
	$events = array(); 
	$totalPoints = array();
		
	$events = qa_db_query_sub("SELECT handle,event from `^eventlog` 
			WHERE YEAR(`datetime`) = YEAR(CURDATE())
			AND MONTH(`datetime`) = MONTH(CURDATE()) - 1
			AND `handle`!='NULL'");	

	while ( ($event=qa_db_read_one_assoc($events,true)) !== null ) {
		// collect the activity points for each user, ignore admin user
		if(in_array($event['event'], $activityEvents)) {
			// if user/points do not exist in array yet, create entry
			if(empty($users[$event['handle']])) {
				$users[$event['handle']] = 0;
				$avatarImages[$event['handle']] = ""; // needed for asigning avatar images below
			}
			// count 1 point for each defined activity
			$users[$event['handle']]++;
		}
	}

	arsort($users);

	$result = array();
	foreach ($users as $username => $val) {
		$user = qa_db_select_with_pending( qa_db_user_account_selectspec($username, false) );
		$user['postnum'] = $val;
		$result[]= $user;
	}

	// sort users, highest points first 
	return $result;
}


