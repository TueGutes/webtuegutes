<?php
/**
 * Nutzerverwaltung
 *
 * Erlaubt es Admins und Moderatoren Benutzer zu ändern, sperren oder zu löschen
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 * @author Shanghui Dai <shanghui.dai@stud.hs-hannover.de> (Nutzersuche)
 */

$CURRENT_PAGE = './admin?page=user';
$user = @$_REQUEST['user'];
$search = @$_REQUEST['search'];

require './includes/UTILS.php';
include "./includes/db_connector.php";

if(!empty($user))
{
	$profile = DBFunctions::db_get_user($user);
	$userFound = $profile != null;
	
	//====ACTIONS====
	if($userFound)
	{
		$emailHead = 'Nachricht vom TueGutes Team';
		$action = @$_POST['action'];
		if($action == 'mail')
		{	
			$message = $_POST['message'];
			sendEmail($profile['email'], $emailHead, $message);
			DBFunctions::db_historyEntry($_USER->getID(), 'SENT email TO  ' . $profile['username'] . ' (' . $profile['email'] . ')', $message, null, $profile['idUser']);
			$_USER->redirect($CURRENT_PAGE . '&user=' . $user);
		}
		else if($action == 'disable')
		{	
			$disabled = $profile['status'] == 'blocked';
			sendEmail($profile['email'], $emailHead, 'Ihr Account wurde ' . ($disabled ? 'wieder freigegeben' : 'gesperrt') . '.');
			DBFunctions::db_historyEntry($_USER->getID(), ($disabled ? 'ENABLED' : 'DISABLED') . ' ' . $profile['username'], '', null, $profile['idUser']);
			$profile['status'] = ($disabled ? 'Verifiziert' : 'blocked');
			DBFunctions::db_update_user($profile);
			$_USER->redirect($CURRENT_PAGE . '&user=' . $user);
		}
		else if($action == 'avatar')
		{	
			$avatarDir = './img/profiles/' . $profile['idUser'];
			if(is_dir($avatarDir))
			{
				array_map('unlink', glob($avatarDir . "/*.*"));
				rmdir($avatarDir);

				sendEmail($profile['email'], $emailHead, 'Ihr Avatar wurde entfernt.');
				DBFunctions::db_historyEntry($_USER->getID(), 'DELETED Avatar FROM ' . $profile['username'], '', null, $profile['idUser']);	
			}
			echo $_USER->getProfileImagePathOf($profile['idUser'], 128);
			exit;
		}
		else if($action == 'username' || 
				$action == 'email' || 
				$action == 'firstname' || 
				$action == 'lastname' || 
				$action == 'phonenumber' || 
				$action == 'messengernumber' || 
				$action == 'hobbies' || 
				$action == 'gender' || 
				$action == 'description')
		{	
			$value = $_POST['value'];
		
			switch($action)
			{
				case 'username':
					$emailMessage = 'Ihr Benutzername wurde geändert. Ab sofort können Sie sich mit dem neuen Benutzernamen <b>' . $value . '</b> einloggen.';
					break;
				case 'email':
					$emailMessage = 'Ihre E-Mail-Adresse wurde geändert. Ab sofort ist <b>' . $value . '</b> Ihre neue E-Mail-Adresse.';
					break;
				case 'firstname':
				case 'lastname':
					$emailMessage = 'Ihr Name wurde geändert.';
					break;
				case 'phonenumber':
					$emailMessage = 'Ihre Telefonnummer wurde geändert.';
					break;
				case 'messengernumber':
					$emailMessage = 'Ihre Messengernummer wurde geändert.';
					break;
				case 'hobbies':
					$emailMessage = 'Ihre Hobbys wurden verändert.';
					break;
				case 'gender':
					$emailMessage = 'Ihr Geschlecht wurde geändert.';
					break;
				case 'description':
					$emailMessage = 'Ihre Beschreibung wurde verändert.';
					break;
			}
			sendEmail($profile['email'], $emailHead, $emailMessage);
		
			if($action == 'hobbies') $action = 'hobbys';
			else if($action == 'phonenumber') $action = 'telefonnumber';
			DBFunctions::db_historyEntry($_USER->getID(), 'CHANGED ' . $action . ' TO ' . $value, 'FROM ' . @$profile[$action], null, $profile['idUser']);
		
			$profile[$action] = $value;
			DBFunctions::db_update_user($profile);
			exit;
		}
		else if($_USER->hasGroup($_GROUP_ADMIN) && $action == 'delete')
		{	
			DBFunctions::db_delete_user($user);
			sendEmail($profile['email'], $emailHead, 'Ihr Account wurde gelöscht.');
			DBFunctions::db_historyEntry($_USER->getID(), 'DELETED ' . $profile['username'], '', null, $profile['idUser']);
			$_USER->redirect($CURRENT_PAGE);
		}
	}
	//====/ACTIONS====	
}





