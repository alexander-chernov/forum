var uagent    = navigator.userAgent.toLowerCase();
var is_win    =  ( (uagent.indexOf("win") != -1) || (uagent.indexOf("16bit") !=- 1) );
var is_mac    = ( (uagent.indexOf("mac") != -1) || (navigator.vendor == "Apple Computer, Inc.") );
var ua_vers   = parseInt(navigator.appVersion);
var ie_range_cache = '';
var is_safari = ( (uagent.indexOf('safari') != -1) || (navigator.vendor == "Apple Computer, Inc.") );
var is_ie     = ( (uagent.indexOf('msie') != -1) && (!is_opera) && (!is_safari) && (!is_webtv) );
var is_ie4    = ( (is_ie) && (uagent.indexOf("msie 4.") != -1) );
var is_moz    = (navigator.product == 'Gecko');
var is_ns     = ( (uagent.indexOf('compatible') == -1) && (uagent.indexOf('mozilla') != -1) && (!is_opera) && (!is_webtv) && (!is_safari) );
var is_ns4    = ( (is_ns) && (parseInt(navigator.appVersion) == 4) );
var is_opera  = (uagent.indexOf('opera') != -1);
var is_kon    = (uagent.indexOf('konqueror') != -1);
var is_webtv  = (uagent.indexOf('webtv') != -1);
var selField = 'mess_text';

function reply(creator) {
	document.getElementById('heading').value=creator;
	document.getElementById('mess_text').focus();
}
function doInsertVideo(type) {
    if (type=='youtube') {
        var inp = prompt('Введите ссылку в формате http://www.youtube.com/watch?v=xxxxxxxxx');
        if (inp != '' && inp != null) {
            $('#mess_text').val($('#mess_text').val()+'[youtube]'+inp+'[/youtube]');
        }
    }
    if (type=='smotri') {
        var inp = prompt('Введите ссылку в формате http://smotri.com/video/view/?id=vvvvvvvvvv')
        if (inp != '' && inp != null) {
            $('#mess_text').val($('#mess_text').val()+'[youtube]'+inp+'[/youtube]');
        }
    }
    if (type=='rutube') {
        var inp = prompt('Введите ссылку в формате http://rutube.ru/tracks/xxxxxxx.html?v=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
        if (inp != '' && inp != null) {
            $('#mess_text').val($('#mess_text').val()+'[youtube]'+inp+'[/youtube]');
        }
    }
    if (type=='tomskfm') {
        var inp = prompt('Введите ссылку в формате http://tomsk.fm/watch/xxxxxx')
        if (inp != '' && inp != null) {
            $('#mess_text').val($('#mess_text').val()+'[youtube]'+inp+'[/youtube]');
        }
    }
}

function doInsert(ibTag, ibClsTag, isSingle)
{
	var isClose = false;
	var obj_ta = eval('fombj.'+ selField);

	if ( (ua_vers >= 4) && is_ie && is_win)
	{
		if (obj_ta.isTextEdit)
		{
			obj_ta.focus();
			var sel = document.selection;
			var rng = ie_range_cache ? ie_range_cache : sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null)
			{
				if(ibClsTag != "" && rng.text.length > 0)
					ibTag += rng.text + ibClsTag;
				else if(isSingle)
					ibTag += rng.text + ibClsTag;
	
				rng.text = ibTag;
			}
		}
		else
		{
			obj_ta.value += ibTag + ibClsTag;
		}
		rng.select();
	ie_range_cache = null;

	}
	else if ( obj_ta.selectionEnd )
	{ 
		var ss = obj_ta.selectionStart;
		var st = obj_ta.scrollTop;
		var es = obj_ta.selectionEnd;
		
		if (es <= 2)
		{
			es = obj_ta.textLength;
		}
		
		var start  = (obj_ta.value).substring(0, ss);
		var middle = (obj_ta.value).substring(ss, es);
		var end    = (obj_ta.value).substring(es, obj_ta.textLength);
		
		if (obj_ta.selectionEnd - obj_ta.selectionStart > 0)
		{
			middle = ibTag + middle + ibClsTag;
		}
		else
		{
			middle = ibTag + middle + ibClsTag;
		}
		
		obj_ta.value = start + middle + end;
		
		var cpos = ss + (middle.length);
		
		obj_ta.selectionStart = cpos+2;
		obj_ta.selectionEnd   = cpos;
		obj_ta.scrollTop      = st;


	}
	else
	{
		obj_ta.value += ibTag + ibClsTag;
	}

	obj_ta.focus();
	return isClose;
}

