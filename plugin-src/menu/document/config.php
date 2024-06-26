<?php

class IssuuPanelPageDocuments extends IssuuPanelSubmenu
{
	protected $slug = 'issuu-document-admin';

	protected $page_title = 'Documents';

	protected $menu_title = 'Documents';

	protected $priority = 1;

	public function page()
	{
		$this->getConfig()->getIssuuPanelDebug()->appendMessage("Issuu Panel Page (Documents)");
		$subpage = filter_input(INPUT_GET, 'issuu-panel-subpage');
		try {
			switch ($subpage) {
				case 'upload':
					$this->uploadPage();
					break;
				case 'url-upload':
					$this->urlUploadPage();
					break;
				case 'update':
					$this->updatePage();
					break;
				case null:
					$this->listPage();
					break;
				default:
					$this->getConfig()->getIssuuPanelDebug()->appendMessage("Page not found");
					$this->getErrorMessage(get_issuu_message('This page not exists'));
					return;
			}
		} catch (Exception $e) {
			$this->getConfig()->getIssuuPanelDebug()->appendMessage("Page Exception - " . $e->getMessage());
			$this->getErrorMessage(get_issuu_message('An error occurred while we try connect to Issuu'));
		}
	}

	private function uploadPage()
	{
		$issuuDocument = $this->getConfig()->getIssuuServiceApi('IssuuDocument');
		$cnt_f = (isset($folders['folder']))? count($folders['folder']) : 0;
		include(ISSUU_PANEL_DIR . "menu/document/forms/upload.php");
	}

	private function urlUploadPage()
	{
		$issuuDocument = $this->getConfig()->getIssuuServiceApi('IssuuDocument');
		$cnt_f = (isset($folders['folder']))? count($folders['folder']) : 0;
		include(ISSUU_PANEL_DIR . "menu/document/forms/url-upload.php");
	}

	private function updatePage()
	{
		$issuuDocument = $this->getConfig()->getIssuuServiceApi('IssuuDocument');
		$issuuFolder = $this->getConfig()->getIssuuServiceApi('IssuuFolder');
        $slug = filter_input(INPUT_GET, 'publication');
		$doc = $issuuDocument->getUpdateData(array('slug' => $slug));
		$tags = '';

		if ($doc['stat'] != 'ok' || empty($doc[$slug]))
		{
            $this->getErrorMessage(get_issuu_message('No documents found'));
            return;
		}
		include(ISSUU_PANEL_DIR . 'menu/document/forms/update.php');
	}

	private function listPage()
	{
		$issuuDocument = $this->getConfig()->getIssuuServiceApi('IssuuDocument');
		$issuuFolder = $this->getConfig()->getIssuuServiceApi('IssuuFolder');
		$image = 'https://image.issuu.com/%s/jpg/page_1_thumb_large.jpg';
		$page = (intval(filter_input(INPUT_GET, 'pn')))? : 1;
		$size = 10;
		$docs = $issuuDocument->issuuList(array(
			'size' => $size,
			'page' => $page
		));
		
		if (isset($docs['totalCount']) && $docs['totalCount'] > $docs['size'])
		{
            $number_pages = ceil($docs['totalCount'] / $size);
		}
		require(ISSUU_PANEL_DIR . 'menu/document/document-list.php');
	}
}