require './includes/_top.php';
?>

<h2>Nutzerverwaltung</h2>

<div>
	<?php	
	if(!empty($search))
	{
		// zeige Suchergebnisse an ODER "Es wurden keine Benutzer gefunden"
		// gefundene Nutzer zeigen auf:
		/*
		<a href="' . $CURRENT_PAGE . '&user=' . $username1 . '">' . $username1 . '</a><br>
		<a href="' . $CURRENT_PAGE . '&user=' . $username2 . '">' . $username2 . '</a>
		<a href="' . $CURRENT_PAGE . '&user=' . $username3 . '">' . $username3 . '</a>
		<a href="' . $CURRENT_PAGE . '&user=' . $username4 . '">' . $username4 . '</a>
		<a href="' . $CURRENT_PAGE . '&user=' . $username5 . '">' . $username5 . '</a>
		*/
		echo 'coming soon: Suche';
	}
	else if(!empty($user) && $userFound)
	{
		 $editSymbol = '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
	?>
	<div class="left">
		<div class="details">
			<noscript><red>Bitte JavaScript aktivieren!</red></noscript>
			<form class="adminOption" method="post">
				<input type="hidden" name="action" value="disable" />
				<input type="submit" value="Benutzer <?php echo $profile['status'] == 'blocked' ? 'entsperren' : 'sperren'; ?>" class="small" />
			</form>
			<br>
			<img id="edit_avatar" src="<?php echo $_USER->getProfileImagePathOf($profile['idUser'], 128); ?>" style="width: 128px;" class="block" />&nbsp;<a href="#" class="edit" data-field="avatar"><i class="fa fa-times" aria-hidden="true"></i></a><br>
			<h4><span id="edit_username"><?php echo $profile['username']; ?></span>&nbsp;<a href="#" class="edit" data-field="username"><?php echo $editSymbol; ?></a></h4>
			<table class="block">
				<tbody>
					<tr>
						<td class="infoLabel">E-Mail:</td>
						<td class="infoValue"><span id="edit_email"><?php echo $profile['email']; ?></span>&nbsp;<a href="#" class="edit" data-field="email"><?php echo $editSymbol; ?></a></td>
					</tr>
					<tr>
						<td colspan="2">
							<form class="adminOption" method="post">
								<input type="hidden" name="action" value="mail" />
								<textarea name="message" cols="40" rows="9" placeholder="E-Mail" required></textarea><br>
								<input type="submit" value="E-Mail senden" class="small" />
							</form>
						</td>
					</tr>
				<tbody>
			</table>
		</div>
		<div class="details">
			<h3>Persönliche Daten</h3>
			<table class="block">
				<tbody>
					<tr>
						<td class="infoLabel">Name:</td>
						<td class="infoValue"><span id="edit_firstname"><?php echo $profile['firstname'];?></span>&nbsp;<a href="#" class="edit" data-field="firstname"><?php echo $editSymbol; ?></a>&nbsp;<span id="edit_lastname"><?php echo $profile['lastname']; ?></span>&nbsp;<a href="#" class="edit" data-field="lastname"><?php echo $editSymbol; ?></a></td>
					</tr>
					<tr>
						<td class="infoLabel">Geschlecht:</td>
						<td class="infoValue">
						<span id="edit_gender"><?php 
							if($profile['gender'] == 'm')
								echo 'männlich'; 
							if($profile['gender'] == 'w')
								echo 'weiblich'; 
							if($profile['gender'] == 'a')
								echo 'anderes'; 
						?></span>&nbsp;<a href="#" class="edit" data-field="gender"><?php echo $editSymbol; ?></a>
						</td>
					</tr>
					<tr>
						<td class="infoLabel">Geboren:</td>
						<td class="infoValue"><span id="edit_birthday"><?php echo (new DateHandler())->set($profile['birthday'])->get('d.m.Y'); ?></span><!--&nbsp;<a href="#" class="edit" data-field="birthday"><?php echo $editSymbol; ?></a>--></td>
					</tr>
					<tr>
						<td class="infoLabel">Telefon:</td>
						<td class="infoValue"><span id="edit_phonenumber"><?php echo $profile['telefonnumber']; ?></span>&nbsp;<a href="#" class="edit" data-field="phonenumber"><?php echo $editSymbol; ?></a></td>
					</tr>
					<tr>
						<td class="infoLabel">Messenger:</td>
						<td class="infoValue"><span id="edit_messengernumber"><?php echo $profile['messengernumber']; ?></span>&nbsp;<a href="#" class="edit" data-field="messengernumber"><?php echo $editSymbol; ?></a></td>
					</tr>
					<tr>
						<td class="infoLabel">Adresse:</td>
						<td class="infoValue"><?php echo $profile['street'] . ' ' . $profile['housenumber'] . ' ' . $profile['place']; ?><!--&nbsp;<?php echo $editSymbol; ?>--></td>
					</tr>
					<tr>
						<td class="infoLabel">Tut Gutes seit:</td>
						<td class="infoValue"><?php echo (new DateHandler())->set($profile['regDate'])->get('d.m.Y'); ?></td>
					</tr>
				<tbody>
			</table>
		</div>
		<div class="details">
			<h3>Über mich</h3>
			<div><span id="edit_description"><?php echo $profile['description']; ?></span>&nbsp;<a href="#" class="edit" data-field="description"><?php echo $editSymbol; ?></a></div>
			<h3>Hobbys</h3>
			<div><span id="edit_hobbies"><?php echo $profile['hobbys']; ?></span>&nbsp;<a href="#" class="edit" data-field="hobbies"><?php echo $editSymbol; ?></a></div>
		</div>
	</div>

	
		<?php
		if($_USER->hasGroup($_GROUP_ADMIN))
		{
		?>
	<br>
	<form class="adminOption" method="post">
		<!--<input type="hidden" name="user" value="<?php echo $user; ?>" />-->
		<input type="hidden" name="action" value="delete" />
		<input type="submit" value="Diesen Benutzer löschen" />
	</form>
		<?php
		}
	}
	else if(!empty($user) && !$userFound)
		echo 'Der Nutzer existiert nicht...';
	
	if(!isset($profile))
	{		
	?>
	<form method="get">
		<?php
		foreach(range('A', 'Z') as $char)
			echo '<a href="' . $CURRENT_PAGE . '&search=' . $char . '">' . $char . '</a>&nbsp;';
		?>
		<br><br>
		<input type="text" name="search" placeholder="Benutzername" /><br>
		<br>
		<input type="hidden" name="page" value="user" />
		<input type="submit" value="suchen" />
	</form>
	<?php
	}
	?>
