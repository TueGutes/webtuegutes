var isMobileSize = false;

function toggleMenu(close)
{
	if(close)
		$('.mobileMenu').slideUp(function()
		{
			$('.mobile').removeClass('closed');
		});
	else
		$('.mobileMenu').slideToggle(function()
		{
			$('.mobile').toggleClass('closed');
		});
}
function toggleProfile(close)
{
	if(close)
		$('.mobileProfileContent').slideUp(function()
		{
			$('.mobileProfileContent').addClass('closed');
		});
	else
		$('.mobileProfileContent').slideToggle(function()
		{
			$('.mobileProfileContent').toggleClass('closed');
		});
}

$(document).ready(function()
{
	checkForMobileSize();

	//mobilemenu
	$('.mobileMenuButton').click(function()
	{
		toggleProfile(true);
		toggleMenu();
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
	
	//mobiel profile
	$('.mobile .profile').click(function()
	{
		toggleMenu(true);
		toggleProfile();
	});
	
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

