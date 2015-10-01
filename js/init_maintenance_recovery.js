var activeLabel;
var submitButton;
var displayConsole;
function secundaryInit() {
	displayConsole = $("div.pane div.console-area", "div#refresh.detail-pane");
	activeLabel = "main_menu_item_maitenance";
	displayActiveLabel();
	var headerHeight = $("html>body>header.ui-helper-clearfix").height();
	var navHeight = $("div#tabs").height();
	var availableHeight = $(window).height() - headerHeight - navHeight - 20;

	$("#maintenance-recovery").height(availableHeight).layout({
		defaults : {
			applyDemoStyles : true
		},
		west : {
			resizable : true,
			size : "50%"
		}
	});
	initMetadataRecoveryControls();

}
function initMetadataRecoveryControls() {
	var $metadataRecoveryForm = $("form#metadata-recovery",
			"div#maintenance-recovery");
	$metadataRecoveryForm.find("input[type=submit]").remove();

	$metadataRecoveryForm.attr("action",
			$metadataRecoveryForm.attr("action") + "/async").ajaxForm({
		dataType : 'xml',
		cache : true,
		beforeSend : function() {

			changeButtonState($metadataRecoveryForm, "pending");
			// displayResult("Veuillez patienter",
			// "La requête de mise à jour a été envoyée.", "wait",
			// $metadataRecoveryDisplayArea, "blue");

		},
		complete : function(xhr) {
			// handleRefreshRequestFirstResponse(xhr.responseXML,
			// $metadataRecoveryDisplayArea, $metadataRecoveryForm,
			// "blue");
		}
	});
	$metadataRecoveryForm.button({
		icons : {
			primary : "ui-icon-preview",
			secondary : "ui-icon-refresh-state"
		}
	}).click(function() {
		if ($metadataRecoveryForm.hasClass("ui-state-disabled"))
			return false;
		$metadataRecoveryForm.submit();
	});
}
function changeButtonState(button, state) {
	var iconClass = "ui-icon-refresh-state";
	switch (state) {
	case "default":
		iconClass = "ui-icon-refresh-state";
		break;
	case "pending":
		iconClass = "ui-icon-refresh-state-wait";
		break;
	case "success":
		iconClass = "ui-icon-refresh-state-success";
		break;
	case "error":
		iconClass = "ui-icon-refresh-state-error";
		break;

	default:
		break;
	}
	if (button.hasClass(iconClass))
		return;
	button.find("span.ui-icon.ui-button-icon-secondary").removeClass(
			"ui-icon-refresh-state").removeClass("ui-icon-refresh-state-wait")
			.removeClass("ui-icon-refresh-state-success").removeClass(
					"ui-icon-refresh-state-error").addClass(iconClass);
	if (state == "pending")
		button.addClass("ui-state-disabled");
	else
		button.removeClass("ui-state-disabled")
}
function displayResult(status, message, decoration, element, color) {
	var decorationClass = "ui-corner-all ";
	var imgsrc = "";
	switch (decoration) {
	case "error":
		decorationClass += "ui-state-error ";
		imgsrc = "warning.png";
		break;
	case "running":
		decorationClass += "ui-state-active ";
		imgsrc = "running.gif";
		break;
	case "wait":
		decorationClass += "ui-state-active ";
		imgsrc = "wait-icon.gif";
		break;
	case "success":
		decorationClass += "ui-state-active ";
		imgsrc = "success.png";
		break;
	}
	element.removeClass().addClass(decorationClass).html(
			"<h5>" + status + "</h5>");
	var moreLines = (decoration == "success") ? "<p></p>" : "";
	displayConsole.html(displayConsole.html() + '<p style="color:' + color
			+ '">' + message + "</p>" + moreLines);
	fixHeightProblems();
}
function fixHeightProblems() {
	if (displayConsole.get(0).scrollHeight > displayConsole.get(0).clientHeight) {
		var first = displayConsole.find("p").first();
		first.animate({
			"margin-top" : -first.height()
		}, {
			complete : function() {
				displayConsole.find("p").first().remove();
				fixHeightProblems();
			}
		});
	}
}
function getDisplayParameters() {
	return "";
}