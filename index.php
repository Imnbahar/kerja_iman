<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the POST request
    $scan1 = isset($_POST['scan1']) ? $_POST['scan1'] : null;
    $scan2 = isset($_POST['scan2']) ? $_POST['scan2'] : null;
    $qr_scanned = $_POST['qr_scanned'];
    $text_extracted = $_POST['text_extracted'];

    // Check if SCAN1 and SCAN2 are not null
    if ($scan1 !== null && $scan2 !== null) {
        // Perform database connection 
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "iman";

        // Create a database connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check the database connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and execute the SQL INSERT statement
        $sql = "INSERT INTO data (scan1, scan2, qr_scanned, text_extracted) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $scan1, $scan2, $qr_scanned, $text_extracted);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                echo "Data inserted successfully.";
            } else {
                echo "Error executing the SQL statement: " . $stmt->error;
            }
        } else {
            echo "Error in preparing the SQL statement: " . $conn->error;
        }
    } else {
        echo "SCAN1 and SCAN2 cannot be null.";
    }
} else {
    //echo "Invalid request method.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <link rel="stylesheet" href="style.css">
    <!-- <script src="index.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.0.0/dist/jsQR.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script src="html5-qrcode-master\html5-qrcode-master\minified\html5-qrcode.min.js"></script>
</head>

<body class="grayscale">
    <div class="container vertical-center">
        <div class="item-container">
            <img src="camera.png" alt="" onclick="openCamera('QR','image')">
            <input id="fileInput" name="content" type="file" accept="image/*" capture="camera" style="display: none">
        </div>
        <div class="item-container">
            <a href="history.php">
                <img src="history.png" alt="">
            </a>
        </div>
        <select id="fileLabel" name="type">
            <option value="QR">QR</option>
            <option value="Picture">Item Image</option>
        </select>
        <div id="uploadedFiles"></div>
        <button onclick="submitFiles()">SUBMIT</button>
    </div>

    // ...

    <script>
        'use strict';

        let selectedOption;
        let scan1 = "QR"; 
        let scan2 = "Image";
        let qrData = null;
        let imageData = null;


        function openCamera(option) {
            selectedOption = option;
            if (selectedOption === 'QR') {
                document.getElementById('fileInput').click();
            } else if (selectedOption === 'Picture') {
                document.getElementById('fileInput').click();
            }
        }

        function submitFiles() {
            selectedOption = document.getElementById('fileLabel').value;
            const fileInput = document.getElementById('fileInput');
            const uploadedFilesContainer = document.getElementById('uploadedFiles');

            if (selectedOption === '') {
                uploadedFilesContainer.innerHTML = 'Please select a category.';
            } else if (selectedOption === 'Picture') {
                processImages(fileInput, uploadedFilesContainer);
            } else if (selectedOption === 'QR') {
                processQRCode(fileInput, uploadedFilesContainer);
            }
        }

        function processImages(fileInput, uploadedFilesContainer) {
            const files = fileInput.files;

            if (files.length > 0) {
                uploadedFilesContainer.innerHTML = '<b>Uploaded image files:</b><br>';

                for (let i = 0; i < files.length; i++) {
                    const fileType = files[i].type;
                    if (fileType === 'image/jpeg' || fileType === 'image/png') {
                        uploadedFilesContainer.innerHTML += `${files[i].name}<br>`;
                        readImageText(files[i], uploadedFilesContainer);
                        if (scan1 === null) {
                            scan1 = files[i];
                        } else if (scan2 === null) {
                            scan2 = files[i];
                        }
                    } else {
                        uploadedFilesContainer.innerHTML += `${files[i].name} (Unsupported file type)<br>`;
                    }
                }
            }
        }

        function readImageText(file, uploadedFilesContainer) {
            const reader = new FileReader();

            reader.onload = function(event) {
                const image = new Image();

                image.src = event.target.result;

                image.onload = function() {
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = image.width;
                    canvas.height = image.height;
                    context.drawImage(image, 0, 0, image.width, image.height);

                    Tesseract.recognize(
                        canvas,
                        'eng', {
                            logger: (m) => console.log(m),
                        }
                    ).then(({
                        data: {
                            text
                        }
                    }) => {
                        console.log('Extracted text:', text);
                        uploadedFilesContainer.innerHTML += `<br><b>Extracted text:</b><br>${text}<br>`;
                        imageData = text;
                        checkAndSendData();
                    }).catch((error) => {
                        console.error('Error processing image:', error);
                        uploadedFilesContainer.innerHTML += `Error processing image: ${error.message}<br>`;
                    });
                };
            };

            reader.readAsDataURL(file);
        }

        function processQRCode(fileInput, uploadedFilesContainer) {
            const files = fileInput.files;

            if (files.length > 0) {
                uploadedFilesContainer.innerHTML = '<b>Scanned QR codes:</b><br>';

                for (let i = 0; i < files.length; i++) {
                    const fileType = files[i].type;
                    if (fileType === 'image/jpeg' || fileType === 'image/png') {
                        decodeQRCode(files[i], uploadedFilesContainer);
                    } else {
                        uploadedFilesContainer.innerHTML += `${files[i].name} (Unsupported file type)<br>`;
                    }
                }
            } else {
                uploadedFilesContainer.innerHTML = 'No image files uploaded.';
            }
        }

        function decodeQRCode(file, uploadedFilesContainer) {
            const image = new Image();

            image.onerror = function() {
                console.error('Error loading image:', file.name);
                uploadedFilesContainer.innerHTML += `Error loading image: ${file.name}<br>`;
            };

            image.src = URL.createObjectURL(file);

            image.onload = function() {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = image.width;
                canvas.height = image.height;
                context.drawImage(image, 0, 0, image.width, image.height);

                const imageData = context.getImageData(0, 0, image.width, image.height);

                const code = jsQR(imageData.data, image.width, image.height);

                if (code) {
                    console.log('QR Code Contents:', code.data);
                    uploadedFilesContainer.innerHTML += `<br><b>QR Code Contents:</b><br>${code.data}<br>`;
                    qrData = code.data;
                    checkAndSendData();
                } else {
                    console.error('QR code not found in:', file.name);
                    uploadedFilesContainer.innerHTML += `QR code not found in: ${file.name}<br>`;
                }
            };
        }

        function checkAndSendData() {
            if (qrData !== null && imageData !== null) {
                // Both QR code and image data are available, send to the server
                sendToServer(qrData, imageData);
            }
        }

        function sendToServer(qrData, imageData) {
            const xhr = new XMLHttpRequest();
            const url = 'index.php';

            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Handle the server's response if needed
                    console.log('Server Response:', xhr.responseText);
                }
            };

            // Initialize scan1 and scan2 here with default values
            if (scan1 === null) {
                scan1 = "QR";
            }
            if (scan2 === null) {
                scan2 = "Image";
            }

            // You can send both QR and image data to the server here
            const data = `scan1=${encodeURIComponent(scan1)}&scan2=${encodeURIComponent(scan2)}&qr_scanned=${encodeURIComponent(qrData)}&text_extracted=${encodeURIComponent(imageData)}`;
            xhr.send(data);
        }
    </script>

    // ...

</body>

</html>