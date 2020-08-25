<?php
require_once '/var/www/camagru/templates/components/Header.php';
?>

<div id="overlay" class="hidden fixed top-0 left-0 right-0 bottom-0 bg-black opacity-75 z-40"></div>
<?php require_once '/var/www/camagru/templates/views/app/components/catalog/PostsComponent.php'; ?>

<script>
    /* user info */
    const loggedInUserId = <?php echo $args['id'] ?>;
    const loggedInUsername = <?php echo("'" . $args['username'] . "'") ?>;
    let postsLikedByUser = [];

    /* fetched posts info */
    let postsCount = 5;
    let postsOffset = 0;
    let loadedAllImages = false;

    async function loadCatalogPosts(postsCount, postsOffset) {
        const requestUrl = 'http://localhost:8098/posts' + '?count=' + postsCount + '&offset=' + postsOffset;
        const rawResponse = await fetch(requestUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        });
        const catalogPosts = await rawResponse.json();

        const catalogPostsContainer = document.getElementById('catalog-posts');

        if (!catalogPosts.length && !document.getElementById('catalog-posts').childElementCount) {
            let noUploadsImg = createImage('templates/views/app/img/Uploads.jpg', 'no-uploads', 'lg:rounded-lg mt-2 xl:mt-0');
            catalogPostsContainer.append(noUploadsImg);
            return 0;
        }
        for (let i = 0; i < catalogPosts.length; i++) {
            catalogPostsContainer.append(createCatalogPostContainer(catalogPosts[i]));
        }
        if (catalogPosts.length) {
            return 1;
        }
        return 0;
    }

    document.addEventListener('DOMContentLoaded', async () => {
        let options = {
            root: null,
            rootMargins: '0px',
            threshold: 0.5
        };
        await getLikedPostsIds();
        await getPosts();
        const observer = new IntersectionObserver(handleIntersect, options);
        observer.observe(document.querySelector('footer'));
    });

    function handleIntersect(entries) {
        if (entries[0].isIntersecting && !loadedAllImages) {
            getPosts();
        }
    }

    async function getPosts() {
        const postsLoaded = await loadCatalogPosts(postsCount, postsOffset);
        if (!postsLoaded) {
            loadedAllImages = true;
        }
        postsOffset = postsOffset + postsCount;
    }

    async function getLikedPostsIds() {
        if (loggedInUserId) {
            const requestUrl = 'http://localhost:8098/userLikedPosts?userId=' + loggedInUserId;
            const rawResponse = await fetch(requestUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            });
            const postsLikedByUserResponse = await rawResponse.json();
            for (let i = 0; i < postsLikedByUserResponse.length; i++) {
                postsLikedByUser.push(postsLikedByUserResponse[i]['postId']);
            }
        }
    }

</script>

<?php
require_once '/var/www/camagru/templates/components/Footer.php';
?>
