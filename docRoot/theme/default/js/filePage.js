/* 
 * file page js
 * @author: Elevenfox
 */

// Set the prev/next photo link area
$("#picture_1000_img").load(function() {
  $("#picture_prev").height($(".picture_1000").height());
  $("#picture_prev").width($(".picture_1000").width()/2);
  $("#picture_next").height($(".picture_1000").height());
  $("#picture_next").width($(".picture_1000").width()/2);
});

// Auto scroll to make the current picture shown in the list
$(".current_pic").load(function() {
  var theNum = 0;
  $.each($("#others_table").children(), function(field, value){
            if(value.children[0].className == 'current_pic') {
              theNum = field + 1;
              return;
            }
         });
  if(theNum >= 4) $("#list_others").scrollLeft(122*(theNum-3));
});

// When scroll to the border, make ajax call to get more pictures
var timeout = false; 
$("#list_others").bind('scroll', function(e){
  var sleft = $(this).scrollLeft();
  if( (sleft + $(this).width()) >= ($("#others_table").width()-20) ) {
    if (timeout){clearTimeout(timeout);}   
    timeout = setTimeout(function(){getNextSameFiles();}, 200);  
  }
  if( sleft <= 20 ) {
    if (timeout){clearTimeout(timeout);}   
    timeout = setTimeout(function(){getPrevSameFiles();}, 200);  
  }
})

// The function to show photo when click the thumbnail
function showPic(obj){
  var thisPic = $(obj);
  fid = thisPic.attr("id");
  $.getJSON('http://' + document.domain + "/picture/ajax/" + fid,
        function(data){
            $("#picture_1000_img").attr({ 
                    src: data.thumbnail,
                    title: data.page_title,
                    alt: data.page_title
                  });
            $("#picture_name").html(data.page_title);
            document.title = data.page_title;
            var theObj = $(".current_pic")[0];
            if(theObj) theObj.className = '';
            thisPic.addClass('current_pic');
            $("#picture_prev a").attr('href', '/photo/'+data.previousFile.fid);
            $("#picture_next a").attr('href', '/photo/'+data.nextFile.fid);
          });
};

function getNextSameFiles() {
  // Make ajax call to get more pictures
  var othersTableWidth = $("#others_table").width();
  var allSameFiles = $("#others_table li");
  fid = allSameFiles[allSameFiles.length-1].lastElementChild.id;
  fd_id = $(".list_others_container").attr("id");
  $.getJSON('http://' + document.domain + "/same_picture/next_ajax/" + fd_id + "/" + fid,
    function(data){
      $.each(data, function(key, sameFile) {
        var newLi = "<li class=\"picture_120\"><img id=\""+sameFile.fid+"\" src=\""+sameFile.thumbnail+"\" onclick=\"javascript: showPic(this);\"></img></li>";
        $("#others_table").append(newLi);
        othersTableWidth = othersTableWidth + 122;
      });
      $("#others_table").width(othersTableWidth);
    });
}

function getPrevSameFiles() {
  // Make ajax call to get more pictures
  var othersTableWidth = $("#others_table").width();
  var allSameFiles = $("#others_table li");
  fid = allSameFiles[0].lastElementChild.id;
  fd_id = $(".list_others_container").attr("id");
  $.getJSON('http://' + document.domain + "/same_picture/prev_ajax/" + fd_id + "/" + fid,
    function(data){
      $.each(data, function(key, sameFile) {
        var newLi = "<li class=\"picture_120\"><img id=\""+sameFile.fid+"\" src=\""+sameFile.thumbnail+"\" onclick=\"javascript: showPic(this);\"></img></li>";
        $("#others_table").prepend(newLi);
        othersTableWidth = othersTableWidth + 122;
      });
      $("#others_table").width(othersTableWidth);
      $("#list_others").scrollLeft(122*data.length);
    });
}