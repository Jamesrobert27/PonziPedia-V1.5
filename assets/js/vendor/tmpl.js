/*
 * Simple JavaScript Templating
 * http://ejohn.org/blog/javascript-micro-templating/
 *
 * John Resig
 * http://ejohn.org
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT/
 */
;(function(){var d={};this.tmpl=function tmpl(a,b){var c=!/\W/.test(a)?d[a]=d[a]||tmpl(document.getElementById(a).innerHTML):new Function("obj","var p=[],print=function(){p.push.apply(p,arguments);};"+"with(obj){p.push('"+a.replace(/[\r\t\n]/g," ").split("<%").join("\t").replace(/((^|%>)[^\t]*)'/g,"$1\r").replace(/\t=(.*?)%>/g,"',$1,'").split("\t").join("');").split("%>").join("p.push('").split("\r").join("\\'")+"');}return p.join('');");return b?c(b):c}})();