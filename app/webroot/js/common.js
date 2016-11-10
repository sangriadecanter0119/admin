 
function Common() {}

Common.message = function () {
		alert("HELLO");
}

//数値を小数点第3位で四捨五入してドル表示のカンマ区切りに変換
Common.addDollarComma = function (num)
{
	return String((Math.round(num * 100) / 100).toFixed(2)).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
}

//カンマ区切りの数値からカンマを取り除く
Common.removeDollarComma = function (str)
{  
   return parseFloat(String(str).replace(/^\s+|\s+$|,/g, ''));
}

//数値を四捨五入して円表示のカンマ区切りに変換
Common.addYenComma = function (num)
{
  return String(Math.round(num)).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
}

//カンマ区切りの数値からカンマを取り除く
Common.removeComma = function (str)
{
   return parseInt(String(str).replace(/^\s+|\s+$|,/g, ''),10);   
}
