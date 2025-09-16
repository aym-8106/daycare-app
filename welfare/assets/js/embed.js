$('iframe#answers-widget-frame').load(function(){

  var $frame = $('iframe#answers-widget-frame');
  var doc = $frame[0].contentWindow.document;
  var $head = $('head',doc);

  console.log("embed", $head.html())
  //var $head = $('iframe#answers-widget-frame').contents().find('head');
  //var url = "<?php //echo $css_url; ?>//";
  //$head.append($("<link/>", { rel: "stylesheet", href: url, type: "text/css" } ));

});