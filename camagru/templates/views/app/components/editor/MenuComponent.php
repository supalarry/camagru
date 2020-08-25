<div id="editorSelect" class="flex flex-col h-screen items-center justify-around lg:flex-row">
    <div id="cameraDiv"
         class="flex justify-around items-center text-center h-full w-full lg:w-1/2 opacity-75 hover:opacity-100">
        <div class="mx-4 bg-white p-16 rounded-md cursor-pointer max-w-sm" onclick="showEditorCamera()">
            <h1 class="heading text-5xl">Camera</h1>
            <h2>Take a photo for editing via your camera</h2>
        </div>
    </div>
    <div id="uploadDiv"
         class="flex justify-around items-center text-center h-full w-full lg:w-1/2 opacity-75 hover:opacity-100">
        <div class="mx-4 bg-white p-16 rounded-md cursor-pointer max-w-sm" onclick="showEditorUpload()">
            <h1 class="heading text-5xl">Upload</h1>
            <h2>Upload a photo for editing from your device</h2>
        </div>
    </div>
</div>

<script>
    async function showEditorCamera() {
        try {
            await initCamera();
            await loadUploadedPosts();
            document.getElementById('editorSelect').style.display = 'none';
            document.getElementById('editorUpload').style.display = 'none';
            document.getElementById('editor').style.display = 'block';
            document.getElementById('editorCamera').style.display = 'block';
        } catch (e) {}
    }

    async function initCamera() {
        const video = document.getElementById('video');

        const constraints = {
            audio: false,
            video: {
                width: 1280, height: 720
            }
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            handleSuccess(stream);
            return Promise.resolve();
        } catch (error) {
            return Promise.reject();
        }

        function handleSuccess(stream) {
            window.stream = stream;
            video.srcObject = stream;
        }
    }

    async function showEditorUpload() {
        const inputFile = document.getElementById('inputFile');
        const previewImage = document.getElementById('previewImage');
        inputFile.addEventListener("change", async function () {
            const file = this.files[0];
            if (file) {
                await loadUploadedPosts();
                const reader = new FileReader();

                reader.addEventListener("load", function () {
                    previewImage.setAttribute("src", this.result);
                });

                reader.readAsDataURL(file);

                document.getElementById('editorSelect').style.display = 'none';
                document.getElementById('snap').style.display = 'none';
                document.getElementById('retake').style.display = 'none';
                document.getElementById('editorCamera').style.display = 'none';
                document.getElementById('editor').style.display = 'block';
                document.getElementById('editorUpload').style.display = 'block';
            }
        });
        inputFile.click();
    }
</script>

<style>
    #cameraDiv {
        background-image: url('templates/views/app/img/Camera.jpg');
    }

    #uploadDiv {
        background-image: url('templates/views/app/img/Upload.jpg');
    }
</style>
