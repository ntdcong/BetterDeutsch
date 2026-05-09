document.addEventListener('DOMContentLoaded', () => {
    const app = document.getElementById('flashcard-app');
    if (!app) return;

    const notebookId = app.dataset.notebookId;
    const cardStack = document.getElementById('card-stack');
    const progressIndicator = document.getElementById('progress-indicator');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnFlip = document.getElementById('btn-flip');
    const btnShuffle = document.getElementById('btn-shuffle');
    const btnAutoPlay = document.getElementById('toggle-autoplay-btn');

    let vocabularies = [];
    let currentIndex = 0;
    let autoPlayAudio = false;

    // Verb Modal
    const verbModal = document.getElementById('verb-modal');
    const verbModalContent = document.getElementById('verb-modal-content');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const verbBody = document.getElementById('verb-body');
    const verbTitle = document.getElementById('verb-title');

    const wordTypesVN = {
        'noun': 'Danh từ', 'verb': 'Động từ', 'adj': 'Tính từ', 'adv': 'Trạng từ',
        'prep': 'Giới từ', 'pron': 'Đại từ', 'conj': 'Liên từ', 'article': 'Mạo từ',
        'phrase': 'Cụm từ', 'none': 'Khác'
    };

    fetch(`/api/vocabularies?notebook_id=${notebookId}`)
        .then(res => res.json())
        .then(data => {
            if (data.data && data.data.length > 0) {
                vocabularies = data.data;
                initCards();
            } else {
                cardStack.innerHTML = '<div class="absolute inset-0 flex items-center justify-center p-12 border-2 border-dashed border-border rounded-2xl bg-white"><div class="text-center text-muted-foreground font-medium">Chưa có từ vựng nào.</div></div>';
                btnPrev.disabled = btnNext.disabled = btnShuffle.disabled = true;
            }
        })
        .catch(() => {
            cardStack.innerHTML = '<div class="absolute inset-0 flex items-center justify-center p-12 border-2 border-dashed border-border rounded-2xl bg-white"><div class="text-destructive font-medium text-center">Lỗi tải dữ liệu.</div></div>';
        });

    function initCards() {
        renderStack();
        updateControls();
        if (autoPlayAudio && vocabularies[currentIndex]) {
            let v = vocabularies[currentIndex];
            speakWord(`${v.article ? v.article + ' ' : ''}${v.word}`);
        }
    }

    function createCardHTML(vocab, indexOffset) {
        if (!vocab) return '';

        let articleClass = '';
        let backBgClass = '';
        if (vocab.article === 'der') { articleClass = 'text-der'; backBgClass = 'bg-der-light'; }
        else if (vocab.article === 'die') { articleClass = 'text-die'; backBgClass = 'bg-die-light'; }
        else if (vocab.article === 'das') { articleClass = 'text-das'; backBgClass = 'bg-das-light'; }

        // Reverse z-index: top card has highest
        const zIndex = 10 - indexOffset;
        const scale = 1 - (indexOffset * 0.05); // scale down cards behind
        const translateY = indexOffset * 15; // move down cards behind

        let transformStr = `translateY(${translateY}px) scale(${scale})`;
        let opacity = indexOffset >= 3 ? 0 : 1 - (indexOffset * 0.15); // Fade out behind

        const speakerHtml = !autoPlayAudio
            ? `<button class="btn-speak absolute top-4 right-4 text-muted-foreground hover:text-foreground text-3xl p-2 z-20 transition-transform active:scale-90" title="Nghe" data-word="${vocab.article ? vocab.article + ' ' : ''}${vocab.word}">🔊</button>`
            : '';

        let wordTypeVn = vocab.word_type ? (wordTypesVN[vocab.word_type] || vocab.word_type) : '';

        return `
            <div class="card-wrapper" data-index="${currentIndex + indexOffset}" style="z-index: ${zIndex}; transform: ${transformStr}; opacity: ${opacity};">
                <div class="card-inner w-full h-full cursor-pointer">
                    <!-- Front -->
                    <div class="card-front items-center justify-center p-6 text-center">
                        ${speakerHtml}
                        <div class="flex-1 flex flex-col items-center justify-center w-full">
                            ${vocab.article ? `<div class="font-bold text-3xl md:text-4xl mb-4 ${articleClass}">${vocab.article}</div>` : ''}
                            <div class="font-extrabold text-foreground leading-tight" style="font-size: clamp(2rem, 8vw, 4rem); word-break: keep-all; overflow-wrap: break-word;">
                                ${vocab.word}
                            </div>
                        </div>
                        <div class="mt-auto pt-6 text-sm text-muted-foreground animate-pulse">Chạm hoặc nhấn Space để lật</div>
                    </div>
                    <!-- Back -->
                    <div class="card-back p-6 sm:p-8 ${backBgClass}">
                        ${wordTypeVn ? `<div class="absolute top-4 right-4 sm:top-6 sm:right-6 text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-md bg-white border shadow-sm text-muted-foreground z-10">${wordTypeVn}</div>` : ''}
                        
                        <div class="w-full h-full flex flex-col justify-center items-center text-center mt-2">
                            <div class="font-bold text-4xl sm:text-5xl mb-6 text-foreground break-words leading-tight" style="max-width: 100%; word-break: keep-all; overflow-wrap: break-word;">${vocab.translation_vn}</div>
                            
                            <div class="flex flex-wrap justify-center gap-3">
                                ${vocab.plural_form ? `<div class="text-sm bg-white border px-4 py-2 rounded-lg shadow-sm"><span class="text-muted-foreground mr-1">Số nhiều:</span><span class="font-bold">${vocab.plural_form}</span></div>` : ''}
                                ${vocab.preposition ? `<div class="text-sm bg-white border px-4 py-2 rounded-lg shadow-sm"><span class="text-muted-foreground mr-1">Giới từ:</span><span class="font-bold">${vocab.preposition}</span></div>` : ''}
                            </div>
                            
                            ${vocab.note ? `<div class="mt-8 text-sm text-muted-foreground italic bg-white/60 p-4 rounded-lg max-w-sm w-full border border-white/40">${vocab.note}</div>` : ''}
                            
                            ${vocab.word_type === 'verb' ? `
                            <div class="mt-10">
                                <button class="btn-verb-lookup inline-flex items-center justify-center rounded-full text-sm font-medium border bg-white text-primary hover:bg-muted h-12 px-8 transition-colors shadow-sm active:scale-95" data-word="${vocab.word}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                    Tra cứu động từ
                                </button>
                            </div>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderStack() {
        cardStack.innerHTML = '';

        // Render behind cards first (indexOffset 2, then 1, then 0)
        for (let i = 2; i >= 0; i--) {
            if (currentIndex + i < vocabularies.length) {
                cardStack.innerHTML += createCardHTML(vocabularies[currentIndex + i], i);
            }
        }

        bindCardEvents();
    }

    function bindCardEvents() {
        const wrappers = document.querySelectorAll('.card-wrapper');
        if (wrappers.length === 0) return;

        // Top card is the last one appended (z-index 10)
        const topCardWrapper = Array.from(wrappers).find(w => w.style.zIndex === '10');
        if (!topCardWrapper) return;

        const topCardInner = topCardWrapper.querySelector('.card-inner');

        topCardInner.addEventListener('click', (e) => {
            if (e.target.closest('button')) return;
            topCardWrapper.classList.toggle('is-flipped');
        });

        const speakers = topCardWrapper.querySelectorAll('.btn-speak');
        speakers.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                speakWord(btn.dataset.word);
            });
        });

        const verbBtns = topCardWrapper.querySelectorAll('.btn-verb-lookup');
        verbBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                lookupVerb(btn.dataset.word);
            });
        });

        setupSwipe(topCardWrapper);
    }

    let isAnimating = false;

    function animateOutLeft() {
        return new Promise(resolve => {
            const topCardWrapper = document.querySelector(`.card-wrapper[data-index="${currentIndex}"]`);
            if (topCardWrapper) {
                topCardWrapper.style.transform = 'translate(-150%, 0) rotate(-15deg)';
                topCardWrapper.style.opacity = '0';
                setTimeout(resolve, 300);
            } else {
                resolve();
            }
        });
    }

    function nextCard() {
        if (currentIndex < vocabularies.length - 1 && !isAnimating) {
            isAnimating = true;
            animateOutLeft().then(() => {
                currentIndex++;
                renderStack();
                updateControls();

                if (autoPlayAudio && vocabularies[currentIndex]) {
                    let v = vocabularies[currentIndex];
                    speakWord(`${v.article ? v.article + ' ' : ''}${v.word}`);
                }
                isAnimating = false;
            });
        }
    }

    function prevCard() {
        if (currentIndex > 0 && !isAnimating) {
            isAnimating = true;
            currentIndex--;

            renderStack();
            updateControls();

            // Animate top card flying in from left
            const topCardWrapper = document.querySelector(`.card-wrapper[data-index="${currentIndex}"]`);
            if (topCardWrapper) {
                // Instantly position it out left
                topCardWrapper.style.transition = 'none';
                topCardWrapper.style.transform = 'translate(-150%, 0) rotate(-15deg)';
                topCardWrapper.style.opacity = '0';

                // Force reflow
                void topCardWrapper.offsetWidth;

                // Animate to center
                topCardWrapper.style.transition = 'all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1)';
                topCardWrapper.style.transform = 'translateY(0) scale(1)';
                topCardWrapper.style.opacity = '1';

                setTimeout(() => {
                    if (autoPlayAudio && vocabularies[currentIndex]) {
                        let v = vocabularies[currentIndex];
                        speakWord(`${v.article ? v.article + ' ' : ''}${v.word}`);
                    }
                    isAnimating = false;
                }, 400);
            } else {
                isAnimating = false;
            }
        }
    }

    function updateControls() {
        const currentNum = currentIndex + 1;
        const totalNum = vocabularies.length;

        const progressText = document.getElementById('progress-text');
        if (progressText) progressText.textContent = `${currentNum}/${totalNum}`;

        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            const percent = totalNum > 0 ? (currentNum / totalNum) * 100 : 0;
            progressBar.style.width = `${percent}%`;
        }

        btnPrev.disabled = currentIndex === 0;
        btnNext.disabled = currentIndex === vocabularies.length - 1;
    }

    function shuffle() {
        if (vocabularies.length <= 1 || isAnimating) return;
        isAnimating = true;

        const stack = document.getElementById('card-stack');

        // Animate out (drop down and fade out)
        stack.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        stack.style.opacity = '0';
        stack.style.transform = 'scale(0.9) translateY(40px)';

        setTimeout(() => {
            // Shuffle data
            for (let i = vocabularies.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [vocabularies[i], vocabularies[j]] = [vocabularies[j], vocabularies[i]];
            }
            currentIndex = 0;
            renderStack();
            updateControls();

            // Prepare animate in (start from top)
            stack.style.transition = 'none';
            stack.style.transform = 'scale(0.9) translateY(-40px)';

            // Force reflow
            void stack.offsetWidth;

            // Animate in (drop into center and fade in)
            stack.style.transition = 'all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1)';
            stack.style.opacity = '1';
            stack.style.transform = 'scale(1) translateY(0)';

            setTimeout(() => {
                isAnimating = false;
                stack.style.transition = '';
                stack.style.transform = '';

                if (autoPlayAudio && vocabularies[currentIndex]) {
                    let v = vocabularies[currentIndex];
                    speakWord(`${v.article ? v.article + ' ' : ''}${v.word}`);
                }
            }, 400);
        }, 300);
    }

    function speakWord(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'de-DE';
            window.speechSynthesis.speak(utterance);
        }
    }

    function lookupVerb(word) {
        openModal();
        verbTitle.textContent = `Động từ: ${word}`;
        verbBody.innerHTML = '<div class="text-center py-8 text-muted-foreground animate-pulse">Đang tra cứu...</div>';

        fetch(`/api/verb?word=${encodeURIComponent(word)}`)
            .then(res => res.json())
            .then(data => {
                if (data.error || !data.data) {
                    verbBody.innerHTML = '<div class="text-destructive font-medium text-center">Không tìm thấy dữ liệu động từ này.</div>';
                    return;
                }
                const v = data.data;
                verbBody.innerHTML = `
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-foreground">
                        <!-- Präsens -->
                        <div class="bg-secondary/30 border border-border/50 rounded-xl p-4 shadow-sm sm:row-span-2">
                            <h4 class="text-[11px] font-bold tracking-widest text-muted-foreground uppercase mb-3 pb-2 border-b border-border/50">Präsens</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-muted-foreground font-medium">ich</span>
                                    <span class="font-semibold text-primary text-base">${v.Praesens_ich || '-'}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-muted-foreground font-medium">du</span>
                                    <span class="font-semibold text-primary text-base">${v.Praesens_du || '-'}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-muted-foreground font-medium">er/sie/es</span>
                                    <span class="font-semibold text-primary text-base">${v.Praesens_er_sie_es || '-'}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Präteritum -->
                        <div class="bg-secondary/30 border border-border/50 rounded-xl p-4 shadow-sm">
                            <h4 class="text-[11px] font-bold tracking-widest text-muted-foreground uppercase mb-3 pb-2 border-b border-border/50">Präteritum</h4>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-muted-foreground font-medium">ich/er/sie</span>
                                <span class="font-semibold text-primary text-base">${v.Praeteritum_ich || '-'}</span>
                            </div>
                        </div>

                        <!-- Konjunktiv II -->
                        <div class="bg-secondary/30 border border-border/50 rounded-xl p-4 shadow-sm">
                            <h4 class="text-[11px] font-bold tracking-widest text-muted-foreground uppercase mb-3 pb-2 border-b border-border/50">Konjunktiv II</h4>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-muted-foreground font-medium">ich/er/sie</span>
                                <span class="font-semibold text-primary text-base">${v.Konjunktiv_II_ich || '-'}</span>
                            </div>
                        </div>

                        <!-- Perfekt -->
                        <div class="bg-secondary/30 border border-border/50 rounded-xl p-4 shadow-sm flex flex-col justify-center">
                            <h4 class="text-[11px] font-bold tracking-widest text-muted-foreground uppercase mb-3 pb-2 border-b border-border/50">Perfekt</h4>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-muted-foreground font-medium">Trợ ĐT + PII</span>
                                <span class="font-semibold text-primary text-base"><span class="text-muted-foreground font-normal mr-1.5">${v.Hilfsverb === 'sein' ? 'ist' : 'hat'}</span>${v.Partizip_II || '-'}</span>
                            </div>
                        </div>

                        <!-- Imperativ -->
                        <div class="bg-secondary/30 border border-border/50 rounded-xl p-4 shadow-sm">
                            <h4 class="text-[11px] font-bold tracking-widest text-muted-foreground uppercase mb-3 pb-2 border-b border-border/50">Imperativ</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-muted-foreground font-medium">du</span>
                                    <span class="font-semibold text-primary text-base">${v.Imperativ_Singular || '-'}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-muted-foreground font-medium">ihr</span>
                                    <span class="font-semibold text-primary text-base">${v.Imperativ_Plural || '-'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(() => {
                verbBody.innerHTML = '<div class="text-destructive font-medium text-center">Lỗi mạng hoặc máy chủ.</div>';
            });
    }

    function openModal() {
        verbModal.classList.remove('opacity-0', 'pointer-events-none');
        setTimeout(() => {
            verbModalContent.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function closeModal() {
        verbModalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            verbModal.classList.add('opacity-0', 'pointer-events-none');
            verbBody.innerHTML = '';
        }, 300);
    }

    btnCloseModal.addEventListener('click', closeModal);
    verbModal.addEventListener('click', (e) => {
        if (e.target === verbModal) closeModal();
    });

    btnNext.addEventListener('click', nextCard);
    btnPrev.addEventListener('click', prevCard);
    btnShuffle.addEventListener('click', shuffle);
    btnFlip.addEventListener('click', () => {
        const topCard = document.querySelector(`.card-wrapper[data-index="${currentIndex}"]`);
        if (topCard) topCard.classList.toggle('is-flipped');
    });

    btnAutoPlay.addEventListener('click', () => {
        autoPlayAudio = !autoPlayAudio;
        btnAutoPlay.textContent = autoPlayAudio ? 'Phát âm: Tự động' : 'Phát âm: Thủ công';
        if (autoPlayAudio) {
            btnAutoPlay.classList.add('text-primary');
            btnAutoPlay.classList.remove('text-muted-foreground');
            let v = vocabularies[currentIndex];
            if (v) speakWord(`${v.article ? v.article + ' ' : ''}${v.word}`);
        } else {
            btnAutoPlay.classList.remove('text-primary');
            btnAutoPlay.classList.add('text-muted-foreground');
        }
        renderStack();
    });

    document.addEventListener('keydown', (e) => {
        if (!verbModal.classList.contains('pointer-events-none')) {
            if (e.key === 'Escape') closeModal();
            return;
        }

        if (vocabularies.length === 0 || isAnimating) return;
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        if (e.code === 'Space') {
            e.preventDefault();
            const topCard = document.querySelector(`.card-wrapper[data-index="${currentIndex}"]`);
            if (topCard) topCard.classList.toggle('is-flipped');
        } else if (e.code === 'ArrowRight') {
            e.preventDefault();
            nextCard();
        } else if (e.code === 'ArrowLeft') {
            e.preventDefault();
            prevCard();
        }
    });

    function setupSwipe(element) {
        let touchstartX = 0;
        let touchendX = 0;

        element.addEventListener('touchstart', e => {
            touchstartX = e.changedTouches[0].screenX;
        }, { passive: true });

        element.addEventListener('touchend', e => {
            touchendX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            const threshold = 50;
            if (touchendX < touchstartX - threshold) {
                nextCard();
            }
            if (touchendX > touchstartX + threshold) {
                prevCard();
            }
        }
    }
});
