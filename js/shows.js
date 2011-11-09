var conObjs = {};
var conObjFrom = {};
var conObjTo = {};
var papers = {};
/* demostuff */

// passthruConObjs = JSON.parse('{"passthru_xmaster_scene":{"fid":"xmaster","tid":"scene"},"passthru_scene_xmaster":{"fid":"scene","tid":"xmaster"},"passthru_scene_anidb":{"fid":"scene","tid":"anidb"},"passthru_anidb_scene":{"fid":"anidb","tid":"scene"},"passthru_anidb_rage":{"fid":"anidb","tid":"rage"},"passthru_rage_anidb":{"fid":"rage","tid":"anidb"},"passthru_trakt_tvdb":{"fid":"trakt","tid":"tvdb"},"passthru_tvdb_trakt":{"fid":"tvdb","tid":"trakt"},"passthru_scene_rage":{"fid":"scene","tid":"rage"},"passthru_rage_scene":{"fid":"rage","tid":"scene"},"passthru_xmaster_rage":{"fid":"xmaster","tid":"rage"},"passthru_rage_xmaster":{"fid":"rage","tid":"xmaster"},"passthru_anidb_xmaster":{"fid":"anidb","tid":"xmaster"},"passthru_xmaster_anidb":{"fid":"xmaster","tid":"anidb"}}');

function getPaperID(fname,tname){
	return 'paper_'+fname+'_'+tname;
}

function drawCon(fname,fse,fep,tname,tse,tep,fake){
	var paperID = getPaperID(fname, tname);
	
	if(typeof(papers[paperID]) == "undefined"){
		return null;
	}
	var curPaper = papers[paperID];
	
	var height = 18;
	var width = 44;
	var strength = 20;
	
	var frontOffset = -1;
	var backOffset = 1;
	var heightOffset = 0;

	var fID = fname+'_'+fse+'_'+fep;
	var tID = tname+'_'+tse+'_'+tep;
	
	
	var fromDOM = $('#'+fID);
	var toDOM = $('#'+tID);
	
	if(fromDOM.length == 0){
		console.log('DOM episode missing '+fID+' which wanted to connect to '+tID);
		return false;	
	}
	if(toDOM.length == 0 ){
		console.log('DOM episode missing '+tID+' which wanted to connect to '+fID);
		return false;
	}
	
	width = width+backOffset;
	var fp = fromDOM.position();
	var tp = toDOM.position();
	var f = fp.top+heightOffset;
	var t = tp.top+heightOffset;
	var fb = f+height;
	var tb = t+height;
	var fs = strength;
	var ts = width-fs;
	
	var newCon = curPaper.path("M"+frontOffset+","+f+"C"+fs+","+f+","+ts+","+t+","+width+","+t+"l0,"+height+"C"+ts+","+tb+","+fs+","+fb+","+frontOffset+","+fb+"Z");
	newCon.attr("stroke-width", 1);
	newCon.attr("stroke", "#fff");
	newCon.attr("fill", '0-'+colors[fname]+'-'+colors[tname]);
	// curPaper.renderfix();
	if(fake){
		newCon.attr("fill-opacity", 0.2);
		newCon.attr("stroke", "#000");	
	}else{
		/* lets save this stuff */
		
		/* save svg element to the paper */
		if(typeof(conObjs[paperID]) == "undefined")
			conObjs[paperID] = {};
		conObjs[paperID][newCon.id] = {'id':newCon.id,'from':fID,'to':tID,fid:fname,fs:fse,fe:fep,tid:tname,ts:tse,te:tep};
	
		/*  */
		if(typeof(conObjFrom[fID]) == "undefined")
			conObjFrom[fID] = {};
		if(typeof(conObjFrom[fID][paperID]) == "undefined")
			conObjFrom[fID][paperID] = new Array();
		conObjFrom[fID][paperID].push(newCon.id);
		
		if(typeof(conObjTo[tID]) == "undefined")
			conObjTo[tID] = {};
		if(typeof(conObjTo[tID][paperID]) == "undefined")
			conObjTo[tID][paperID] = new Array();
		conObjTo[tID][paperID].push(newCon.id);
		
	
		abstractConObj = {fid:fname,fs:fse,fe:fep,tid:tname,ts:tse,te:tep}
		abstractConObjs[abstractConObjHash(abstractConObj)] = abstractConObj;
		reverseAbstractConObj = buildReverseRule(abstractConObj);
		abstractConObjs[abstractConObjHash(reverseAbstractConObj)] = reverseAbstractConObj;
	}
	return newCon;
}
function abstractConObjHash(con){
	var fields = [con.fid,
					con.fs,
					con.fe,
					con.tid,
					con.ts,
					con.te];
	return fields.join("_");
}

function buildReverseRule(con){
	var newCon = {	fid:con.tid,
					fs:con.ts,
					fe:con.te,
					tid:con.fid,
					ts:con.fs,
					te:con.fe}

	return newCon;
}


function checkConnectionValues(showTip){
	if(typeof(showTip) == "undefined")
		showTip = true;
	
	var fromLi = $('div.edit.from li.episode.selected');
	var toLi = $('div.edit.to li.episode.selected');
	clearPapers();
	createPapers();
	redraw();	
	if(fromLi.length==1 && toLi.length==1){
		$('.connect').addClass('active');
		$('.seasonConnection.edit').addClass('ready');
		if(showTip)
			displayConnectTip(fromLi,toLi);
		return true;
	}
	$('.connect').removeClass('active');
	$('.seasonConnection.edit').removeClass('ready wait');

	return false;
}

