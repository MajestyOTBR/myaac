<?php
/**
 * Delete character
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$player_name = isset($_POST['delete_name']) ? stripslashes($_POST['delete_name']) : null;
$password_verify = isset($_POST['delete_password']) ? $_POST['delete_password'] : null;
$password_verify = encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $password_verify);
if(isset($_POST['deletecharactersave']) && $_POST['deletecharactersave'] == 1) {
	if(empty($player_name) || empty($password_verify)) {
		$errors[] = 'Character name or/and password is empty. Please fill in form.';
	}

	if(empty($errors) && !Validator::characterName($player_name)) {
		$errors[] = 'Name contain illegal characters.';
	}

	$player = new OTS_Player();
	$player->find($player_name);
	if(empty($errors) && !$player->isLoaded()) {
		$errors[] = 'Character with this name doesn\'t exist.';
	}

	if(empty($errors)) {
		$player_account = $player->getAccount();
		if($account_logged->getId() != $player_account->getId()) {
			$errors[] = 'Character <b>' . $player_name . '</b> is not on your account.';
		}
	}

	if(empty($errors) && $password_verify != $account_logged->getPassword()) {
		$errors[] = 'Wrong password to account.';
	}

	if(empty($errors) && $player->isOnline()) {
		$errors[] = 'This character is online.';
	}

	if(empty($errors) && $player->isDeleted()) {
		$errors[] = 'This player has been already deleted.';
	}

	if(empty($errors)) {
		//dont show table "delete character" again
		$show_form = false;
		/** @var OTS_DB_MySQL $db */
		if ($db->hasColumn('players', 'deletion'))
			$player->setCustomField('deletion', 1);
		else
			$player->setCustomField('deleted', 1);

		$account_logged->logAction('Deleted character <b>' . $player->getName() . '</b>.');
		$twig->display('success.html.twig', [
			'title' => 'Character Deleted',
			'description' => 'The character <b>' . $player_name . '</b> has been deleted.'
		]);
	}
}

if($show_form) {
	if(!empty($errors)) {
		$twig->display('error_box.html.twig', array('errors' => $errors));
	}

	$twig->display('account.delete_character.html.twig');
}
