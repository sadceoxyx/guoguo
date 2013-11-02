jQuery(document).ready(function(){    
    var list = jQuery('#the-list').children();
    var iclList = jQuery('.icl_translations');
    iclList.each(function(index){
        var postId = list[index].id;
        var id = postId.split('-');
        var link = jQuery(this).find("a");
        var src = link.attr('href');
        link.attr('href', src + '&post=' + id[1]);
    });
});