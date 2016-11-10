/*
 * 文字コード UTF-8
 */

function Customer() {}

Customer.input = function (object, key) {
	
	try {
		if (object) {
			var element = $('span', object.parentNode);
			var value = object.options[object.selectedIndex].value;
			if (value == 'date') {
				element.html('フォーマット: <input type="text" name="item[' + key + '][item_property]" class="inputalpha" value="Y/m/d H:i" />');
			} else if (value == 'textarea') {
				element.html('行数: <input type="text" name="item[' + key + '][item_property]" class="inputnumeric" value="5" />');
			} else if (value == 'select' || value == 'checkbox' || value == 'radio') {
				var property = element.html();
				if (!property.match('値')) {
					element.html('値: <input type="text" name="item[' + key + '][item_property]" class="inputalpha" value="" />');
				}
			} else {
				element.html('');
			}
		}
	} catch(e) {
		alert(e.message);
	}
	
}

Customer.increment = function (type) {
	
	try {
		var element = document.getElementById('configitem');
		if (element) {
			var count = element.rows.length - 1;
			if (type == 'remove') {
				if (count > 1) {
					$(element.rows[count]).remove();
				}
			} else if (count < type) {
				option = {'text': '標準', 'numeric': '数字', 'alpha': '英字', 'alphanumeric': '英数字', 'date': '日時', 'textarea': 'テキストエリア', 'select': 'セレクトボックス', 'checkbox': 'チェックボックス', 'radio': 'ラジオボタン'};
				var string = '<tr><td><input type="text" name="item[' + count + '][item_caption]" class="inputalpha" value="" /></td>';
				string += '<td class="customeriteminput"><select name="item[' + count + '][item_input]" onchange="Customer.input(this, ' + count + ')">';
				for (var key in option) {
					string += '<option value="' + key + '">' + option[key] + '</option>';
				}
				string += '</select><span></span></td>';
				string += '<td class="customeritemcheck"><input type="checkbox" name="item[' + count + '][item_null]" value="notnull" /></td>';
				string += '<td class="customeritemcheck"><input type="checkbox" name="item[' + count + '][item_display]" value="1" /></td>';
				string += '<td><input type="text" name="item[' + count + '][item_order]" class="inputnumeric" value="" /></td></tr>';
				$(element).append(string);
			} else if (count >= type) {
				alert('これ以上追加できません。');
			}
		}
	} catch(e) {
		alert(e.message);
	}
	
}

Customer.configdefault = function (object) {
	
	try {
		var element = document.forms['configitem'];
		var elementdefault = document.forms['configitemdefault'];
		if (element.style.display == 'none') {
			element.style.display = 'block';
			elementdefault.style.display = 'none';
			object.innerHTML = '標準設定';
		} else {
			element.style.display = 'none';
			elementdefault.style.display = 'block';
			object.innerHTML = '項目の変更';
		}
	} catch(e) {
		alert(e.message);
	}
	
}

Customer.companylist = function (object) {
	
	try {
		var string = document.forms['customer'].elements['customer_company'].value;
		if (string && string.length > 0) {
			App.loader('companylist.php', {search: string}, 'companylist');
		} else {
			alert('検索する会社名を入力してください。');
		}
	} catch(e) {
		alert(e.message);
	}
	
}

Customer.set = function (id, company, companyruby, department, url) {
	
	try {
		var element = document.forms['customer'].elements;
		element['customer_company'].value = company;
		element['customer_companyruby'].value = companyruby;
		element['customer_department'].value = department;
		element['customer_url'].value = url;
		document.getElementById('belong').innerHTML = '<input type="checkbox" name="customer_parent" id="customer_parent" value="' + id + '" checked="checked" /><label for="customer_parent">リンク</label>';
		$('#companylist').remove();
	} catch(e) {
		alert(e.message);
	}
	
}