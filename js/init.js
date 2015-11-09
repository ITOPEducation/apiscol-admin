(function($) {
	$(function() {
		$("html").removeClass("nojs").addClass("js");
		$("html.js>body>div.content, html.js>body>header.ui-helper-clearfix")
				.css('visibility', 'visible');
		handleConnexionBox();
		handleLanguagePad();
		handleErrorMessages();
		secundaryInit();

	});
})(jQuery);
function handleConnexionBox() {
	$("div.connexion-box form input[type=submit]").button();
	$("div.status-bar span.status form span.disconnect-wrapper").button({
		icons : {
			primary : "ui-icon-disconnect",
		},
		text : false
	}).click(function() {
		$(this).closest("form").submit();
	});
	$(".status.connexion").button({
		icons : {
			primary : "ui-icon-connect",
		},
		text : false
	}).popup({
		popup : $(".connexion-box"),
		position : {
			my : "right top",
			at : "right bottom"
		}
	});
	// $(".connexion-box").hide();
}
function handleErrorMessages() {
	if ($("div.private-error-message").text().match(/^\s*$/)) {
		$("div.private-error-message").remove();

	} else {
		$('<span class="private-error-message-button"></span>')
				.button({
					icons : {
						primary : "ui-icon-bug",
					},
					text : false
				})
				.appendTo(
						$("header.ui-helper-clearfix div.status-bar span.ui-state-highlight"))
				.popup({
					popup : $("div.private-error-message"),
					position : {
						my : "right top",
						at : "right bottom"
					}
				});
	}
}
function handleLanguagePad() {
	$("form.pad_langs label img.item_pad_lang").not('.active').click(
			function() {
				$(this).closest('label').prev('input').attr('checked',
						'checked').closest("form").submit();
			})
}
$.extend({
	confirm : function(message, title, okAction) {
		$("<div></div>").dialog({
			open : function(event, ui) {
				$(".ui-dialog-titlebar-close").hide();
			},
			buttons : {
				"Ok" : function() {
					$(this).dialog("close");
					okAction();
				},
				"Cancel" : function() {
					$(this).dialog("close");
				}
			},
			close : function(event, ui) {
				$(this).remove();
			},
			resizable : false,
			title : title,
			modal : false
		}).text(message);
	}
});
function getHiddenParameter($key) {
	console.log($("input#" + $key))
	return $("input#" + $key + "[type=hidden]").val();
}
function displayActiveLabel() {
	if (!activeLabel)
		return;
	$("nav ul.menu li.menu-item", "div#tabs").not("#second_menu_item_back")
			.each(function(index, elem) {
				$(elem).toggleClass("ui-state-focus", activeLabel == elem.id);
			});
}
function displayBlockingModal(bool) {
	if (bool) {
		$('body').addClass('blocked');
		displayTestInBlockingModal("", true);
	} else
		$('body').removeClass('blocked');
}
function displayTestInBlockingModal(html, erase) {
	if (erase)
		$("#blocking-modal-text").empty();
	$("#blocking-modal-text").html(
			$("#blocking-modal-text").html() + "<br/>" + html);
}
function sendSelectedResourcesList(selectedMetadataId, selectedMetadata,
		callback) {
	$.ajax({
		type : "POST",
		url : "/resources/structure/async",
		data : {
			'select-metadata[]' : selectedMetadata,
			'select-metadata-id[]' : selectedMetadataId
		},
		headers : {
			accept : "application/atom+xml"
		},
		error : function(xhr) {
			console.log(xhr.responseXML);

		},
		success : callback
	});
}
function activateSubmitButton(bool) {
	$("span.submit-button").button(bool ? "enable" : "disable");
	setDirty(bool);
}
function putWaiterOnSubmitButton(bool) {
	if (bool)
		$("span.submit-button").find("span.ui-icon")
				.removeClass("ui-icon-save").addClass("ui-icon-wait");
	else
		$("span.submit-button").find("span.ui-icon")
				.removeClass("ui-icon-wait").addClass("ui-icon-save");
}
function setDirty(bool) {
	if (bool)
		$(window)
				.bind(
						'beforeunload',
						function() {
							return "Voulez vous réellement abandonner vos modifications non enregistrées ?";
						});
	else
		$(window).unbind('beforeunload');
}