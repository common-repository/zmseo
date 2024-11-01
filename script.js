"use strict";
/*global $, jQuery, alert, console*/

var zm_focusIsSet = false;
function zm_getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		if(pair[0] == variable){return pair[1];}
	}
	return(false);
}


var zm_mark_text = function(button){

	//console.log(tinyMCE.activeEditor);
	//if(tinyMCE.activeEditor != null){
	if( !$(".is-button.is-primary").length ){
		var content = tinyMCE.activeEditor.getContent();
		//console.log(content);

		if( button.hasClass("zm-active") ){
			//console.log("-- start - --");
			var regexStr = "<mark\\b[^>]*data-zm-counter-" + button.data("zm-counter") + "\\b[^>]*>([^<>]*)<\/mark>";
			var regex = new RegExp(regexStr,"ig");
			content = content.replace(regex,"$1");
			content = content.replace(regex,"$1");
			content = content.replace(regex,"$1");

		} else {
			var keywords = button.data("keywords").split("-|-");

			for (var i = 0; i < keywords.length; i++) {
				//console.log("-- start + --");
				//console.log(keywords[i]);
				//console.log(content);
				var regex = new RegExp(keywords[i],"ig");
				content = content.replace(regex,"<mark class='annotation-text zm-annotation-text data-zm-counter-"+button.data("zm-counter")+" ' id='zm-annotation-text-" + i + "'>$&</mark>");
			}
		}
		tinyMCE.activeEditor.setContent(content);
		zm_focusIsSet = true;
	} else {

		if( button.hasClass("zm-active") ){
			//console.log("-- start - --");
			var regexStr = "<mark\\b[^>]*data-zm-counter-" + button.data("zm-counter") + "\\b[^>]*>([^<>]*)<\/mark>";
			var regex = new RegExp(regexStr,"g");
			//console.log(regex);
			//var regex = /<mark data-zm-counter-\b[^>]*>([^<>]*)<\/mark>/ig;
			$(".editor-rich-text, .block-library-rich-text__tinymce").each(function(){
				var content = $(this).html();
				content = content.replace(regex,"$1");
				content = content.replace(regex,"$1");
				content = content.replace(regex,"$1");
				//console.log(content);
				$(this).html(content);
			});
		} else {
			var keywords = button.data("keywords").split("-|-");
			//console.log("-- start + --");
			//console.log(keywords);

			$(".editor-rich-text, .block-library-rich-text__tinymce").each(function(){
				//var content = $(this).click();
				var content = $(this).html();
				//console.log(content);
				for (var i = 0; i < keywords.length; i++) {
					//console.log(keywords[i]);
					var regex = new RegExp(keywords[i],"ig");
					content = content.replace(regex,"<mark class='annotation-text zm-annotation-text data-zm-counter-" + button.data("zm-counter") + "' id='zm-annotation-text-" + i + "'>$&</mark>");
				}
				$(this).html(content);
			});
		}

	}


	button.toggleClass("zm-active");
};



var tinyOnChange = function(){
	if(tinyMCE.activeEditor != null){
		if(0){
			//tinyMCE.activeEditor.dom.setHTML(tinymce.activeEditor.dom.select('*'), 'some inner html');
		}
		//console.log("Start");
		if(zm_focusIsSet){
			//console.log("change");
			//setTimeout(function(){
			$(".js-zm-mark-button").removeClass("zm-active");
				var content = tinyMCE.activeEditor.getContent();
				content = content.replace(/(\r\n|\n|\r)/gm, "");
				var regexStr = "<mark\\b[^>]*data-zm-counter-\\b[^>]*>([^<>]*)<\/mark>";
				var regex = new RegExp(regexStr,"gi");
				content = content.replace(regex,"$1");
				content = content.replace(regex,"$1");
				content = content.replace(regex,"$1");
				tinyMCE.activeEditor.setContent(content);
				//tinyMCE.activeEditor.getBody().innerHTML = content;
				//tinyMCE.activeEditor.dom.setHTML(tinymce.activeEditor.dom.select('body'), content);
			//},500);
		}
		zm_focusIsSet = false;
	}
};

$(document).ready(function() {

	$("#publish, #content-html").mousedown(function(){
		//console.log("mousedown");
		$(".js-zm-mark-button").removeClass("zm-active");
		//console.log("!");
		var content = tinyMCE.activeEditor.getContent();

		var regexStr = "<mark\\b[^>]*data-zm-counter-\\b[^>]*>([^<>]*)<\/mark>";
		var regex = new RegExp(regexStr,"g");
		content = content.replace(regex,"$1");
		content = content.replace(regex,"$1");
		content = content.replace(regex,"$1");

		tinyMCE.activeEditor.setContent(content);
		zm_focusIsSet = false;
	});


	$(".js-zm-mark-button").click(function(){
		zm_mark_text($(this));
	});

	// Костыль на сохранение настроек
	$(".js-zm-ajax-btn").click(function(){
		//console.log(tinyMCE);
		//if(tinyMCE.activeEditor != null){
		if( $(".is-button.is-primary").length ){
			var that = $(this);
			if(that.attr("name") == "add_keys"){ // Ключи
				var arr_keys = $(".zmseo_tabs").find("textarea[name=arr_keys]").val(),
					type = $(".zmseo_tabs").find("input[name=type]").val(),
					post_id = zm_getQueryVariable("post"),
					postData = "zm_save_ajax=true&add_keys=true&arr_keys=" + arr_keys + "&type=" + type + "&post_id=" + post_id;
			} else if(that.attr("name") == "save_supp"){ // Радиокнопки
				var title = $(".zmseo_tabs").find("input[name=title]:checked").val(),
					description = $(".zmseo_tabs").find("input[name=description]:checked").val(),
					h1 = $(".zmseo_tabs").find("input[name=h1]:checked").val(),
					postData = "zm_save_ajax=true&save_supp=true&title=" + title + "&description=" + description + "&h1=" + h1;
			}
			if(typeof postData != "undefined"){
				//console.log(postData);
				that.fadeTo(120,0.2);
				$.ajax({
					type: "POST",
					url: window.location.href,
					data: postData,
					success: function(data){
						var text = that.text();
						that.text("Сохранено").fadeTo(120,1);
						setTimeout(function(){
							that.text(text);
						},2000);
						//console.log("Success");
						//console.log(data);
					}
				});
			}
		}
	});


});

//  tinymce.activeEditor.setContent(content);
//$(document).ready(function() {
//setTimeout(function(){
//    var wat = $(".editor-rich-text").html();
//    console.log(wat);
//},900);
//});
//var content = wp.data.select( "core/editor" ).getCurrentPost().content;