var activeLabel;
var putBlocked;
var buttonOriginalText;
var saveApiscolLinkHtml;
var submitButton;
var displayConsole;
var scanIsRunning = false;
var awaitedRefreshProcessReports = new Array();
function secundaryInit() {
	displayConsole = $("div.pane div.console-area", "div#refresh.detail-pane");
	var headerHeight = $("html>body>header.ui-helper-clearfix").height();
	var navHeight = $("div#tabs").height();
	var availableHeight = $(window).height() - headerHeight - navHeight - 20;

	$("#structure").height(availableHeight).layout({
		defaults : {
			applyDemoStyles : true
		},
		west : {
			resizable : true,
			size : "20%"
		}
	});
	$(
	"div.pane.ui-layout-west.ui-layout-pane.ui-layout-pane-west ul li.resource-selected a.resource-detail-link", "div#structure")
	.button({
		icons : {
			primary : "ui-icon-extlink"
		},
		text : false
	});
	

}
function getDisplayParameters() {
	return "";
}