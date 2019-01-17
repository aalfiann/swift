'use strict';

/** Custom App Helper */
var $$app = {
  hasClass: function hasClass(ele, cls) {
    return !!ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
  },
  addClass: function addClass(ele, cls) {
    if (!this.hasClass(ele, cls)) ele.className += " " + cls;
  },
  removeClass: function removeClass(ele, cls) {
    if (this.hasClass(ele, cls)) {
      var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
      ele.className = ele.className.replace(reg, ' ');
    }
  },
  addInlineCss: function addInlineCss(inlinecss) {
    var css = inlinecss,
        head = document.head || document.getElementsByTagName('head')[0],
        style = document.createElement('style');
    style.type = 'text/css';
    if (style.styleSheet) {
      style.styleSheet.cssText = css;
    } else {
      style.appendChild(document.createTextNode(css));
    }
    head.appendChild(style);
  },
  throttle: function throttle(f, delay){
    var timer = null;
    return function(){
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = window.setTimeout(function(){f.apply(context, args);},delay || 500);
    };
  }
};
/** Local Storage */
var $$storage = {
  has: function has(key) {
    return !!localStorage.getItem(key);
  },
  get: function get(key) {
    return JSON.parse(localStorage.getItem(key));
  },
  set: function set(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
  }
};
/** Validate */
var $$validate = {
  isString: function isString(value) {
    if (typeof value === 'string') {
      return true;
    }
    return false;
  },
  isInt: function isInt(value) {
    return Number.isInteger(value);
  },
  isJson: function IsJson(value) {
    try {
        JSON.parse(value);
    } catch (e) {
        return false;
    }
    return true;
  },
  isEmpty: function isEmpty(value) {
    for (var key in value) {
      if (value.hasOwnProperty(key)) return false;
    }
    return true;
  },
  isNotEmpty: function isNotEmpty(value) {
    if (value !== '' && value !== null && typeof value !== 'undefined') {
      return true;
    }
    return false;
  },
  isRequired: function isRequired(value) {
    var rgx = /.*\S.*/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isDate: function isDate(value) {
    var rgx = /([123456789]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isTimestamp: function isTimestamp(value) {
    var rgx = /^\d\d\d\d-(0?[1-9]|1[0-2])-(0?[1-9]|[12][0-9]|3[01]) (00|[0-9]|1[0-9]|2[0-3]):([0-9]|[0-5][0-9]):([0-9]|[0-5][0-9])$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isAlphanumeric: function isAlphanumeric(value) {
    var rgx = /^[a-zA-Z0-9]+$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isAlphabet: function isAlphabet(value) {
    var rgx = /^[a-zA-Z]+$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isDecimal: function isDecimal(value) {
    var rgx = /^[+-]?[0-9]+(?:\.[0-9]+)?$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isNotLeadingZero: function isNotLeadingZero(value) {
    var rgx = /^[1-9][0-9]*$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isNumeric: function isNumeric(value) {
    var rgx = /^[0-9]+$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isDouble: function isDouble(value) {
    var rgx = /^[+-]?[0-9]+(?:,[0-9]+)*(?:\.[0-9]+)?$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isUsername: function isUsername(value) {
    var rgx = /^[a-zA-Z0-9]{3,20}$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isDomain: function isDomain(value) {
    var rgx = /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  isEmail: function isEmail(value) {
    var rgx = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  },
  withRegex: function withRegex(value, rgx) {
    if (rgx.test(value) == false) {
      return false;
    }
    return true;
  }
};