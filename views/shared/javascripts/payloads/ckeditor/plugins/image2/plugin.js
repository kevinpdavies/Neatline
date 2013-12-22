﻿/*
 Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.md or http://ckeditor.com/license
*/
(function(){function p(a){if(!(a.name in{div:1,p:1}))return!1;var b=a.children;if(1!==b.length)return!1;var b=b[0],c=b.name;return"img"!=c&&!("figure"==c&&b.hasClass("caption"))?!1:"center"==CKEDITOR.tools.parseCssText(a.attributes.style||"",!0)["text-align"]?!0:!1}function z(a){var b=a.data,b={width:b.width,height:b.height},a=a.parts.image,c;for(c in b)b[c]?a.setAttribute(c,b[c]):a.removeAttribute(c)}function A(a){var b=a.editor,c=b.editable(),e=b.document,d=e.createElement("span");d.addClass("cke_image_resizer");
d.setAttribute("title",b.lang.image2.resizer);d.append(new CKEDITOR.dom.text("​",e));if(a.inline)a.wrapper.append(d);else{var g=a.element.getFirst(),f=e.createElement("span");f.addClass("cke_image_resizer_wrapper");f.append(a.parts.image);f.append(d);a.element.append(f,!0);g.is("span")&&g.remove()}d.on("mousedown",function(g){function i(a,b,c){var d=CKEDITOR.document,i=[];e.equals(d)||i.push(d.on(a,b));i.push(e.on(a,b));if(c)for(a=i.length;a--;)c.push(i.pop())}function l(){m=j+u*q;n=Math.round(m/
o)}function h(){n=s-k;m=Math.round(n*o)}var v=a.parts.image,u="right"==a.data.align?-1:1,f=g.data.$.screenX,p=g.data.$.screenY,j=v.$.clientWidth,s=v.$.clientHeight,o=j/s,w=[],y="cke_image_s"+(!~u?"w":"e"),x,m,n,t,q,k,r;b.fire("saveSnapshot");i("mousemove",function(a){x=a.data.$;q=x.screenX-f;k=p-x.screenY;r=Math.abs(q/k);1==u?0>=q?0>=k?l():r>=o?l():h():0>=k?r>=o?h():l():h():0>=q?0>=k?r>=o?h():l():h():0>=k?l():r>=o?l():h();15<=m&&15<=n?(v.setAttributes({width:m,height:n}),t=!0):t=!1},w);i("mouseup",
function(){for(var i;i=w.pop();)i.removeListener();c.removeClass(y);d.removeClass("cke_image_resizing");t&&(a.setData({width:m,height:n}),b.fire("saveSnapshot"));t=!1},w);c.addClass(y);d.addClass("cke_image_resizing")});a.on("data",function(){d["right"==a.data.align?"addClass":"removeClass"]("cke_image_resizer_left")})}function B(a){var b=[];return function(c){var e=a.getCommand("justify"+c);if(e){b.push(function(){e.refresh(a,a.elementPath())});if(c in{right:1,left:1,center:1})e.on("exec",function(d){var e=
s(a);if(e){e.setData("align",c);for(e=b.length;e--;)b[e]();d.cancel()}});e.on("refresh",function(b){var e=s(a),f={right:1,left:1,center:1};e&&(this.setState(e.data.align==c?CKEDITOR.TRISTATE_ON:c in f?CKEDITOR.TRISTATE_OFF:CKEDITOR.TRISTATE_DISABLED),b.cancel())})}}}function s(a){return(a=a.widgets.focused)&&"image"==a.name?a:null}var C=/^\s*(\d+\%)\s*$/i;CKEDITOR.plugins.add("image2",{lang:"af,ar,bg,bn,bs,ca,cs,cy,da,de,el,en,en-au,en-ca,en-gb,eo,es,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,id,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,ug,uk,vi,zh,zh-cn",
requires:"widget,dialog",icons:"image",hidpi:!0,onLoad:function(){CKEDITOR.addCss(".cke_editable.cke_image_sw, .cke_editable.cke_image_sw *{cursor:sw-resize !important}.cke_editable.cke_image_se, .cke_editable.cke_image_se *{cursor:se-resize !important}.cke_image_resizer{display:none;position:absolute;width:10px;height:10px;bottom:-5px;right:-5px;background:#000;outline:1px solid #fff;cursor:se-resize;}.cke_image_resizer_wrapper{position:relative;display:inline-block;line-height:0;}.cke_image_resizer.cke_image_resizer_left{right:auto;left:-5px;cursor:sw-resize;}.cke_widget_wrapper:hover .cke_image_resizer,.cke_image_resizer.cke_image_resizing{display:block}")},
init:function(a){var b=a.config,c=a.lang.image2;b.filebrowserImage2BrowseUrl=b.filebrowserImageBrowseUrl;b.filebrowserImage2UploadUrl=b.filebrowserImageUploadUrl;j.pathName=c.pathName;j.editables.caption.pathName=c.pathNameCaption;a.widgets.add("image",j);a.ui.addButton&&a.ui.addButton("Image",{label:a.lang.common.image,command:"image",toolbar:"insert,10"});a.contextMenu&&(a.addMenuGroup("image",10),a.addMenuItem("image",{label:c.menu,command:"image",group:"image"}));CKEDITOR.dialog.add("image2",
this.path+"dialogs/image2.js")},afterInit:function(a){var b={left:1,right:1,center:1,block:1},a=B(a),c;for(c in b)a(c)}});var j={allowedContent:{div:{match:p,styles:"text-align"},figcaption:!0,figure:{classes:"!caption",styles:"float,display"},img:{attributes:"!src,alt,width,height",styles:"float"},p:{match:p,styles:"text-align"}},contentTransformations:[["img[width]: sizeToAttribute"]],editables:{caption:{selector:"figcaption",allowedContent:"br em strong sub sup u s; a[!href]"}},parts:{image:"img",
caption:"figcaption"},dialog:"image2",template:'<img alt="" src="" />',data:function(){var a=this,b=a.editor,c=b.document,e=b.editable();a.shiftState({element:a.element,oldState:a.oldData,newState:a.data,destroy:function(){this.destroyed||(b.widgets.focused==a&&(this.focused=!0),b.widgets.destroy(a),this.destroyed=!0)},init:function(d){if(this.destroyed)a=b.widgets.initOn(d,"image",a.data),a.inline&&!(new CKEDITOR.dom.elementPath(a.wrapper,e)).block&&(d=c.createElement(b.activeEnterMode==CKEDITOR.ENTER_P?
"p":"div"),d.replace(a.wrapper),a.wrapper.move(d)),this.focused&&(a.focus(),delete this.focused),delete this.destroyed;else{var d=a,g=d.wrapper,f=d.data.align;"center"==f?(d.inline||g.setStyle("text-align","center"),g.removeStyle("float")):(d.inline||g.removeStyle("text-align"),"none"==f?g.removeStyle("float"):g.setStyle("float",f))}}});a.parts.image.setAttributes({src:a.data.src,"data-cke-saved-src":a.data.src,alt:a.data.alt});z(a);a.oldData=CKEDITOR.tools.extend({},a.data)},init:function(){var a=
CKEDITOR.plugins.image2,b=this.parts.image,c={hasCaption:!!this.parts.caption,src:b.getAttribute("src"),alt:b.getAttribute("alt")||"",width:b.getAttribute("width")||"",height:b.getAttribute("height")||"",lock:this.ready?a.checkHasNaturalRatio(b):!0};c.align||(c.align=this.element.getStyle("float")||b.getStyle("float")||"none",this.element.removeStyle("float"),b.removeStyle("float"));c.hasCaption||this.wrapper.setStyle("line-height","0");this.setData(c);A(this);this.shiftState=a.stateShifter(this.editor);
this.on("contextMenu",function(a){a.data.image=CKEDITOR.TRISTATE_OFF});this.on("dialog",function(a){a.data.widget=this},this)},upcast:function(a,b){var c={width:1,height:1},e=a.name,d;if(!a.attributes["data-cke-realelement"]&&(p(a)?("div"==e&&(d=a.getFirst("figure"),a.replaceWith(d),a=d),b.align="center",d=a.getFirst("img")):"figure"==e&&a.hasClass("caption")?d=a.getFirst("img"):"img"==e&&(d=a),d)){for(var g in c)(e=d.attributes[g])&&e.match(C)&&delete d.attributes[g];return a}},downcast:function(a){var b=
a.attributes,c=this.data.align;if(!this.inline){var e=a.getFirst("span"),d=e.getFirst("img");e.replaceWith(d)}c&&"none"!=c&&(e=CKEDITOR.tools.parseCssText(b.style||""),"center"==c&&"p"!=a.name?a=a.wrapWith(new CKEDITOR.htmlParser.element("img"==a.name?"p":"div",{style:"text-align:center"})):c in{left:1,right:1}&&(e["float"]=c),CKEDITOR.tools.isEmpty(e)||(b.style=CKEDITOR.tools.writeCssText(e)));return a}};CKEDITOR.plugins.image2={stateShifter:function(a){function b(a,b){return a.oldState?a.oldState[b]!==
a.newState[b]:!1}function c(a,b){var c=d.createElement(a.activeEnterMode==CKEDITOR.ENTER_P?"p":"div",{styles:{"text-align":"center"}});e(c,b);b.move(c);return c}function e(b,c){if(c.getParent()){var d=a.createRange();d.moveToPosition(c,CKEDITOR.POSITION_BEFORE_START);c.remove();g.insertElementIntoRange(b,d)}else b.replace(c)}var d=a.document,g=a.editable(),f=["hasCaption","align"],j={align:function(d,e,h){var g=d.newState.hasCaption,f=d.element;if(b(d,"align")){if(!g&&("center"==h&&(d.destroy(),d.element=
c(a,f)),!b(d,"hasCaption")&&"center"==e&&"center"!=h))d.destroy(),e=f.findOne("img"),e.replace(f),d.element=e}else"center"==h&&(b(d,"hasCaption")&&!g)&&(d.destroy(),d.element=c(a,f));f.is("figure")&&("center"==h?f.setStyle("display","inline-block"):f.removeStyle("display"))},hasCaption:function(a,c,f){if(b(a,"hasCaption"))if(c=a.element,a.destroy(),f){var f=c.findOne("img")||c,g=CKEDITOR.dom.element.createFromHtml('<figure class="caption"><img alt="" src="" /><figcaption>Caption</figcaption></figure>',
d);e(g,c);f.replace(g.findOne("img"));a.element=g}else f=c.findOne("img"),f.replace(c),a.element=f}};return function(a){for(var b=a.oldState,c=a.newState,d,e=0;e<f.length;e++)d=f[e],j[d](a,b?b[d]:null,c[d]);a.init(a.element)}},checkHasNaturalRatio:function(a){var b=a.$,a=this.getNatural(a);return Math.round(b.clientWidth/a.width*a.height)==b.clientHeight||Math.round(b.clientHeight/a.height*a.width)==b.clientWidth},getNatural:function(a){if(a.$.naturalWidth)a={width:a.$.naturalWidth,height:a.$.naturalHeight};
else{var b=new Image;b.src=a.getAttribute("src");a={width:b.width,height:b.height}}return a}}})();