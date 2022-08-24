 window.addEventListener('load', function()
{
    var turn = null;

    getXmlHttpRequestObject = function()
    {
        if(!turn)
        {               
            // Create a new XMLHttpRequest object 
            turn = new XMLHttpRequest();
        }
        return turn;
    };

    updateLiveData = function()
    {
        var nowt = new Date();
        // Date string is appended as a query with live data 
        // for not to use the cached version 
        var urlt = 'getTurn.php?' + nowt.getTime();
        turn = getXmlHttpRequestObject();
        turn.onreadystatechange = evenHandler;
        // asynchronous requests
        turn.open("GET", urlt, true);
        // Send the request over the network
        turn.send(null);
    };

    updateLiveData();

    function evenHandler()
    {
        // Check response is ready or not
        if(turn.readyState == 4 && turn.status == 200)
        {
            dataDivt = document.getElementById('liveTurn');
            // Set current data text
            dataDivt.innerHTML = turn.responseText;
            // Update the live data every 1 sec
            setTimeout(updateLiveData(), 1000);

        }
    }

});