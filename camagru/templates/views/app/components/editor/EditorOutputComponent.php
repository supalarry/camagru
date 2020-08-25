<div class="relative" id="editorView">
    <div id="editorUpload" class="relative overflow-hidden bg-red-600 lg:rounded-lg"
         style="padding-bottom: 56.25%">
        <input type="file" name="inputFile" id="inputFile" class="hidden">
        <img src="" id="previewImage" class="absolute object-cover w-full h-full lg:rounded-lg z-0"/>
    </div>
    <div id="editorCamera" class="relative">
        <video id="video" class="lg:rounded-lg" playsinline autoplay></video>
    </div>
    <img onclick="takePicture()" id="snap" src="templates/views/app/img/Snap.png"
         class="mx-auto cursor-pointer w-16 my-4 md:mt-0 md:pb-4 md:absolute md:bottom-0 md:left-0 md:right-0 z-10"/>
    <img id="retake" src="templates/views/app/img/Retake.png"
         class="hidden mx-auto cursor-pointer w-16 my-4 md:mt-0 md:pb-4 md:absolute md:bottom-0 md:left-0 md:right-0 z-10"/>
    <canvas id="generatedImageCanva" width="1280" height="720"
            class="absolute top-0 left-0 w-full"></canvas>
</div>

<script>
    let uploadData = {
        background: '',
        frames: {}
    };

    function removeOverlayFrames() {
        let parent = document.getElementById('editorCamera').style.display === 'block' ? document.getElementById('editorCamera') : document.getElementById('editorUpload');

        const children = parent.childNodes;
        const appliedFramesIds = [];


        for (let i = 0; i < children.length; i++) {
            if (children[i].id && children[i].id.match(/.*-Applied/)) {
                appliedFramesIds.push(children[i].id);
            }
        }
        appliedFramesIds.forEach(frameId => document.getElementById(frameId).remove());
    }

    function showSnapButton() {
        document.getElementById('retake').classList.add('hidden');
        document.getElementById('snap').classList.remove('hidden');
        document.getElementById('snap').classList.add('visible');
    }

    function showRetakeButton() {
        document.getElementById('snap').classList.add('hidden');
        document.getElementById('retake').classList.remove('hidden');
        document.getElementById('retake').classList.add('visible');
    }

    function clearCanva(canvaId) {
        let canva = document.getElementById(canvaId);
        canva.getContext('2d').clearRect(0, 0, canva.width, canva.height);
    }

    function drawBackgroundImageOnCanva(canvaId) {
        if (document.getElementById('editorCamera').style.display === 'block') {
            let canva = document.getElementById(canvaId);
            canva.getContext('2d').drawImage(video, 0, 0, 1280, 720);
        } else {
            let canva = document.getElementById(canvaId);
            let previewImage = document.getElementById('previewImage');
            canva.getContext('2d').drawImage(previewImage, 0, 0, 1280, 720);
        }
    }

    function getCanvaData(canvaId) {
        let canva = document.getElementById(canvaId);
        return canva.toDataURL('image/jpeg');
    }

    async function takePicture() {
        clearCanva('generatedImageCanva');
        drawBackgroundImageOnCanva('generatedImageCanva');
        blockFrameSelection();
        removeOpacityPropertyFromSelectedFrames();
        showRetakeButton();
        document.getElementById('retake').addEventListener('click', function (event) {
            clearCanva('generatedImageCanva');
            addOpacityPropertyToSelectedFrames();
            enableFrameSelection();
            showSnapButton();
        });
    }

    async function takeUploadPicture() {
        clearCanva('generatedImageCanva');
        drawBackgroundImageOnCanva('generatedImageCanva');
    }

    async function getImageUrl() {
        uploadData.background = getCanvaData('generatedImageCanva');
        const rawResponse = await fetch('http://localhost:8098/generateImage', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(uploadData)
        });
        const response = await rawResponse.json();
        if (response) {
            return response.imageUrl;
        }
        return '';
    }

    function toggleFrame(id, imgSrc) {
        let parent = document.getElementById('editorCamera').style.display === 'block' ? document.getElementById('editorCamera') : document.getElementById('editorUpload');

        const children = parent.childNodes;
        for (let i = 0; i < children.length; i++) {
            if (children[i].id === id + '-Applied') {
                document.getElementById(id).classList.remove("opacity-25");
                document.getElementById(id).classList.remove("frameIsApplied");
                document.getElementById(children[i].id).remove();
                delete uploadData.frames[id];
                return;
            }
        }

        let frame = createImage(imgSrc, id + '-Applied', 'h-full w-full absolute top-0 left-0 z-10');
        uploadData.frames[id] = imgSrc;

        document.getElementById(id).classList.add("opacity-25");
        document.getElementById(id).classList.add("frameIsApplied");
        parent.prepend(frame);
    }

</script>

<style>
    #editorCamera {
        display: none;
    }

    #editorUpload {
        display: none;
    }
</style>
