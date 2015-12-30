<?php

/*
 * admin view class
 * @author: Elevenfox
 */
Class folderPageView extends ViewCore {
  
  public function preDisplay() {
    parent::preDisplay();
    
    // Set header if needed
    $this->setHeader($this->data['currentFolderName'] . ' - ' . SITE_NAME, 'title');
    
    if(isset($this->data['folders'])) {
      foreach ($this->data['folders'] as $key=>$folder) {
        if($folder['filesNum'] == 0 && $folder['subFoldersNum'] == 0) $folder['link'] = '#';
        else $folder['link'] = '/list/' . $folder['fd_id'];

        $folder['desc'] = '（含' . $folder['subFoldersNum'] . '个图集， ' . $folder['filesNum'] . '张图片）';

        $this->data['folders'][$key] = $folder;
      }
    
      if($this->data['subFolderPager'] == 'more') {
        $this->data['subFolderPager'] = '<div id="pagerMore"><a href="/list_subfolders/' . $this->data['currentFolderId'] . '">更多</a></div>';
      }
      else {
        import('Pager');
        $pager = new Pager(
                $this->data['limitNum'], 
                $this->data['subFolderTotal'], 
                $this->data['page'], 
                '/list_subfolders/'.$this->data['currentFolderId'].'/');
        $this->data['subFolderPager'] = $pager->generatePages();
      }
    }
    
    if(isset($this->data['files'])) {
      if($this->data['filesPager'] == 'more') {
        $this->data['filesPager'] = '<div id="pagerMore"><a href="/list_files/' . $this->data['currentFolderId'] . '">更多</a></div>';
      }
      else {
        import('Pager');
        $pager = new Pager(
                $this->data['limitNum'], 
                $this->data['filesTotal'], 
                $this->data['page'], 
                '/list_files/'.$this->data['currentFolderId'].'/');
        $this->data['filesPager'] = $pager->generatePages();
      }
    }
  }
}
?>
