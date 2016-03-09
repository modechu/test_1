//Function to preload and swap rollover buttons in main navigation
if (document.images) {
	homeOff = new Image
	homeOver = new Image
	storeOff = new Image
	storeOver = new Image
	servicesOff = new Image
	servicesOver = new Image
	aboutOff = new Image
	aboutOver = new Image
	gradesOff = new Image
	gradesOver = new Image
	brandsOff = new Image
	brandsOver = new Image
	accessOff = new Image
	accessOver = new Image
	contactOff = new Image
	contactOver = new Image
	linksOff = new Image
	linksOver = new Image
	sitemapOff = new Image
	sitemapOver = new Image

	homeOff.src = "siteimages/menu_home.gif"
	homeOver.src = "siteimages/menu_home_ro.gif"
	storeOff.src = "siteimages/menu_store.gif"
	storeOver.src = "siteimages/menu_store_ro.gif"
	servicesOff.src = "siteimages/menu_services.gif"
	servicesOver.src = "siteimages/menu_services_ro.gif"
	aboutOff.src = "siteimages/menu_about.gif"
	aboutOver.src = "siteimages/menu_about_ro.gif"
	gradesOff.src = "siteimages/menu_grades.gif"
	gradesOver.src = "siteimages/menu_grades_ro.gif"
	brandsOff.src = "siteimages/menu_brands.gif"
	brandsOver.src = "siteimages/menu_brands_ro.gif"
	accessOff.src = "siteimages/menu_accessories.gif"
	accessOver.src = "siteimages/menu_accessories_ro.gif"
	contactOff.src = "siteimages/menu_contact.gif"
	contactOver.src = "siteimages/menu_contact_ro.gif"
	linksOff.src = "siteimages/menu_links.gif"
	linksOver.src = "siteimages/menu_links_ro.gif"
	sitemapOff.src = "siteimages/menu_sitemap.gif"
	sitemapOver.src = "siteimages/menu_sitemap_ro.gif"
}

else {
	homeOff = ""
	homeOver = ""
	document.homeRO = ""
	storeOff = ""
	storeOver = ""
	document.storeRO = ""
	servicesOff = ""
	servicesOver = ""
	document.servicesRO = ""
	aboutOff = ""
	aboutOver = ""
	document.aboutRO = ""
	gradesOff = ""
	gradesOver = ""
	document.gradesRO = ""
	brandsOff = ""
	brandsOver = ""
	document.brandsRO = ""
	accessOff = ""
	accessOver = ""
	document.accessRO = ""
	contactOff = ""
	contactOver = ""
	document.contactRO = ""
	linksOff = ""
	linksOver = ""
	document.linksRO = ""
	sitemapOff = ""
	sitemapOver = ""
	document.sitemapRO = ""
}

//Function to open popup window
function custWindow(doc,w,h,align,valign,scroll) {
	if (custWindow.arguments.length < 6) {
		var scroll = 'no'
		}
	if (custWindow.arguments.length < 4) {
		var align = 'c'
		var valign = 'm'
		}
	topPos=0
	leftPos=0
	if (screen) {
		if (align == 'c') {
			leftPos = screen.width/2 - w/2
			} else {
			if (align == 'r') {
				leftPos = screen.width - w - 12
				}
			}
		if (valign == 'm') {
			topPos = screen.height/2 - h/2
			} else {
			if (valign == 'b') {
				topPos = screen.height - h - 5
					}
				}
			}
		custWin = window.open (doc,'custWin','toolbar=no,location=no,scrollbars='+scroll+',resizable=yes,width='+w+',height='+h+',left='+leftPos+',top='+topPos+'')
		custWin.document.close()
		custWin.focus()
	}
