<?php

class IssuuPageDocuments extends IssuuPanelSubmenu
{
	protected $slug = 'issuu-document-admin';

	protected $page_title = 'Documents';

	protected $menu_title = 'Documents';

	protected $priority = 1;

	public function page()
	{
		$issuu_panel_api_key = IssuuPanelConfig::getVariable('issuu_panel_api_key');
		$issuu_panel_api_secret = IssuuPanelConfig::getVariable('issuu_panel_api_secret');
		issuu_panel_debug("Issuu Panel Page (Documents)");
		$action = new IssuuPanelAction();

		echo '<div class="wrap">';

		try {
			$issuu_document = new IssuuDocument($issuu_panel_api_key, $issuu_panel_api_secret);
		} catch (Exception $e) {
			issuu_panel_debug("Page Exception - " . $e->getMessage());
			return "";
		}

		$action->setParam('issuuDocument', $issuu_document);

		if (isset($_GET['upload']) && !isset($_POST['delete']))
		{
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$action->setName('issuu-panel-document-upload');
				do_action($action->getName(), $action);
			}
			else
			{
				try {
					$issuu_folder = new IssuuFolder($issuu_panel_api_key, $issuu_panel_api_secret);
					$folders = $issuu_folder->issuuList();
				} catch (Exception $e) {
					issuu_panel_debug("Page Exception - " . $e->getMessage());
					return "";
				}

				$cnt_f = (isset($folders['folder']))? count($folders['folder']) : 0;
				include(ISSUU_PANEL_DIR . 'menu/documento/forms/upload.php');
				$load = true;
			}
		}
		else if (isset($_GET['url_upload']) && !isset($_POST['delete']))
		{
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$action->setName('issuu-panel-document-url-upload');
				do_action($action->getName(), $action);
			}
			else
			{
				try {
					$issuu_folder = new IssuuFolder($issuu_panel_api_key, $issuu_panel_api_secret);
					$folders = $issuu_folder->issuuList();
				} catch (Exception $e) {
					issuu_panel_debug("Page Exception - " . $e->getMessage());
					return "";
				}

				$cnt_f = (isset($folders['folder']))? count($folders['folder']) : 0;
				include(ISSUU_PANEL_DIR . 'menu/documento/forms/url-upload.php');
				$load = true;
			}
		}
		else if (isset($_GET['ip-update']) && strlen($_GET['ip-update']) > 0)
		{
			$load = true;

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$action->setName('issuu-panel-document-update');
				do_action($action->getName(), $action);

				$doc = $action->getParam('result');
				$load = false;
			}
			else
			{
				$params['name'] = strtr($_GET['ip-update'], array('%20' => '+', ' ' => '+'));

				try {
					$doc = $issuu_document->update($params);
				} catch (Exception $e) {
					issuu_panel_debug("Page Exception - " . $e->getMessage());
					return "";
				}
			}

			if ($doc['stat'] == 'ok' && !empty($doc['document']))
			{
				if ($load)
				{
					$doc = $doc['document'];
				}
				else
				{
					$doc = $doc['document'];
				}
			}
			else
			{
				echo '<div class="error"><p>' . get_issuu_message('No documents found') . '</p></div>';
				exit;
			}

			$tags = '';

			if (isset($doc->tags))
			{
				foreach ($doc->tags as $tag) {
					$tags .= $tag . ',';
				}
			}

			if (($length = strlen($tags)) > 0)
			{
				$tags = substr($tags, 0, $length - 1);
			}

			include(ISSUU_PANEL_DIR . 'menu/documento/forms/update.php');
		}

		echo $action->getParam('message', '');

		if (!isset($load))
		{
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['delete']) && $_POST['delete'] == 'true'))
			{
				try {
					require(ISSUU_PANEL_DIR . 'menu/documento/requests/delete.php');
				} catch (Exception $e) {
					issuu_panel_debug("Document Delete Exception - " . $e->getMessage());
					return "";
				}
			}

			$image = 'http://image.issuu.com/%s/jpg/page_1_thumb_large.jpg';
			$page = (isset($_GET['pn']))? $_GET['pn'] : 1;
			$per_page = 10;
			$params = array(
				'pageSize' => $per_page,
				'startIndex' => $per_page * ($page - 1)
			);

			try {
				$docs = $issuu_document->issuuList($params);
			} catch (Exception $e) {
				issuu_panel_debug("Page Exception - " . $e->getMessage());
				return "";
			}
			
			if (isset($docs['totalCount']) && $docs['totalCount'] > $docs['pageSize'])
			{
				$number_pages = ceil($docs['totalCount'] / $per_page);
			}

			require(ISSUU_PANEL_DIR . 'menu/documento/document-list.php');
		}
	}
}

new IssuuPageDocuments();