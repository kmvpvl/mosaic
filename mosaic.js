var palettes = null;
function sendDataToServer(api, data, callback) {
	showLoading();
	var p = $.post(api + ".php",
	{
		//username: $("#username").val(),
		//password: $("#password").val(),
		data: data
	},
	callback);
	p.fail(function(data, status) {
		hideLoading();
		switch (data.status) {
			case 401:
				clearInstance();
				showLoginForm();
			default:				
				showError(api + "<br>Description: " + data.status + ": " + data.statusText + ". " + data.responseText);
		}
	});
}

function recieveDataFromServer(data, status) {
	hideLoading();
	var ls = null;
	switch (status) {
		case "success":
			try {
				ls = data;
				if (ls.result == 'FAIL') {
					showError("Application says: " + ls.description);
				} 
			} catch(e) {
				showError("Wrong data from server: " + e + " - " + data);
			}
			break;
		default:
			showError("Unsuccessful request: " + " - " + data);
	}
	return ls;
}

function receiveHtmlFromServer(data, status) {
	hideLoading();
	switch (status) {
		case "success":
			return data;
			break;
		default:
			clearInstance();
	}
	return null;
}

class Palette extends EventHandlerPrototype {
	element = null;
	id = null;
	constructor(jsonPalette, element = null){
		super();
		this.data = jsonPalette;
		if (!element) this.element = $('<palette/>');
		else this.element = element;
		this.element[0].Palette = this;
		this.drawElement();
	}
	drawElement (){
		$(this.element).html(this.data.name);
		for (let [k, v] of Object.entries(this.data.colormap)) {
			$(this.element).append('<span style="background-color:'+v+'">'+k+'</span>');
		}
	}
}