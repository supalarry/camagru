<div id="uploaded-posts"
     class="mb-8 flex flex-wrap items-start justify-center max-w-5xl mx-auto xl:w-2/6 xl:pl-2 xl:overflow-scroll xl:content-start">
</div>

<script>
    function createImage(imgSrc, imgId, imgClass) {
        let img = document.createElement('img');
        img.src = imgSrc;
        img.setAttribute('id', imgId);
        img.setAttribute('class', imgClass);
        return img;
    }

    function createDeleteBox(postIdToDelete) {
        let deleteBox = document.createElement('div');
        deleteBox.setAttribute('class', 'cursor-pointer absolute top-0 right-0 rounded-lg bg-red-500 py-2 px-4 mt-2 mr-2 border-2 border-white');
        deleteBox.onclick = async function () {
            const rawResponse = await fetch('http://localhost:8098/deletePost', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({postId: postIdToDelete})
            });
            document.getElementById(postIdToDelete).remove();
            if (document.getElementById('uploaded-posts').childElementCount == 0) {
                let img = createImage('templates/views/app/img/Uploads.jpg', 'no-uploads', 'lg:rounded-lg mt-2 xl:mt-0');
                document.getElementById('uploaded-posts').append(img);
            }
        };
        let deleteX = document.createElement('h1');
        deleteX.setAttribute('class', 'text-white');
        deleteX.innerHTML = 'X';
        deleteBox.append(deleteX);
        return deleteBox;
    }

    function createDescriptionContainer(descriptionContent) {
        let descriptionContainer = document.createElement('div');
        descriptionContainer.setAttribute('class', 'px-8 pb-4 pt-6 shadow rounded-b-lg -mt-2');
        let description = document.createElement('p');
        description.innerHTML = descriptionContent;
        descriptionContainer.append(description);
        return descriptionContainer;
    }

    function createUploadedPostContainer(uploadedPost) {
        let uploadedPostContainer = document.createElement('div');
        uploadedPostContainer.setAttribute('id', uploadedPost.id);
        uploadedPostContainer.setAttribute('class', 'relative mt-2 mb-2 xl:mt-0');

        uploadedPostContainer.append(createImage(uploadedPost.imageUrl, uploadedPost.imageId, 'lg:rounded-t-lg'));
        uploadedPostContainer.append(createDeleteBox(uploadedPost.id));
        uploadedPostContainer.append(createDescriptionContainer(uploadedPost.description));

        return uploadedPostContainer;
    }

    async function loadUploadedPosts() {
        const rawResponse = await fetch('http://localhost:8098/editorUploads', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        });
        const uploadedPosts = await rawResponse.json();

        const uploadedPostsContainer = document.getElementById('uploaded-posts');

        if (!uploadedPosts.length) {
            let noUploadsImg = createImage('templates/views/app/img/Uploads.jpg', 'no-uploads', 'lg:rounded-lg mt-2 xl:mt-0');
            uploadedPostsContainer.append(noUploadsImg);
            return;
        }
        for (let i = 0; i < uploadedPosts.length; i++) {
            uploadedPostsContainer.prepend(createUploadedPostContainer(uploadedPosts[i]));
        }
    }
</script>

<style>
    @media (min-width: 1280px) {
        #uploaded-posts {
            height: 720px;
        }
    }
</style>
