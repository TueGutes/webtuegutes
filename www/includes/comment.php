<?php
/**
 * Kommentarfunktion
 *
 * Bietet Kommentarfunktion
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

require_once './includes/UTILS.php';

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
			
			$_USER->redirect($HOST . '/deeds_details?id=' . $deedID . '&success=1');
		}
	}
}

if(isset($_GET['success']) && $_GET['success'] == '1')
{
	$output = "<green>" . $wlang['comment_suc_sent'] . "</green><br>";
	$scrollToElemID = "#commentWrap";
}

$js = '';
if(isset($scrollToElemID))
	$js = "<script type='text/javascript'>
	$(function() {
		$('html, body').animate({
			scrollTop: $('" . $scrollToElemID . "').offset().top - 60
		}, 800);
	});
	</script>"; // - 60 nav bar height
	
if($_USER->hasGroup($_GROUP_MODERATOR))
	$js .= "<script type='text/javascript'>
	$('.delete_comment').click(function(e)
	{
		e.preventDefault();
		
		var confirmed = confirm('Bist du wirklich sicher?');
		if(confirmed)
		{
			$.ajax(
			{
				url: '" . $HOST . "/admin?page=user" . "',
				type: 'post',
				data: { action: 'deleteComment', value: $(this).data('id') },
				success: function(data)
				{}
			});
			var comment = $(this).closest('.comment');
			comment.next('br').remove();
			comment.remove();
		}
	});
	</script>";


//====get comments====
$commentCount = DBFunctions::db_countDeedComments($deedID);
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
	if($entry->status != 'deleted')
		$author = '<div class="author"><a href="' . $HOST . '/profile?user=' . $username . '"><img src="' . $_USER->getProfileImagePathOf($userid, 32) . '" /> ' . $username . '</a>';
	else
		$author = '<div class="author deleted">gelöschter Nutzer';
	$comments .= '<div class="comment"><div class="createDate">' . $dh->get('d.m.Y H:i:s') . ($_USER->hasGroup($_GROUP_MODERATOR) ? '&nbsp;<a href="#" class="delete_comment" data-id="' . $entry->id . '"><i class="fa fa-times" aria-hidden="true"></i></a>' : '') . '</div>' . $author . '</div><div class="text">' . $entry->commenttext . '</div></div><br>';
}


$_COMMENTS = "
<div id='commentWrap'>
	<h3>" . $wlang['comment_head'] . "</h3>
	<div id='comments'>" . $pageSelect . '<br>' . $comments . '<br>' . $pageSelect . "<br></div>
	
	<h3 id='commentCreate'>" . $wlang['comment_form_head'] . "<br></h3>
	<div>" . $output . "<br></div>
	<form method='post' class='block'>
		<textarea cols='42' rows='4' name='comment' size='20' placeholder='" . $wlang['comment_form_message'] . "' required>" . $message . "</textarea>
		<br>
		<div class='center'>
			<input type='hidden' name='set' value='1' />
			<input type='submit' value='" . $wlang['comment_form_submit'] . "'>
		</div>
	</form>
</div>
" . $js;
?>

