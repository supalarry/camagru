<div id="editor">
    <div class="lg:pt-2 xl:p-2 xl:flex xl:w-11/12 mx-auto">
        <div id="create-image" class="max-w-5xl mx-auto xl:w-4/6">
            <?php require_once '/var/www/camagru/templates/views/app/components/editor/EditorOutputComponent.php'; ?>
            <?php require_once '/var/www/camagru/templates/views/app/components/editor/FrameSelectionComponent.php'; ?>
            <?php require_once '/var/www/camagru/templates/views/app/components/editor/CommentBoxPostButtonComponent.php'; ?>
        </div>
        <?php require_once '/var/www/camagru/templates/views/app/components/editor/UploadedPostsComponent.php'; ?>
    </div>
</div>

<style>
    #editor {
        display: none;
    }
</style>
