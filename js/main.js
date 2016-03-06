
var colors = {};
colors.tvdb = "#B6D415";
colors.anidb = "#8354ee";
colors.scene = "#808080";
colors.rage = "#FFA500";
colors.trakt = "#008FBB";
colors.master = "#FF0000";

function getApiUrl() {
    return "/api/";
}


/**
 * issue a request to sickbeard api keeps log of requests
 * 
 * @param params
 * @param success_callback
 * @param error_callback
 */
function genericRequest(curFunction, params, success_callback, error_callback) {

    var apiUrl = getApiUrl() + curFunction;
    $.ajax( { type : "POST",
		    url : apiUrl,
		    data : params,
		    dataType : 'json',
		    success : function(data) {
		        checkForError(data, params, success_callback, error_callback);
		    }, error : function(data) {
		        genericRequestError(data, params);
    		}
    });
}
// TODO: refactor !!!
function genericMapRequest(curFunction, params, success_callback, error_callback) {

    var apiUrl = '/map/' + curFunction;
    $.ajax( { type : "POST",
            url : apiUrl,
            data : params,
            dataType : 'json',
            success : function(data) {
                checkForError(data, params, success_callback, error_callback);
            }, error : function(data) {
                genericRequestError(data, params);
            }
    });
}

function fakeResHandler(){}

/**
 * checks id data has the attr "error" if not noErrorCallback is called with data and paramString as arguments
 * 
 * @param response
 * @param paramString
 * @param success_callback
 */
function checkForError(response, params, success_callback, error_callback) {
    /*
    {RESULT_SUCCESS:"success",
        RESULT_FAILURE:"failure",
        RESULT_TIMEOUT:"timeout",
        RESULT_ERROR:"error",
        RESULT_DENIED:"denied",
        }
*/
    var connectionStatus = false;
    if (response.result != "success") {
        console.log("Reg received for BUT not successful : " + params);
        if (response.result == "denied") {
            connectionStatus = false;
            console.log("user has not enough permission");
        } else {
            connectionStatus = true;
        }
        console.log(response);
        if (error_callback)
            error_callback(response.data, params);
    } else {
        console.log("Reg successful for: " + params);
        connectionStatus = true;
        console.log(response);
        if (success_callback)
            success_callback(response.data, params);
    }
}

function genericRequestError(data, params) {
    console.log(params, data);
}


/**
 * log the error response
 * 
 * @param data
 * @param params
 */
function genericResponseError(data, params) {
    console.log("an error in response for reg: " + params);
}


/**
 * 
 * @returns {Params}
 */
function Params() {

}

/**
 * create a simple string from the param dict/obj the string looks kinda like a get request
 * 
 * @param params
 * @returns {String}
 */
Params.prototype.toString = function() {
    var string = "";
    var first = true;
    $.each(this, function(key, value) {
        if (key != "toString") {
            var splitter = "&";
            if (first) {
                splitter = "?";
                first = false;
            }
            string += splitter + key + "=" + value;
        }
    });
    return string;
};
var pressTimerText;
var pressTimerLogout;

function mainInit(){

    $('#elementSelector').change(function(){
        if ($(this).val() == "0") {
            $('#elementSelector').hide();
            $('#newStuff').show();
            $('#newElementName').focus();
        } else if($(this).val() != "choose"){
            $('#newStuff').hide();
            document.location.assign("/xem/show/" + $(this).val());
        }
    });
    $('#cancelNewElement').click(function(){
        $('#newStuff').hide();
        $('#elementSelector').show();
        $('#elementSelector').val('choose').focus();
    });

    var query = '';
    $("#search").autocomplete({
        source: function (request, response) {
            //keep track of user submitted search term
            query = $.ui.autocomplete.escapeRegex(request.term);
            var params = new Params();
            params.term = request.term;
            genericRequest("autocomplete", params, response, response);
        },
        select: function (event, ui) {
            // update input with selected name before searching
            $('#search').val(ui.item.value);
            $('#searchForm').submit();
        },
        position: {my: "right top", at: "right bottom"},
        minLength: 4
    })
        .data("ui-autocomplete")._renderItem = function (ul, item) {
        //highlight the matched search term from the item -- note that this is global and will match anywhere
        var result_item = item.label;
        var x = new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + query + ")(?![^<>]*>)(?![^&;]+;)", "gi");
        result_item = result_item.replace(x, function (FullMatch, n) {
            return '<b>' + FullMatch + '</b>';
        });
        return $("<li></li>")
            .data("ui-autocomplete-item", item)
            .append(result_item)
            .appendTo(ul);
    };

	$("#logout").mouseup(function(){
		clearTimeout(pressTimerText);
		clearTimeout(pressTimerLogout);
		$(this).text('Profile');
		// Clear timeout
		document.location.href='/user';
		return false;
	}).mousedown(function(){
		// Set timeout
		var link = $(this);
		pressTimerText = window.setTimeout(function() { link.text('Logout'); }, 100);
		pressTimerLogout = window.setTimeout(function() {document.location.href=link.attr('href');},1200);
		return false; 
	});
	$(document).ready(function() {
	    $('label').each(function(){
	        var curLabel = $(this);
	        var curInput = curLabel.next('input');
	        var parent = curLabel.parents('#toolbox'); // disabled for all labels that have these as parents
	        // check for the for attr to not being invasive
	        if(typeof(curLabel.attr('for')) == "undefined" && curInput.length && parent.length == 0){
	            var id = curInput.attr('id');
	            if(!id){
	                // remove "0." from the random to get ids without a dot
	                id = $.now()+((''+Math.random()).split('.')[1]);
	                curInput.attr('id',id);
	            }
	            curLabel.attr('for',id);
	        }
	    });
	});
	
	console.log('normal init done');
}

$(document).ready(function() {
    mainInit();
});
