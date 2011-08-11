$(document).ready(function () {

    $('input:checkbox:not([safari]).fancy').checkbox();
    $('input[safari]:checkbox.fancy').checkbox({ cls: 'jquery-safari-checkbox' });
    $('input:radio.fancy').checkbox();

    $('#topbarSitelist').bind('change', function () {
        $.fancybox('<img src="/static/images/loading.gif" alt="Loading.." />',
        {
            'showCloseButton'	: false,
        });
        doRequest('sitemanagement/getsitedetails', function (site) {
            if (isset(site.success)) {
                location.reload(true);
            } else {
                getMessage(site);
            }
        }, { 'id': $('#topbarSitelist').attr('value') }, 'json', true);
    });
});

// code for generating classes easy with jquery. 
// Inspired by base2 and Prototype
(function() {
    var initializing = false, fnTest = /xyz/.test(function() { xyz; }) ? /\b_super\b/ : /.*/;

    // The base Class implementation (does nothing)
    this.Class = function() { };

    // Create a new Class that inherits from this class
    Class.extend = function(prop) {
        var _super = this.prototype;

        // Instantiate a base class (but only create the instance,
        // don't run the init constructor)
        initializing = true;
        var prototype = new this();
        initializing = false;

        // Copy the properties over onto the new prototype
        for (var name in prop) {
            // Check if we're overwriting an existing function
            prototype[name] = typeof prop[name] == "function" &&
        typeof _super[name] == "function" && fnTest.test(prop[name]) ?
        (function(name, fn) {
            return function() {
                var tmp = this._super;

                // Add a new ._super() method that is the same method
                // but on the super-class
                this._super = _super[name];

                // The method only need to be bound temporarily, so we
                // remove it when we're done executing
                var ret = fn.apply(this, arguments);
                this._super = tmp;

                return ret;
            };
        })(name, prop[name]) :
        prop[name];
        }

        // The dummy class constructor
        function Class() {
            // All construction is actually done in the init method
            if (!initializing && this.init)
                this.init.apply(this, arguments);
        }

        // Populate our constructed prototype object
        Class.prototype = prototype;

        // Enforce the constructor to be what we expect
        Class.constructor = Class;

        // And make this class extendable
        Class.extend = arguments.callee;

        return Class;
    };
})();
function ValidateEmail(email) {
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    return (reg.test(email));
}
isset = function (variable) {
    if (variable == null || variable == undefined) {
        return false;
    }
    return true;
}
String.prototype.trim = function () {
    return this.replace(/^\s*/, "").replace(/\s*$/, "");
}
loadinggif = function (element) {
    $(element).html('<img src="/static/images/loading.gif" alt="Loading.." />');
}

