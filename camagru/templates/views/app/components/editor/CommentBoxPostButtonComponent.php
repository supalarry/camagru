<div class="mx-2 mt-2 lg:mx-0 md:flex">
    <div class="md:w-3/4 md:pr-2">
        <textarea id="description" class="h-full mx-auto w-full p-3 border-2 border-gray-900 rounded-lg"
                  rows="2" placeholder="description"></textarea>
    </div>
    <div onclick="post()"
         class="p-2 border-2 border-gray-900 w-full rounded-lg text-center md:w-1/4 flex justify-center align-center flex-col cursor-pointer">
        <h1 id="postHeading" class="text-gray-900 text-2xl tracking-wider">Post</h1>
        <img id="loadingIcon" class="hidden w-16 mx-auto" src='templates/views/app/img/PostSubmitLoading.svg'>
    </div>
</div>

<script>
    function showLoadingIcon() {
        document.getElementById('postHeading').classList.add('hidden');
        document.getElementById('loadingIcon').style.display = 'block';
    }

    function hideLoadingIcon() {
        document.getElementById('loadingIcon').style.display = 'none';
        document.getElementById('postHeading').classList.remove('hidden');
    }

    function isCanvaBlank(canvaId) {
        const canva = document.getElementById(canvaId);
        return !canva.getContext('2d')
            .getImageData(0, 0, canva.width, canva.height).data
            .some(channel => channel !== 0);
    }

    async function post() {
        if (document.getElementById('description').value.length == 0) {
            alert('Please, fill in description 😎');
            return;
        }
        if (document.getElementById('editorUpload').style.display === 'block') {
            await takeUploadPicture();
        }
        if (isCanvaBlank('generatedImageCanva')) {
            alert('Please, take a picture first 😊');
            return;
        }
        const description = document.getElementById('description').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        showLoadingIcon();
        const imageUrl = await getImageUrl();
        const rawResponse = await fetch('http://localhost:8098/post', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({imageUrl, description})
        });
        hideLoadingIcon();

        enableFrameSelection();
        const content = await rawResponse.json();
        const id = content.image.id;
        let uploadedPostContainer = createUploadedPostContainer({id, imageUrl, description,});

        if (document.getElementById('no-uploads')) {
            document.getElementById('no-uploads').remove();
        }

        document.getElementById('uploaded-posts').prepend(uploadedPostContainer);

        /* clear for next image */
        clearCanva('generatedImageCanva');
        removeOpacityPropertyFromSelectedFrames();
        showSnapButton();
        removeFrameIsAppliedClass();

        uploadData = {
            background: '',
            frames: {}
        };

        removeOverlayFrames();
        document.getElementById('description').value = '';
    }
</script>

<style>
    textarea:focus {
        outline: 0 !important;
    }

    button:focus {
        outline: 0 !important;
    }
</style>
