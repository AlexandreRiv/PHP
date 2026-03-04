document.addEventListener('DOMContentLoaded', () => {
    const ring = document.getElementById('carousel-ring');
    const items = document.querySelectorAll('.carousel-item');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    const pauseBtn = document.getElementById('carousel-pause');
    const pauseIcon = document.getElementById('pause-icon');
    const playIcon = document.getElementById('play-icon');

    const detailName = document.getElementById('detail-name');
    const detailType = document.getElementById('detail-type');
    const detailDesc = document.getElementById('detail-desc');
    const detailDate = document.getElementById('detail-date');

    if (!ring || items.length === 0) return;

    const totalItems = items.length;
    const angleStep = 360 / totalItems;
    const radius = totalItems <= 4 ? 320 : 380;

    // Use a continuous angle for smooth infinite looping
    let currentAngle = 0;
    let currentIndex = 0;
    let autoPlayInterval = null;
    let isPlaying = true;

    function positionItems() {
        items.forEach((item, i) => {
            const angle = angleStep * i;
            const radian = (angle * Math.PI) / 180;
            const x = Math.sin(radian) * radius;
            const z = Math.cos(radian) * radius;
            item.style.transform = `translateX(${x}px) translateZ(${z}px)`;
        });
    }

    function rotateToIndex(index) {
        // Calculate the shortest angular distance for smooth looping
        const targetAngle = -angleStep * index;
        let diff = targetAngle - currentAngle;

        // Normalize to [-180, 180] for shortest path
        while (diff > 180) diff -= 360;
        while (diff < -180) diff += 360;

        currentAngle += diff;
        currentIndex = index;

        ring.style.transform = `rotateY(${currentAngle}deg)`;

        items.forEach((item, i) => {
            const angle = angleStep * i;
            const radian = (angle * Math.PI) / 180;
            const x = Math.sin(radian) * radius;
            const z = Math.cos(radian) * radius;
            const counterRotation = -currentAngle;
            item.style.transform = `translateX(${x}px) translateZ(${z}px) rotateY(${counterRotation}deg)`;
            item.classList.toggle('active', i === index);
        });

        updateDetail(index);
    }

    function updateDetail(index) {
        const item = items[index];
        if (!item) return;

        const name = item.querySelector('.carousel-card-info h3')?.textContent?.trim() || '';
        const type = item.querySelector('.carousel-card-info span')?.textContent?.trim() || '';

        if (detailName) detailName.textContent = name;
        if (detailType) detailType.textContent = type;
        if (detailDesc) detailDesc.textContent = item.dataset.desc || '';
        if (detailDate) detailDate.textContent = item.dataset.date ? 'Ajouté le ' + item.dataset.date : '';
    }

    function goNext() {
        rotateToIndex((currentIndex + 1) % totalItems);
    }

    function goPrev() {
        rotateToIndex((currentIndex - 1 + totalItems) % totalItems);
    }

    prevBtn?.addEventListener('click', () => { goPrev(); resetAutoPlay(); });
    nextBtn?.addEventListener('click', () => { goNext(); resetAutoPlay(); });

    // Click on item: if active, navigate to game page; otherwise, rotate to it
    items.forEach((item, i) => {
        item.addEventListener('click', (e) => {
            if (i === currentIndex) {
                const link = item.querySelector('.carousel-card-link');
                if (link) {
                    window.location.href = link.href;
                }
            } else {
                e.preventDefault();
                rotateToIndex(i);
                resetAutoPlay();
            }
        });
    });

    // Prevent default link click on non-active items
    document.querySelectorAll('.carousel-card-link').forEach((link, i) => {
        link.addEventListener('click', (e) => {
            if (i !== currentIndex) {
                e.preventDefault();
            }
        });
    });

    function updatePauseBtn() {
        if (!pauseIcon || !playIcon) return;
        if (isPlaying) {
            pauseIcon.classList.remove('hidden');
            playIcon.classList.add('hidden');
        } else {
            pauseIcon.classList.add('hidden');
            playIcon.classList.remove('hidden');
        }
    }

    function startAutoPlay() {
        stopAutoPlay();
        autoPlayInterval = setInterval(goNext, 3000);
        isPlaying = true;
        updatePauseBtn();
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
        autoPlayInterval = null;
        isPlaying = false;
        updatePauseBtn();
    }

    function resetAutoPlay() {
        if (isPlaying) {
            startAutoPlay();
        }
    }

    pauseBtn?.addEventListener('click', () => {
        if (isPlaying) {
            stopAutoPlay();
        } else {
            startAutoPlay();
        }
    });

    /* ── Keyboard ── */
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') { goPrev(); resetAutoPlay(); }
        if (e.key === 'ArrowRight') { goNext(); resetAutoPlay(); }
        if (e.key === ' ') {
            e.preventDefault();
            if (isPlaying) stopAutoPlay(); else startAutoPlay();
        }
    });

    const scene = document.getElementById('carousel-scene');
    scene?.addEventListener('mouseenter', () => { if (isPlaying) clearInterval(autoPlayInterval); });
    scene?.addEventListener('mouseleave', () => { if (isPlaying) startAutoPlay(); });

    /* ── Init ── */
    positionItems();
    rotateToIndex(0);
    startAutoPlay();
});
