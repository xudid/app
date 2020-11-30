function InitCollapsible()
{
	var collapsibles = document.getElementsByClassName('collapsible-header');
	var i;

	for (i = 0; i < collapsibles.length; i++)
	{
		collapsibles[i]
			.addEventListener(
				'click',
				function() {
					this.classList.toggle('active');
					var content = this.nextElementSibling;
					let caret = this.getElementsByClassName('caret');
					caret.innerHTML = 'expand_more';
					console.log(caret);
					if (content.style.maxHeight)
					{
						content.style.maxHeight = null;
						content.style.padding = '0em';
					} else {
						content.style.maxHeight = content.scrollHeight + 20+'px';
						// content.style.background = 'lightgray';
						content.style.padding = '1em';
						content.style.overflow = 'auto' ;
					}
				});
	}
}
