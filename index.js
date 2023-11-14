'use strict';

let selectedOption;

function openCamera() {
    document.getElementById('fileInput').click();
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
        scanQRCode(fileInput, uploadedFilesContainer);
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
                extractTextFromImage(files[i], uploadedFilesContainer);
            } else {
                uploadedFilesContainer.innerHTML += `${files[i].name} (Unsupported file type)<br>`;
            }
        }
    }
}

function extractTextFromImage(file, uploadedFilesContainer) {
    const image = new Image();

    image.onerror = function () {
        console.error('Error loading image:', file.name);
        uploadedFilesContainer.innerHTML += `Error loading image: ${file.name}<br>`;
    };

    image.src = URL.createObjectURL(file);

    image.onload = function () {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = image.width;
        canvas.height = image.height;
        context.drawImage(image, 0, 0, image.width, image.height);

        Tesseract.recognize(
            canvas,
            'eng',
            {
                logger: (m) => console.log(m),
            }
        ).then(({ data: { text } }) => {
            console.log('Extracted text:', text);
            uploadedFilesContainer.innerHTML += `<br><b>Extracted text:</b><br>${text}<br>`;
            // Send the extracted text to the server using AJAX
            sendToServer(selectedOption, text);
        }).catch((error) => {
            console.error('Error processing image:', error);
            uploadedFilesContainer.innerHTML += `Error processing image: ${error.message}<br>`;
        });
    };
}

function scanQRCode(fileInput, uploadedFilesContainer) {
    const files = fileInput.files;

    if (files.length > 0) {
        uploadedFilesContainer.innerHTML = '<b>Scanned QR codes:</b><br>';

        for (let i = 0; i < files.length; i++) {
            const fileType = files[i].type;
            if (fileType === 'image/jpeg' || fileType === 'image/png') {
                decodeQRCode(files[i], uploadedFilesContainer, selectedOption); // Pass selectedOption
            } else {
                uploadedFilesContainer.innerHTML += `${files[i].name} (Unsupported file type)<br>`;
            }
        }
    } else {
        uploadedFilesContainer.innerHTML = 'No image files uploaded.';
    }
}

// Update decodeQRCode function to accept the selectedOption parameter
function decodeQRCode(file, uploadedFilesContainer, selectedOption) {
    const image = new Image();

    image.onerror = function () {
        console.error('Error loading image:', file.name);
        uploadedFilesContainer.innerHTML += `Error loading image: ${file.name}<br>`;
    };

    image.src = URL.createObjectURL(file);

    image.onload = function () {
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
            // Send the QR code data to the server using AJAX
            sendToServer(selectedOption, code.data); // Pass selectedOption here
        } else {
            console.error('QR code not found in:', file.name);
            uploadedFilesContainer.innerHTML += `QR code not found in: ${file.name}<br>`;
        }
    };
}

function isValidURL(str) {
    const pattern = new RegExp('^(https?:\\/\\/)?' +
        '([a-z\\d.-]+\\.[a-z]{2,4})' +
        '([-a-zA-Z0-9:%_+.,~#?&//=]*)$', 'i');
    return pattern.test(str);
}

// Function to send data to the server using AJAX
function sendToServer(type, content) {
    let scan1 = type;
    let scan2 = type; // Assign the same value as type to scan2

    let qr_scanned = null;
    let text_extracted = null;

    if (type === 'QR') {
        qr_scanned = content;
    } else if (type === 'Picture') {
        text_extracted = content;
    }

    const xhr = new XMLHttpRequest();
    const url = 'index.php'; // Replace with the actual URL of your server script

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Handle the server's response if needed
            // console.log('Server Response:', xhr.responseText);
        }
    };
    const data = `scan1=${encodeURIComponent(scan1)}&scan2=${encodeURIComponent(scan2)}&qr_scanned=${encodeURIComponent(qr_scanned)}&text_extracted=${encodeURIComponent(text_extracted)}`;

    xhr.send(data);
}
