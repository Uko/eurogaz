// cross browser safe JSON parsing
// illegal JSON stops here ...
(function (window, undefined)
{
	// taken from http://json.org/json2.js
	var ok_json = function ( data )
	{
		return /^[\],:{}\s]*$/.test(
			data.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@")
			.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]")
			.replace(/(?:^|:|,)(?:\s*\[)+/g, "")
			) ;
	}
	// true if native JSON exists and supports non-standard JSON
	var ok_wrong_json = function ()
	{
		try {
			JSON.parse("{ a : 1 }");
			return true ;
		} catch(x) {
			return false ;
		}
	}();

	window.json_parse =
	( window.JSON && ("function" === typeof window.JSON.parse) ) ?

	( ok_wrong_json ) ?
	function json_parse ( data )
	{
		// Case 1 : native JSON is here but supports illegal strings
		if ( ! ok_json( data ) )
			throw new Error(0xFFFF,"Bad JSON string.") ;
		return window.JSON.parse( data ) ;
	}
	: // else
	function json_parse ( data )
	{
		// Case 2: native JSON is here , and does not support illegal strings
		// this will throw on illegal strings
		return window.JSON.parse( data ) ;
	}
	: // else
	function json_parse ( data )
	{
		// Case 3: there is no native JSON present
		if ( ! ok_json( data ) ) throw new Error(0xFFFF,"Bad JSON string.") ;
		return (new Function("return " + data))();
	}
;
})(window) ;