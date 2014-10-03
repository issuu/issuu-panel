<?php

$data = true;

foreach ($_POST['pub'] as $key => $value) {
	if ($value != '') $data = false;
}

if ($data)
{
	$_POST['publishDate'] = $datetime;	
}
else
{
	if ($_POST['pub']['day'] == '' || $_POST['pub']['month'] == '' || $_POST['pub']['year'] == '')
	{
		$_POST['publishDate'] = $date;
	}
	else
	{
		$_POST['publishDate'] = $_POST['pub']['year'] . '-' . $_POST['pub']['month'] . '-' . $_POST['pub']['day'] . 'T';
	}

	$_POST['publishDate'] .= $time;
}

unset($_POST['pub']);

if (trim($_POST['name']) != '')
{
	$_POST['name'] = str_replace(" ", "", $_POST['name']);
}

if (!isset($_POST['commentsAllowed']) || trim($_POST['commentsAllowed']) != 'true')
{
	$_POST['commentsAllowed'] = 'false';
}

if (!isset($_POST['downloadable']) || trim($_POST['downloadable']) != 'true')
{
	$_POST['downloadable'] = 'false';
}

foreach ($_POST as $key => $value) {
	$_POST[$key] = trim($value);
}

$result = $document->update($_POST);

if ($result['stat'] == 'ok')
{
	echo '<div class="updated"><p>Documento atualizado com sucesso</p></div>';
}
else if ($result['stat'] == 'fail')
{
	echo '<div class="error"><p>' . $result['message'] . ': ' . $result['field'] . '</p></div>';
}