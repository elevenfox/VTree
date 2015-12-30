<?php include 'header.tpl.php';?>

<?php $file = $data['file'];?>
  <div id="nav"><?php print $data['nav'];?></div>
  <div class="left_top_ad"></div>
  <div id="content_file_hz">
    <div class="picture_1000">
      <a href="/photo/<?php print $file['fid'];?>"><img id="picture_1000_img" src="<?php print $file['thumbnail'];?>"></img></a>
      <div id="picture_prev">
        <a href="/photo/<?php print $data['previousFile']['fid']?>" hidefocus="true" id="p_btn_p"></a>
      </div>
      <div id="picture_next">
        <a href="/photo/<?php print $data['nextFile']['fid']?>" hidefocus="true" id="p_btn_n"></a>
      </div>
    </div>
    <div><span id="picture_name"><?php print $file['name'];?></span></div>
    <div class="list_others_container" id="<?php print $file['fd_id']; ?>">
     <div id="prev_folder" class="fleft" title="<?php print $data['previousFolder']['page_title']?>">
       <a href="/list/<?php print $data['previousFolder']['fd_id']?>">
         <img src="<?php print $data['previousFolder']['thumbnail']?>"></img>
       </a><br />
       <span><a href="/list/<?php print $data['previousFolder']['fd_id']?>" >< 上一相册</a></span>
     </div>
     <div id="load_prev" class="fleft"><a href="#"></a></div>
     <div id="list_others" class="fleft">
      <ul id="others_table" style="width: <?php print count($data['sameFiles']) * 122; ?>px">
      <?php 
        foreach ($data['sameFiles'] as $sameFile) { 
          if($sameFile['fid'] == $file['fid']) $current_class = 'class="current_pic"';
          else $current_class = '';
      ?>
        <li class="picture_120">
          <img <?php print $current_class;?> 
               id="<?php print $sameFile['fid'];?>" 
               src="<?php print $sameFile['thumbnail'];?>" onclick="javascript: showPic(this);">
          </img>
        </li>
      <?php }?>
      </ul>
     </div>
     <div id="load_next" class="fleft"><a href="#"></a></div>
     <div id="next_folder"  class="fleft"  title="<?php print $data['nextFolder']['page_title']?>">
       <a href="/list/<?php print $data['nextFolder']['fd_id']?>">
         <img src="<?php print $data['nextFolder']['thumbnail']?>"></img>
       </a><br />
       <span><a href="/list/<?php print $data['nextFolder']['fd_id']?>">下一相册 ></a></span>
     </div>
    </div>
  </div><!--end of content-->
  <div class="right_top_ad"></div>

<?php include 'footer.tpl.php';?>