function displayConnectTip(fromLi,toLi){
	
	var fromName = fromLi.attr('id').split("_")[0];
	var fromSeason = fromLi.attr('id').split("_")[1];
	var fromEpisode = fromLi.attr('id').split("_")[2];
	
	var toName = toLi.attr('id').split("_")[0];
	var toSeason = toLi.attr('id').split("_")[1];
	var toEpisode = toLi.attr('id').split("_")[2];

	var fwidth = parseInt(fromLi.css('width'));
	var from_posx = (fromLi.offset().left) + fwidth;
	var from_posy = fromLi.offset().top;
	
	var to_posx = toLi.offset().left;
	var to_posy = toLi.offset().top;
	
	var averagex = average(new Array(from_posx, to_posx));
	var averagey = average(new Array(from_posy, to_posy));
	
	var my_pos = 'top right';
	if(from_posy>to_posy){
		my_pos = 'bottom right';
	}
	
	$('.seasonConnection.edit').qtip({
		content: function() {
			var container = $('<div>');
			var text = '<p>Click connect to create a direct link between<br/>';
			text += fromName+':<br/>- Season '+fromSeason+' Episode '+fromEpisode+'<br/>';
			text += toName+':<br/>- Season '+toSeason+' Episode '+toEpisode+'<br/>';
			var connectB = $('<input type="button">');
			connectB.attr('value', 'Connect');
			connectB.click(function(){$('.ui-tooltip').remove();connect();});
			var cancelB = $('<input type="button">');
			cancelB.attr('value', 'Cancel');
			cancelB.click(function(){$('.hover').removeClass('hover');$('.selected').removeClass('selected');$('.ui-tooltip').remove();});
			container.append(text);
			container.append(cancelB);
			container.append(connectB);
	    	return container;
	   },
	   show: {
	   	  solo: true,
	      ready: true
	   },
	   position: {
	   	my: my_pos,
	   	target: [averagex, averagey]
   		},
   		hide: {
	      event: 'click'
	   },
	   events: {
	      blur: function(event, api) {
	         // For more information on the API object, check-out the API documentation
	         $('.ui-tooltip').remove();
	      }
	   }
	})
}

function connect(fake){
	if(checkConnectionValues(false)){
		var fromLi = $('div.edit.from li.episode.selected');
		var toLi = $('div.edit.to li.episode.selected');		
	
		fromName = fromLi.attr('id').split("_")[0];
		fromSeason = fromLi.attr('id').split("_")[1];
		fromEpisode = fromLi.attr('id').split("_")[2];
		
		toName = toLi.attr('id').split("_")[0];
		toSeason = toLi.attr('id').split("_")[1];
		toEpisode = toLi.attr('id').split("_")[2];
		
		if(typeof(fake) == "undefined")
			fake = false;
		
		var newCon = drawCon(fromName,fromSeason,fromEpisode,toName,toSeason,toEpisode,fake);
		if(!fake){
			fromLi.addClass('full');
			toLi.addClass('full');
			$('.seasonConnection.edit').removeClass('ready');
			$('.seasonConnection.edit').addClass('wait');	
	
			var params = new Params();
		    params.fromLiID = fromLi.attr('id');
		    params.toLiID = toLi.attr('id');
		    params.conID = newCon.id;
		    params.paperID = getPaperID(fromName,toName);
		    params.elementID = parseInt($('#element').dataset('id'));
		    
		    params.fID = parseInt($('div.'+fromName).dataset('id'));
		    params.fName = fromName;
		    params.fSeason = fromSeason;
		    params.fEpisode = fromEpisode;
		    
		    params.tID = parseInt($('div.'+toName).dataset('id'));
		    params.tName = toName;
		    params.tSeason = toSeason;
		    params.tEpisode = toEpisode;
		    
		    genericRequest("saveCon", params, con_success, con_fail);
	   	}
		
	}	
}


function con_success(data, params){
	window.setTimeout(function(){
		$('#'+params.fromLiID).removeClass('selected full');
		$('#'+params.toLiID).removeClass('selected full');		
		checkConnectionValues();
        updateAllConIcons();
	},800);
}

function con_fail(data, params){
	window.setTimeout(function(){
		var paper = papers[params.paperID];
		var el = paper.getById(params.conID);
		ninjaSVGElement(params.paperID, el);
		$('#'+params.fromLiID).removeClass('selected full');
		$('#'+params.toLiID).removeClass('selected full');
		$('#'+params.paperID).removeClass('wait');
	},800);
	
}

function disconnect(paperID, svgElement){
	// console.log(conObjs[paperID][svgElement.id]);
	var con = conObjs[paperID][svgElement.id];

	var params = new Params();
    params.conID = svgElement.id;
    params.paperID = paperID;
    params.elementID = parseInt($('#element').dataset('id'));
    
    params.fID = parseInt($('div.'+con.fid).dataset('id'));
    params.fName = con.fid;
    params.fSeason = con.fs;
    params.fEpisode = con.fe;
    
    params.tID = parseInt($('div.'+con.tid).dataset('id'));
    params.tName = con.tid;
    params.tSeason = con.ts;
    params.tEpisode = con.te;
    
	$('.seasonConnection.edit').addClass('wait');
    genericRequest("deleteCon", params, del_success, del_fail);

}
function del_success(data, params){
	window.setTimeout(function(){
		var paper = papers[params.paperID];
		var el = paper.getById(params.conID);
		ninjaSVGElement(params.paperID, el);
		$('.seasonConnection.edit').removeClass('wait');
        updateAllConIcons();
	},800);
}

function del_fail(data, params){
	window.setTimeout(function(){
		$('.seasonConnection.edit').removeClass('wait');
	},800);
}


