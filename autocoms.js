 window.addEventListener('load', function()
{
    var x = null;

    getXmlHttpRequestObject = function()
    {
        if(!x)
        {               
            // Create a new XMLHttpRequest object 
            x = new XMLHttpRequest();
        }
        return x;
    };

    updateLiveData = function()
    {
        var n = new Date();
        // Date string is appended as a query with live data 
        // for not to use the cached version 
        var url = 'getcoms.php?' + n.getTime();
        x = getXmlHttpRequestObject();
        x.onreadystatechange = evenHandler;
        // asynchronous requests
        x.open("GET", url, true);
        // Send the request over the network
        x.send(null);
    };

    updateLiveData();

    function evenHandler()
    {
        // Check response is ready or not
        if(x.readyState == 4 && x.status == 200)
        {
            dataDiv = document.getElementById('liveComs');
            // Set current data text
            dataDiv.innerHTML = x.responseText;
            // Update the live data every 1 sec
            setTimeout(updateLiveData(), 4000);

        }
    }

});

