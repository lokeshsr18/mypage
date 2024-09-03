<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DDoS Mitigation with Blockchain</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>DDoS Mitigation with Blockchain</h1>
    <button id="sendRequest">Send Request</button>
    <button id="simulateDDoS">Simulate DDoS</button>
    <div id="result"></div>

    <script>
        $(document).ready(function() {
            $('#sendRequest').click(function() {
                sendRequest();
            });

            $('#simulateDDoS').click(function() {
                simulateDDoS();
            });
        });

        function sendRequest() {
            $.post('request.php', { action: 'sendRequest' }, function(response) {
                $('#result').append('<p>' + response + '</p>');
            });
        }

        function simulateDDoS() {
            for (let i = 0; i < 150; i++) {
                setTimeout(function() {
                    sendRequest();
                }, i * 100);
            }
        }
    </script>
</body>
</html>
