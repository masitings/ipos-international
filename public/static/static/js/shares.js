var uri = window.location.href;
var linkedinUrl = $(".linkedin").attr("href");
$(".linkedin").attr("href",linkedinUrl + "?mini=true&url="+uri+'?q='+Math.random());

var facebookUrl = $(".facebook").attr("href");
$(".facebook").attr("href",facebookUrl + "?u="+uri+'?q='+Math.random());

var twitterUrl = $(".twitter").attr("href");
$(".twitter").attr("href",twitterUrl + "?text="+document.title + "&url="+uri+'?q='+Math.random());

$(".email").attr("href","mailto:?sbject="+document.title + "&body="+uri+'?q='+Math.random());