function opa_st(a,b){
    if(b>0){
        a.style.display='block';
    }else{
        a.style.display='none';
    }
}
function opa_sf(a,b){
    if(b>0){
        a.style.display='block';
        a.style.top=(Math.round($('#mess_bottom_last').offset().top)-90)+'px';
        a.style.left=(Math.round($('#mess_bottom_last').offset().left)+100)+'px';
    }else{
        a.style.display='none';
    }
}
function opa_sm(a,b){
    if(b>0){
        $('#help_'+a).css({top:($('#err_'+a+'_mess').offset().top+20)+'px'});
        $('#help_'+a).show();
    }else{
        $('#help_'+a).hide();
    }
}


function smilie_box(){
    for(i=0;i<smiles.length;i++){
        document.writeln(' <a href="javascript:;;" onclick="add_smilie(\''+smiles[i]+'\');return false"><img class="png" src="/images/smiles/'+sfiles[i]+'" alt="'+smiles[i]+'" title="'+smiles[i]+'" /></a> ')
    }
}


function add_smilie(x){
    a=document.getElementById('mess_text');
    if(a.disabled==false){
        a.value=a.value+ ' '+x+' ';
        a.focus();
    }
}

String.prototype.replaceAll = function(search, replace){
    return this.split(search).join(replace);
}

