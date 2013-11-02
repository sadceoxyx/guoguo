jQuery(document).ready(main);

var checkinput = '<input type="checkbox" id="check" name="check';
var nameinput = '<input type ="textbox" size="20" id="name" name ="name';
var urlinput = '<input type ="textbox" size="80" id="url" name ="url';
var count = 1;

function main(){

    jQuery('#add').click(add_input);
    jQuery('#delete').click(delete_input);
}

function add_input(){
    jQuery('#textvalue').append('<li class = "af_item">' + checkinput + count + '" />' + nameinput + count + '" />' + urlinput + count + '" />');
    count++;
}

function delete_input(){
    jQuery('.af_item').each(function(){
        if(jQuery(this).children("input:first").is(':checked')){
            jQuery(this).remove();
        }
    });
}

