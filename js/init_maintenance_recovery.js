var activeLabel;
var submitButton;
var displayConsole;
var scanIsRunning = false;
var $titleContainer, $consoleArea, $progressBar;
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
	$progressBar = $("#progressbar");
	$progressBar.progressbar({
		disabled : true,
		max : 100
	});
	$('div.console-header').width(
			$('.console-header').parent().innerWidth() - 22);
	$('div.console-area-container').css('margin-top',
			$('div.console-header').outerHeight())
	initMetadataRecoveryControls();
	scanForMaintenanceProcessReports();

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
					setRunningMaintenanceProcess($metadataRecoveryForm
							.attr("id"));
					setRunningMaintenanceProcessState("running");
					updateButtonState();
					displayResult("Veuillez patienter",
							"La requête de maintenance a été envoyée.");

				},
				complete : function(xhr) {
					handleMetadataRecoveryFirstResponse(xhr.responseXML);
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
function maintenanceReportUrl() {
	return $.cookie('maintenance-report-url');
}
function setMaintenanceReportUrl(url) {
	$.cookie('maintenance-report-url', url);
}
function runningMaintenanceProcess() {
	return $.cookie('running-maintenance-process');
}
function setRunningMaintenanceProcess(identifier) {
	$.cookie('running-maintenance-process', identifier);
}
function runningMaintenanceProcessState() {
	return $.cookie('running-maintenance-process-state');
}
function setRunningMaintenanceProcessState(identifier) {
	$.cookie('running-maintenance-process-state', identifier);
}
function handleMetadataRecoveryFirstResponse(data) {
	if (!data)
		displayResult("Problème inconnu", "Pas de réponse du serveur");
	else if (data.firstChild.tagName == "error") {

		displayResult($(data).find("intro").text(), $(data).find("message")
				.text());
		setRunningMaintenanceProcessState("error");
		updateButtonState();

	} else {
		var messages = getMessagesAsText(data);
		var state = $(data).find("apiscol\\:state, state").text();
		displayResult(state, messages);
		var linkElement = $(data).find("link[rel='self']");
		setMaintenanceReportUrl(linkElement.attr("href"));
		if (!scanIsRunning)
			scanForMaintenanceProcessReports();
		if (state == "done") {
			{
				setRunningMaintenanceProcessState("success");
				updateButtonState();
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
	if (runningMaintenanceProcessState() != "running") {
		scanIsRunning = false;
		return;
	}
	var fullReport = $("#full-report-checkbox").is(":checked");
	$.ajax({
		dataType : 'xml',
		type : "GET",
		url : window.location + "/maintenance-process-report/"
				+ maintenanceReportUrl() + "/async"
				+ (fullReport ? "" : "?nb-lines=25"),
		error : function(msg) {
			console.log(msg);
		},
		success : function(result) {
			handleMaintenanceProcessReport(result);
		}
	});
}
function handleMaintenanceProcessReport(data) {
	if (data.firstChild.tagName == "error") {
		{
			displayResult($(data).find("intro").text(), $(data).find("message")
					.text());
			setRunningMaintenanceProcessState("error");
			updateButtonState();
			return;
		}
	}
	var state = $(data).find("apiscol\\:state,state").text();
	var progress = parseInt($(data).find("apiscol\\:processed,processed")
			.text() * 100);
	$progressBar.progressbar("option", "value", progress);
	var messages = getMessagesAsText(data);
	var link = $(data).find("link[rel='self']").attr("href");
	if (state == "done") {
		displayResult("Processus terminé", messages);
		setRunningMaintenanceProcessState("success");
		updateButtonState();
	} else if (state == "aborted") {
		$('form#send_file').find("input").removeClass("ui-state-disabled");
		displayResult("Abandon", messages);
		setRunningMaintenanceProcessState("error");
		updateButtonState();
	} else if (state == "recovery_running") {
		displayResult("En cours", messages);
		setRunningMaintenanceProcessState("running");
		updateButtonState();
	}

	if (state == "done" || state == "aborted") {

	}
	setTimeout(scanForMaintenanceProcessReports, 500);
}
function updateButtonState() {
	$(".recovery-control>form").addClass("ui-icon-refresh-state");
	if (!runningMaintenanceProcess())
		return;
	$(".recovery-control>form").addClass("ui-state-disabled");
	button = $("form#" + runningMaintenanceProcess());
	state = runningMaintenanceProcessState();
	var iconClass = "ui-icon-refresh-state";
	switch (state) {
	case "default":
		iconClass = "ui-icon-refresh-state";
		break;
	case "running":
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
	if (state == "running")
		button.addClass("ui-state-disabled");
	else
		button.removeClass("ui-state-disabled")
}
function displayResult(title, message) {
	if (!$consoleArea)
		$consoleArea = $("div.console-area");
	$consoleArea.empty();
	if (!$titleContainer)
		$titleContainer = $("#console-title-container");
	$("div.console-area").html(message);
	$('div.console-area-container').scrollTop(1E10);
	$titleContainer.text(title);
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