<?php
/**
 * Kommentarfunktion
 *
 * Bietet Kommentarfunktion
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

require './includes/UTILS.php';

$deedID = $_GET['id'];
$message = isset($_POST['comment']) ? $_POST['comment'] : '';

$output = '';
if(isset($_POST['set']) && $_POST['set'] == '1')
{
	$scrollToElemID = "#commentCreate";

	$error = false;
	if(empty($message))
	{
		$output .= "<red>" . $wlang['comment_err_messageEmpty'] . "</red><br>";
		$error = true;
	}
	else if(strlen($message) > 8000)
	{
		$output .= "<red>" . $wlang['comment_err_messageLength'] . "</red><br>";
		$error = true;
	}
	
	if(!$error)
	{ 
		if(DBFunctions::db_createDeedComment($deedID, $_USER->getID(), $message))
		{
			$username = $_USER->getUsername();
			if(DBFunctions::db_getUsernameOfContactPersonByGuteTatID($deedID) != $username)
			{
				$deedName = $tat["name"];
				$infos = "<a href='" . $HOST . "/profile?user=" . $username . "'>" . $username . "</a> hat ein Kommentar unter deiner Guten Tat <a href='" . $HOST . "/deeds_details?id=" . $deedID . "'>" . $deedName . "</a> verfasst.";
			
				$subject = "Nachicht TueGutes";
				$mailMessage = $infos;
				//$mailFrom = "From: TueGutes";
				
				/*$array = array(); // admin mails
				$count = count($array);
				for($i = 0; $i < $count; $i++)
					mail($array[$i], $subject, $mailMessage, $mailFrom);*/

				sendEmail(DBFunctions::db_getEmailOfContactPersonByGuteTatID($deedID), $subject, $mailMessage);
			}
			
			$output .= "<green>" . $wlang['comment_suc_sent'] . "</green><br>";
			
			// Felder resetten
			$message = '';
			
			$scrollToElemID = "#commentWrap";
		}
	}
	
	echo "<script type='text/javascript'>
	$(function() {
		$('html, body').animate({
			scrollTop: $('" . $scrollToElemID . "').offset().top - 60
		}, 800);
	});
	</script>"; // - 60 nav bar height
}


//====get comments====
$commentCount = DBFunctions::countDeedComments($deedID);
$commentsPerPage = 10;
$neededPages = ceil($commentCount/$commentsPerPage);
$currentPage = isset($_POST['commentPage']) ? $_POST['commentPage'] : 1;
$pageSelect = '';
if($neededPages > 1)
{
	$pageSelect .= '<div class="commentPageSelection">';
	for($i = 0; $i < $neededPages; $i++)
		$pageSelect .= '<form action="" method="post"><input type="submit" name="commentPage"' . ($currentPage == ($i+1) ? ' class="selected"' : '') . ' value="' . ($i+1) . '" /></form>';
	$pageSelect .= '</div>';
}


$commentsArray = DBFunctions::db_createDeedCommentsToList($deedID, ($currentPage-1)*$commentsPerPage, $commentsPerPage);
$comments = '';
for($i = 0; $i < sizeof($commentsArray); $i++)
{
	$entry = $commentsArray[$i];
	$userid = $entry->user_id_creator;
	$username = $entry->username;
	$dh = (new DateHandler())->set($entry->date_created);
	$comments .= '<div class="comment"><div class="createDate">' . $dh->get('d.m.Y H:i:s') . '</div><div class="author"><a href="' . $HOST . '/profile?user=' . $username . '"><img src="' . $_USER->getProfileImagePathOf($userid, 32) . '" />' . $username . '</a></div><div class="text">' . $entry->commenttext . '</div></div><br>';
}
?>

<div id='commentWrap'>
	<h3><?php echo $wlang['comment_head']; ?></h3>
	<div id='comments'><?php echo $pageSelect . '<br>' . $comments . '<br>' . $pageSelect . '<br>'; ?></div>
	
	<h3 id='commentCreate'><?php echo $wlang['comment_form_head']; ?><br></h3>
	<div><?php echo $output; ?><br></div>
	<form method='post' class='block'>
		<textarea cols='42' rows='4' name='comment' size='20' placeholder='<?php echo $wlang['comment_form_message']; ?>' required><?php echo $message; ?></textarea>
		<br>
		<div class='center'>
			<input type='hidden' name='set' value='1' />
			<input type='submit' value='<?php echo $wlang['comment_form_submit']; ?>'>
		</div>
	</form>
</div>