// not used just here for history and nice code snippets
function setDefauldQtipForSeasonConnection(){
	
	$('.seasonConnection').qtip({
		content: function(api) {
			return '<div id="curMode">...</span>';
	    },
	    position: {
	      my: 'bottom left',  // Position my top left...
	      /* at: 'bottom right', // at the bottom right of... */
	      target: 'mouse', // Position at the mouse...
	      adjust: {
	         x: -3
	      }
	   },
	   hide: {
	      event: 'click mouseleave'
	   },
	   show: {
	      event: 'mousemove'
	   },
   	   events: {
	      move: function(event, api) {
			if($('.seasonConnection.edit').length == 1){
				if(getSVGElementByCurPos(event.originalEvent.pageX, event.originalEvent.pageY))
					$('#curMode').text('Edit Connection...');
				else if(checkConnectionValues())
					$('#curMode').text('Connect');
				else
					$('#curMode').text('Cancel');
			}else{
				$('#curMode').text('Edit');
			}
	      }
	  }
	});	
}

function getSVGElementByCurPos(pageX,pageY){
	var scrollTop = $(window).scrollTop();
	pageY = pageY-scrollTop;
	var out = false;
	jQuery.each(papers, function(k,paper){
		var el = paper.getElementByPoint(pageX, pageY);
		if(el)
			out = el;
	});
	return out;
	
}

function getSVGElementCenter(el, paper){
	var elBox = el.getBBox();
	var offset = $(paper).offset();
	var wPosX = elBox.x+(elBox.width*0.5)+offset.left;
	var wPosY = elBox.y+(elBox.height*0.5)+offset.top;
	return {x: wPosX, y: wPosY};
}

function ninjaSVGElement(paperID, el){
	
	jQuery.each( conObjFrom, function(liID,ids){
		if(typeof(ids[paperID]) != "undefined"){
			jQuery.each( ids[paperID], function(k,id){
				if(id == el.id)
					ids[paperID].splice(k, 1);
			});
			if(ids[paperID].length == 0)
				delete conObjFrom[liID][paperID];
		}
	});

	jQuery.each( conObjTo, function(liID,ids){
		if(typeof(ids[paperID]) != "undefined"){	
			jQuery.each( ids[paperID], function(k,id){
				if(id == el.id)
					ids[paperID].splice(k, 1);
			});
			if(ids[paperID].length == 0)
				delete conObjTo[liID][paperID];
		}
	});

	var con = conObjs[paperID][el.id];
	delete abstractConObjs[con.from+"_"+con.to];
	delete abstractConObjs[con.to+"_"+con.from];
	delete conObjs[paperID][el.id];
	el.remove();
}

function markBySVGElement(paperID,curCon){
	unmarkAllSVGElements();
	unmarkALLLIELements();
	
	if(curCon.removed)
		return false;
	curCon.attr("stroke", "#000");
	curCon.attr("stroke-width", 2);
	curCon.toFront();
	var curConObj = conObjs[paperID][curCon.id];
	$("#"+curConObj['from']).addClass('hover');
	$("#"+curConObj['to']).addClass('hover');
}

function unmarkAllSVGElements(){
	
	jQuery.each( conObjs, function(paperID, conObjIDs){
		jQuery.each(conObjIDs, function(conID,conObj){
			var curCon = papers[paperID].getById(conID);
			curCon.attr("stroke", "#fff");
			curCon.attr("stroke-width", 1);
			$(conObj['from']).removeClass('hover');
			$(conObj['to']).removeClass('hover');
			
		});
	});
}

function unmarkALLLIELements(){
	$('li.episode.hover').removeClass('hover');
}

function createPapers(){
	
	$('.clicker').remove();
	var list = $('#sortable li.entityList');
	jQuery.each(list ,function(k,curLi){
		var curLoc = $(curLi);
		var seasonConnectionDOM = $(curLoc).find('.seasonConnection');
		if(k < list.length-1){
			seasonConnectionDOM.show();
			var curLocName = curLoc.dataset('locationName');
			var nextLoc = $(list[k+1]);
			var nextLocName = nextLoc.dataset('locationName');
			
			var maxHeight = Math.max(parseInt(curLoc.css('height')),parseInt(nextLoc.css('height')))
			
			var newID = "paper_"+curLocName+"_"+nextLocName;
			
			seasonConnectionDOM.attr('id',newID);
			if(curLocName == 'master' || nextLocName == 'master'){
				// curLoc.find('.entity').addClass('masterCon');
				seasonConnectionDOM.addClass('masterCon');
				// nextLoc.find('.entity').addClass('masterCon');
			}
			curLoc.find('.entity').addClass((nextLoc.find('.entity').dataset('name'))+'Con')
			nextLoc.find('.entity').addClass((curLoc.find('.entity').dataset('name'))+'Con')
			
			
			
			var newPaper = Raphael(newID, 44, maxHeight);
			
			createEditIcon(seasonConnectionDOM);
			papers[newID] = newPaper;
			
		}else{
			seasonConnectionDOM.hide();
		}
		
		
	});
}

