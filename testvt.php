<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Claim Form</title>
    <script src="https://www.youtube.com/iframe_api"></script>
    <style>
        #loader {
            display: none;
        }
    </style>
</head>
<body>
    <div id="video-placeholder"></div>
    <form id="claim-form" action="" method="post" enctype="multipart/form-data">
        <div class="form-group basic">
            <button type="submit" name="deposit" id="claim-button" class="btn btn-primary btn-block btn-lg" data-bs-dismiss="modal" disabled>Claim</button>
        </div>
    </form>

    <script>
        var player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('video-placeholder', {
                height: '360',
                width: '640',
                videoId: 'uXlWYZ022zU', // Ganti YOUR_VIDEO_ID dengan ID video YouTube yang diinginkan
                playerVars: {
                    'controls': 0, // Menonaktifkan kontrol pemutar
                    'disablekb': 1 // Menonaktifkan kontrol keyboard
                },
                events: {
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.ENDED) {
                document.getElementById('claim-button').disabled = false;
            }
        }

        document.getElementById('claim-form').addEventListener('submit', function(event) {
            if (player.getPlayerState() != YT.PlayerState.ENDED) {
                event.preventDefault();
                alert('Anda harus menonton video sampai selesai sebelum mengklaim.');
            } else {
                loadingForm();
            }
        });

        function loadingForm() {
            document.getElementById("loader").style.display = "";
        }
    </script>
</body>
</html>
