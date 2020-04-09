document.addEventListener("DOMContentLoaded", function(event){
  var $jq = jQuery.noConflict();
  $jq(".yx-super-cat-dropdown-list").change(function(){
    var item = $jq(this).find(':selected');
    let container = item.attr("data-container");
    let term = item.attr("data-term");
    let posts = item.attr("data-posts");

    // hide all
    $jq("#"+posts).find('article').hide();
    // set all to inactive
    $jq(".yx-super-cat-post-filter").removeClass("elementor-active");

    // Show / Hide all
    if (term == '') {
      // show all
      history.replaceState(null, null, ' ');
      $jq("#"+posts).find('article').fadeIn(400);
      $jq('.yx-super-cat-post-filter[data-term=""]').addClass("elementor-active");
    } else {
      // show some
      $jq('.yx-super-cat-post-filter[data-term="' + term + '"]').addClass("elementor-active");
      window.location.hash = "#" + term;
      $jq("#"+posts).find('article').each(function(){
        var classes = $jq(this).attr("class").split(" ");
        if(classes.includes(term)){
          $jq(this).fadeIn(400);
        }
      });
    }

  });

  if(window.location.hash){
    let hhh = window.location.hash.replace("#", "");
    var posts = "";
    $jq('.yx-super-cat-dropdown-list').each(function(){
      let toSelect = $jq(this).find('option[data-term="' + hhh + '"]');
      if(toSelect.size() > 0){
        toSelect.attr('selected','selected');
        $jq(this).trigger('change');
      }
    });
  }

});