function citata(id) {
    var regExpF = new RegExp('http://forum.site/route.php',"g");
    var regExpPic = new RegExp('<img style="text-" src="/images/link1.gif" onmouseover="this.src=\'/images/link2.gif\'" onmouseout="this.src=\'/images/link1.gif\'">',"g");
    var regExp = new RegExp('<a(.+?)>(.+?)<\/a>',"g");
    var regExp2 = new RegExp('href="(.+?)"',"g");
    var RegExpAnyTag = new RegExp("<(.|\n)*?>","ig");

    var block = $('#hide_'+id).html().trim();
    var txt = '';
    var repl = '';
    var repl2 = '';
    if (window.getSelection) {
        txt = window.getSelection().toString();
    } else if (document.getSelection) {
        txt = document.getSelection();
    } else if (document.selection) {
        txt = document.selection.createRange().text;
    }
    if (txt != '') {
        block = txt;
    }
    block = block.replaceAll('<img src="/images/smiles/1.png" class="png">',":)");
    block = block.replaceAll('<img src="/images/smiles/2.png" class="png">',":(");
    block = block.replaceAll('<img src="/images/smiles/3.png" class="png">',":(");
    block = block.replaceAll('<img src="/images/smiles/4.png" class="png">',"0_o");
    block = block.replaceAll('<img src="/images/smiles/5.png" class="png">',":sad:");
    block = block.replaceAll('<img src="/images/smiles/6.png" class="png">',":kiss:");
    block = block.replaceAll('<img src="/images/smiles/7.png" class="png">',":eye:");
    block = block.replaceAll('<img src="/images/smiles/8.png" class="png">',":tong:");
    block = block.replaceAll('<img src="/images/smiles/9.png" class="png">',":hungry:");
    block = block.replaceAll('<img src="/images/smiles/10.png" class="png">',":sleep:");
    block = block.replaceAll('<img src="/images/smiles/11.png" class="png">',":ugly:");
    block = block.replaceAll('<img src="/images/smiles/12.png" class="png">',":smile:");
    block = block.replaceAll('<img src="/images/smiles/13.png" class="png">',":flower:");
    block = block.replaceAll('<img src="/images/smiles/14.png" class="png">',":devel:");
    block = block.replaceAll('<img src="/images/smiles/15.png" class="png">',":glass:");
    block = block.replaceAll('<img src="/images/smiles/16.png" class="png">',":sunglass:");
    block = block.replaceAll('<img src="/images/smiles/17.png" class="png">',":lazy:");
    block = block.replaceAll('<img src="/images/smiles/18.png" class="png">',":crazy:");
    block = block.replaceAll('<img src="/images/smiles/19.png" class="png">',":anger:");
    block = block.replaceAll('<img src="/images/smiles/20.png" class="png">',":love:");
    block = block.replaceAll('<img src="/images/smiles/21.png" class="png">',":cry:");
    block = block.replaceAll('<img src="/images/smiles/22.png" class="png">',":bigcry:");
    block = block.replaceAll('<img src="/images/smiles/23.png" class="png">',":fun:");
    block = block.replaceAll('<img src="/images/smiles/24.png" class="png">',":cool:");
    block = block.replaceAll('<img src="/images/smiles/25.png" class="png">',":pnone:");
    block = block.replaceAll('<img src="/images/smiles/26.png" class="png">',":angel:");
    
    block = block.replaceAll('&amp;',"&");
    block = block.replaceAll('&lt;',"<");
    block = block.replaceAll('&gt;',">");
    block = block.replaceAll('<div class="box_cite">',"[re]");
    block = block.replaceAll('</div>',"[/re]");
    block = block.replace(/(<([^>]+)>)/ig,"");
    block = block.replace(regExpF,"");
    block = block.replace(/\?http/ig,'http');



    if(block.indexOf('<br>') != -1){
        var regExp = new RegExp("<br>","g");
        var repl = block.replace(regExp,"");
    } else repl = block;
    var start_txt = '';
    if ($('#mess_text').val() != '') {
        start_txt = $('#mess_text').val() + '\n';
    }
    var inputObject =  $('#mess_text').val(start_txt + '[re]' + repl + '[/re]\n')[0];
    if (inputObject.selectionStart) {
        var end = inputObject.value.length;
        inputObject.setSelectionRange(end,end);
        inputObject.focus();
    }

    return false;
}

function wordwrap (str, int_width, str_break, cut) {
    var m = ((arguments.length >= 2) ? arguments[1] : 75);
    var b = ((arguments.length >= 3) ? arguments[2] : "\n");
    var c = ((arguments.length >= 4) ? arguments[3] : false);
    var regExp = new RegExp('((ht|f)tp(s?)\:\/\/([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*)(\/?)([a-zA-Z0-9\-\.\?\,\;\'\/\\\=\+&%\$#_]*))',"g");

    var i, j, l, s, r;

    str += '';

    if (m < 1) {
        return str;
    }

    for (i = -1, l = (r = str.split(/\r\n|\n|\r/)).length; ++i < l; r[i] += s) {
        var repl = r[i].match(regExp);
        if (repl == null) {
            for (s = r[i], r[i] = ""; s.length > m; r[i] += s.slice(0, j) + ((s = s.slice(j)).length ? b : "")) {
                j = c == 2 || (j = s.slice(0, m + 1).match(/\S*(\s)?$/))[1] ? m : j.input.length - j[0].length || c == 1 && m || j.input.length + (j = s.slice(m).match(/^\S*/)).input.length;
            }
        } else {
            s = '';
        }
    }
    return r.join("\n");
}

