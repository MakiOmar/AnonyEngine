<?php
/**
 * Share by email styles
 *
 * @package AnonyEngine
 */

defined( 'ABSPATH' ) || die();
?>

<style>
/* arabic */
@font-face {
	font-family: 'Tajawal';
	font-style: normal;
	font-weight: 500;
	font-display: swap;
	src: local('Tajawal Medium'), local('Tajawal-Medium'), url(https://fonts.gstatic.com/s/tajawal/v3/Iurf6YBj_oCad4k1l8KiHrRpiYlJ.woff2) format('woff2');
	unicode-range: U+0600-06FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE80-FEFC;
}
/* latin */
@font-face {
	font-family: 'Tajawal';
	font-style: normal;
	font-weight: 500;
	font-display: swap;
	src: local('Tajawal Medium'), local('Tajawal-Medium'), url(https://fonts.gstatic.com/s/tajawal/v3/Iurf6YBj_oCad4k1l8KiHrFpiQ.woff2) format('woff2');
	unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
html,
body {
	margin: 0 auto !important;
	padding: 0 !important;
	height: 100% !important;
	width: 100% !important;
	background: #f1f1f1;
	direction: <?php echo esc_html( $dir ); ?>
}

/* What it does: Stops email clients resizing small text. */
* {
	-ms-text-size-adjust: 100%;
	-webkit-text-size-adjust: 100%;
}

/* What it does: Centers email on Android 4.4 */
div[style*="margin: 16px 0"] {
	margin: 0 !important;
}
/* What it does: Stops Outlook from adding extra spacing to tables. */
table, td {
	
	mso-table-lspace: 0pt !important;
	mso-table-rspace: 0pt !important;
	
}
/* What it does: Fixes webkit padding issue. */
table {
	border-spacing: 0 !important;
	border-collapse: collapse !important;
	table-layout: fixed !important;
	margin: 0 auto !important;
}

/* What it does: Uses a better rendering method when resizing images in IE. */
img {
	-ms-interpolation-mode:bicubic;
}

/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
a {
	text-decoration: none;
}

/* What it does: A work-around for email clients meddling in triggered links. */
*[x-apple-data-detectors],  /* iOS */
.unstyle-auto-detected-links *,
.aBn {
	border-bottom: 0 !important;
	cursor: default !important;
	color: inherit !important;
	text-decoration: none !important;
	font-size: inherit !important;
	font-family: inherit !important;
	font-weight: inherit !important;
	line-height: inherit !important;
}

/* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
.a6S {
	display: none !important;
	opacity: 0.01 !important;
}

/* What it does: Prevents Gmail from changing the text color in conversation threads. */
.im {
	color: inherit !important;
}

/* If the above doesn't work, add a .g-img class to any image in question. */
img.g-img + div {
	display: none !important;
}

/* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
/* Create one of these media queries for each additional viewport size you'd like to fix */

/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
	u ~ div .email-container {
		min-width: 320px !important;
	}
}
/* iPhone 6, 6S, 7, 8, and X */
@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
	u ~ div .email-container {
		min-width: 375px !important;
	}
}
/* iPhone 6+, 7+, and 8+ */
@media only screen and (min-device-width: 414px) {
	u ~ div .email-container {
		min-width: 414px !important;
	}
}

</style>

	<!-- CSS Reset : END -->

	<!-- Progressive Enhancements : BEGIN -->
<style>

.primary{
	background: #f3a333;
}

.bg_white{
	background: #ffffff;
}
.bg_light{
	background: #fafafa;
}
.bg_black{
	background: #000000;
}
.bg_dark{
	background: rgba(0,0,0,.8);
}
.email-section{
	padding:2.5em;
}

/*BUTTON*/
.btn{
	padding: 10px 15px;
}
.btn.btn-primary{
	border-radius: 30px;
	background: #f3a333;
	color: #ffffff;
}



h1,h2,h3,h4,h5,h6{
	font-family: 'Tajawal', serif;
	color: #000000;
	margin-top: 0;
}

body{
	font-family: 'Tajawal', sans-serif;
	font-weight: 400;
	font-size: 15px;
	line-height: 1.8;
	color: rgba(0,0,0,.4);
}

a{
	color: #f3a333;
}
/*LOGO*/

.logo h1{
	margin: 0;
}
.logo h1 a{
	color: #000;
	font-size: 20px;
	font-weight: 700;
	text-transform: uppercase;
	font-family: 'Tajawal', sans-serif;
}

/*HERO*/
.hero{
	position: relative;
}
.hero .text{
	color: rgba(255,255,255,.8);
}
.hero .text h2{
	color: #ffffff;
	font-size: 30px;
	margin-bottom: 0;
}


/*HEADING SECTION*/
.heading-section h2{
	color: #000000;
	font-size: 28px;
	margin-top: 0;
	line-height: 1.4;
}
.heading-section .subheading{
	margin-bottom: 20px !important;
	display: inline-block;
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: 2px;
	color: rgba(0,0,0,.4);
	position: relative;
}
.heading-section .subheading::after{
	position: absolute;
	left: 0;
	right: 0;
	bottom: -10px;
	content: '';
	width: 100%;
	height: 2px;
	background: #f3a333;
	margin: 0 auto;
}

.heading-section-white{
	color: rgba(255,255,255,.8);
}
.heading-section-white h2{
	font-size: 28px;
	font-family: 'Tajawal';
	line-height: 1;
	padding-bottom: 0;
}
.heading-section-white h2{
	color: #ffffff;
}
.heading-section-white .subheading{
	margin-bottom: 0;
	display: inline-block;
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: 2px;
	color: rgba(255,255,255,.4);
}


.icon{
	text-align: center;
}

/*SERVICES*/
.text-services{
	padding: 10px 10px 0; 
	text-align: center;
}
.text-services h3{
	font-size: 20px;
}

/*BLOG*/
.text-services .meta{
	text-transform: uppercase;
	font-size: 14px;
}

/*TESTIMONY*/
.text-testimony .name{
	margin: 0;
}
.text-testimony .position{
	color: rgba(0,0,0,.3);

}


/*VIDEO*/
.img{
	width: 100%;
	height: auto;
	position: relative;
}
.img .icon{
	position: absolute;
	top: 50%;
	left: 0;
	right: 0;
	bottom: 0;
	margin-top: -25px;
}
.img .icon a{
	display: block;
	width: 60px;
	position: absolute;
	top: 0;
	left: 50%;
	margin-left: -25px;
}



/*COUNTER*/
.counter-text{
	text-align: center;
}
.counter-text .num{
	display: block;
	color: #444;
	font-size: 34px;
	font-weight: 700;
}
.counter-text .name{
	display: block;
	color: rgba(68,68,68,.6);
	font-size: 13px;
}


/*FOOTER*/

.footer{
	color: rgba(255,255,255,.5);

}
.footer .heading{
	color: #ffffff;
	font-size: 20px;
}
.footer ul{
	margin: 0;
	padding: 0;
}
.footer ul li{
	list-style: none;
	margin-bottom: 10px;
}
.footer ul li a{
	color: rgba(255,255,255,1);
}


@media screen and (max-width: 500px) {

	.icon{
		text-align: left;
	}

	.text-services{
		padding-left: 0;
		padding-right: 20px;
		text-align: left;
	}

}

</style>
