$(document).ready(function() {
	
	function displayMenu(link) {
		var menu = document.getElementById(link.id.replace('Link', '') + 'Menu');
		
		$('#dropdownLinks.right a.actionLink').not(link).removeClass('active');
		$(link).addClass('active');
		
		$('#dropdownLinks.right div.dropdownMenu').not(menu).css('display', 'none');
		menu.style.display = 'block';
		
		var input = $(menu).find('input');
		if(input && input.get(0)) {
			input.get(0).focus();
		}
		
		$('#dropdownLinks').data('active', true);
	}
	
	function hideMenus() {
		$('#dropdownLinks.right a.actionLink').removeClass('active');
		$('#dropdownLinks.right div.dropdownMenu').css('display', 'none');
		$('#dropdownLinks').data('active', false);
	}
	
	$(document)
		.click(function(e) {
			if($('#dropdownLinks').data('active') && $('#dropdownLinks').find(e.target).get(0) == null) {
				hideMenus();
			}
		});
	
	$('#dropdownLinks').data('active', false);
	
	$('#dropdownLinks.right a.actionLink')
		.click(function(e) {
			e.preventDefault();
			if($('#dropdownLinks').data('active')) {
				hideMenus();
			} else {
				displayMenu(this)
				e.stopPropagation();
			}
		})
		.mouseover(function(e) {
			if(!$('#dropdownLinks').data('active')) {
				return;
			}
			
			displayMenu(this);
		});
	
});