loadinggifReplaceSmall = function (element) {
    var oldelement = $(element);
    $(element).replaceWith('<img src="/static/images/ajax-loader-small-white.gif" alt="Loading" id="' + $(element).attr('id') + '"/>');
    return oldelement;
}
addLoadingImageToElement = function (element) {
    $(element).parent('div').append('<img src="/static/images/ajax-loader-small-white-button.gif" alt="" id="loadinggif" />');
}
removeLoadingImageInElement = function () {
    $('#loadinggif').remove();
}
replaceButtonwithloading = function (element) {
    var oldElement = $(element).html();
    $(element).html('<img src="/static/images/ajax-loader-small-white-button.gif" alt="" />Loading...');
    return oldElement;
}
replaceButtonWithCancel = function (element) {
    var oldElement = $(element).html();
    $(element).html('<img src="/static/images/icons/16/remove.png" alt="" />Cancel');
    return oldElement;
    
}
//code that runs an jquery ajax request. Access method for saving lines and caching. 
doRequest = function(path, readyFunc, values, type, asynchronous) {
    if (type === null || type === undefined) {
        var type = 'json';
    }
    if(asynchronous === null || asynchronous === undefined){
    	asynchronous = true;
    }
    if (values === null || values === undefined) {
        values = {};
    }
    path = path.replace('/index.php?rt=', '');
    path = path.replace('index.php?rt=', '');
    path = path.replace('?rt=','');
    path = path.replace('/?rt=','');
    
    var request = $.ajax({
        type: "POST",
        url: '/index.php?rt=' + path,
        data: (values),
        async : asynchronous,
        dataType: type,
        success: readyFunc,
        error : function(XMLHttpRequest, textStatus, errorThrown){
        	alert('Error: ' + textStatus);
        }
    });
    return request;
}
var sleep = false;
getMessage = function(result, func){
	if($.fancybox.isBusy()){
		setTimeout(function(){getMessage(result, func);}, 600);
		return false;
	}
	var type = 'notification';
	var key = 'error';
	if(typeof result != 'string'){
		if(isset(result.success)){
			key = result.success;
		}else if(isset(result.error)){
			key = result.error;
		}
	}else if(typeof result == 'string'){
		key = result;
	}
	doRequest('message/getMessage', function(data){
		$.fancybox(
			data,
			{
				onComplete : function(){
					$('#acceptmessage').bind('click', function(){
						if(typeof func == 'function'){
							func();
						}
						$.fancybox.close();
						return false;
					});
					if($('#cancelmessage').length > 0){
						$('#cancelmessage').bind('click', function(){
							$.fancybox.close();
							return false;
						});
					}
				}
			}
		);
			
	},
	{
		'type' : type,
		'key' : key
	},
	'html'
	);
}
//extending jquery sort with custom function
jQuery.fn.sort = function() {
    return this.pushStack([].sort.apply(this, arguments), []);
};
//custom sort function to sort list
function sortId(a, b) {
    return a.id > b.id ? 1 : -1;
};
//function to include a script given a path 
includescript = function (scriptPath) {
    var include = true;
    $('script').each(function(index, element){
        if($(element).attr('src') == '/static/js/' + scriptPath){
            include = false;
        }
    });
    if(include){
        var script = $('<script type="text/javascript" src="/static/js/' + scriptPath + '"></script>');
        $('head').append($(script));
    }
}
validate = function (elements) {
    if (!isArray(elements)) {
        elements = elements.split(';');
    }
    for (var element in elements) {
        $('#' + elements[element]).addClass('inputerror');
    }
}
function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
/*
 * This document is licensed as free software under the terms of the
 * MIT License: <a href="http://www.opensource.org/licenses/mit-license.php" title="http://www.opensource.org/licenses/mit-license.php">http://www.opensource.org/licenses/mit-license.php</a>
 *
 * Adapted by Rahul Singla.
 *
 * Brantley Harris wrote this plugin. It is based somewhat on the JSON.org
 * website's <a href="http://www.json.org/json2.js" title="http://www.json.org/json2.js">http://www.json.org/json2.js</a>, which proclaims:
 * "NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.", a sentiment that
 * I uphold.
 *
 * It is also influenced heavily by MochiKit's serializeJSON, which is
 * copyrighted 2005 by Bob Ippolito.
 */
 
/**
 * jQuery.JSON.encode( json-serializble ) Converts the given argument into a
 * JSON respresentation.
 *
 * If an object has a "toJSON" function, that will be used to get the
 * representation. Non-integer/string keys are skipped in the object, as are
 * keys that point to a function.
 *
 * json-serializble: The *thing* to be converted.
 */