function createEditIcon(seasonConnectionDOM){
	if(!seasonConnectionDOM.hasClass('masterCon'))
		return false;
	
	seasonConnectionDOM.find('.clicker').remove();
	
	var clicker = $('<div>');
	clicker.addClass('clicker');
	var id = ''+$.now();
	clicker.attr('id', id);
	seasonConnectionDOM.append(clicker);
	var paper = Raphael(id, 40, 40);
	
	var shuffleIcon = 'M9.089,13.133c0.346,0.326,0.69,0.75,1.043,1.228c0.051-0.073,0.099-0.144,0.15-0.219c0.511-0.75,1.09-1.599,1.739-2.421c0.103-0.133,0.211-0.245,0.316-0.371c-0.487-0.572-1.024-1.12-1.672-1.592C9.663,9.02,8.354,8.506,6.899,8.517H0.593v3.604H6.9C7.777,12.138,8.333,12.422,9.089,13.133zM22.753,16.082v2.256c-0.922-0.002-2.45-0.002-2.883-0.002c-1.28-0.03-2.12-0.438-2.994-1.148c-0.378-0.311-0.74-0.7-1.097-1.133c-0.268,0.376-0.538,0.764-0.813,1.168c-0.334,0.488-0.678,0.99-1.037,1.484c-0.089,0.121-0.189,0.246-0.283,0.369c1.455,1.528,3.473,2.846,6.202,2.862h2.905v2.256l3.515-2.026l3.521-2.03l-3.521-2.028L22.753,16.082zM16.876,13.27c0.874-0.712,1.714-1.118,2.994-1.148c0.433,0,1.961,0,2.883-0.002v2.256l3.515-2.026l3.521-2.028l-3.521-2.029l-3.515-2.027V8.52h-2.905c-3.293,0.02-5.563,1.93-7.041,3.822c-1.506,1.912-2.598,3.929-3.718,4.982C8.332,18.033,7.777,18.32,6.9,18.336H0.593v3.604H6.9c1.455,0.011,2.764-0.502,3.766-1.242c1.012-0.735,1.772-1.651,2.454-2.573C14.461,16.267,15.574,14.34,16.876,13.27z';
	var checkIcon = 'M2.379,14.729 5.208,11.899 12.958,19.648 25.877,6.733 28.707,9.561 12.958,25.308z';
	var c = paper.circle(20,20,16);
	if(seasonConnectionDOM.hasClass('edit')){
		var i = paper.path(checkIcon);
	}else
		var i = paper.path(shuffleIcon);
			
	i.transform('T5,5');

	
	c.attr({fill: "#000"});
	i.attr({fill: "#fff", stroke: "none"});
	if(logedIn){
		clicker.hover(function(t){
				i.attr('fill','#83e700');
			},function(t){
				i.attr('fill','#fff')
		});
		clicker.click(function(){
			conFieldAction(seasonConnectionDOM)
			if(seasonConnectionDOM.hasClass('edit')){
				i.animate({'path':checkIcon},500);
			}else
				i.animate({'path':shuffleIcon},500)
				
			resetOtherEditIcons();
		});
	}
	return true;
}

function resetOtherEditIcons(){
	$('.seasonConnection').each(function(){
		if(!$(this).hasClass('edit')){
			createEditIcon($(this));
		}	
	});	
}


function conFieldAction(seasonConnectionDOM){
	
	if(!seasonConnectionDOM.hasClass('edit'))
		resetAllClasses();
	
	seasonConnectionDOM.toggleClass('edit');
	var fromName = seasonConnectionDOM.attr('id').split("_")[1];
	var toName = seasonConnectionDOM.attr('id').split("_")[2];
	
	var fromdiv = $('div.'+fromName);
	var todiv = $('div.'+toName);
	var toClass = todiv.attr('class');
	fromdiv.toggleClass('edit from');	
	todiv.toggleClass('edit to');
	updatePassthruIcons();

}

function clearPapers(){
	
	$('.masterCon').removeClass('masterCon');
	jQuery.each(papers, function(paperID,paper){
		paper.remove();
	});
	conObjs = {};
	conObjFrom = {};
	conObjTo = {};
	papers = {};
}

function redraw(){
	jQuery.each(abstractConObjs, function(k,con){
		drawCon(con.fid,
				con.fs,
				con.fe,
				con.tid,
				con.ts,
				con.te);
	});
}

function resetAllClasses(){
	var classes = ['edit','selected','hover','from','to']
	jQuery.each(classes, function(k,curClass){
		$('.'+curClass).removeClass(curClass);	
	});
}

function normaliseAll(entity){
	jQuery.each($('div.'+entity+' li.episode'),function(k, li){
		normalise($(li).attr('id'))
	});
	clearPapers();
	createPapers();
	redraw();
}

function normalise(liID){
	var li = liID;
	var fids = conObjFrom[li];
	var tids = conObjTo[li];
	
	unmarkAllSVGElements()
	var fTops = new Array();
	var tTops = new Array();
	$('div li.episode').removeClass('hover');
	jQuery.each( [fids,tids], function(paperID,fORtIDs){
		if(typeof(fORtIDs) != "undefined"){
			jQuery.each( fORtIDs, function(paperID,curIDs){
				jQuery.each(curIDs, function(k,svgID){
					var curConObj = conObjs[paperID][svgID];
					fTops.push($('#'+curConObj['from']).position().top);
					tTops.push($('#'+curConObj['to']).position().top);
				});
			});
		}
	});
	
	$('#'+li).css('position','relative');
	var offset = average(fTops) - $('#'+li).position().top;
	$('#'+li).css('top',offset+'px');
	$('#'+li).css('margin-bottom',(offset+1)+'px');
	
	

}
function average(args){
   var items = args.length
   var sum = 0
   for (i = 0; i < items;i++)
   {
      sum += args[i]
   }
   return (sum/items)
}