</div>
<script type="text/javascript">
	$('.adminOption').submit(function(e)
	{
		var confirmed = confirm('Bist du wirklich sicher?');
		if(!confirmed)
			e.preventDefault();
	});
	
	$('.edit').click(function(e)
	{
		e.preventDefault();
	
		var field = $(this).data('field');
		if(field == 'avatar')
		{
			var confirmed = confirm('Bist du sicher, dass du das Profilbild dieses Nutzers löschen willst?');
			if(confirmed)
			{
				$.ajax(
				{
					url: '',
					type: 'post',
					data: { action: 'avatar' },
					success: function(data)
					{
						$('#edit_avatar').attr('src', data);
					}
				});
			}
		}
		else
		{
			$(this).children('i').toggleClass('fa-pencil-square-o');
			$(this).children('i').toggleClass('fa-check'); 
			var element = $('#edit_' + field);
			var save = !$(this).children('i').hasClass('fa-check');
			
			var prev = element.html();
			if(save)
				prev = $(this).data('prev');
			else
				$(this).data('prev', element.html());
				
			if(field == 'username' ||
				field == 'email' ||
				field == 'firstname' ||
				field == 'lastname' ||
				field == 'phonenumber' ||
				field == 'messengernumber' ||
				field == 'hobbies')
			{
				if(save)
				{
					var value = element.find('input').val();
					if(prev == value || !saveEdit(field, value))
						value = prev;
					element.html(value);
				}
				else
					element.html('<input type="text" value="' + element.html() + '" />');
			}
			else if(field == 'gender')
			{
				if(save)
				{
					var value = element.find('select').val();
					if(prev == value || !saveEdit(field, value.substring(0, 1))) // männlich -> m
						value = prev;
					element.html(value);
				}
				else
					element.html('<select><option value="männlich"' + (element.html() == 'männlich' ? ' selected' : '') + '>männlich</option><option value="weiblich"' + (element.html() == 'weiblich' ? ' selected' : '') + '>weiblich</option><option value="anderes"' + (element.html() == 'anderes' ? ' selected' : '') + '>anderes</option></select>');
			}
			else if(field == 'description')
			{
				if(save)
				{
					var value = element.find('textarea').val();
					if(prev == value || !saveEdit(field, value))
						value = prev;
					element.html(value);
				}
				else
					element.html('<textarea>' + element.html() + '</textarea>');
			}
		}
	});
	function saveEdit(key, value)
	{
		var confirmed = confirm('Bist du wirklich sicher?');
		if(confirmed)
			$.ajax(
			{
				url: '',
				type: 'post',
				data: { action: key, value: value },
				success: function(data)
				{ }
			});
		return confirmed;
	}
</script>

<?php
require './includes/_bottom.php';
?>