
$(document).ready(function(e) {
    
	$('#filters').click(show_filers);
	
	$('#filter_panel_inner a').click(setExPresets);
	
	$('.exchallenge').click(setExChallenges);
	
});

function show_filers (){
	
		$('#filter_panel').css({'display':'block', top : $(this).height()+95});

	
}
function setExAffliate (an){
	exaffliatename = an;
}
function close_filters (){
	$('#filter_panel').css({'display':'none'});
}


function setExSchool (sc){
	exschool = sc;
}

function setExChallenges (){
	
	ch = [];
	
	$.each($('input.challenge:checked'), function(){
		ch.push($(this).data('value'));	
	});
	
	challenge =  ch.join(',');
}

function setExPresets (){
	
	data = $(this).data();
	
	 if(data.key == 'exschool'){
		exschool = data.value;	
	}
	else if(data.key == 'exchallenge'){
		exchallenge = data.value;	
	}
	
	close_filters();
	submitFilters();
}

function submitFilters (isExport){
	
	$('#filter_form').remove();
	
	if(isExport){
		str = '<form id="filter_form" method="post" action="summedexport.php" target="_blank" >';
	}
	else {
		str = '<form id="filter_form" method="post" action="exporttypes.php" >';
	}
	str += '<input type="hidden" name="export" value="'+isExport+'" />';
        str += '<input type="hidden" name="exschool" value="'+exschool+'" />';
	str += '<input type="hidden" name="exchallenge" value="'+exchallenge+'" />';	
	str += '<input type="hidden" name="exaffliatename" value="'+exaffliatename+'" />';
	str += '</form>';
	
	$(document.body).append(str);
	document.getElementById('filter_form').submit();
}