function updatePassthruIcons(){
	$('.passthru').remove();

	$('.seasonConnection').each(function(k,v){
		var seasonConnection = $(this);
		fName = seasonConnection.attr('id').split("_")[1];
		tName = seasonConnection.attr('id').split("_")[2];
		
		var passthruContainer = $('<div>');
		passthruContainer.attr('class','passthru');
		passthruContainer.dataset('fName',fName);
		passthruContainer.dataset('tName',tName);
		var id = $.now()+"";
		passthruContainer.attr('id', id)
		$(seasonConnection).append(passthruContainer);
		var paper = Raphael(id, 40, 40);
		
		// var arrows = 'M8.982,7.107L0.322,15.77l8.661,8.662l3.15-3.15L6.621,15.77l5.511-5.511L8.982,7.107zM21.657,7.107l-3.148,3.151l5.511,5.511l-5.511,5.511l3.148,3.15l8.662-8.662L21.657,7.107z';
		var chain = 'M15.667,4.601c-1.684,1.685-2.34,3.985-2.025,6.173l3.122-3.122c0.004-0.005,0.014-0.008,0.016-0.012c0.21-0.403,0.464-0.789,0.802-1.126c1.774-1.776,4.651-1.775,6.428,0c1.775,1.773,1.777,4.652,0.002,6.429c-0.34,0.34-0.727,0.593-1.131,0.804c-0.004,0.002-0.006,0.006-0.01,0.01l-3.123,3.123c2.188,0.316,4.492-0.34,6.176-2.023c2.832-2.832,2.83-7.423,0-10.255C23.09,1.77,18.499,1.77,15.667,4.601zM14.557,22.067c-0.209,0.405-0.462,0.791-0.801,1.131c-1.775,1.774-4.656,1.774-6.431,0c-1.775-1.774-1.775-4.653,0-6.43c0.339-0.338,0.725-0.591,1.128-0.8c0.004-0.006,0.005-0.012,0.011-0.016l3.121-3.123c-2.187-0.316-4.489,0.342-6.172,2.024c-2.831,2.831-2.83,7.423,0,10.255c2.833,2.831,7.424,2.831,10.257,0c1.684-1.684,2.342-3.986,2.023-6.175l-3.125,3.123C14.565,22.063,14.561,22.065,14.557,22.067zM9.441,18.885l2.197,2.197c0.537,0.537,1.417,0.537,1.953,0l8.302-8.302c0.539-0.536,0.539-1.417,0.002-1.952l-2.199-2.197c-0.536-0.539-1.416-0.539-1.952-0.002l-8.302,8.303C8.904,17.469,8.904,18.349,9.441,18.885z'
		// var c = paper.circle(20,20,4);
		var a = paper.path(chain);
		a.attr({"stroke":"none"});
		// c.attr({fill: "#000"});
		a.transform('T4,-1s0.87');
		
		var curPassthruObj = passthruConObjs['passthru_'+fName+'_'+tName];
		if(curPassthruObj){
			passthruContainer.addClass('active');
	        	passthruContainer.addClass(curPassthruObj.type);
			if(curPassthruObj.type == "absolute"){
				a.attr('fill','#ff0000');
			}if(curPassthruObj.type == "sxxexx"){
				a.attr('fill','#f7ff29');
			}
		}else{
			a.attr('fill','#fff');	
		}
		
		var absoluteText = '<span class="absolute">Absolute</span> passthru:<br>The two entitiy have there episodes link by the absolute numbers';
		var sxxexxText = '<span class="sxxexx">SxxExx</span> passthru:<br>The two entitiy have there episodes link by the season-episode numbers';
		
		
		
		passthruContainer.dataset('fName',fName);
		passthruContainer.dataset('tName',tName);
		if(logedIn){
			passthruContainer.hover(function(t){
					// p.attr('fill','#83e700');
				},function(t){
					// p.attr('fill','#000');
			});
			passthruContainer.qtip({
			   content: function(){
			   		// console.log(fName,tName);
			   		var curfName = passthruContainer.dataset('fName');
			   		var curtName = passthruContainer.dataset('tName');
			   		
			   		// passthruContainer.addClass('userAction');
					var container = $('<div>');
			   		var absoluteB = $('<input type="button" value="Absolute Passthru (Red)" class="fullWidthButton">')
			   		absoluteB.click(function(){
			        	passthruContainer.addClass('active absolute');
			   			a.animate({'fill':'red'},200);
						passthruConObjs['passthru_'+curfName+'_'+curtName] = {'fid':curfName,'tid':curtName, 'type':'absolute'};
						passthruConObjs['passthru_'+curtName+'_'+curfName] = {'fid':curtName,'tid':curfName, 'type':'absolute'};
						conPassthru(passthruConObjs['passthru_'+curfName+'_'+curtName],'absolute');
			   		});
			   		var seasonepisodeB = $('<input type="button" value="SxxExx Passthru (Yellow)" class="fullWidthButton">')
			   		seasonepisodeB.click(function(){
			        	 passthruContainer.addClass('active sxxexx');
			   			a.animate({'fill':'#f7ff29'},200);
						passthruConObjs['passthru_'+curfName+'_'+curtName] = {'fid':curfName,'tid':curtName, 'type':'sxxexx'};
						passthruConObjs['passthru_'+curtName+'_'+curfName] = {'fid':curtName,'tid':curfName, 'type':'sxxexx'};
						conPassthru(passthruConObjs['passthru_'+curfName+'_'+curtName],'sxxexx');
			   		});
			   		var deleteB = $('<input type="button" value="No Passthru" class="fullWidthButton">')
			   		deleteB.click(function(){
						a.animate({'fill':'#fff'},200);
						window.setTimeout(function(){passthruContainer.removeClass('active sxxexx absolute');},230)
						var obj = passthruConObjs['passthru_'+curfName+'_'+curtName];
						if(!obj)
							obj = passthruConObjs['passthru_'+curtName+'_'+curfName];
						delPassthru(obj);
						delete passthruConObjs['passthru_'+curfName+'_'+curtName];
						delete passthruConObjs['passthru_'+curtName+'_'+curfName];
			   		});
			   		container.append('<p style="width:170px;">'+absoluteText+'<p>');
			   		container.append(absoluteB);
			   		container.append('<br/>');
			   		container.append('<p style="width:170px;">'+sxxexxText+'</p>');
			   		container.append(seasonepisodeB);
			   		container.append('<br/>');
			   		container.append('<br/>');
			   		container.append(deleteB);	
			      return container;
			   },
			   show: {
			      event: 'click'
			   },
			   hide: {
			      fixed: true,
			      delay: 200
			   },
			   events: {
			      hide: function(event, api) {
			         passthruContainer.removeClass('userAction');
			      },
			      show: function(event, api){
			         passthruContainer.addClass('userAction');
			      }
			   }
			});
		}else{		// end if logedIn
			$('.passthru:not(.active)').remove();
			$('.passthru.absolute').qtip({
				content:{
					text: absoluteText
				}	
			})
			$('.passthru.sxxexx').qtip({
				content:{
					text: sxxexxText
				}	
			})
		}
		
		
	});
}

