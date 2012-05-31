<br>
<br>
<script type="text/javascript">

var currentPage = <?=json_encode($page) ?>;
var originalContent = '';
function editPage(){
    var height = $('#content').height();

    var theHtml = $('#content').html();
    originalContent = theHtml;
    var textArea = $('<textarea id="pageHtml">'+theHtml+'</textarea>');
    textArea.height($('#content').height());
    textArea.width($('#content').width());

    $('#content').html('');
    $('#content').append(textArea);
    $('#savePage').removeAttr('disabled');
}

function preview(){
    setNewContent($('#pageHtml').val());
}

function setNewContent(c){
    $('#content').html(c);
}

function restore(){
    if(originalContent)
        setNewContent(originalContent);
    $('#savePage').attr('disabled','disabled');
}

function save(){
    var newContent = '';
    if($('#pageHtml').length){
        newContent = $('#pageHtml').val();
    }else{
        newContent = $('#content').html();
    }
    if(newContent == originalContent)
       return;

    var params = new Params();
    params.page = currentPage;
    params.content = newContent;

    genericRequest("savePage", params, fakeResHandler, genericResponseError);

    setNewContent(newContent);
    $('#savePage').attr('disabled','disabled');
}

</script>
<input onCLick="editPage()" value="Edit" type="button"/>
<input onCLick="preview()" value="Preview" type="button"/>
<input onCLick="restore()" value="Restore" type="button"/>
<input id="savePage" onCLick="save()" value="Save" type="button" disabled="disabled"/>