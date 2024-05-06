<!DOCTYPE html>
<html>
<head>
    <title>TUS Upload Demo</title>
    <script src="https://unpkg.com/tus-js-client@1.8.0/dist/tus.js"></script>
</head>
<body>
<input type="file" id="file-input">
<button onclick="upload()">Upload</button>

<script>
    function upload() {
        var input = document.getElementById('file-input');
        var file = input.files[0];

        if (!file) {
            console.log("No file selected");
            return;
        }

        var upload = new tus.Upload(file, {
            endpoint: 'http://localhost:8200/files',
            retryDelays: [0, 1000, 3000, 5000],
            chunkSize: 1024,
            metadata: {
                filename: file.name,
                filetype: file.type,
            },
            onError: function (error) {
                console.log("Failed because: " + error)
            },
            onProgress: function (bytesUploaded, bytesTotal) {
                var percentage = (bytesUploaded / bytesTotal * 100).toFixed(2)
                console.log(bytesUploaded, bytesTotal, percentage + "%")
            },
            onSuccess: function () {
                console.log("Download %s from %s", upload.file.name, upload.url)
            }
        })

        upload.start();
        // localStorage.clear();
    }

    // localStorage.clear();
</script>
</body>
</html>