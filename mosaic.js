var mosaic = null;

function sendDataToServer(api, data, callback) {
	showLoading();
	let p = $.ajax(api + ".php", {
		method: 'POST',
		data: {userid: localStorage.getItem('userid'), data: data},
		processData: true,
		success: callback,
		error: function(xhr, textStatus, error) {
			hideLoading();
			switch (xhr.status) {
				case 401:
					clearInstance();
					showLoginForm();
				default:				
					showError(api + "<br>Description: " + xhr.status + ": " + xhr.statusText + ". " + xhr.responseText);
			}
		}
	});
}

function sendFileToServer(api, data, callback) {
	showLoading();
	let fd = new FormData();
	fd.append('userid', localStorage.getItem('userid'));
	for (let [i, v] of Object.entries(data)) {
//		fd.append(i, v);
		fd.append('data['+i+']', v);
	};
	let p = $.ajax(api + ".php", {
		method: 'POST',
		data: fd,
		processData: false,
		contentType: false,
		dataType    : 'json',
		success: callback,
		error: function(xhr, textStatus, error) {
			hideLoading();
			switch (xhr.status) {
				case 401:
					clearInstance();
					showLoginForm();
				default:				
					showError(api + "<br>Description: " + xhr.status + ": " + xhr.statusText + ". " + xhr.responseText);
			}
		}
	});
}

function recieveDataFromServer(data, status, xhr) {
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
		$(this.element).html('<input type="radio" name="radioPalette" palette="'+this.data.name+'">'+this.data.name);
		for (let [k, v] of Object.entries(this.data.colormap)) {
			$(this.element).append('<span style="background-color:'+v+'">'+k+'</span>');
		}
	}
}

class Navigation extends EventHandlerPrototype {
	element = null;
	constructor(element) {
		super();
		if (!element) throw new Error('Element expected');
		this.element = element;
		this.drawElement();
	}
	drawElement() {
	}
}