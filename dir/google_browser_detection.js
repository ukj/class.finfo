// Copyright 2007 Google Inc.
// All Rights Reserved.

// This is mildly crufty - we need to be doing object detection instead of all
// of this browser detection stuff.
var userAgentStr = navigator.userAgent.toLowerCase();

var isIE = ((userAgentStr.indexOf("msie") != -1)
    && (userAgentStr.indexOf("opera") == -1) && (userAgentStr.indexOf("webtv")
    == -1)) ? true : false;
var oldIE = (userAgentStr.indexOf("msie 7") != -1) || (userAgentStr.indexOf("msie 6") != -1) || (userAgentStr.indexOf("msie 5") != -1)
         || (userAgentStr.indexOf("msie 4") != -1) || (userAgentStr.indexOf("msie 3") != -1);
var isGecko = (userAgentStr.indexOf("gecko") != -1) ? true : false;
var isGooglechrome = (userAgentStr.indexOf("chrome") != -1) ? true : false;

var allowSafari = false;
var allowCamino = true;
var isCamino = (userAgentStr.indexOf("camino") != -1) ? true : false;
if (isCamino && userAgentStr.indexOf("1.0") == -1) {
	allowCamino = false;
}

var isKonqueror = (userAgentStr.indexOf("konqueror") != -1) ? true : false;

var isSafari = (userAgentStr.indexOf("applewebkit") != -1) ? true : false;
// Safari claims to be "like Gecko"
if (isSafari) {
  isGecko = false;
}

var isOpera = (window.opera) ? true : false;
// Opera likes to lie
if (isOpera) {
  isIE = oldIE = isGecko = isSafari = isCamino = isKonqueror = false;
}

var isMac = (userAgentStr.indexOf("Macintosh") != -1) ? true : false;
var isLinux = (userAgentStr.indexOf("Linux") != -1) ? true : false;

var isWebkit = (isSafari) || (isKonqueror) || (isGooglechrome);
var isGecko = ( (!isSafari && !isKonqueror && !isOpera && !isIE ) && isGecko ) ? true : false;

var userAgentStr_splits = userAgentStr.split('/');
var isOldSafari = isSafari;
if (isSafari) {
  // Parse out the user string, look for the build number -
  // anything above 412 is cool.
  try {
    isOldSafari = parseInt(userAgentStr_splits[userAgentStr_splits.length - 1], 10) < 412;
  } catch (e) {
    isOldSafari = true;
    // Can't parse, assume the worst.
  }
}
var isOldGecko = isGecko;
if (isOldGecko) {
	try {
		// Safari can lie in navigator.productSub
        isOldGecko = (parseInt(userAgentStr_splits[userAgentStr_splits.length - 2], 10) < 20061208);
        isOldGecko =  ( isLinux && (parseInt(navigator.productSub,10) < 20061208));
        //(parseInt(userAgentStr_splits[userAgentStr_splits.length - 3], 10) < 20061208))
		//isOldGecko = (parseInt(navigator.productSub,10) < 20061208);
	} catch (e) {
		isOldGecko = true;
	}
}
var isOldOpera = isOpera;
if (isOldOpera) {
  try {
    isOldOpera = parseInt(userAgentStr_splits[userAgentStr_splits.length - 1], 10) < 8;
  } catch (e) {
    isOldOpera = true;
    // Can't parse, assume the worst.
  }
}

var isOldBrowser = false;
if(isOldSafari || isIE || oldIE || isOldOpera || isOldGecko) {
	isOldBrowser = true;
}