function conPassthru(passthruConObj,curtype){
	
	var params = new Params();    
    params.origin = passthruConObj.fid;
    params.destination = passthruConObj.tid;
    params.element_id = parseInt($('#element').dataset('id'));
    params.type = curtype;
    params.id = passthruConObj.id;
    if(typeof(passthruConObj.id) == "undefined")
	    params.id = 0;
    
    genericRequest("savePassthru", params, pass_con_success, pass_con_fail);
}
function pass_con_success(data, params){
	passthruConObjs['passthru_'+fName+'_'+tName] = {'fid':fName,'tid':tName};
	passthruConObjs['passthru_'+tName+'_'+fName] = {'fid':tName,'tid':fName};
	
	updateAllConIcons();
}

function pass_con_fail(data, params){}

function delPassthru(passthruConObj){
	var params = new Params();    
    params.origin = passthruConObj.fid;
    params.destination = passthruConObj.tid;
    params.element_id = parseInt($('#element').dataset('id'));
    params.id = passthruConObj.id;
    
    genericRequest("deletePassthru", params, pass_con_success, pass_con_fail);
}
function pass_del_success(data, params){

	// a.animate({'transform':'T5,4r90'},200);
	delete passthruConObjs['passthru_'+fName+'_'+tName];
	delete passthruConObjs['passthru_'+tName+'_'+fName];
	updateAllConIcons();
}

function pass_del_fail(data, params){}

function fakeResHandler(){}

function showConnectionOverview(entity, entityID){
	var order = new Array();
	$('.entity').each(function(k,v){
		order.push($(this).dataset('name'))	
	})
	
	var orderString = order.join(',');
	var before = orderString.split(entity)[0].split(',').reverse();
	var after = orderString.split(entity)[1].split(',');
	before.shift();
	after.shift();
	var finalOrder = before.concat(after);
	finalOrder.push(entity);
	
	var origin = {};
	origin.name = entity;
	var pos = $('h3.'+entity).offset();
	origin.y = parseInt(pos.top);
	origin.x = parseInt(pos.left);
	origin.color = colors[entity];
	
	
	var bigPaper = Raphael('overlay',$(window).width(),800);
	jQuery.each(finalOrder, function(k,curEntityName){
		var headlineOld = $('h3.'+curEntityName);
		var curPos = headlineOld.offset();
		var headlineCopy = headlineOld.clone();
		var xOffset = 25;
		var yOffset = -30;
		headlineCopy.css('position','absolute').css('top',(curPos.top+yOffset)+'px').css('left',(curPos.left+xOffset)+'px')
		$('#overlay').append(headlineCopy);
		
		drawConnetionOverview(origin, getConnectionOverview(curEntityName), bigPaper);
		if(curEntityName != entity){
			headlineCopy.qtip({
				content: {
					text: 'Here will be a<br/>connection overview<br/>from '+entity+' to '+curEntityName+'<br/>dummy:<br/>- no passthru<br/>- 200 direct connections'
				},
				position: {
				   my: 'bottom center',  // Position my top left...
				   at: 'top center' // at the bottom right of...
				},
				show: {
					ready: true,
					event: 'clicks'
				},
				hide: {
				   event: 'click'
				}
			});
		}
		
	});
	$('#overlay').show();
	$('#overlay').click(function(){
		$('#overlay').html('').hide();	
		$('.entity h3').css('visibility','visible')
	});
	$('.entity h3').css('visibility','hidden')
};

function getConnectionOverview(entityName){
	var out = {};
	out.name = entityName;
	var pos = $('h3.'+entityName).offset();
	out.y = parseInt(pos.top);
	out.x = parseInt(pos.left);
	out.color = colors[entityName];
	return out;
}

function drawConnetionOverview(origin, destination, paper){
	
	var strength = Math.abs(origin.x-destination.x)*0.5;
	var xOffset = 60;
	var yOffset = 20;
	var yDOffset = 0;
	
	
	var oStrengthX = origin.x+xOffset;
	var oStrengthY = origin.y+strength;
	
	var dStrengthX = destination.x+xOffset;
	var dStrengthY = destination.y+strength;
	
	var oX = origin.x+xOffset;
	var dX = destination.x+xOffset;
	
	var oY = origin.y+yOffset;
	var dY = destination.y+yOffset+yDOffset;
	
	var pathString = 'M'+oX+','+oY+'C'+oStrengthX+','+oStrengthY+','+dStrengthX+','+dStrengthY+','+dX+','+dY;
	
	var path = paper.path(pathString);
	path.attr({stroke: destination.color,'stroke-width':4, 'arrow-end':'classic'});
	if(origin.name == destination.name)
		path.remove();

}

function updateAllConIcons(){
	$('.entity').each(function(k,v){
		updateConIconsFor($(this).dataset('name'));
	});
}

function updateConIconsFor(entityName){
	var connectedTo = resolvePassthrus(entityName);
	var iconContainer = $('.entity.'+entityName+' .seasonHeaderInfo');
	iconContainer.html('');
	jQuery.each(connectedTo,function(k,v){
			var curDesination = $('.entity.'+(v.name));
			// console.log(curDesination);
			// if(!curDesination.hasClass(entityName+'Con'))// ignore if we are next to it
			if(v.name != 'after')
				appendConIcon(iconContainer,v.name,v.type);	
	});
	if(entityName != 'master'){
	    var conCount = resolveConCount(entityName);
	    if(conCount){
	        var countSpan = $('<span style="color:red;vertical-align:top;">'+conCount+'</span>');
	        countSpan.qtip({
	            content: {
	              text: 'This has  <span class="master">'+conCount+'</span> direct connections'
	            }
	        });
	        iconContainer.append(countSpan);
	    }
	}
}

