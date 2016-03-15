chssStatusImage.BAD = -1;
chssStatusImage.NEUTRAL = 0;
chssStatusImage.GOOD = 1;
chssStatusImage.AGAIN = 2;

function chssStatusImage()
{
	this._good_images = [[chssOptions.images_url + "dragon/Schaakdraak-1.png", 360, 358, "right"],
	                     [chssOptions.images_url + "dragon/Schaakdraak-2.png", 295, 360, "right"],
	                     [chssOptions.images_url + "dragon/Schaakdraak-3.png", 340, 360, "right"],
	                     [chssOptions.images_url + "dragon/Schaakdraak-4.png", 298, 360, "right"],
	                     [chssOptions.images_url + "dragon/Schaakdraak-5.png", 313, 360, "left"],
	                     [chssOptions.images_url + "dragon/Schaakdraak-6.png", 245, 360, "left"]];
	this._bad_images = [[chssOptions.images_url + "dragon/Schaakdraak-7.png", 351, 360, "right"],
	                    [chssOptions.images_url + "dragon/Schaakdraak-8.png", 328, 360, "left"],
	                    [chssOptions.images_url + "dragon/Schaakdraak-9.png", 313, 360, "left"],
	                    [chssOptions.images_url + "dragon/Schaakdraak-10.png", 360, 348, "right"]];
	this._neutral_images = [[chssOptions.images_url + "dragon/Schaakdraak-1.png", 351, 360, "right"],
	                        [chssOptions.images_url + "dragon/Schaakdraak-2.png", 328, 360, "right"],
	                        [chssOptions.images_url + "dragon/Schaakdraak-5.png", 313, 360, "left"],
	                        [chssOptions.images_url + "dragon/Schaakdraak-7.png", 360, 348, "right"]];
	this._again_images = [[chssOptions.images_url + "dragon/Schaakdraak-1.png", 351, 360, "right"],
	                        [chssOptions.images_url + "dragon/Schaakdraak-2.png", 328, 360, "right"],
	                        [chssOptions.images_url + "dragon/Schaakdraak-5.png", 313, 360, "left"],
	                        [chssOptions.images_url + "dragon/Schaakdraak-7.png", 360, 348, "right"]];
	this._good_reaction = [[658, 659, 660, 661, 663],
	                       [658, 659, 660, 661, 663],
	                       [658, 659, 660, 661, 663],
	                       [658, 659, 660, 661, 663],
	                       [662],
	                       [658, 659, 660, 661, 663]];
	this._bad_reaction = [[664, 665, 666, 667],
	                      [664, 665, 666, 667],
	                      [664, 665, 666, 667],
	                      [664, 665, 666, 667]];
	this._neutral_reaction = [[1393, 1394, 1395],
	                          [1393, 1394, 1395],
	                          [1393, 1394, 1395],
	                          [1393, 1394, 1395]];
	this._again_reaction = [[1396, 1397, 1398],
	                        [1396, 1397, 1398],
	                        [1396, 1397, 1398],
	                        [1396, 1397, 1398]];
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.backgroundColor = chssOptions.background_color;
	this._wrapper.style.position = "relative";
	
	this._text = document.createElement("div");
	this._text.style.fontWeight = "bold";
	
	this._image = document.createElement("div");
	this._image.style.position = "absolute";
	this._image.style.backgroundSize = "cover";
	this._image.style.top = 40 * (chssOptions.board_size/360) + "px";
	
	this._wrapper.appendChild(this._text);
	this._wrapper.appendChild(this._image);
}

chssStatusImage.prototype = {
	switchStatusImage: function(validation, check)
	{	
		var marginHor = 20 * (chssOptions.moves_size/200),
			marginVer = 2 * (chssOptions.board_size/360),
			fontSize = 26 * (chssOptions.board_size/360);
	
		this._text.style.margin = marginVer + "px " + marginHor + "px";
		this._text.style.fontSize = fontSize + "px";
		
		switch(validation)
		{
			case chssStatusImage.GOOD: var length = this._good_images.length,
										   arr_reaction = this._good_reaction,
										   arr_images = this._good_images;
											break;
			case chssStatusImage.BAD: var length = this._bad_images.length,
										  arr_reaction = this._bad_reaction,
										  arr_images = this._bad_images;
											break;
			case chssStatusImage.NEUTRAL: var length = this._neutral_images.length,
										   	  arr_reaction = this._neutral_reaction,
										      arr_images = this._neutral_images;
												break;
			case chssStatusImage.AGAIN: var length = this._neutral_images.length,
											arr_reaction = this._neutral_reaction,
											arr_images = this._neutral_images;
											break;
		}

		var indexG = Math.floor(Math.random() * length);
		this._image.style.backgroundImage = "url('" + arr_images[indexG][0] + "')";
		var transG = arr_reaction[indexG][Math.floor(Math.random() * arr_reaction[indexG].length)];
		this._text.innerHTML = chssLanguage.translate(transG);
		this._text.style.textAlign = arr_images[indexG][3];
		var width = ((arr_images[indexG][1]/360)*parseFloat(this._wrapper.style.width)) * (150/360);
		var height = ((arr_images[indexG][2]/360)*parseFloat(this._wrapper.style.width)) * (150/360);
		var top = parseFloat(this._image.style.top) + (10 * (chssOptions.board_size/360));
		
		if(width<150)
		{
			height = (height/width)*150;
			width = 150;
		}
		width = width * (chssOptions.board_size/360);
		height = height * (chssOptions.board_size/360) + top;
		if(height>=check)
		{
			height = height - top;
			check = check - top;

			width = width * (check/height);
			height = check;
		}
		else
			height = height - top;
		this._image.style.left = parseFloat(this._wrapper.style.width)/2 - width/2 + "px";
		this._image.style.width = width + "px";
		this._image.style.height = height + "px";
		this._wrapper.style.height = height + top + "px";
	},
	
	resize: function(diffCoeff)
	{
		var marginHor = 20 * (chssOptions.moves_size/200),
			marginVer = 2 * (chssOptions.board_size/360),
			fontSize = 26 * (chssOptions.board_size/360);
	
		this._text.style.margin = marginVer + "px " + marginHor + "px";
		this._text.style.fontSize = fontSize + "px";
		
		this._image.style.width = parseFloat(this._image.style.width) * diffCoeff + "px";
		this._image.style.height = parseFloat(this._image.style.height) * diffCoeff + "px";
		this._image.style.left = parseFloat(this._wrapper.style.width)/2 -  parseFloat(this._image.style.width)/2 + "px";
		this._wrapper.style.height = parseFloat(this._wrapper.style.height) * diffCoeff + "px";
	},
	
	getImageElement: function()
	{
		return this._wrapper;
	},
	
	getImage: function()
	{
		return this._image;
	}
}