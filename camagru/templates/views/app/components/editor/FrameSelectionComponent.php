<div id="overlayFrames" class="flex overflow-scroll px-2 lg:px-0 relative">
    <div id="overlayFramesBlocker"
         class="hidden absolute top-0 left-0 w-full h-full bg-gray-900 opacity-50 xl:rounded-b-lg z-100"></div>
    <img id="Cudi" src="templates/views/app/img/frames/Cudi.png"
         onclick="toggleFrame('Cudi', 'templates/views/app/img/frames/Cudi.png')"
         class="appliedFrame cursor-pointer w-32 h-32 rounded-lg my-2"/>
    <img id="Word" src="templates/views/app/img/frames/Wood.png"
         onclick="toggleFrame('Word', 'templates/views/app/img/frames/Wood.png')"
         class="appliedFrame cursor-pointer w-32 h-32 rounded-lg my-2 ml-2"/>
    <img id="Colors" src="templates/views/app/img/frames/Colors.png"
         onclick="toggleFrame('Colors', 'templates/views/app/img/frames/Colors.png')"
         class="appliedFrame cursor-pointer w-32 h-32 rounded-lg my-2 ml-2"/>
    <img id="Gold" src="templates/views/app/img/frames/Gold.png"
         onclick="toggleFrame('Gold', 'templates/views/app/img/frames/Gold.png')"
         class="appliedFrame cursor-pointer w-32 h-32 rounded-lg my-2 ml-2"/>
    <img id="Simple" src="templates/views/app/img/frames/Simple.png"
         onclick="toggleFrame('Simple', 'templates/views/app/img/frames/Simple.png')"
         class="appliedFrame cursor-pointer w-32 h-32 rounded-lg my-2 ml-2"/>
    <img id="Flowers" src="templates/views/app/img/frames/Flowers.png"
         onclick="toggleFrame('Flowers', 'templates/views/app/img/frames/Flowers.png')"
         class="appliedFrame cursor-pointer w-32 h-32 rounded-lg my-2 ml-2"/>
    <img id="Fancy" src="templates/views/app/img/frames/Fancy.png"
         onclick="toggleFrame('Fancy', 'templates/views/app/img/frames/Fancy.png')"
         class="appliedFrame cursor-pointer w-32 h-32 rounded-lg my-2 ml-2"/>
    <img id="Leaf" src="templates/views/app/img/frames/Leaf.png"
         onclick="toggleFrame('Leaf', 'templates/views/app/img/frames/Leaf.png')"
         class="appliedFrame cursor-pointer w-32 h-32 rounded-lg my-2 ml-2"/>
</div>

<script>
    function blockFrameSelection() {
        document.getElementById('overlayFramesBlocker').classList.remove('hidden');
        document.getElementById('overlayFrames').classList.remove('overflow-scroll');
        document.getElementById('overlayFrames').classList.add('overflow-hidden');
    }

    function enableFrameSelection() {
        document.getElementById('overlayFrames').classList.add('overflow-scroll');
        document.getElementById('overlayFrames').classList.remove('overflow-hidden');
        document.getElementById('overlayFramesBlocker').classList.add('hidden');
    }

    function removeOpacityPropertyFromSelectedFrames() {
        let overlayFrames = document.querySelectorAll('.frameIsApplied');
        for (let i = 0; i < overlayFrames.length; i++) {
            overlayFrames[i].classList.remove('opacity-25');
        }
    }

    function addOpacityPropertyToSelectedFrames() {
        let overlayFrames = document.querySelectorAll('.frameIsApplied');
        for (let i = 0; i < overlayFrames.length; i++) {
            overlayFrames[i].classList.add('opacity-25');
        }
    }

    function removeFrameIsAppliedClass() {
        let overlayFrames = document.querySelectorAll('.frameIsApplied');
        for (let i = 0; i < overlayFrames.length; i++) {
            overlayFrames[i].classList.remove('frameIsApplied');
        }
    }
</script>
