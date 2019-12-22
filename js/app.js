//https://canvasjs.com/html5-javascript-line-chart/
//https://canvasjs.com/docs/charts/basics-of-creating-html5-chart/
//https://canvasjs.com/docs/charts/basics-of-creating-html5-chart/date-time-axis/
//https://canvasjs.com/javascript-charts/dynamic-live-column-chart/

(function (d) {
	var
		qS = d["querySelector"],
		q = d["getElementById"],
		__HTMLElement = 'HTMLElement',
		top_tble_row = getUI('#top-tble-info'),
		templates = {
			'info_table': getUIval('#info_table').replace(/[\r\n\t]/g, "").trim(),
			'temp_table': ''
		},
		temp_table_data = {},
		chart = new CanvasJS.Chart("chartContainer", {
			zoomEnabled: true,
			animationEnabled: true,
			animationDuration: 50,
			theme: "light2",
			//backgroundColor: "#F5DEB3",
			markerType: "circle",  //"circle", "square", "cross", "triangle", "none"
			title: {
				text: "Core Temperature per minute"
			},
			toolTip: {
				content: //"{x}: {y}°C"
					"{x}<br/>index: {index}<br/>{name} <span style='\"'color: red;'\"'><strong>{y} °C</strong></span>"
			},
			axisX: {
				title: 'timeline'
			},
			axisY: {
				includeZero: false,
				title: 'temperature'
			},
			data: [{
				type: "line", // "area", "line", "splineArea"
				xValueType: "dateTime",
				name: "temp",
				dataPoints: []
			}]
		});

	//generate DOM templates  templates.temp_table temp_table_data
	let tr = false,
		totalEntries = 60,
		cols = 6; // 10
	for (let i = 0; i < totalEntries; i++) {
		if (!(i % cols)) {
			templates.temp_table += !tr ? '<tr>' : '</tr><tr>';
			tr = !tr;
		}
		//value holder
		temp_table_data['temp_' + i] = '?';
		//HTML template
		let style = i == 59 ? '  class="table-dark"' : '';
		templates.temp_table += '<td' + style + '>{temp_' + i + '}</td>';
	}
	!tr && (templates.temp_table += '</tr>');

	ttime = setInterval(function () {
		render();
	}, 60 * 1000);
	render();

	function isDOM(obj) {
		// DOM, Level2
		if (__HTMLElement in window) {
			return (obj && obj instanceof HTMLElement);
		}
		// Older browsers
		return !!(obj && typeof obj === dab.__object && obj.nodeType === 1 && obj.nodeName);
	}

	function getUI(id) {
		try {
			return ui = qS.call(d, id);
		} catch (error) {
			console.log(err);
		}
	}
	function getUIval(id, html) {
		let ui = isDOM(id) ? id : getUI(id);
		return ui && ui[html == true ? 'innerHTML' : 'innerText'];
	}
	function setUIval(id, val, html) {
		let ui = isDOM(id) ? id : getUI(id);
		ui && (ui[html == true ? 'innerHTML' : 'innerText'] = val);
	}

	function htmlToElement(html) {
		var template = document.createElement("template");
		template.innerHTML = html;
		return template.content.firstChild;
	}

	function loadDomTemplate(id) {
		document.querySelector('#info_table').innerText.replace(/[\r\n\t]/g, "").trim()
	}

	function nano(n, e) {
		return n.replace(/\{([\w\.]*)\}/g, function (n, t) {
			for (var r = t.split("."), f = e[r.shift()], u = 0, i = r.length; i > u; u++) f = f[r[u]];
			return "undefined" != typeof f && null !== f ? f : "";
		});
	}

	function render() {
		$.getJSON("/temp/get_temp.php", function (data) {
			/*var items = [];
	  $.each( data, function( key, val ) {
		items.push( "<li id='" + key + "'>" + val + "</li>" );
	  });
	 
	  $( "<ul/>", {
		"class": "my-new-list",
		html: items.join( "" )
	  }).appendTo( "body" );
	  */
			d0 = data.date.split(' ');
			date0 = d0[0].split('-');
			time0 = d0[1].split(':');

			dt = new Date(date0[0], date0[1] - 1, date0[2], time0[0], time0[1], 0);

			data.date = dt.toLocaleString();
			let html = nano(templates.info_table, data);
			setUIval(top_tble_row, html, true);

			//console.log(dt);
			dt.setHours(dt.getHours() - 1);
			//console.log(dt);

			dps = chart.options.data[0].dataPoints;
			for (let i = 0; i < data.list.length; i++) {
				dt.setMinutes(dt.getMinutes() + 1);

				dps[i] = {
					x: new Date(dt.getTime()),
					y: data.list[i],
					index: i
				};
				//update new value
				temp_table_data['temp_' + i] = data.list[i] + ' °C';
			}
			dps[dps.length - 1].markerColor = "red";

			chart.options.data[0].dataPoints = dps;
			chart.render();

			let tb = getUI('#tble-data>tbody');
			html = nano(templates.temp_table, temp_table_data);
			setUIval(tb, html, true);
		});
	}
})(document);