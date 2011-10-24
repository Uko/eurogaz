/**
 *	class Fader
 */
function Fader(_itemID, _fadeSpeed)
{
	var faderIntervalId = 0;
	var fadeSpeed = 5;
	if(_fadeSpeed != null)
		fadeSpeed = _fadeSpeed;
	var id = _itemID;
	this.FadeIn = function()
	{
		var opac = 1;
		var step = 1 / fadeSpeed;
		clearInterval(faderIntervalId);
		var elem = $('#'+id);
		faderIntervalId = setInterval(function(){	opac -= step; 
													if(opac <= 0) 
														opac = 0; 
													elem.css('opacity', opac);
													//setElementOpacity(id, opac);
													if(opac == 0) 
														clearInterval(faderIntervalId);
												}, 30);
	}
	this.FadeOut = function()
	{
		var opac = 0;
		var step = 1 / fadeSpeed;
		clearInterval(faderIntervalId);
		var elem = $('#'+id);
		faderIntervalId = setInterval(function(){ 	opac += step; 
													if(opac >= 1) 
														opac = 1; 
													elem.css('opacity', opac);
													//setElementOpacity(id, opac);
													if(opac == 1) 
														clearInterval(faderIntervalId);
												}, 30);
	}
	this.getID = function(){return id;}
}
/**
 *	all faders on the page
 */
var faders = new Object();

function afterAll()
{
	var fadersIDs = getElementsByClassName("imgFader", "img", document.getElementById("navigation"));
	for(var i = 0; i < fadersIDs.length; i++)
	{
		faders[fadersIDs[i].id] = new Fader(fadersIDs[i].id);
	}
}