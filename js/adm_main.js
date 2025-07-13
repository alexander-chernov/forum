var el;
var elType;
var elName;
var re = new RegExp("(\\[del\\]\\[\\]){1,1}", 'gim');
var reClosed = true;

function js_invert(){
	for (var j = 0; j < document.forms.length; j++) {
		var form = document.forms[j];
		if (form != undefined){
			for (var i = 0; i < form.elements.length; i++) {
				el = form.elements[i];
				elName = el.name;
				elType = el.type.toLowerCase();
				if (elType == 'checkbox'){
				}
				if (elType == 'checkbox' && elName.match(re)){
					if (el.checked){
						el.checked = false;
					}else{
						el.checked = true;
					}
				}
			}
		}
	}
	return false;
}

function chk_check(id){
	var check = document.getElementById(id);
	if (check != undefined){
		if (check.checked){
			check.checked = false;
		}else{
			check.checked = true;
		}
	}
	return false;
}

function checkAll(what, flag) {
	for (var i = 0; i < indexes.length; i++) {
		if (indexes[i] > 0) {
			document.getElementById(what + "_" + indexes[i]).checked = flag;
		}
	}
}

function selectAll(what) {
	for (var i = 0; i < indexes.length; i++) {
		if (indexes[i] > 0) {
			document.getElementById(what + "_" + indexes[i]).checked = !document.getElementById(what + "_" + indexes[i]).checked;
		}
	}
}
function sendEventForm(eventName, what) {
	if (what == undefined){
		what = '';
	}
	if (eventName != undefined) {
		document.getElementById("event" + what).value = eventName;
	}
	document.getElementById("mainform" + what).submit();
}
function deleteConfirm(what, eventName, localeString, selector) {
	var oneCheck = false;
	for (var i = 0; i < indexes.length; i++) {
		if (indexes[i] > 0) {
			oneCheck = document.getElementById(what + "_" + indexes[i]).checked;
			if (oneCheck) break;
		}
	}

	if (oneCheck) {
		if (confirm("Вы уверен что хотите " + localeString + " выделенные записи?")) {
			if (eventName != '') {
				$("#mainform #event").remove();
				$("#mainform").append('<input type="hidden" id="event" name="event" value="' + eventName + '" />');
			}

			if (selector > 0) document.getElementById("selector").value = selector;
			$("#mainform").submit();
		}
	}
	else alert("Вы ничего не выбрали!");	
	return false;
}

function banConfirm(what, eventName, localeString) {
	var oneCheck = false;
	for (var i = 0; i < indexes.length; i++) {
		if (indexes[i] > 0) {
			oneCheck = document.getElementById(what + "_" + indexes[i]).checked;
			if (oneCheck) break;
		}
	}

	if (oneCheck) {
		if (eventName) document.getElementById("event").value = eventName;
		$("#commentLocale").html(localeString);

		if (eventName == 'forumbanuser') {
			$('#ban_type').hide();
		}
		else $('#ban_type').show();
		
		$("#reason").show();
	}
	else alert("Вы ничего не выбрали!");	
}

function forumSort() {
	document.getElementById("mainform").submit();
}