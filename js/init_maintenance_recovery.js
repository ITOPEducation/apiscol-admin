var activeLabel;
var submitButton;
var displayConsole;
var awaitedMetadataRecoveryReports = new Array();
var scanIsRunning = false;
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
			$metadataRecoveryForm.attr("action") + "/async").ajaxForm(
			{
				dataType : 'xml',
				cache : true,
				beforeSend : function() {

					changeButtonState($metadataRecoveryForm, "pending");
					displayResult("Veuillez patienter",
							"La requête de mise à jour a été envoyée.");

				},
				complete : function(xhr) {
					handleMetadataRecoveryFirstResponse(xhr.responseXML,

					$metadataRecoveryForm, "blue");
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
function handleMetadataRecoveryFirstResponse(data, $refreshPreviewForm, color) {
	if (!data)
		displayResult("Problème inconnu", "Pas de réponse du serveur");
	else if (data.firstChild.tagName == "error") {

		displayResult($(data).find("intro").text(), $(data).find("message")
				.text());
		changeButtonState($refreshPreviewForm, "error");

	} else {
		var messages = getMessagesAsText(data);
		var state = $(data).find("apiscol\\:state, state").text();
		displayResult(state, messages);
		var linkElement = $(data).find("link[rel='self']");
		awaitedMetadataRecoveryReports.push({
			url : linkElement.attr("href"),
			button : $refreshPreviewForm
		});
		if (!scanIsRunning)
			scanForMaintenanceProcessReports();
		if (state == "done") {
			{
				changeButtonState($refreshPreviewForm, "success");
			}
		} else {

		}
	}
}
function getMessagesAsText(data) {
	$messages = $(data).find("apiscol\\:message, message");
	$messagesText = "";
	$messages.each(function(index, elem) {
		$messagesText += $(elem).text() + "<br/>";
	});
	return $messagesText;
}
function scanForMaintenanceProcessReports() {
	scanIsRunning = true;
	if (awaitedMetadataRecoveryReports.length == 0) {
		scanIsRunning = false;
		return;
	}
	var maintenanceProcessReport = awaitedMetadataRecoveryReports[0];

	$.ajax({
		dataType : 'xml',
		type : "GET",
		url : window.location + "/maintenance-process-report/"
				+ maintenanceProcessReport.url + "/async",
		error : function(msg) {
			console.log(msg);
		},
		success : function(result) {
			handleMaintenanceProcessReport(result,
					maintenanceProcessReport.button);
		}
	});
}
function handleMaintenanceProcessReport(data, button) {
	if (data.firstChild.tagName == "error") {
		{
			displayResult($(data).find("intro").text(), $(data).find("message")
					.text());
			changeButtonState(button, "error");
			awaitedMetadataRecoveryReports.shift();
			return;
		}
	}
	var state = $(data).find("apiscol\\:state,state").text();
	var messages = getMessagesAsText(data);
	var link = $(data).find("link[rel='self']").attr("href");
	if (state == "done") {
		displayResult("Processus terminé", messages);
		changeButtonState(button, "success");
	} else if (state == "aborted") {
		$('form#send_file').find("input").removeClass("ui-state-disabled");
		displayResult("Abandon", messages);
		changeButtonState(button, "error");
	} else if (state == "recovery_running") {
		displayResult("En cours", messages);
		changeButtonState(button, "pending");
	}

	if (state == "done" || state == "aborted") {
		awaitedMetadataRecoveryReports.shift();

	}
	setTimeout(scanForMaintenanceProcessReports, 500);
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
function displayResult(title, message) {
	$("div.console-area").empty();
	$("div.console-area").html(message).scrollTop(1E10);
	;
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