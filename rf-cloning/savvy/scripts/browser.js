if (navigator.platform && (navigator.platform.indexOf('Mac')) >-1){
	// Mac Users
	document.write('<link href="styles/mac.css" rel="styleSheet" type="text/css">');
	}

else {
//windows or unix
if(navigator.platform.indexOf('Win')>-1){
if (navigator.appName.indexOf('Microsoft') > -1){
	//explorer
	document.write('<link href="styles/winie.css" rel="styleSheet" type="text/css">');
	}
else{
document.write('<link href="styles/winns.css" rel="styleSheet" type="text/css">');
}}
else{
	document.write('<link href="styles/unix.css" rel="styleSheet" type="text/css">');
	//alert("Unix")
}
}