function appendConIcon(iconContainer,destination,type){
	var img = $('<img src="/images/entitys/icon_'+destination+'.png" class="conIcon '+type+'" alt=""/>');
	img.qtip({
		content: {
	      text: 'This has a <span class="'+type+'">'+type+'</span> passthru to <span class="'+destination+'">'+destination+'</span>'
	    }
	});
	iconContainer.append(img);
}

function resolvePassthrus(entityName){
	var connectedToSimple = new Array();
	var absoluteConnectedTo = new Array();
	var sxxexxConnectedTo = new Array();
	
	jQuery.each(passthruConObjs, function(k,v){
		// "passthru_xmaster_scene":{"fid":"xmaster","tid":"scene"}
		if(v.fid == entityName && $.inArray(v.tid,connectedToSimple) == -1){
			connectedToSimple.push(v.tid);
			if(v.type == "sxxexx")
				sxxexxConnectedTo.push({'name':v.tid,'type':v.type});
			else
				absoluteConnectedTo.push({'name':v.tid,'type':v.type});
				
		}
		if(v.tid == entityName && $.inArray(v.fid,connectedToSimple) == -1){
			connectedToSimple.push(v.fid);
			if(v.type == "sxxexx")
				sxxexxConnectedTo.push({'name':v.fid,'type':v.type});
			else
				absoluteConnectedTo.push({'name':v.fid,'type':v.type});
		}
	});
	var final = absoluteConnectedTo.concat(sxxexxConnectedTo);
	// console.log(entityName,connectedToSimple);
	return final;
}
function resolveConCount(entityName){
    var counter = 0;    

    jQuery.each(abstractConObjs, function(k,v){
        if(v.tid == entityName)
            counter++;
        
    });
    return counter;
}

function saveEntityOrder(){
	var order = new Array();
	$('.entity').each(function(k,v){
		order.push($(this).dataset('id'));
	});
	var params = new Params();
	params.element_id = parseInt($('#element').dataset('id'));
	params.order = order.join(',');
	// console.log(params.order);
    genericRequest("saveEntityOrder", params, fakeResHandler, fakeResHandler);
}


function saveNewName(newName){
	var elementID = parseInt($('#element').dataset('id'));
	$('#elementSelector option[value="'+elementID+'"]').text(newName);
	var params = new Params();    
    params.element_id = elementID;
    params.name = newName;
   
    genericRequest("saveNewName", params, fakeResHandler, genericResponseError);
}

function saveAltenativeName(nameID,newName){
	var params = new Params();    
    params.name_id = nameID;
    params.name = newName;
    genericRequest("saveAltenativeName", params, fakeResHandler, genericResponseError);
}
function deleteAltenativeName(nameID){
	var params = new Params();    
    params.name_id = nameID;
    genericRequest("deleteAltenativeName", params, fakeResHandler, genericResponseError);
}


function markSeasonForDeleteAndSubmit(location,season){
	$('#seasonEditForm_'+location+'_'+season).find('input[name="delete"]').val('true');
	$('#seasonEditForm_'+location+'_'+season).submit();
}

function saveSeasonValues(location,season){
	$('#seasonEditForm_'+location+'_'+season).submit();
}


function deleteMe(){
	$('body').qtip({
	   id: "deleteShowModal",
	   content: function() {
	   		var container = $('<div>');
	   		var cancel = $('<input type="button" value="Cancel">')
	   		cancel.click(function(){$('.ui-tooltip-red,#qtip-overlay').remove()})
	   		var del = $('<input type="button" value="DELETE">')
	   		del.click(function(){$('#deleteShowForm').submit()})
	   	
		   	container.append('Sure ?<br/>');
	   		container.append(cancel);
	   		container.append('<br/>');
	   		container.append(del);
	      return container;
	   },
	   position: {
	      my: 'center',
	      at: 'center',
	      target: $(document.body)
	   },
	   show: {
	      modal: true, // Omit the object and set ti to true as short-hand
	      ready: true
	   },
	   style: {
	   		classes: 'ui-tooltip-red'
	   },
	   hide: {
	      fixed: true
	   }
	});
	
}


function showHistory(data,params){
	$('#historyContainer').html("");
	
	var ul = $('<ul>');
	
	jQuery.each(data,function(k,event){
		var li = $('<li style="list-style: circle;margin-bottom: 10px;">');
		li.append(event.time+" "+event.user+" "+event.action+" "+event.type);
		var table = $('<table>');
		table.append('<tr><th>name</th><th>old</th><th>new</th></tr>')
		var curOld = JSON.parse(event.old);
		var curNew = JSON.parse(event.new);
		jQuery.each(curNew,function(key,value){
			table.append("<tr><td>"+key+": </td><td>"+curOld[key]+" &rarr;</td><td>"+curNew[key]+"</td></tr>");
		});
		li.append(table);
		/*
         * li.append("old: "+event.old); li.append("<br/>"); li.append("new: "+event.new);
         */
		ul.append(li);
	})
	$('#historyContainer').append(ul);
}


