var saveApiscolLinkHtml;
var putBlocked;
var thumbSubmitButton;
var previewSubmitButton;
var activeLabel;
function secundaryInit() {
	activeLabel = "second_menu_item_display";
	displayActiveLabel();
	buildInterface();
	refreshTabsState();

}
function refreshTabsState() {

	var displayModeSelected = function() {
		$("#display-mode").popup("close");
	}
	var displayDeviceSelected = function() {
		$("#display-device").popup("close");
	}
	refreshPortlet();
	if (!$("#change-display-mode").hasClass("ui-button")) {
		$("#change-display-mode").button({
			icons : {
				primary : "ui-icon-eye",
				secondary : "ui-icon-triangle-1-s"
			}
		}).popup({
			popup : $('#display-mode'),
			position : {
				my : "right top",
				at : "right bottom"
			}
		}).next().menu({
			select : displayModeSelected,
			trigger : $("#change-display-mode")
		});
		$(
				"div.display-options ul#display-mode.ui-popup li.ui-widget a.ui-corner-all")
				.click(function() {
					refreshPortlet({
						mode : $(this).attr("data-mode")
					});
					return false;
				})
		$("#change-display-device").button({
			icons : {
				primary : "ui-icon-device",
				secondary : "ui-icon-triangle-1-s"
			}
		}).popup({
			popup : $('#display-device'),
			position : {
				my : "right top",
				at : "right bottom"
			}
		}).next().menu({
			select : displayDeviceSelected,
			trigger : $("#change-display-device")
		});
		$(
				"div.display-options ul#display-device.ui-popup li.ui-widget a.ui-corner-all")
				.click(function() {
					refreshPortlet({
						device : $(this).attr("data-device")
					});
					return false;
				})
	}

}
function refreshPortlet(options) {
	var reset = true;
	var $wrapper = $("div#display.detail-pane div.display-wrapper");
	if (!saveApiscolLinkHtml) {
		reset = false;
		saveApiscolLinkHtml = $wrapper.html();
	}
	if (reset)
		$wrapper.empty().html(saveApiscolLinkHtml);
	if (options && options.mode) {
		$wrapper.children("a").attr("data-mode", options.mode);
		saveApiscolLinkHtml = $wrapper.html();
	}
	$wrapper.children("a").apiscol();
}

