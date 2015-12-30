<?php include 'header.tpl.php';?>

<?php
//ZDebug::my_print($data, 'data');
?>
  <div id="nav"><?php print $data['nav'];?></div>
  <div class="left_top_ad fleft">  </div>
  <div id="content">
    <div class="hot_pictures fleft">
      <div class="picture_260"></div>
    </div>
<?php if(!empty($data['folders'])) { ?>
    <div class="folder_list">
      <div class="block_title"><?php print $data['currentFolderName']; ?> 其中的图集</div>
      <div class="picture_list">
<?php foreach ($data['folders'] as $folder) { ?>
        <div class="cover_160">
          <div class="cover_160_pic" >
            <a href="<?php print $folder['link'];?>">
              <img src="<?php print $folder['thumbnail'];?>"></img>
            </a>
          </div>
          <div class="picture_desc"><?php print $folder['desc'];?></div>
          <div class="picture_name"><a href="<?php print $folder['link'];?>"><?php print $folder['name'];?></a></div>
        </div>
<?php } ?>
      </div><!--end of picture list-->
    </div><!--end of folder list-->
    <div id="pager"><? print $data['subFolderPager']; ?></div>
<?php }?>
<?php if(!empty($data['files'])){?>
    <div class="file_list">
      <div class="block_title"><?php print $data['currentFolderName']; ?> 其中的照片</div>
      <div class="picture_list">
<?php foreach ($data['files'] as $file) { ?>
        <div class="picture_160">
          <a href="/photo/<?php print $file['fid'];?>"><img src="<?php print $file['thumbnail'];?>"></img></a>
          <div class="picture_name"><a href="/photo/<?php print $file['fid'];?>"><?php print $file['name'];?></a></div>
        </div>
<?php } ?>
      </div><!--end of picture list-->
    </div><!--end of file list-->
    <div id="pager"><? print $data['filesPager']; ?></div>
<?php } ?>
  </div><!--end of content-->
  <div class="right_top_ad"></div>

<?php include 'footer.tpl.php';?>