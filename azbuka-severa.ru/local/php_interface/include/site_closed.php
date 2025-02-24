<?php 

// заполнить данные или оставить пустыми
$email = "as@azbuka-severa.ru";
$phone = "+7 (495) 121-0110";
$adres = "Москва, Аминьевское шоссе, 26А";

//ссылка на лого, вставляется в IMG
$logo_img = "https://lib.novoxdev.ru/azbuka/logo.svg";

//ссылка на фон, можно с unsplash. По дефолту ресторанная картинка. Вставляется в background-image
$background_img = "https://images.unsplash.com/photo-1562158079-e4b9ed06b62d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1935&q=80";

//цвет заголовка, по дефолту темный #212121
$title_color = "#1b389e"; //hex #ff9920


// тут уже проверки пошли
if(!$background_img) {
	$background_img = "https://images.unsplash.com/photo-1535473895227-bdecb20fb157?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1974&q=80";
}

if (!$title_color) {
	$title_color = "#212121";
}

?>
<!doctype html>
<html lang="ru" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Сайт скоро откроется">
    <title>Азбука Севера — сайт заработает совсем скоро</title>

    <!-- Bootstrap core CSS -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap-grid.min.css" rel="stylesheet" crossorigin="anonymous">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400&display=swap" rel="stylesheet">

    <link rel="shortcut icon" href="https://azbuka-severa.ru/favicon.ico" type="image/x-icon" />

    <style>
    	.w-100{
    		width:100%;
    	}
    	.h-100vh{
    		height:100vh;
    	}
    	.h-100{
    		height:100%;
    	}
    	.bg{
    		background-image: url(<? echo $background_img; ?>);
    		background-size: cover;
    		background-position: center;
    	}
    	.logo{
    		max-width: 350px;
    	  max-height: 50px;
    	}
    	.sub-title{
    		font-family: 'Roboto', sans-serif;
		    font-weight: 400;
		    font-size: 1.3rem;
    	}
    	.h1{
    		font-family: 'Montserrat', sans-serif;
		    font-weight: bold;
		    font-size: 3.6rem;
		    line-height: 1;
		    color:<? echo $title_color; ?>;
    	}
    	.footer{
    		font-family: 'Montserrat', sans-serif;
    	}
    	.link{
    		color:#0358FF;
    		text-decoration: none;
    	}
    	.link--footer {
    		color:#0358FF;
    		text-decoration: none;
    	}
    	.sub-info{
    		font-family: 'Roboto', sans-serif;
		    font-weight: 500;
    	}

    	.footer__socials {
			    margin: 1rem 0;
			}
			.social {
		    display: -webkit-box;
		    display: -ms-flexbox;
		    display: flex;
		    -ms-flex-wrap: wrap;
		    flex-wrap: wrap;
		    color: var(--switcher);
		    margin: 0 -3px;
		    padding: 3px 0;
		    list-style: none;
			}
			.social__item {
			    margin: 10px;
			}
			
			
			.social__icon {
			    fill: currentColor;
			    stroke-width: 0;
			    fill-rule: evenodd;
			    clip-rule: evenodd;
			    height: 20px;
			    width: 20px;
			    z-index: 2;
			}
			.social__link {
			    position: relative;
			    display: -webkit-box;
			    display: -ms-flexbox;
			    display: flex;
			    -webkit-box-pack: center;
			    -ms-flex-pack: center;
			    justify-content: center;
			    -webkit-box-align: center;
			    -ms-flex-align: center;
			    align-items: center;
			    width: 50px;
			    height: 50px;
			    font-size: 18px;
			    background-color: #bcc0c8;
			    color: currentColor;
			    border-radius: 50%;
			    overflow: hidden;
			}
			.social__link {
			    background: #1b389e;
			    color: #fff;
			}
			.social__link--whatsapp {
			    background: #36D670;
			}
			.social__link--telegram {
			    background: #27A6E6;
			}

     @media (max-width: 640px){
        .h1{
       	    font-size: 2rem;
    		line-height: 1.1;
        }
        .sub-title{
		    font-size: 1.1rem;
    	}
    	.bg{
    		height: 240px;
    	}
    	.footer {
		    font-size: 0.8rem;
		}
		.sub-info {
		    font-size: 0.9rem;
		}
		.h-100vh{
			height: 95vh;
		}
      }
    </style>
    
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/dmhendricks/bootstrap-grid-css@4.1.3/dist/css/bootstrap-grid.min.css" />
  </head>
  <body class="py-0 px-0 my-0 mx-0">
    
		<div class="row w-100 h-100vh justify-content-between mx-0">
		  <div class="col-12 col-md-5 bg d-md-block d-none"></div>
		  <div class="col-12 col-md-6">
		  	<div class="row align-content-between h-100 py-5">
		  		<div class="col-12">
		  			<? if($logo_img) { ?>
		  			<img class="logo" src="<? echo $logo_img; ?>">
		  			<? } ?>
		  		</div>
		  		<div class="col-12">
		  			<div class="sub-title mb-2">Делаем сайт лучше и быстрее</div>
		  			<div class="h1">Сайт заработает совсем скоро</div>
		  			
		  			<? if($phone OR $email OR $adres) { ?>
		  			<div class="sub-info mt-5">
		  				
		  				<? if($phone) { ?>
			  				<div class="sub-info--phone mb-2">
			  					<a class="link" href="tel:<? echo str_replace(" ", '', $phone); ?>"><? echo $phone; ?></a>
			  				</div>
			  			<? } if($adres) { ?>
			  				<div class="sub-info--adres mb-2">
			  					<? echo $adres; ?>
			  				</div>
			  			<? } if($email) { ?>
			  				<div class="sub-info--email mb-2">
			  					<a class="link" href="mailto:<? echo $email; ?>"><? echo $email; ?></a>
			  				</div>
			  			<? } ?>

			  				<div class="footer__socials">
									<ul class="social">
  
									  <li class="social__item">
									    <a href="https://t.me/azbukasevera" target="_blank" class="social__link social__link--telegram" data-noicon="">
									      <svg class="social__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43 43">
									        <path d="M2.95603 19.3408C14.4987 14.3118 22.1957 10.9964 26.0469 9.39457C37.0428 4.82099 39.3276 4.02651 40.8169 4.00028C41.1444 3.99451 41.8768 4.07568 42.3512 4.46062C42.7517 4.78566 42.8619 5.22473 42.9147 5.5329C42.9674 5.84107 43.0331 6.54308 42.9809 7.09162C42.385 13.3525 39.8067 28.546 38.495 35.5583C37.9399 38.5255 36.8471 39.5203 35.789 39.6177C33.4897 39.8293 31.7437 38.0981 29.5166 36.6383C26.0318 34.3539 24.0631 32.9319 20.6804 30.7028C16.7711 28.1266 19.3053 26.7107 21.5332 24.3968C22.1163 23.7912 32.2472 14.5763 32.4433 13.7404C32.4678 13.6358 32.4906 13.2461 32.2591 13.0403C32.0276 12.8345 31.6859 12.9049 31.4393 12.9609C31.0898 13.0402 25.5227 16.7199 14.738 23.9998C13.1578 25.0849 11.7265 25.6136 10.4441 25.5859C9.03031 25.5554 6.31084 24.7866 4.2892 24.1294C1.80957 23.3234 -0.161192 22.8972 0.0104172 21.5283C0.0998017 20.8153 1.08167 20.0862 2.95603 19.3408Z"></path>
									      </svg>
									    </a>
									  </li>
									  <li class="social__item">
									    <a href="https://wa.me/79152975864" class="social__link social__link--whatsapp" target="_blank" data-noicon="">
									      <svg class="social__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43 43">
									        <path d="M36.5596 6.24844C32.5379 2.21719 27.1821 0 21.4904 0C9.74219 0 0.182366 9.55982 0.182366 21.308C0.182366 25.0609 1.16138 28.7275 3.02344 31.9621L0 43L11.2971 40.0342C14.4069 41.733 17.9103 42.6257 21.4808 42.6257H21.4904C33.229 42.6257 43 33.0658 43 21.3176C43 15.6259 40.5812 10.2797 36.5596 6.24844ZM21.4904 39.0359C18.3038 39.0359 15.1844 38.1817 12.4681 36.5692L11.825 36.1853L5.12545 37.9417L6.91071 31.4054L6.48839 30.7335C4.71272 27.9116 3.7817 24.6578 3.7817 21.308C3.7817 11.5467 11.729 3.59933 21.5 3.59933C26.2319 3.59933 30.6759 5.44219 34.0161 8.79196C37.3562 12.1417 39.4103 16.5857 39.4007 21.3176C39.4007 31.0886 31.2518 39.0359 21.4904 39.0359ZM31.2038 25.7712C30.6759 25.5025 28.0556 24.2163 27.5661 24.0435C27.0766 23.8612 26.7214 23.7748 26.3663 24.3123C26.0112 24.8498 24.9938 26.04 24.677 26.4047C24.3699 26.7598 24.0531 26.8078 23.5252 26.5391C20.3962 24.9746 18.3422 23.746 16.2786 20.2042C15.7315 19.2636 16.8257 19.3308 17.8431 17.296C18.0158 16.9408 17.9295 16.6337 17.7951 16.365C17.6607 16.0962 16.5953 13.4759 16.1538 12.4105C15.7219 11.3739 15.2804 11.5179 14.954 11.4987C14.6469 11.4795 14.2917 11.4795 13.9366 11.4795C13.5815 11.4795 13.0056 11.6138 12.5161 12.1417C12.0266 12.6792 10.654 13.9654 10.654 16.5857C10.654 19.206 12.5641 21.74 12.8232 22.0951C13.092 22.4502 16.5761 27.8252 21.9223 30.1384C25.3009 31.5973 26.6254 31.7221 28.3147 31.4725C29.3417 31.319 31.4629 30.1864 31.9045 28.9386C32.346 27.6908 32.346 26.6254 32.2116 26.4047C32.0868 26.1647 31.7317 26.0304 31.2038 25.7712Z"></path>
									      </svg>
									    </a>
									  </li>
									  
									 
									</ul>			
								</div>

			  		</div>
			  		<? } ?>

		  		</div>
		  		<div class="col-12 bg"></div>
		  		<div class="col-12 footer"><a class="link--footer" href="https://novoxpro.ru/" target="_blank">NOVOX PRO</a> — создаем сайты и заботимся о них</div>
		  	</div>
		  </div>
		</div>
    
  </body>
</html>
