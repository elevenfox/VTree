<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head<?php print $data['header_attrs'];?>>
<title><?php print $data['page_title']; ?></title>
<?php print $data['metas']; ?>
<?php print $data['styles']; ?>
<?php print $data['scripts_header']; ?>
</head>
<body>
<div id="body">
  <div id="loading" style="display:none">
    <img src="/images/loading.gif" alt="Loader" />
  </div>
  <div id="header">
    <div class="logo fleft">
      <img src="<?php print $data['blocks']['blockHeader']['data']['site_logo'];?>"></img>
    </div>
    <div id="header_text"><?php print SITE_NAME; ?></div>
    <div id="menu_window">
     <div id="menu_container">
      <?php foreach($data['blocks']['blockHeader']['data']['menuLinks'] as $menuLink) {?>    
      <div class="menu_item"><a href="<?php print $menuLink['url'];?>"><?php print $menuLink['name'];?></a ></div>    
      <?php } ?>
     </div>
    </div><!--end of menu window-->
    <div id="more_menu">更多栏目 >></div>
  </div><!--end of header-->
  <div id="main">