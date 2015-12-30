   </div>
</body>
</html>
<?php print $data['scripts_footer'];?>
<script type="text/javascript">
jQuery(document).ready(
function($){
$("img").lazyload({
     placeholder : "/images/grey.gif",
     effect      : "fadeIn"
   });
});

// For menu displaying
var height_org = $("#menu_window").height();
if($("#menu_container").height() > height_org) {
  $("#more_menu").css('display', 'block');
  $("#more_menu").bind('click',function(e){
    var am_time = 500;
    if($("#menu_window").height() == $("#menu_container").height()) {
      $("#menu_window").animate({
        'height': height_org
      }, am_time ); 
      var newTop = $("#main").offset().top - $("#menu_container").height();
      $("#main").animate({
        'top': newTop + 'px'
      }, am_time ); 
    }
    else {
      $("#menu_window").animate({
        'height': $("#menu_container").height()
      }, am_time ); 
      var newTop = $("#main").offset().top + $("#menu_container").height();
      $("#main").animate({
        'top': newTop + 'px'
      }, am_time ); 
    }
  });
  
//var am_time = 500;   
//  $("#menu_window").bind('mouseenter',function(e){
//    $("#menu_window").animate({
//      'height': $("#menu_container").height()
//    }, am_time ); 
//  });
//  var newObj = $("#menu_window");
//  newObj.bind('mouseleave',function(e){
//    alert($(this).html());
//    $("#menu_window").height(height_org);
//    e.stopPropagation(); 
//  });
}

//**************** Ajax loading *********//
//弹出提示层背景变灰不可操作
function showloading(){
    //创建一个div对象（背景层）
    var bgObj = document.createElement("div");

	var mainColor = "#369";
	bgObj.setAttribute("id","tips_a");
	document.body.appendChild(bgObj);
	bgObj.style.position = "absolute";
	bgObj.style.zIndex = "9998";
	bgObj.style.background = "#777";
	bgObj.style.filter = "alpha(opacity=50)";
	bgObj.style.opacity = "0.5";
	bgObj.style.top = "0";
	bgObj.style.left = "0";
	bgObj.style.width = $(document).width()+"px";
	bgObj.style.height = getDocHeight()+"px";
    bgObj.style.overflow = "none";
    var tsObj = $("#loading")[0];
	tsObj.style.position = "absolute";
	tsObj.style.zIndex = "9999";
	tsObj.style.top = document.documentElement.scrollTop + (document.documentElement.clientHeight - tsObj.clientHeight)/2 + "px";
	tsObj.style.left = (document.documentElement.clientWidth - tsObj.clientWidth)/2 + "px";
}
//删除提示层
function hideloading(){
	$("#loading").hide();
	$("#tips_a").remove();
}

$().ready(function() {
        $("#loading").bind("ajaxSend", function() {
            $(this).show();
            showloading();
        }).bind("ajaxComplete", function() {
            hideloading();
        });
    });

function getDocHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}
</script>