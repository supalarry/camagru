<main id="catalog-posts" class="flex flex-wrap items-center justify-center mx-auto"></main>

<script>
    function createImage(imgSrc, imgId, imgClass) {
        let img = document.createElement('img');
        img.src = imgSrc;
        img.setAttribute('id', imgId);
        img.setAttribute('class', imgClass);
        return img;
    }

    function createDescriptionContainer(description) {
        let descriptionContainer = document.createElement('div');
        let descriptionParagraph = document.createElement('p');
        descriptionParagraph.innerHTML = description;
        descriptionParagraph.setAttribute('class', 'text-xl text-gray-900');
        descriptionContainer.append(descriptionParagraph);
        return descriptionContainer;
    }

    function createLikesContainer(postId, numberOfLikes) {
        let likesContainer = document.createElement('div');
        likesContainer.setAttribute('class', 'likes-container flex items-center w-1/3 mx-4 justify-center');

        let heart = createImage('templates/views/app/img/catalog/heart.png', '', '');
        if (loggedInUserId) {
            heart.setAttribute('class', 'h-8 visible cursor-pointer');
        } else {
            heart.setAttribute('class', 'h-8 visible');
        }

        let heartLiked = createImage('templates/views/app/img/catalog/heart-liked.png', '', '');
        if (loggedInUserId) {
            heartLiked.setAttribute('class', 'h-8 hidden cursor-pointer');
        }

        let likes = document.createElement('h1');
        if (!numberOfLikes) {
            numberOfLikes = 0;
        }
        likes.innerHTML = numberOfLikes;
        likes.setAttribute('class', 'text-2xl mx-2');

        if (loggedInUserId) {
            heart.onclick = async function () {
                likes.innerHTML = (parseInt(likes.innerHTML) + 1).toString();
                postsLikedByUser.push(postId);

                heart.classList.remove('visible');
                heart.classList.add('hidden');
                heartLiked.classList.remove('hidden');
                heartLiked.classList.add('visible');

                const rawResponse = await fetch('http://localhost:8098/likePost', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({postId, userId: loggedInUserId})
                });
            };

            heartLiked.onclick = async function () {
                likes.innerHTML = (parseInt(likes.innerHTML) + -1).toString();
                const index = postsLikedByUser.indexOf(postId);
                if (index > -1) {
                    postsLikedByUser.splice(index, 1);
                }

                heartLiked.classList.remove('visible');
                heartLiked.classList.add('hidden');
                heart.classList.remove('hidden');
                heart.classList.add('visible');

                const rawResponse = await fetch('http://localhost:8098/dislikePost', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({postId, userId: loggedInUserId})
                });
            };
        }
        likesContainer.append(heart);
        if (loggedInUserId) {
            likesContainer.append(heartLiked);
        }
        determineHeartIcon(postId, heart, heartLiked);

        likesContainer.append(likes);
        return likesContainer;
    }

    function determineHeartIcon(postId, heart, likedHeart) {
        if (!loggedInUserId) {
            return;
        }

        if (postsLikedByUser.includes(postId)) {
            heart.classList.add('hidden');
            likedHeart.classList.remove('hidden');
        }
    }

    function createCommentContainer(postId, numberOfComments) {
        let commentButtonContainer = document.createElement('div');
        commentButtonContainer.setAttribute('class', 'comment-container flex items-center w-1/3 mx-4 justify-center');

        let commentIcon = createImage('templates/views/app/img/catalog/comment.png', '', '');
        if (loggedInUserId || numberOfComments !== 0) {
            commentIcon.setAttribute('class', 'h-8 cursor-pointer');
        } else {
            commentIcon.setAttribute('class', 'h-8');
        }

        let comments = document.createElement('h1');
        if (!numberOfComments) {
            numberOfComments = 0;
        }
        comments.innerHTML = numberOfComments;
        comments.setAttribute('class', 'comments-count text-2xl mx-2');

        if (loggedInUserId || numberOfComments !== 0) {
            commentIcon.onclick = async function () {
                const catalogPostModalContainer = await createCatalogPostModalContainer(postId);
                document.getElementById('overlay').classList.remove('hidden');
                document.getElementById('catalog-posts').append(catalogPostModalContainer);
            };
        }

        commentButtonContainer.append(commentIcon);
        commentButtonContainer.append(comments);


        return commentButtonContainer;
    }

    function createSharingContainer(imageUrl) {
        let sharingContainer = document.createElement('div');
        sharingContainer.setAttribute('class', 'comment-container flex items-center w-1/3 justify-center');

        let facebook = document.createElement('a');
        facebook.setAttribute('class', 'fa fa-facebook');
        facebook.setAttribute('href', 'https://www.facebook.com/sharer/sharer.php?u=' + imageUrl);
        facebook.setAttribute('target', '_blank');

        sharingContainer.append(facebook);
        return sharingContainer;
    }

    function createCatalogPostContainer(catalogPost) {
        let catalogPostContainer = document.createElement('div');
        catalogPostContainer.setAttribute('id', catalogPost.id);
        catalogPostContainer.setAttribute('class', 'relative m-2 max-w-4xl shadow rounded-b-lg pb-4');

        let imageContainer = document.createElement('div');
        imageContainer.setAttribute('class', 'w-full');
        imageContainer.append(createImage(catalogPost.imageUrl, '', 'rounded-t-lg'));
        catalogPostContainer.append(imageContainer);

        let descriptionContainer = createDescriptionContainer(catalogPost.description);
        descriptionContainer.setAttribute('class', 'pt-8 pb-4 px-8 w-full');
        catalogPostContainer.append(descriptionContainer);

        let iconsContainer = document.createElement('div');
        iconsContainer.setAttribute('class', 'icons-container flex w-full justify-around py-2 px-8');
        iconsContainer.append(createLikesContainer(catalogPost.id, catalogPost.likes));
        iconsContainer.append(createCommentContainer(catalogPost.id, catalogPost.comments));
        iconsContainer.append(createSharingContainer(catalogPost.imageUrl));
        catalogPostContainer.append(iconsContainer);

        return catalogPostContainer;
    }

    document.getElementById('overlay').onclick = function () {
        document.getElementById('post-modal').remove();
        document.getElementById('overlay').classList.add('hidden');
    }

    async function createCatalogPostModalContainer(postId) {
        let catalogPostModalContainer = document.createElement('div');
        catalogPostModalContainer.setAttribute('id', 'post-modal');
        catalogPostModalContainer.setAttribute('class', 'w-11/12 fixed bg-white z-50 rounded-lg');
        catalogPostModalContainer.style.top = '50%';
        catalogPostModalContainer.style.left = '50%';
        catalogPostModalContainer.style.transform = 'translate(-50%, -50%)';
        catalogPostModalContainer.style.maxWidth = '768px';
        catalogPostModalContainer.style.maxHeight = '768px';

        let commentsContainer = document.createElement('div');
        commentsContainer.setAttribute('class', 'bg-white rounded-lg overflow-scroll px-4 m-4 mx-2');

        commentsContainer.style.height = '20rem';

        const rawResponse = await fetch('http://localhost:8098/postComments' + '?postId=' + postId, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        });

        const comments = await rawResponse.json();

        for (let i = 0; i < comments.length; i++) {
            let comment = document.createElement('div');
            comment.setAttribute('class', 'mb-8');

            let commentContent = document.createElement('h1');
            commentContent.setAttribute('class', 'text-lg break-words');
            commentContent.innerHTML = comments[i].content;
            comment.append(commentContent);

            const rawResponseUser = await fetch('http://localhost:8098/user' + '?userId=' + comments[i].commentatorId, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            });
            const commenter = await rawResponseUser.json();
            let commentCommenter = document.createElement('h1');
            commentCommenter.setAttribute('class', 'text-sm break-words');
            commentCommenter.innerHTML = commenter.username;
            comment.append(commentCommenter);

            commentsContainer.append(comment);
        }
        catalogPostModalContainer.append(commentsContainer);

        if (loggedInUserId) {
            let addCommentContainer = document.createElement('div');
            addCommentContainer.setAttribute('class', 'flex flex-col h-20 sm:h-16 rounded-b-lg');

            let commentBoxContainer = document.createElement('div');
            commentBoxContainer.setAttribute('class', 'px-2 w-full');
            let commentBox = document.createElement('input');
            commentBox.setAttribute('type', 'text');
            commentBox.setAttribute('class', 'p-4 w-full h-full outline-none');
            commentBox.setAttribute('placeholder', 'add comment...');
            commentBoxContainer.append(commentBox);

            let buttonContainer = document.createElement('div');
            buttonContainer.setAttribute('class', 'flex bg-white rounded-b-lg w-full justify-around mt-2');

            let closeButtonContainer = document.createElement('div');
            closeButtonContainer.setAttribute('class', 'w-1/2 mt-1 shadow flex justify-around m-4 p-4 rounded-lg cursor-pointer');
            let closeButton = createImage('templates/views/app/img/catalog/close.png', '', '');
            closeButton.setAttribute('class', 'h-8');
            closeButtonContainer.onclick = function () {
                document.getElementById('post-modal').remove();
                document.getElementById('overlay').classList.add('hidden');
            }

            closeButtonContainer.append(closeButton);

            let commentButtonContainer = document.createElement('div');
            commentButtonContainer.setAttribute('class', 'w-1/2 mt-1 shadow flex justify-around m-4 p-4 rounded-lg cursor-pointer');
            let commentButton = createImage('templates/views/app/img/catalog/send.png', '', '');
            commentButton.setAttribute('class', 'h-8');
            commentButtonContainer.onclick = async function () {
                if (!commentBox.value.length) {
                    return;
                }
                let comment = document.createElement('div');
                comment.setAttribute('class', 'mb-8');

                let commentContent = document.createElement('h1');
                commentContent.setAttribute('class', 'text-lg break-words');
                commentContent.innerHTML = commentBox.value.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                comment.append(commentContent);

                const rawResponseLoggedInUser = await fetch('http://localhost:8098/user' + '?userId=' + loggedInUserId, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                });
                const commenter = await rawResponseLoggedInUser.json();
                let commentCommenter = document.createElement('h1');
                commentCommenter.setAttribute('class', 'text-sm break-words');
                commentCommenter.innerHTML = commenter.username;
                comment.append(commentCommenter);

                commentsContainer.prepend(comment);
                commentBox.value = '';

                increaseCommentCounter(postId);
                const rawResponse = await fetch('http://localhost:8098/commentPost', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({postId, commentatorId: loggedInUserId, content: commentContent.innerHTML})
                });
            }
            commentButtonContainer.append(commentButton);

            buttonContainer.append(closeButtonContainer);
            buttonContainer.append(commentButtonContainer);

            addCommentContainer.append(commentBoxContainer);
            addCommentContainer.append(buttonContainer);
            catalogPostModalContainer.append(addCommentContainer);
        }
        return catalogPostModalContainer;
    }

    function increaseCommentCounter(postId) {
        let postChildren = document.getElementById(postId).childNodes;
        let iconsContainerChildren = null;
        let commentContainerChildren = null;

        for (let i = 0; i < postChildren.length; i++) {
            if (postChildren[i].classList.contains('icons-container')) {
                iconsContainerChildren = postChildren[i].childNodes;
                break;
            }
        }
        if (iconsContainerChildren) {
            for (let i = 0; i < iconsContainerChildren.length; i++) {
                if (iconsContainerChildren[i].classList.contains('comment-container')) {
                    commentContainerChildren = iconsContainerChildren[i].childNodes;
                    break;
                }
            }
        }
        if (commentContainerChildren) {
            for (let i = 0; i < commentContainerChildren.length; i++) {
                if (commentContainerChildren[i].classList.contains('comments-count')) {
                    commentContainerChildren[i].innerHTML = parseInt(commentContainerChildren[i].innerHTML) + 1;
                    break;
                }
            }
        }
    }
</script>

<style>
    .fa {
        padding: 1rem;
        width: 4rem;
        height: 4rem;
        font-size: 2rem;
        text-align: center;
        text-decoration: none;
        border-radius: 0.375rem;
        cursor: pointer;
    }

    .fa:hover {
        opacity: 0.7;
    }

    .fa-facebook {
        color: black;
    }
</style>