jQuery.JSON = {
    encode: function(o) {
        if (typeof (JSON) == 'object' && JSON.stringify)
            return JSON.stringify(o);
 
        var type = typeof (o);
 
        if (o === null)
            return "null";
 
        if (type == "undefined")
            return undefined;
 
        if (type == "number" || type == "boolean")
            return o + "";
 
        if (type == "string")
            return this.quoteString(o);
 
        if (type == 'object') {
            if (typeof o.toJSON == "function")
                return this.encode(o.toJSON());
 
            if (o.constructor === Date) {
                var month = o.getUTCMonth() + 1;
                if (month < 10)
                    month = '0' + month;
 
                var day = o.getUTCDate();
                if (day < 10)
                    day = '0' + day;
 
                var year = o.getUTCFullYear();
 
                var hours = o.getUTCHours();
                if (hours < 10)
                    hours = '0' + hours;
 
                var minutes = o.getUTCMinutes();
                if (minutes < 10)
                    minutes = '0' + minutes;
 
                var seconds = o.getUTCSeconds();
                if (seconds < 10)
                    seconds = '0' + seconds;
 
                var milli = o.getUTCMilliseconds();
                if (milli < 100)
                    milli = '0' + milli;
                if (milli < 10)
                    milli = '0' + milli;
 
                return '"' + year + '-' + month + '-' + day + 'T' + hours + ':'
                        + minutes + ':' + seconds + '.' + milli + 'Z"';
            }
	 
            if (o.constructor === Array) {
                var ret = [];
                for ( var i = 0; i < o.length; i++)
                    ret.push(this.encode(o[i]) || "null");
	 
                return "[" + ret.join(",") + "]";
            }
 
            var pairs = [];
            for ( var k in o) {
                var name;
                var type = typeof k;
 
                if (type == "number")
                    name = '"' + k + '"';
                else if (type == "string")
                    name = this.quoteString(k);
                else
                    continue; // skip non-string or number keys
 
                if (typeof o[k] == "function")
                    continue; // skip pairs where the value is a function.
 
                var val = this.encode(o[k]);
 
                pairs.push(name + ":" + val);
            }
 
            return "{" + pairs.join(", ") + "}";
        }
    },

    /**
     * jQuery.JSON.decode(src) Evaluates a given piece of json source.
     */
    decode: function(src) {
        if (typeof (JSON) == 'object' && JSON.parse)
		    return JSON.parse(src);
        return eval("(" + src + ")");
    },
 
    /**
     * jQuery.JSON.decodeSecure(src) Evals JSON in a way that is *more* secure.
     */
    decodeSecure: function(src) {
        if (typeof (JSON) == 'object' && JSON.parse)
            return JSON.parse(src);
 
        var filtered = src;
        filtered = filtered.replace(/\\["\\\/bfnrtu]/g, '@');
        filtered = filtered
                .replace(
                        /"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,
                        ']');
        filtered = filtered.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
 
        if (/^[\],:{}\s]*$/.test(filtered))
            return eval("(" + src + ")");
        else
            throw new SyntaxError("Error parsing JSON, source is not valid.");
    },
 
    /**
     * jQuery.JSON.quoteString(string) Returns a string-repr of a string, escaping
     * quotes intelligently. Mostly a support function for JSON.encode.
     *
     * Examples: >>> jQuery.JSON.quoteString("apple") "apple"
     *
     * >>> jQuery.JSON.quoteString('"Where are we going?", she asked.') "\"Where
     * are we going?\", she asked."
     */
    quoteString: function(string) {
        if (string.match(this._escapeable)) {
            return '"' + string.replace(this._escapeable, function(a) {
                var c = this._meta[a];
                if (typeof c === 'string')
                    return c;
                c = a.charCodeAt();
                return '\\u00' + Math.floor(c / 16).toString(16)
                        + (c % 16).toString(16);
            }) + '"';
        }
        return '"' + string + '"';
    },
 
    _escapeable: /["\\\x00-\x1f\x7f-\x9f]/g,
 
    _meta: {
        '\b': '\\b',
        '\t': '\\t',
        '\n': '\\n',
        '\f': '\\f',
        '\r': '\\r',
        '"': '\\"',
        '\\': '\\\\'
    }
}
/**
 * @author Ralph Voigt (info -at- drakedata.com)
 * @version 1.1
 * @date 29.01.2010
 * @modified Krister Karto
 * @date 
 *
 * @name serializeTree
 * @type jQuery
 * @homepage http://plugins.jquery.com/project/serializeTree/
 * @desc Recursive function to serialize ordered or unordered lists of arbitrary depth and complexity. The resulting array will keep the order of the tree and is suitable to be posted away.
 * @example $("#myltree").serializeTree("id","myArray",".elementsToExclude")
 * @param String attribute The attribute of the li-elements to be serialised
 * @param String levelString The Array to store data in
 * @param String exclude li-Elements to exclude from being serialised (optional)
 * @return String The array to be sent to the server via post
 *          Boolean false if the passed variable is not a list or empty
 * @cat Plugin
 */
 
 
jQuery.fn.serializeTree = function (attribute, levelString, exclude) {
	var dataString = '';
	var elems;
	if (exclude==undefined) elems = this.children();
	else elems = this.children().not(exclude);
	if( elems.length > 0) {
		elems.each(function() {
			var curLi = $(this);
			var toAdd = '';
			if( curLi.find('ul').length > 0) {
				var kids = $(curLi).children('a');
				var curLiChild = kids[0];
				levelString += '['+$(curLiChild).attr(attribute)+']';

				toAdd = $('ul:first', curLi).serializeTree(attribute, levelString, exclude);
				levelString = levelString.replace(/\[[^\]\[]*\]$/, '');
			} else if( curLi.find('ol').length > 0) {
				var kids = curLi.children('a');
				var curLiChild = kids[0];
				levelString += '['+$(curLiChild).attr(attribute)+']';
				toAdd = $('ol:first', curLi).serializeTree(attribute, levelString, exclude);
				levelString = levelString.replace(/\[[^\]\[]*\]$/, '');
			} else {
				var kids = $(curLi).children('a');
				var curLiChild = kids[0];
				dataString += '&'+levelString+'[]='+$(curLiChild).attr(attribute);
			}
			if(toAdd) dataString += toAdd;
		});
	} else {
		dataString += '&'+levelString+'['+this.attr(attribute)+']=';
	}
	if(dataString) return dataString;
	else return false;
};
function pause(millis)
{
	var date = new Date();
    var curDate = null;
    do { 
    	curDate = new Date(); 
    }while(curDate-date < millis)
}
function IsNan(number) {
    var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
    if (numberRegex.test(number)) {
        return true;
    } else {
        return false;
    }
}
function isArray(obj) {
    if (obj.constructor.toString().indexOf("Array") == -1)
        return false;
    else
        return true;
}