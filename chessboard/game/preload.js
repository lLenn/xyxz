function chssPreload(callback, object)
{
	var images = [chssOptions.images_url + "chessboard/Board_flip.png",
	              chssOptions.images_url + "chessboard/Board.png",
	              chssOptions.images_url + "chessboard/BoardChoose.png",
	              chssOptions.images_url + "chessboard/BoardPromotion.png",
	              chssOptions.images_url + "dragon/Schaakdraak-1.png",
                  chssOptions.images_url + "dragon/Schaakdraak-2.png",
                  chssOptions.images_url + "dragon/Schaakdraak-3.png",
                  chssOptions.images_url + "dragon/Schaakdraak-4.png",
                  chssOptions.images_url + "dragon/Schaakdraak-5.png",
                  chssOptions.images_url + "dragon/Schaakdraak-6.png",
                  chssOptions.images_url + "dragon/Schaakdraak-7.png",
				  chssOptions.images_url + "dragon/Schaakdraak-8.png",
				  chssOptions.images_url + "dragon/Schaakdraak-9.png",
				  chssOptions.images_url + "dragon/Schaakdraak-10.png",
				  chssOptions.images_url + "logo.png"],
		loadingImages = new Array(),
		loadedImages = 0;
		
	
	for(var i = 0; i<images.length; i++)
	{
		loadingImages[i] = new Image();
		loadingImages[i].onload = function()
			{
				loadedImages++;
				if(loadedImages == images.length)
				{
					callback.call(object);
				}
			}
		loadingImages[i].src = images[i];
	}
}