/*
jCountdown Plugin for jQuery JavaScript Library

v 1.1.1 02/12/2012

http://codecanyon.net/user/ufoufoufo

Copyright (c) 2012
*/

jQuery.fn.extend({
	jCountdown:function(){
		var Flip = function(target){
			//---------------------------------------------------------------------------------------
			//vars
			//---------------------------------------------------------------------------------------
			this._target = target;
			this._width = 50;
			this._height = 64;
			this._frame = 1;
			this._totalFrames = 15;
			this._fps = 24;
			this._intervalId = -1;
			this._value = 0;
			
			//---------------------------------------------------------------------------------------
			//methods
			//---------------------------------------------------------------------------------------
			this.stop = function(){
				clearInterval(this._intervalId);
			}
			
			this.update = function(flag){
				if(flag){
					this.frame(1);
					
					this.stop();
					
					var target = this;
					this._intervalId = setInterval(function(){
						if(target.frame()==target.totalFrames()){
							clearInterval(target._intervalId);
							target.onFinish();
						}else{
							target.frame(target.frame()+1);
						}
					},Math.ceil(1000/this.fps()));
				}else{
					this.frame(this.totalFrames());
				}
			}
			
			this.value = function(v,flag){
				if(v==undefined){
					return this._value;
				}else{
					this._value = v;
					this.update(flag);
				}
			}
			
			this.onFinish = function(){
			}

			this.destroy = function(){
				this.stop();
				this._target = null;
			}

			//---------------------------------------------------------------------------------------
			//properties
			//---------------------------------------------------------------------------------------					
			this.width = function(v){
				if(v==undefined){
					return this._width;
				}else{
					this._width = v;
				}
			}
					
			this.height  = function(v){
				if(v==undefined){
					return this._height ;
				}else{
					this._height  = v;
				}
			}
			
			this.frame = function(v){
				if(v==undefined){
					return this._frame;
				}else{
					this._frame = v;
					var left = -(9-this.value())*this.width();
					var top = -(this.frame()-1)*this.height();
					this._target.children(".text").css("background-position", left+"px"+" "+top+"px");
				}
			}

			this.totalFrames = function(v){
				if(v==undefined){
					return this._totalFrames;
				}else{
					this._totalFrames = v;
				}
			}

			this.fps = function(v){
				if(v==undefined){
					return this._fps;
				}else{
					this._fps = v;
				}
			}

			//---------------------------------------------------------------------------------------
			//init
			//---------------------------------------------------------------------------------------
			this.update(false);
		}

		var Slide = function(target){
			//---------------------------------------------------------------------------------------
			//vars
			//---------------------------------------------------------------------------------------
			this._target = target;
			this._width = 50;
			this._height = 64;
			this._frame = 1;
			this._totalFrames = 15;
			this._fps = 24;
			this._intervalId = -1;
			this._value = 0;
			
			//---------------------------------------------------------------------------------------
			//methods
			//---------------------------------------------------------------------------------------
			this.stop = function(){
				clearInterval(this._intervalId);
			}
			
			this.update = function(flag){
				if(flag){
					this.frame(1);
					
					this.stop();
					
					var target = this;
					this._intervalId = setInterval(function(){
						if(target.frame()==target.totalFrames()){
							clearInterval(target._intervalId);
							target.onFinish();
						}else{
							target.frame(target.frame()+1);
						}
					},Math.ceil(1000/this.fps()));
				}else{
					this.frame(this.totalFrames());
				}
			}
			
			this.value = function(v,flag){
				if(v==undefined){
					return this._value;
				}else{
					this._value = v;
					this.update(flag);
				}
			}
			
			this.onFinish = function(){
			}

			this.destroy = function(){
				this.stop();
				this._target = null;
			}

			//---------------------------------------------------------------------------------------
			//properties
			//---------------------------------------------------------------------------------------					
			this.width = function(v){
				if(v==undefined){
					return this._width;
				}else{
					this._width = v;
				}
			}
					
			this.height  = function(v){
				if(v==undefined){
					return this._height ;
				}else{
					this._height  = v;
				}
			}
			
			this.frame = function(v){
				if(v==undefined){
					return this._frame;
				}else{
					this._frame = v;
					var left = 0;
					var top = -((1+this.value())*this.height()) + (Math.sin((this.frame()-1)/(this.totalFrames()-1)*Math.PI/2)*this.height());
					this._target.children(".text").css("background-position", left+"px"+" "+top+"px");
				}
			}

			this.totalFrames = function(v){
				if(v==undefined){
					return this._totalFrames;
				}else{
					this._totalFrames = v;
				}
			}

			this.fps = function(v){
				if(v==undefined){
					return this._fps;
				}else{
					this._fps = v;
				}
			}

			//---------------------------------------------------------------------------------------
			//init
			//---------------------------------------------------------------------------------------
			this.update(false);
		}
		
		var Metal = function(target){
			//---------------------------------------------------------------------------------------
			//vars
			//---------------------------------------------------------------------------------------
			this._target = target;
			this._width = 60;
			this._height = 60;
			this._frame = 1;
			this._totalFrames = 15;
			this._fps = 24;
			this._intervalId = -1;
			this._value = 0;
			
			//---------------------------------------------------------------------------------------
			//methods
			//---------------------------------------------------------------------------------------
			this.stop = function(){
				clearInterval(this._intervalId);
			}
			
			this.update = function(flag){
				if(flag){
					this.frame(1);
					
					this.stop();
					
					var target = this;
					this._intervalId = setInterval(function(){
						if(target.frame()==target.totalFrames()){
							clearInterval(target._intervalId);
							target.onFinish();
						}else{
							target.frame(target.frame()+1);
						}
					},Math.ceil(1000/this.fps()));
				}else{
					this.frame(this.totalFrames());
				}
			}
			
			this.value = function(v,flag){
				if(v==undefined){
					return this._value;
				}else{
					this._value = v;
					this.update(flag);
				}
			}
			
			this.onFinish = function(){
			}

			this.destroy = function(){
				this.stop();
				this._target = null;
			}

			//---------------------------------------------------------------------------------------
			//properties
			//---------------------------------------------------------------------------------------					
			this.width = function(v){
				if(v==undefined){
					return this._width;
				}else{
					this._width = v;
				}
			}
					
			this.height  = function(v){
				if(v==undefined){
					return this._height ;
				}else{
					this._height  = v;
				}
			}
			
			this.frame = function(v){
				if(v==undefined){
					return this._frame;
				}else{
					this._frame = v;
					var lastValue = this.value()+1;
					if(lastValue>9){
						lastValue = 0;
					}
					var progress = this.frame()/this.totalFrames();
					
					var opacity;
					if(progress>=.4 && progress<=.6){
						opacity = 0;
					}else if(progress<=.4){
						opacity = 1-progress/.4;
					}else if(progress>=.6){
						opacity = (progress-.6)/.4;
					}
					
					var left = 0;
					var top = -(progress>.5 ? this.value() : lastValue)*this.height();
					top -= (1-opacity)*3;
										
					this._target.children(".text").css("background-position", left+"px"+" "+top+"px").css("opacity",opacity);
					this._target.children(".cover").css("opacity",opacity);
				}
			}

			this.totalFrames = function(v){
				if(v==undefined){
					return this._totalFrames;
				}else{
					this._totalFrames = v;
				}
			}

			this.fps = function(v){
				if(v==undefined){
					return this._fps;
				}else{
					this._fps = v;
				}
			}

			//---------------------------------------------------------------------------------------
			//init
			//---------------------------------------------------------------------------------------
			this.update(false);
		}

		var Crystal = function(target){
			//---------------------------------------------------------------------------------------
			//vars
			//---------------------------------------------------------------------------------------
			this._target = target;
			this._width = 40;
			this._height = 40;
			this._widthSmall= 42;
			this._heightSmall = 42;
			this._frame = 1;
			this._totalFrames = 15;
			this._fps = 24;
			this._intervalId = -1;
			this._value = 0;
			
			//---------------------------------------------------------------------------------------
			//methods
			//---------------------------------------------------------------------------------------
			this.stop = function(){
				clearInterval(this._intervalId);
			}
			
			this.update = function(flag){
				if(flag){
					this.frame(1);
					
					this.stop();
					
					var target = this;
					this._intervalId = setInterval(function(){
						if(target.frame()==target.totalFrames()){
							clearInterval(target._intervalId);
							target.onFinish();
						}else{
							target.frame(target.frame()+1);
						}
					},Math.ceil(1000/this.fps()));
				}else{
					this.frame(this.totalFrames());
				}
			}
			
			this.value = function(v,flag){
				if(v==undefined){
					return this._value;
				}else{
					this._value = v;
					this.update(flag);
				}
			}
			
			this.onFinish = function(){
			}

			this.destroy = function(){
				this.stop();
				this._target = null;
			}

			//---------------------------------------------------------------------------------------
			//properties
			//---------------------------------------------------------------------------------------					
			this.width = function(v){
				if(v==undefined){
					return this._width;
				}else{
					this._width = v;
				}
			}
					
			this.height  = function(v){
				if(v==undefined){
					return this._height ;
				}else{
					this._height  = v;
				}
			}
			
			this.frame = function(v){
				if(v==undefined){
					return this._frame;
				}else{
					this._frame = v;
					var left = 0;
					var top = -this.value()*this.height();
					var opacity = Math.sin((this.frame()-1)/(this.totalFrames()-1)*Math.PI/2);
					if(opacity>0 && opacity<0.001){
						opacity = 0;
					}else if(opacity<0 && opacity>-0.001){
						opacity = 0;
					}
					
					this._target.children(".text").css("background-position", left+"px"+" "+top+"px").css("opacity",opacity);
				}
			}

			this.totalFrames = function(v){
				if(v==undefined){
					return this._totalFrames;
				}else{
					this._totalFrames = v;
				}
			}

			this.fps = function(v){
				if(v==undefined){
					return this._fps;
				}else{
					this._fps = v;
				}
			}

			//---------------------------------------------------------------------------------------
			//init
			//---------------------------------------------------------------------------------------
			this.update(false);
		}
		
		var Countdown = function(){
			//---------------------------------------------------------------------------------------
			//vars
			//---------------------------------------------------------------------------------------
			this._days = [];
			this._hours = [];
			this._minutes = [];
			this._seconds = [];
			this._tickId = -1;
			this._tickDelay = 100;
			this._timeText = "";
			this._timeZone = 0;
			this._time = null;

			//---------------------------------------------------------------------------------------
			//methods
			//---------------------------------------------------------------------------------------
			//check time
			this.checkTime = function(update){
				var currentTime = new Date();

				if(this._time.getTime()<(currentTime.getTime()+currentTime.getTimezoneOffset()*60*1000)){
					for(var i=0; i<this._days.length; i++){
						this._days[i].value(0);
					}					
					for(var i=0; i<this._hours.length; i++){
						this._hours[i].value(0);
					}					
					for(var i=0; i<this._minutes.length; i++){
						this._minutes[i].value(0);
					}					
					for(var i=0; i<this._seconds.length; i++){
						this._seconds[i].value(0);
					}

					this.stop();
					this.onFinish();

					return true;
				}else{
					var currentTimeText = this.timeFormat(this._time.getTime()-(currentTime.getTime()+currentTime.getTimezoneOffset()*60*1000), this._days.length, this._hours.length, this._minutes.length, this._seconds.length);
					var currentTimeChars = currentTimeText.split("");

					if (!isNaN(this._time)){
						for(var i=0; i<this._days.length; i++){
							var v = parseInt(currentTimeChars.shift(),10);
							
							if(v != this._days[i].value()){
								this._days[i].value(v, update);
							}
						}						
						for(var i=0; i<this._hours.length; i++){
							var v = parseInt(currentTimeChars.shift(),10);
							
							if(v != this._hours[i].value()){
								this._hours[i].value(v, update);
							}
						}						
						for(var i=0; i<this._minutes.length; i++){
							var v = parseInt(currentTimeChars.shift(),10);
							
							if(v != this._minutes[i].value()){
								this._minutes[i].value(v, update);
							}
						}						
						for(var i=0; i<this._seconds.length; i++){
							var v = parseInt(currentTimeChars.shift(),10);
							
							if(v != this._seconds[i].value()){
								this._seconds[i].value(v, update);
							}
						}						
					}
					
					return false;
				}
			}

			//text format
			this.textFormat = function(text, length, fillChar){
				text = text.toString();
				while (text.length<length){
					text = fillChar+text;
				}
				if(text.length>length){
					text = text.substr(text.length-length,length);
				}
				return text;
			}

			//time format
			this.timeFormat = function(msec, dayTextNumber, hourTextNumber, minuteTextNumber, secondTextNumber){
				var time = Math.floor(msec/1000);
				var s = time%60;
				var i = Math.floor(time%(60*60)/60);
				var h = Math.floor(time%(24*60*60)/(60*60));
				var d = Math.floor(time/(24*60*60));
				return this.textFormat(d, dayTextNumber, "0")+this.textFormat(h, hourTextNumber, "0")+this.textFormat(i, minuteTextNumber, "0")+this.textFormat(s, secondTextNumber, "0");
			}
			
			//start
			this.start = function(){
				this.stop();
				
				for(var i=0; i<this._days.length; i++){
					this._days[i].update();
				}				
				for(var i=0; i<this._hours.length; i++){
					this._hours[i].update();
				}				
				for(var i=0; i<this._minutes.length; i++){
					this._minutes[i].update();
				}				
				for(var i=0; i<this._seconds.length; i++){
					this._seconds[i].update();
				}

				var finish = this.checkTime(false);
				
				if(!finish){
					var target = this;
					this._tickId = setInterval(function(){
						target.checkTime(true);
					}, this._tickDelay);
				}
			}
			
			//stop
			this.stop = function(){
				for(var i=0; i<this._days.length; i++){
					this._days[i].stop();
				}				
				for(var i=0; i<this._hours.length; i++){
					this._hours[i].stop();
				}				
				for(var i=0; i<this._minutes.length; i++){
					this._minutes[i].stop();
				}				
				for(var i=0; i<this._seconds.length; i++){
					this._seconds[i].stop();
				}

				clearInterval(this._tickId);
			}
			
			this.onFinish = function(){
			}

			this.destroy = function(){
				for(var i=0; i<this._days.length; i++){
					this._days[i].destroy();
				}				
				for(var i=0; i<this._hours.length; i++){
					this._hours[i].destroy();
				}				
				for(var i=0; i<this._minutes.length; i++){
					this._minutes[i].destroy();
				}				
				for(var i=0; i<this._seconds.length; i++){
					this._seconds[i].destroy();
				}

				this._days = [];
				this._hours = [];
				this._minutes = [];
				this._seconds = [];

				this.stop();
			}

			//---------------------------------------------------------------------------------------
			//properties
			//---------------------------------------------------------------------------------------	
			this.items = function(days, hours, minutes, seconds){
				this._days = days;
				this._hours = hours;
				this._minutes = minutes;
				this._seconds = seconds;
			}
			
			this.timeText = function(v){
				if(v==undefined){
					return this._timeText;
				}else{
					this._timeText = v;
					this.time(this.timeText(), this.timeZone());
				}
			}
			
			this.timeZone = function(v){
				if(v==undefined){
					return this._timeZone;
				}else{
					this._timeZone = v;
					this.time(this.timeText(), this.timeZone());
				}
			}
		
			//time
			this.time = function(tt,tz){
				this._timeText = tt;
				this._timeZone = tz;
				
				var time = this._timeText.split("/").join(" ").split(":").join(" ").split(" ");
				var y = parseInt(time[0],10);
				var m = parseInt(time[1],10)-1;
				var d = parseInt(time[2],10);
				var h = parseInt(time[3],10)
				var i = parseInt(time[4],10)-this._timeZone*60;
				var s = parseInt(time[5],10);
				this._time = new Date(y, m, d, h, i, s, 0);

				this.start();
			}
			//---------------------------------------------------------------------------------------
			//init
			//---------------------------------------------------------------------------------------
		}

		//---------------------------------------------------------------------------------------
		//init
		//---------------------------------------------------------------------------------------
		var getCountdown = function(){
			return target.data("countdown");
		}
		var initCountdown = function(){
			if(getCountdown()==undefined){
				var countdown = new Countdown();
				target.data("countdown", countdown);
				
				return getCountdown();
			}
		}
		var destroyCountdown = function(){
			if(getCountdown()!=undefined){
				getCountdown().destroy();

				target.removeData("countdown");
			}
		}
		var init = function(setting){
			countdown = initCountdown();
				
			var browserVersion = parseInt(jQuery.browser.version,10);
			
			var timeText = typeof(setting.timeText)=="string" ? setting.timeText : "";
			var timeZone = parseFloat(setting.timeZone);
			if(isNaN(timeZone)){
				timeZone = 0;
			}			
			var style = typeof(setting.style)=="string" ? setting.style.toLowerCase() : "";
			switch(style){
				case "flip":
					break;
				case "slide":
					break;
				case "crystal":
					break;
				case "metal":
					break;
				default:
					style = "flip";
			}			
			var color = typeof(setting.color)=="string" ? setting.color.toLowerCase() : "";
			switch(color){
				case "black":
					break;
				case "white":
					break;
				default:
					color = "black";
			}			
			var width = parseInt(setting.width,10);
			if(width>=10){
			}else{
				width = 0;
			}
			var textGroupSpace = parseInt(setting.textGroupSpace,10);
			if(textGroupSpace>=0){
			}else{
				textGroupSpace = 15;
			}			
			var textSpace = parseInt(setting.textSpace,10);
			if(textSpace>0){
			}else{
				textSpace = 0;
			}			
			var reflection = setting.reflection!=false;
			var reflectionOpacity = parseFloat(setting.reflectionOpacity);
			if(reflectionOpacity>0){
				if(reflectionOpacity>100){
					reflectionOpacity = 100;
				}
			}else{
				reflectionOpacity = 10;
			}			
			var reflectionBlur = parseInt(setting.reflectionBlur,10);
			if(reflectionBlur>0){
				if(reflectionBlur>10){
					reflectionBlur = 10;
				}
			}else{
				reflectionBlur = 0;
			}			
			var dayTextNumber = parseInt(setting.dayTextNumber,10)>2 ? parseInt(setting.dayTextNumber,10) : 2;
			var hourTextNumber = 2;
			var minuteTextNumber = 2;
			var secondTextNumber = 2;
			var displayDay = setting.displayDay!=false;
			var displayHour = setting.displayHour!=false;
			var displayMinute = setting.displayMinute!=false;
			var displaySecond = setting.displaySecond!=false;	
			var displayLabel = setting.displayLabel!=false;	
			var onFinish = typeof(setting.onFinish)=="function" ? setting.onFinish : function(){};

			var html = "";
			var itemClass = "";
			var lastClass = "";

			html += '<div class="jCountdown">';
				if(displayDay){
				var lastItem = (!displayHour && !displayMinute && !displaySecond) ? " lastItem" : "";
				html += '<div class="group day'+lastItem+'">';
					for(var i=0; i<dayTextNumber; i++){
					itemClass = " item"+(i+1);
					lastClass = i==(dayTextNumber-1) ? " lastItem" : "";
					html += '<div class="container'+itemClass+lastClass+'">';
						if(style=="slide" || style=="crystal" || style=="metal"){
							html += '<div class="cover"></div>';
						}
						html += '<div class="text"></div>';
					html += '</div>';
					}
					if(displayLabel){
					html += '<div class="label"></div>';
					}
				html += '</div>';
				}
				
				if(displayHour){
				var lastItem = (!displayMinute && !displaySecond) ? " lastItem" : "";
				html += '<div class="group hour'+lastItem+'">';
					for(var i=0; i<hourTextNumber; i++){
					itemClass = " item"+(i+1);
					lastClass = i==(hourTextNumber-1) ? " lastItem" : "";
					html += '<div class="container'+itemClass+lastClass+'">';
						if(style=="slide" || style=="crystal" || style=="metal"){
							html += '<div class="cover"></div>';
						}
						html += '<div class="text"></div>';
					html += '</div>';
					}
					if(displayLabel){
					html += '<div class="label"></div>';
					}
				html += '</div>';
				}
				
				if(displayMinute){
				var lastItem = (!displaySecond) ? " lastItem" : "";
				html += '<div class="group minute'+lastItem+'">';
				  for(var i=0; i<minuteTextNumber; i++){
					itemClass = " item"+(i+1);
					lastClass = i==(minuteTextNumber-1) ? " lastItem" : "";
					html += '<div class="container'+itemClass+lastClass+'">';
						if(style=="slide" || style=="crystal" || style=="metal"){
							html += '<div class="cover"></div>';
						}
						html += '<div class="text"></div>';
					html += '</div>';
					}
					if(displayLabel){
					html += '<div class="label"></div>';
					}
				html += '</div>';
				}
				
				if(displaySecond){
				html += '<div class="group second lastItem">';
					for(var i=0; i<secondTextNumber; i++){
					itemClass = " item"+(i+1);
					lastClass = i==(secondTextNumber-1) ? " lastItem" : "";
					html += '<div class="container'+itemClass+lastClass+'">';
						if(style=="slide" || style=="crystal" || style=="metal"){
							html += '<div class="cover"></div>';
						}
						html += '<div class="text"></div>';
					html += '</div>';
					}
					if(displayLabel){
					html += '<div class="label"></div>';
					}
				html += '</div>';
				}
			html += '</div>';
			
			target.html(html);

			var countdownObject = target.children(".jCountdown");

			countdownObject.addClass(style);
			countdownObject.addClass(color);
			
			countdownObject.children(".group").css("margin-right",textGroupSpace+"px");
			countdownObject.children(".group.lastItem").css("margin-right","0px");
			countdownObject.children(".group").children(".container").css("margin-right",textSpace+"px");
			countdownObject.children(".group").children(".container.lastItem").css("margin-right","0px");
			
			if(reflection){
				if((jQuery.browser.msie && browserVersion<10)){
				}else{
					reflectionObject = countdownObject.clone();
	
					reflectionObject.addClass("reflection");
					if(displayLabel){
						reflectionObject.addClass("displayLabel");
					}
					
					if(reflectionOpacity!=100){
						reflectionObject.css("opacity",reflectionOpacity/100);
					}
					if(reflectionBlur!=0){
						reflectionObject.addClass("blur"+reflectionBlur);
					}
					
					countdownObject = countdownObject.add(reflectionObject);
				}
			}

			var countdownContainer = jQuery('<div class="jCountdownContainer"></div>');
			countdownContainer.append(countdownObject);

			target.append(countdownContainer);
			
			if(width!=0){
				var countdownScaleObject = jQuery('<div class="jCountdownScale"></div>');
				countdownScaleObject.append(countdownObject);
				
				countdownContainer.append(countdownScaleObject);
				
				var countdownScaleObjectWidth = countdownScaleObject.width();
				var countdownScaleObjectHeight = countdownScaleObject.height();
				
				var scale = width/countdownScaleObjectWidth;
				var left = -(1-scale)*countdownScaleObjectWidth/2;
				var top = -(1-scale)*countdownScaleObjectHeight/2;
				var scaleCss = "scale("+scale+")";
				
				countdownContainer.width(countdownScaleObjectWidth*scale);
				countdownContainer.height(countdownScaleObjectHeight*scale);
				
				if(jQuery.browser.msie && browserVersion<=8){
					countdownScaleObject.css("zoom", scale);
				}else{
					countdownScaleObject.css("transform", scaleCss).
					css("-moz-transform", scaleCss).
					css("-webkit-transform", scaleCss).
					css("-o-transform", scaleCss).
					css("-ms-transform", scaleCss);
					
					countdownScaleObject.css("left",left).css("top",top);
				}
			}

			var selector = "";
			var index = 0;
			var days = [];
			var hours = [];
			var minutes = [];
			var seconds = [];
			var itemClass = function(){};
			
			switch(style){
				case "flip":
					itemClass = Flip;
					break;
				case "slide":
					itemClass = Slide;
					break;
				case "crystal":
					itemClass = Crystal;
					break;
				case "metal":
					itemClass = Metal;
					break;
				default:
			}

			index = 1;
			selector = ".group.day>.container.item";
			while(countdownObject.find(selector+index).length){
				days.push(new itemClass(countdownObject.find(selector+index)));
				index++;
			};
			
			index = 1;
			selector = ".group.hour>.container.item";
			while(countdownObject.find(selector+index).length){
				hours.push(new itemClass(countdownObject.find(selector+index)));
				index++;
			};
			
			index = 1;
			selector = ".group.minute>.container.item";
			while(countdownObject.find(selector+index).length){
				minutes.push(new itemClass(countdownObject.find(selector+index)));
				index++;
			};
			
			index = 1;
			selector = ".group.second>.container.item";
			while(countdownObject.find(selector+index).length){
				seconds.push(new itemClass(countdownObject.find(selector+index)));
				index++;
			};

			countdown.items(days, hours, minutes, seconds);

			countdown.onFinish = onFinish;
			countdown.time(timeText, timeZone);
		}
		var destroy = function(){
			destroyCountdown();
			target.children().remove();
		}
		var start = function(){
			countdown.start();
		}
		var stop = function(){
			countdown.stop();
		}

		if(arguments.length>0){
			var target = this;
			var countdown = getCountdown();

			if(arguments.length==1 && typeof(arguments[0])=="object"){
				//destroy the old countdown
				if(countdown!=undefined){
					destroy();
				}

				//init new countdown
				init(arguments[0]);
			}else if(typeof(arguments[0])=="string"){
				//set setting & call method
				if(countdown!=undefined){
					switch(arguments[0]){
						case "stop":
							stop();
							break;
						case "start":
							start();
							break;
						case "destroy":
							destroy();
							break;
						default:
					}
				}
			}
		}

		return this;
	}
});