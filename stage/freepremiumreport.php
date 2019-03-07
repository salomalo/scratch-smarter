<?php
// check if they've been here, if they haven't set
// a cookie for subsequent visits

if($_COOKIE['FreePremiumReport']==null) { 
    setcookie("FreePremiumReport", '1');
	header('Location: http://www.scratchsmarter.com/scraper/weekly_pdf/download.php?state='.$_GET["state"]);
	}
else {
    	switch ($_GET["state"]) {
			case "AZ":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/17fafe5f6ce2f1904eb09d2e80a4cbf6/0');
				break;
			case "AR":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/51174add1c52758f33d414ceaf3fe6ba/0');
				break;
			case "CA":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/fe51510c80bfd6e5d78a164cd5b1f688/0');
				break;
			case "CO":
				header('Location: http://scratchsmarter.com');
				break;
			case "CT":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/28e209b61a52482a0ae1cb9f5959c792/0');
				break;
			case "DC":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/ff49cc40a8890e6a60f40ff3026d2730/0');
				break;
			case "DE":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/8edd72158ccd2a879f79cb2538568fdc/0');
				break;
			case "FL":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/9cb67ffb59554ab1dabb65bcb370ddd9/0');
				break;
			case "GA":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/3d779cae2d46cf6a8a99a35ba4167977/0');
				break;
			case "IA":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/e48e13207341b6bffb7fb1622282247b/0');
				break;
			case "ID":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/05311655a15b75fab86956663e1819cd/0');
				break;
			case "IL":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/2d405b367158e3f12d7c1e31a96b3af3/0');
				break;
			case "IN":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/4f87658ef0de194413056248a00ce009/0');
				break;
			case "KS":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/33ebd5b07dc7e407752fe773eed20635/0');
				break;
			case "KY":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/5e1b18c4c6a6d31695acbae3fd70ecc6/0');
				break;
			case "LA":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/674bfc5f6b72706fb769f5e93667bd23/0');
				break;
			case "ME":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/82965d4ed8150294d4330ace00821d77/0');
				break;
			case "MD":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/86109d400f0ed29e840b47ed72777c84/0');
				break;
			case "MA":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/a50abba8132a77191791390c3eb19fe7/0');
				break;
			case "MI":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/0e55666a4ad822e0e34299df3591d979/0');
				break;
			case "MN":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/c73dfe6c630edb4c1692db67c510f65c/0');
				break;
			case "MO":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/bcc0d400288793e8bdcd7c19a8ac0c2b/0');
				break;    
			case "MT":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/4b86abe48d358ecf194c56c69108433e/0');
				break;    
			case "NE":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/cbef46321026d8404bc3216d4774c8a9/0');
				break;
			case "NH":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/ee8374ec4e4ad797d42350c904d73077/0');
				break;
			case "NJ":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/b8c27b7a1c450ffdacb31483454e0b54/0');
				break;
			case "NM":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/980ecd059122ce2e50136bda65c25e07/0');
				break;
			case "NY":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/c26820b8a4c1b3c2aa868d6d57e14a79/0');
				break;
			case "NC":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/3e313b9badf12632cdae5452d20e1af6/0');
				break;
			case "OH":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/83adc9225e4deb67d7ce42d58fe5157c/0');
				break;
			case "OK":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/8d3369c4c086f236fabf61d614a32818/0');
				break;
			case "OR":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/fb508ef074ee78a0e58c68be06d8a2eb/0');
				break;
			case "PA":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/4c22bd444899d3b6047a10b20a2f26db/0');
				break;
			case "RI":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/cf9a242b70f45317ffd281241fa66502/0');
				break;
			case "SC":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/a9be4c2a4041cadbf9d61ae16dd1389e/0');
				break;
			case "SD":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/9683cc5f89562ea48e72bb321d9f03fb/0');
				break;
			case "TN":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/effc299a1addb07e7089f9b269c31f2f/0');
				break;
			case "TX":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/cf1f78fe923afe05f7597da2be7a3da8/0');
				break;
			case "VT":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/6c1da886822c67822bcf3679d04369fa/0');
				break;
			case "VA":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/6a5889bb0190d0211a991f47bb19a777/0');
				break;
			case "WA":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/0e3a37aa85a14e359df74fa77eded3f6/0');
				break;
			case "WV":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/81ca0262c82e712e50c580c032d99b60/0');
				break;
			case "WI":
				header('Location: http://www.secureinfossl.com/carts/shopping_cart/oneClickBundleBuy/d1ee59e20ad01cedc15f5118a7626099/0');
				break;

				
		}
}
?>