function showInit(){
	$.fn.qtip.defaults.style.classes = 'ui-tooltip-shadow ui-tooltip-tipsy';
    $.fn.qtip.defaults.position.my = 'top center';
    $.fn.qtip.defaults.position.at = 'bottom center';

	$('.entity h3').bind('dblclick',function(){
		normaliseAll($(this).attr('class'));	
	})
	
	
	$('.entity li.episode').live('hover', function(){
		var li = $(this).attr('id');
		var fids = conObjFrom[li];
		var tids = conObjTo[li];
		
		unmarkAllSVGElements()
		$('div li.episode').removeClass('hover');
		jQuery.each( [fids,tids], function(paperID,fORtIDs){
			if(typeof(fORtIDs) != "undefined"){
				jQuery.each( fORtIDs, function(paperID,curIDs){
					jQuery.each(curIDs, function(k,svgID){
						var svgObj = papers[paperID].getById(svgID);
						svgObj.attr("stroke", "#000");
						svgObj.attr("stroke-width", 2);
						svgObj.toFront();
						var curConObj = conObjs[paperID][svgID];
						$('#'+curConObj['from']).addClass('hover');
						$('#'+curConObj['to']).addClass('hover');
					});
				});
			}
		});		
	});
	
	if(logedIn){
		$('.entity.edit.masterCon li.episode, .entity.edit.master li.episode').live('click', function(){
			if($(this).hasClass('selected'))
				$(this).removeClass('selected');
			else{
				var className = $(this).attr('id').split('_')[0];
				
				$('div.'+className+' li.episode.selected').removeClass('selected');
				$(this).addClass('selected');
			}
			checkConnectionValues()
		});
		$('.seasonHeader').qtip({
			content: function(api) {
		         // Retrieve content from custom attribute of the $('.selector') elements.
		         var season = $(this).dataset('season');
		         var locName = $(this).dataset('locationName');
		         return $('#seasonEdit_'+season+'_'+locName);
		    },
		   hide: {
		      fixed: true,
		      delay: 200
		   },
		   show: {
		      event: 'click'
		   },
	   	   style: {
	          classes: 'ui-tooltip-shadow ui-tooltip-tipsy'
	       },
		   events: {
		      show: function(event, api) {
		      	 var season = $(event.originalEvent.currentTarget).dataset('season');
		         var locName = $(event.originalEvent.currentTarget).dataset('locationName');
		      	 $('#seasonHeader_'+season+'_'+locName).addClass('edit');
		      },
		      hide:  function(event, api) {
		      	 $('.seasonHeader').removeClass('edit');
		      },
		   }
		});	
		$('div.seasonConnection').live('click',function(event){
			var curFirerer = $(this);
			var id = curFirerer.attr('id');
			var el = getSVGElementByCurPos(event.pageX, event.pageY);
			if(el && $(this).hasClass('edit')){
				var wPos = getSVGElementCenter(el, this);
				$(this).qtip({
					content: function(api){
						var delButton = $('<input type="button" value="Delete"/>');
						delButton.bind('click', function(event){
							// curFirerer.click();
							$('.editConncetion').remove();
							disconnect(id,el);
						})
						markBySVGElement(id,el);
						return delButton;	
					},
					show: {
					   	solo: true,
						ready: true
					},
					position: {
						 my: 'bottom left',
					    target: [event.pageX, event.pageY]
					},
				    hide: {
				  		delay: 300,
				        event: 'click'
				    },
					events: {
					   hide: function(event, api) {
					        $(this).remove();
						    unmarkAllSVGElements();
						    unmarkALLLIELements();
					   },
					   show: function(event, api){
					   		$(this).addClass('editConncetion');
					   }
					}
				});
			}
		});
		// mein name edit
		$("#element h1").editable(function(value, settings) { 
		     saveNewName(value);
		     return(value);
		  }, {
		     submit  : 'Set new Name',
	      	 style: "inline",
	      	 cssclass: "mainNameInlineEdit"
		 });
		// main name autogrow
		$('.mainNameInlineEdit input').live('focus',function(){
			var curHalfWidth = parseInt($('#content').css('width'))/2;
			$(this).autoGrowInput({
		    	comfortZone: 30,
		    	maxWidth: curHalfWidth
			});	
		}); 
		// main name qtip
		$("#element h1").qtip({
		   content: {
		      text: 'Click to Edit'
		   },
		   hide: {
		      event: 'click mouseleave'
		   }
		});
		
		$('.names li span.name').editable(function(value, settings) { 
			var nameID = $(this).dataset('id')
			
			if(value == ""){
				deleteAltenativeName(nameID);
				$(this).remove();
			}else
			    saveAltenativeName(nameID,value);
		    return(value);
		  }, {
		    submit  : 'Chane Name',
	      	style: "inline",
	      	cssclass: "alternativeNamesInlineEdit"
		 });
	};
	
	$('.names li span.flag').editable(function(value, settings) {
	    nameID = $(this).dataset('id');
        var params = new Params();
        params.name_id = nameID;
        params.language = value;

        genericRequest("nameUpdate", params, fakeResHandler, genericResponseError);
        $('#flag_'+nameID).attr('src','/images/flags/'+value+'.png');
        
        console.log(value,nameID);
        return(value);
    }, {
        loadurl : '/api/getLanguagesForSelect', 
        type    : 'select',
        submit  : 'OK'
    });
	
	
	// TODO: implement
	$('.conInfo').click(function(){
		var icon = $(this)
		// alert('conection info for '+icon.dataset('entity')+' ('+icon.dataset('entityID')+')');
		showConnectionOverview(icon.dataset('entity'), icon.dataset('entityID'));
	});
	
	$( "#sortable" ).sortable({
			placeholder: "seasonFake"
		});
	// $( "#sortable" ).disableSelection();
	$( "#sortable" ).bind( "sortstop", function(event, ui) {	
		// clearPapers();
		createPapers();
		redraw();
		updatePassthruIcons();	
		updateAllConIcons();
	});
	
	$("#sortable").bind( "sortstart", function(event, ui) {	
		clearPapers();
		resetAllClasses();
		$('.passthru').remove();
		$('.conIcon').remove();
		$('.seasonHeaderInfo').html('');
	});
	$("#sortable").sortable("option", "cancel", ':input,button,a');
	
	resetAllClasses();
	
	$('.passthru').remove();
	clearPapers();
	createPapers();
	redraw();
	updatePassthruIcons();
	updateAllConIcons();
	
	if(!logedIn){
		$('.clicker').remove();
	}
	
	$('#history').click(function(){
		
		var elementID = parseInt($('#element').dataset('id'));
		var params = new Params();
		params.element_id = elementID;
		
	    genericRequest("showRevision", params, showHistory, fakeResHandler);
		
	});
	
	console.log('show init done');
	
	
}

$(document).ready(function() {
   showInit();
 });


