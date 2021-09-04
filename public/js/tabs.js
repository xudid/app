function closeTabContainers(tabContainers)
{
    for (let i = 0; i < tabContainers.length; i++) {
        tabContainers[i].style.display = "none";
    }
}

function activeTab()
{
    let activeTabButtons = Array.from(document.getElementsByClassName('tab-button active'));
    activeTabButtons.forEach(
        function(activeTabButton) {
            document.getElementById(activeTabButton.dataset.target).style.display = "block";
        }
    );
}

window.addEventListener('load', function() {
    let tabButtons = Array.from(document.getElementsByClassName('tab-button'));
    let tabContainers = Array.from(document.getElementsByClassName('tab-container'));

    closeTabContainers(tabContainers);
    activeTab();

    tabButtons.forEach(
        function(tabButton) {
            tabButton.onclick = function(event) {
                // Hide all tab containers
                let tabContainers = Array.from(document.getElementsByClassName('tab-container'));
                closeTabContainers(tabContainers);

                // inactive button
                let activeTabButtons = Array.from(document.getElementsByClassName('tab-button active'))
                activeTabButtons.forEach(
                    function (activeTabButton) {
                        activeTabButton.className = activeTabButton.className.replace('active', '');
                    }
                );
                // set active class to button and show target
                document.getElementById(tabButton.dataset.target).style.display = "block";
                event.currentTarget.className += " active";
            };
        }
    );
});