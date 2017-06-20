
$(document).ready(function(e) {
    
	$('#filters').click(show_filers);
	
	$('#start').datepicker({onSelect: setStartDate  });
	$('#end').datepicker({onSelect: setEndDate  });
	
	$('#filter_panel_inner a').click(setPresets);
	
	$('.challenge').click(setChallenges);
	
});

function show_filers (){
	
		$('#filter_panel').css({'display':'block', top : $(this).height()+95});

	
}
function setAffliate (an){
	affliatename = an;
}
function close_filters (){
	$('#filter_panel').css({'display':'none'});
}

function setStartDate (dt){
	start = dt;
}

function setEndDate (dt){
	end = dt;
}

function setSchool (sc){
	school = sc;
}

function setChallenges (){
	
	ch = [];
	
	$.each($('input.challenge:checked'), function(){
		ch.push($(this).data('value'));	
	});
	
	challenge =  ch.join(',');
}

function setPresets (){
	
	data = $(this).data();
	
	if(data.key == 'start'){
		start = data.value;	
	}
	else if(data.key == 'school'){
		school = data.value;	
	}
	else if(data.key == 'challenge'){
		challenge = data.value;	
	}
	
	close_filters();
	submitFilters();
}

function submitFilters (isExport){
	
	$('#filter_form').remove();
	
	if(isExport){
		str = '<form id="filter_form" method="post" action="export.php" target="_blank" >';
	}
	else {
		str = '<form id="filter_form" method="post" action="index.php" >';
	}
	
	str += '<input type="hidden" name="start" value="'+start+'" />';
	str += '<input type="hidden" name="end" value="'+end+'" />';
	str += '<input type="hidden" name="school" value="'+school+'" />';
	str += '<input type="hidden" name="challenge" value="'+challenge+'" />';	
	str += '<input type="hidden" name="affliatename" value="'+affliatename+'" />';
	str += '</form>';
	
	$(document.body).append(str);
	document.getElementById('filter_form').submit();
}