<script>
$(document).ready(function() {
    currentPage = <?=json_encode($page) ?>;
    originalContent = '';

    // initialize button states
    resetControls();
});

function resetControls() {
    $('#cmsEdit').prop('disabled', false);
    $('#cmsPreview').prop('disabled', true);
    if(originalContent == '') {
        $('#cmsRestore').prop('disabled', true);
    }
    if(originalContent != '') {
        $('#savePage').prop('disabled', true);
    }
}

function editPage(){
    $('#cmsEdit').prop('disabled', true);
    var height = $('#content').height();

    var theHtml = $('#content').html();
    originalContent = theHtml;
    var textArea = $('<textarea id="pageHtml">'+theHtml+'</textarea>');
    textArea.height($('#content').height());
    textArea.width($('#content').width());

    $('#content').html('');
    $('#content').append(textArea);

    $('#cmsPreview').prop('disabled', false);
    $('#cmsRestore').prop('disabled', false);
    $('#savePage').prop('disabled', false);
}

function preview(){
    setNewContent($('#pageHtml').val());
    resetControls();
}

function setNewContent(c){
    $('#content').html(c);
}

function restore(){
    if(originalContent) {
        setNewContent(originalContent);
    }
    originalContent = '';
    resetControls();
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
    resetControls();
}
</script>
<br>
<div class="btn-toolbar">
    <div class="btn-group">
        <input id="cmsEdit" onclick="editPage()" value="Edit" type="button" class="btn" />
        <input id="cmsPreview" onclick="preview()" value="Preview" type="button" class="btn" />
        <input id="cmsRestore" onclick="restore()" value="Restore" type="button" class="btn" />
    </div>
    <div class="btn-group">
        <input id="savePage" onclick="save()" value="Save" type="button" disabled="disabled" class="btn" />
    </div>
</div>
