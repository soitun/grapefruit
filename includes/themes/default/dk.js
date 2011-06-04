function setContainerWidth() {
	var width;

	if (typeof window.innerWidth != 'undefined') {
		width = window.innerWidth;
	}
	else if (typeof document.documentElement != 'undefined'
		&& typeof document.documentElement.clientWidth !=
		'undefined' && document.documentElement.clientWidth != 0) {
			width = document.documentElement.clientWidth;
	}
	else {
		width = document.getElementsByTagName('body')[0].clientWidth;
	}

	var html = document.getElementsByTagName('html')[0];
	var container = document.getElementById("container");

	var numCol = (width / 350);

	numCol = numCol % 1 > .5 ? numCol.toFixed(0) - 1 : numCol.toFixed(0);

	var newWidth = (350 * numCol) + 10;

	container.style.width=newWidth + "px";

}

window.onload = setContainerWidth
window.onresize = setContainerWidth