function replaceSmilesId(id){
    var regExp = new RegExp('((ht|f)tp(s?)\:\/\/([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*)(\/?)([a-zA-Z0-9\-\.\?\,\;\'\/\\\=\+&%\$#_!]*))',"g");
    var regExpYoutube = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/ig;
    var width = "620";
    var height = "420";
    var regExpF = new RegExp('http:\/\/forum.site\/route\.php\?',"g");
    var regExp2 = new RegExp('&lt;embed .+&lt;\/embed&gt;',"g");
    var regExpDiv = new RegExp('&lt;div .+&lt;\/div&gt;',"ig");
    var regExpWordWrap = new RegExp('([^\s-]{5})([^\s-]{5})',"ig");
    var htmlStr = $('#message_'+id).html();
    htmlStr = htmlStr.split(':)').join('<img src="/images/smiles/1.png" class="png">');
    htmlStr = htmlStr.split(':))').join('<img src="/images/smiles/12.png" class="png">');
    htmlStr = htmlStr.split(':)))').join('<img src="/images/smiles/12.png" class="png">');
    htmlStr = htmlStr.split('>:(').join('<img src="/images/smiles/3.png" class="png">');
    htmlStr = htmlStr.split('&gt;:(').join('<img src="/images/smiles/3.png" class="png">');
    htmlStr = htmlStr.split(':(').join('<img src="/images/smiles/2.png" class="png">');
    htmlStr = htmlStr.split('0_o').join('<img src="/images/smiles/4.png" class="png">');
    htmlStr = htmlStr.split('o_0').join('<img src="/images/smiles/4.png" class="png">');
    htmlStr = htmlStr.split('O_o').join('<img src="/images/smiles/4.png" class="png">');
    htmlStr = htmlStr.split('o_O').join('<img src="/images/smiles/4.png" class="png">');
    htmlStr = htmlStr.split('О_о').join('<img src="/images/smiles/4.png" class="png">');
    htmlStr = htmlStr.split('о_О').join('<img src="/images/smiles/4.png" class="png">');
    htmlStr = htmlStr.split('O_O').join('<img src="/images/smiles/4.png" class="png">');
    htmlStr = htmlStr.split('0_0').join('<img src="/images/smiles/4.png" class="png">');
    htmlStr = htmlStr.split('О_О').join('<img src="/images/smiles/4.png" class="png">');
    htmlStr = htmlStr.split(':sad:').join('<img src="/images/smiles/5.png" class="png">');
    htmlStr = htmlStr.split(':kiss:').join('<img src="/images/smiles/6.png" class="png">');
    htmlStr = htmlStr.split(':eye:').join('<img src="/images/smiles/7.png" class="png">');
    htmlStr = htmlStr.split(':tong:').join('<img src="/images/smiles/8.png" class="png">');
    htmlStr = htmlStr.split(':hungry:').join('<img src="/images/smiles/9.png" class="png">');
    htmlStr = htmlStr.split(':sleep:').join('<img src="/images/smiles/10.png" class="png">');
    htmlStr = htmlStr.split(':ugly:').join('<img src="/images/smiles/11.png" class="png">');
    htmlStr = htmlStr.split(':smile:').join('<img src="/images/smiles/12.png" class="png">');
    htmlStr = htmlStr.split(':flower:').join('<img src="/images/smiles/13.png" class="png">');
    htmlStr = htmlStr.split(':devel:').join('<img src="/images/smiles/14.png" class="png">');
    htmlStr = htmlStr.split(':glass:').join('<img src="/images/smiles/15.png" class="png">');
    htmlStr = htmlStr.split(':sunglass:').join('<img src="/images/smiles/16.png" class="png">');
    htmlStr = htmlStr.split(':lazy:').join('<img src="/images/smiles/17.png" class="png">');
    htmlStr = htmlStr.split(':crazy:').join('<img src="/images/smiles/18.png" class="png">');
    htmlStr = htmlStr.split(':anger:').join('<img src="/images/smiles/19.png" class="png">');
    htmlStr = htmlStr.split(':love:').join('<img src="/images/smiles/20.png" class="png">');
    htmlStr = htmlStr.split(':cry:').join('<img src="/images/smiles/21.png" class="png">');
    htmlStr = htmlStr.split(':bigcry:').join('<img src="/images/smiles/22.png" class="png">');
    htmlStr = htmlStr.split(':fun:').join('<img src="/images/smiles/23.png" class="png">');
    htmlStr = htmlStr.split(':cool:').join('<img src="/images/smiles/24.png" class="png">');
    htmlStr = htmlStr.split(':pnone:').join('<img src="/images/smiles/25.png" class="png">');
    htmlStr = htmlStr.split(':angel:').join('<img src="/images/smiles/26.png" class="png">');

    var replDiv = htmlStr.match(regExpDiv);
    if (replDiv != null) {
        htmlStr = htmlStr.replace(regExpDiv,'');
    }
    htmlStr = htmlStr.replace(regExpYoutube,'<iframe width="'+width+'" height="'+height+'" src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>');
    htmlStr = htmlStr.replace(regExpF,'');
    htmlStr = htmlStr.replace(/\?http/ig,'http');
    htmlStr = htmlStr.replace(regExp,'<a href="http://forum.site/route.php?$1" target="_blank" title="$1" alt="$1">$4 <img style="text-" src="/images/link1.gif" onMouseOver="this.src=\'/images/link2.gif\'" onMouseOut="this.src=\'/images/link1.gif\'"></a>');

    htmlStr = wordwrap(htmlStr, 50, ' ', true);

    $('#message_'+id).html(htmlStr);
}
function replaceSmiles(perpage){
    var countPage = perpage;
    var regExp = new RegExp('((ht|f)tp(s?)\:\/\/([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*)(\/?)([a-zA-Z0-9\-\.\?\,\;\'\/\\\=\+&%\$#_!]*))',"g");
    var regExpF = new RegExp('http:\/\/forum.site\/route\.php\?',"g");
    var regExp2 = new RegExp('&lt;embed .+&lt;\/embed&gt;',"g");
    var regExpDiv = new RegExp('&lt;div .+&lt;\/div&gt;',"ig");
    var regExpWordWrap = new RegExp('([^\s-]{5})([^\s-]{5})',"ig");
    var regExpYoutube = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/ig;
    var width = "620";
    var height = "420";
    var htmlStr = '';
    var id = 0;
    var messId = 0;
    for(var i=0;i<countPage;i++){
        if ($('.text_box_2_mess:eq('+i+')').html()!=null) {
            var htmlStr = $('.text_box_2_mess:eq('+i+')').html();
            var id = $('.text_box_2_mess:eq('+i+')').attr('id');
            var messId = $('#url_'+id).val();
            if (messId != 1) {
                $('#url_'+id).val('1');
                htmlStr = htmlStr.split(':)').join('<img src="/images/smiles/1.png" class="png">');
                htmlStr = htmlStr.split(':))').join('<img src="/images/smiles/12.png" class="png">');
                htmlStr = htmlStr.split(':)))').join('<img src="/images/smiles/12.png" class="png">');
                htmlStr = htmlStr.split('>:(').join('<img src="/images/smiles/3.png" class="png">');
                htmlStr = htmlStr.split('&gt;:(').join('<img src="/images/smiles/3.png" class="png">');
                htmlStr = htmlStr.split(':(').join('<img src="/images/smiles/2.png" class="png">');
                htmlStr = htmlStr.split('0_o').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('o_0').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('O_o').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('o_O').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('О_о').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('о_О').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('O_O').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('0_0').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('О_О').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split(':sad:').join('<img src="/images/smiles/5.png" class="png">');
                htmlStr = htmlStr.split(':kiss:').join('<img src="/images/smiles/6.png" class="png">');
                htmlStr = htmlStr.split(':eye:').join('<img src="/images/smiles/7.png" class="png">');
                htmlStr = htmlStr.split(':tong:').join('<img src="/images/smiles/8.png" class="png">');
                htmlStr = htmlStr.split(':hungry:').join('<img src="/images/smiles/9.png" class="png">');
                htmlStr = htmlStr.split(':sleep:').join('<img src="/images/smiles/10.png" class="png">');
                htmlStr = htmlStr.split(':ugly:').join('<img src="/images/smiles/11.png" class="png">');
                htmlStr = htmlStr.split(':smile:').join('<img src="/images/smiles/12.png" class="png">');
                htmlStr = htmlStr.split(':flower:').join('<img src="/images/smiles/13.png" class="png">');
                htmlStr = htmlStr.split(':devel:').join('<img src="/images/smiles/14.png" class="png">');
                htmlStr = htmlStr.split(':glass:').join('<img src="/images/smiles/15.png" class="png">');
                htmlStr = htmlStr.split(':sunglass:').join('<img src="/images/smiles/16.png" class="png">');
                htmlStr = htmlStr.split(':lazy:').join('<img src="/images/smiles/17.png" class="png">');
                htmlStr = htmlStr.split(':crazy:').join('<img src="/images/smiles/18.png" class="png">');
                htmlStr = htmlStr.split(':anger:').join('<img src="/images/smiles/19.png" class="png">');
                htmlStr = htmlStr.split(':love:').join('<img src="/images/smiles/20.png" class="png">');
                htmlStr = htmlStr.split(':cry:').join('<img src="/images/smiles/21.png" class="png">');
                htmlStr = htmlStr.split(':bigcry:').join('<img src="/images/smiles/22.png" class="png">');
                htmlStr = htmlStr.split(':fun:').join('<img src="/images/smiles/23.png" class="png">');
                htmlStr = htmlStr.split(':cool:').join('<img src="/images/smiles/24.png" class="png">');
                htmlStr = htmlStr.split(':pnone:').join('<img src="/images/smiles/25.png" class="png">');
                htmlStr = htmlStr.split(':angel:').join('<img src="/images/smiles/26.png" class="png">');

                var replDiv = htmlStr.match(regExpDiv);
                if (replDiv != null) {
                    htmlStr = htmlStr.replace(regExpDiv,'');
                }

                htmlStr = htmlStr.replace(regExpYoutube,'<iframe width="'+width+'" height="'+height+'" src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>');

                htmlStr = htmlStr.replace(regExpF,'');
                htmlStr = htmlStr.replace(/\?http/ig,'http');
                htmlStr = htmlStr.replace(regExp,'<a href="http://forum.site/route.php?$1" target="_blank" title="$1" alt="$1">$4 <img style="text-" src="/images/link1.gif" onMouseOver="this.src=\'/images/link2.gif\'" onMouseOut="this.src=\'/images/link1.gif\'"></a>');

                htmlStr = wordwrap(htmlStr, 50, ' ', true);
                $('.text_box_2_mess:eq('+i+')').replaceWith('<div id="'+id+'" class="text_box_2_mess">'+htmlStr+'</div>');
            }
        }
    }
}
function removeAllHtmlInsideDiv(tag) {
    $(tag).html( $(tag).text() );
};
function replaceSmilesPager(perpage){
    var countPage = perpage;
    var regExp = new RegExp('((ht|f)tp(s?)\:\/\/([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*)(\/?)([a-zA-Z0-9\-\.\?\,\;\'\/\\\=\+&%\$#_]*))',"g");
    var regExpF = new RegExp('http:\/\/forum.site\/route\.php\?',"g");
    var regExp2 = new RegExp('&lt;embed .+&lt;\/embed&gt;',"g");
    var regExpDiv = new RegExp('&lt;div .+&lt;\/div&gt;',"ig");
    var regExpWordWrap = new RegExp('([^\s-]{5})([^\s-]{5})',"ig");
    var regExpYoutube = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/ig;
    var width = "620";
    var height = "420";

    for(var i=0;i<countPage;i++){
        if ($('.text_box_2_mess:eq('+i+')').html()!=null) {
            var htmlStr = $('.text_box_2_mess:eq('+i+')').html();
            var id = $('.text_box_2_mess:eq('+i+')').attr('id');
            var messId = $('#url_'+id).val();
            if (messId != 1) {
                $('#url_'+id).val('1');
                htmlStr = htmlStr.split(':)').join('<img src="/images/smiles/1.png" class="png">');
                htmlStr = htmlStr.split(':))').join('<img src="/images/smiles/12.png" class="png">');
                htmlStr = htmlStr.split(':)))').join('<img src="/images/smiles/12.png" class="png">');
                htmlStr = htmlStr.split('>:(').join('<img src="/images/smiles/3.png" class="png">');
                htmlStr = htmlStr.split('&gt;:(').join('<img src="/images/smiles/3.png" class="png">');
                htmlStr = htmlStr.split(':(').join('<img src="/images/smiles/2.png" class="png">');
                htmlStr = htmlStr.split('0_o').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('o_0').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('O_o').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('o_O').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('О_о').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('о_О').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('O_O').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('0_0').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split('О_О').join('<img src="/images/smiles/4.png" class="png">');
                htmlStr = htmlStr.split(':sad:').join('<img src="/images/smiles/5.png" class="png">');
                htmlStr = htmlStr.split(':kiss:').join('<img src="/images/smiles/6.png" class="png">');
                htmlStr = htmlStr.split(':eye:').join('<img src="/images/smiles/7.png" class="png">');
                htmlStr = htmlStr.split(':tong:').join('<img src="/images/smiles/8.png" class="png">');
                htmlStr = htmlStr.split(':hungry:').join('<img src="/images/smiles/9.png" class="png">');
                htmlStr = htmlStr.split(':sleep:').join('<img src="/images/smiles/10.png" class="png">');
                htmlStr = htmlStr.split(':ugly:').join('<img src="/images/smiles/11.png" class="png">');
                htmlStr = htmlStr.split(':smile:').join('<img src="/images/smiles/12.png" class="png">');
                htmlStr = htmlStr.split(':flower:').join('<img src="/images/smiles/13.png" class="png">');
                htmlStr = htmlStr.split(':devel:').join('<img src="/images/smiles/14.png" class="png">');
                htmlStr = htmlStr.split(':glass:').join('<img src="/images/smiles/15.png" class="png">');
                htmlStr = htmlStr.split(':sunglass:').join('<img src="/images/smiles/16.png" class="png">');
                htmlStr = htmlStr.split(':lazy:').join('<img src="/images/smiles/17.png" class="png">');
                htmlStr = htmlStr.split(':crazy:').join('<img src="/images/smiles/18.png" class="png">');
                htmlStr = htmlStr.split(':anger:').join('<img src="/images/smiles/19.png" class="png">');
                htmlStr = htmlStr.split(':love:').join('<img src="/images/smiles/20.png" class="png">');
                htmlStr = htmlStr.split(':cry:').join('<img src="/images/smiles/21.png" class="png">');
                htmlStr = htmlStr.split(':bigcry:').join('<img src="/images/smiles/22.png" class="png">');
                htmlStr = htmlStr.split(':fun:').join('<img src="/images/smiles/23.png" class="png">');
                htmlStr = htmlStr.split(':cool:').join('<img src="/images/smiles/24.png" class="png">');
                htmlStr = htmlStr.split(':pnone:').join('<img src="/images/smiles/25.png" class="png">');
                htmlStr = htmlStr.split(':angel:').join('<img src="/images/smiles/26.png" class="png">');
                var replDiv = htmlStr.match(regExpDiv);
                if (replDiv != null) {
                    htmlStr = htmlStr.replace(regExpDiv,'');
                }
                htmlStr = htmlStr.replace(regExpYoutube,'<iframe width="'+width+'" height="'+height+'" src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>');
                htmlStr = htmlStr.replace(regExpF,'');
                htmlStr = htmlStr.replace(/\?http/ig,'http');
                htmlStr = htmlStr.replace(regExp,'<a href="http://forum.site/route.php?$1" target="_blank" title="$1" alt="$1">$4 <img style="text-" src="/images/link1.gif" onMouseOver="this.src=\'/images/link2.gif\'" onMouseOut="this.src=\'/images/link1.gif\'"></a>');
                htmlStr = wordwrap(htmlStr, 50, ' ', true);
                $('.text_box_2_mess:eq('+i+')').replaceWith('<div id="'+id+'" class="text_box_2_mess">'+htmlStr+'</div>');
            }
        }
    }
}



