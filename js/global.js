var isMobileSize = false;

$(document).ready(function()
{
	checkForMobileSize();

	//mobilemenu
	$('.mobile').click(function()
	{
		var $self = $(this);
		$('.mobileMenu').slideToggle(function()
		{
			$self.toggleClass('closed');
		});
	});

	//navigation script
	$('.navigation ul li a').click(function()
	{
		$('.mobileMenu').removeAttr('style');
		$('#mobileSection .mobile').removeClass('closed');
	});

	//====DO NOT TOUCH====
	$('a.slicknav_btn').click(function()
	{
		$('.mobilemenu ul').css({ 'display': 'block' });
	});
	//====/DO NOT TOUCH====
	
	//====Loop through all anchors====
	/*$('a[href*=\\#]:not([href=\\#])').click(function()
	{
	});*/

	window.addEventListener('scroll', function(e)
	{
		if(!isMobileSize)
		{
			var distanceY = window.pageYOffset || document.documentElement.scrollTop,
				shrinkOn = 50;

			if(distanceY > shrinkOn)
				$('header').addClass('smaller');
			else
				$('header').removeClass('smaller');
		}
	});
}); 

function checkForMobileSize()
{
	isMobileSize = window.matchMedia("only screen and (min-width: 480px) and (max-width: 767px)").matches ||
	window.matchMedia("only screen and (max-width: 479px)").matches;
}

$(window).on('load', function()
{
	//$('header').removeClass('smaller'); // header hat standardm‰ﬂig smaller nur bei aktiven js wird smaller entfernt
});