function buildInterface() {
	$("a.choose-thumb").click(function(e) {
		if (putBlocked)
			return false;
		$target = $(e.target).closest("a");
		var urlend = "";
		urlend += getDisplayParameters();
		$target.attr("href", $target.attr("href") + urlend);
	});

	var headerHeight = $("html>body>header.ui-helper-clearfix").height();
	var navHeight = $("div#tabs").height();
	var availableHeight = $(window).height() - headerHeight - navHeight - 20;

	$("#display").height(availableHeight).layout({
		defaults : {
			applyDemoStyles : true
		},
		south : {
			resizable : true,
		},
		west : {
			resizable : true,
			size : 390
		}
	});

	$
			.ajax({
				dataType : 'html',
				type : "GET",
				url : $('form#set_custom_thumb').attr("action") + "/async",
				error : function(msg) {

					console.log(msg);
				},
				success : function(result) {

					$(
							"div#display.detail-pane div.pane div.refresh-area div.thumbs-container")
							.removeClass("void-thumbs-container").empty().html(
									result);
					createCarousel();
				}
			});

	var bar = $('.bar');
	var status = $('#status');
	$('form#set_custom_preview').attr("action",
			$('form#set_custom_preview').attr("action") + "/async").ajaxForm({
		beforeSend : function() {
			if (putBlocked)
				return false;
			showProgressBar(true);
			$(this).find("input").attr("disabled", true);

			freeze(true);

			putBlocked = true;
			status.empty();
			var percentVal = '0%';
			bar.width(percentVal)

		},
		uploadProgress : function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			bar.width(percentVal)
		},
		success : function(result) {
			var percentVal = '100%';
			bar.width(percentVal)

		},
		complete : function(xhr) {
			freeze(false);
			putBlocked = false;
			showProgressBar(false);
			refreshPortlet();
		}
	});
	$('form#set_custom_thumb')
			.attr("action",
					$('form#set_custom_thumb').attr("action") + "/async")
			.ajaxForm(
					{
						beforeSend : function() {
							if (putBlocked)
								return false;
							showProgressBar(true);
							$(this).find("input").attr("disabled", true);

							freeze(true);
							$(
									"div#display.detail-pane div.pane div.refresh-area div.thumbs-container div.present-thumb img")
									.attr("src", $("input#waiter-url").val())
							putBlocked = true;
							status.empty();
							var percentVal = '0%';
							bar.width(percentVal)

						},
						uploadProgress : function(event, position, total,
								percentComplete) {
							var percentVal = percentComplete + '%';
							bar.width(percentVal)
						},
						success : function(result) {
							var percentVal = '100%';
							bar.width(percentVal)

						},
						complete : function(xhr) {
							$(
									"div#display.detail-pane div.pane div.refresh-area div.thumbs-container")
									.empty().html(xhr.responseText);
							createCarousel();
							freeze(false);
							putBlocked = false;
							showProgressBar(false);
							reloadPortletThumb();
						}
					});
	$('input#thumb_submit').button();
	$('input#preview_submit').button();
	thumbSubmitButton = $("input#thumb_submit.ui-button",
			"form#set_custom_thumb");
	thumbSubmitButton.click(function() {
		$('form#set_custom_thumb #thumb_upload').trigger("click");
		return false;
	});
	previewSubmitButton = $("input#preview_submit.ui-button",
			"form#set_custom_preview");
	previewSubmitButton.click(function() {
		$('form#set_custom_preview #preview_upload').trigger("click");
		return false;
	});
	$('form#set_custom_thumb #thumb_upload').change(function() {
		if (!putBlocked)
			$('form#set_custom_thumb').submit();
	}).click(function() {
		return !putBlocked;
	});
	$('form#set_custom_preview #preview_upload').change(function() {
		if (!putBlocked)
			$('form#set_custom_preview').submit();
	}).click(function() {
		return !putBlocked;
	});
	showProgressBar(false);

}
function showProgressBar(bool) {
	$(
			"div#display.detail-pane div.pane div.custom-image-input-container div.progress")
			.toggle(bool);
}
function createCarousel() {
	$("div#carousel form.choose-thumb input[type=submit]").hide();
	$("div#carousel form.choose-thumb img").click(function() {
		$(this).closest("form").submit();
	});
	var children = 0;
	$("div#carousel form.choose-thumb")
			.each(
					function(index, elem) {
						children++;
						$(elem)
								.attr("action",
										$(elem).attr("action") + "/async")
								.ajaxForm(
										{
											beforeSend : function() {
												if (putBlocked)
													return false;
												freeze(true);
												$(
														"div#display.detail-pane div.pane div.refresh-area div.thumbs-container div.present-thumb img")
														.attr(
																"src",
																$(
																		"input#waiter-url")
																		.val())
												putBlocked = true;

											},
											complete : function(xhr) {
												$(
														"div#display.detail-pane div.pane div.refresh-area div.thumbs-container")
														.empty()
														.html(xhr.responseText);
												createCarousel();
												putBlocked = false;
												freeze(false);
												reloadPortletThumb();
											}
										});

					});
	if (children > 2)
		$("#carousel").rcarousel({
			visible : 2,
			auto : {
				enabled : false
			},
			orientation : "horizontal",
			step : 2,
			width : 96,
			height : 96,
			margin : 20,
			speed : 1000
		});
	else {
		$("a#ui-carousel-prev, a#ui-carousel-next").remove();
		$("#carousel").addClass("disabled-carousel")
	}

}
function freeze(bool) {
	var $target;
	if (bool) {
		$target = thumbSubmitButton
		if (!previewSubmitButton.attr("data-no-content")) {
			$target = $target.add(previewSubmitButton)
		}
		$target.addClass("ui-state-disabled").attr("disabled", "disabled");
		$("div#carousel.ui-carousel div.wrapper form.choose-thumb").fadeTo(400,
				0.5);
	} else {
		$target = thumbSubmitButton
		if (!previewSubmitButton.attr("data-no-content")) {
			$target = $target.add(previewSubmitButton)
		}
		$target.removeClass("ui-state-disabled").removeAttr("disabled");

	}
}
function reloadPortletThumb() {
	var thumb = $("div.apiscol-notice div.thumb img", "div#display.detail-pane");
	if (thumb.length == 0)
		return;
	var src = thumb.attr("src");
	src = src.replace(/\?timestamp=\d+$/, "");
	thumb.attr("src", src + "?timestamp=" + new Date().getTime());
}
function getDisplayParameters() {
	return "";
}