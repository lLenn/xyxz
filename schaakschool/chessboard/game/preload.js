function chssPreload(cbLoad, cbInitiate, object)
{
	chssPreload.load_images = [chssOptions.images_url + "chessboard/Board.png",
	                           chssOptions.images_url + "logo.png"];

	chssPreload.initiate_images = [chssOptions.images_url + "chessboard/Board_flip.png",
	                               chssOptions.images_url + "chessboard/BoardChoose.png",
	                               chssOptions.images_url + "chessboard/BoardPromotion.png",
	                               chssOptions.images_url + "dragon/Schaakdraak-1.png"];

	chssPreload.other_images = [chssOptions.images_url + "dragon/Schaakdraak-2.png",
			                    chssOptions.images_url + "dragon/Schaakdraak-3.png",
			                    chssOptions.images_url + "dragon/Schaakdraak-4.png",
			                    chssOptions.images_url + "dragon/Schaakdraak-5.png",
			                    chssOptions.images_url + "dragon/Schaakdraak-6.png",
			                    chssOptions.images_url + "dragon/Schaakdraak-7.png",
			                    chssOptions.images_url + "dragon/Schaakdraak-8.png",
			                    chssOptions.images_url + "dragon/Schaakdraak-9.png",
			                    chssOptions.images_url + "dragon/Schaakdraak-10.png"];
	
	chssPreload.loadImages(0, cbLoad, cbInitiate, object);
}


chssPreload.loadImages = function(j, cbLoad, cbInitiate, object)
{
    var loadingImages = new Array(),
		loadedImages = 0,
		loadArray = undefined;

	switch(j)
	{
		case 0: loadArray = chssPreload.load_images; break;
		case 1: loadArray = chssPreload.initiate_images; break;
		case 2: loadArray = chssPreload.other_images; break;
	}
	
	for(var i=0, len=loadArray.length; i<len; i++)
	{
		loadingImages[i] = new Image();
		if(j==0 || j==1)
		{
			loadingImages[i].onload = function()
			{
				loadedImages++;
				if(loadedImages == loadArray.length)
				{
					if(j==0)
						cbLoad.call(object);
					else
						cbInitiate.call(object, true);
					
					chssPreload.loadImages(++j, cbLoad, cbInitiate, object);
				}
			}
		}
		loadingImages[i].src = loadArray[i];
	}
}