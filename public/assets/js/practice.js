document.addEventListener('DOMContentLoaded', () => {
    const app = document.getElementById('practice-app');
    const notebookId = app.dataset.notebookId;
    const isShared = app.dataset.isShared === '1';
    const shareToken = app.dataset.shareToken || '';
    
    const loadingState = document.getElementById('loading-state');
    const menuState = document.getElementById('menu-state');
    const quizState = document.getElementById('quiz-state');
    const matchingState = document.getElementById('matching-state');
    const summaryState = document.getElementById('summary-state');
    const btnBack = document.getElementById('btn-back');

    let vocabularies = [];
    let currentMode = '';
    let questions = [];
    let currentQuestionIndex = 0;
    let score = { correct: 0, incorrect: 0 };
    let timerInterval = null;

    let fetchUrl = `/api/vocabularies?notebook_id=${notebookId}`;
    if (isShared && shareToken) {
        fetchUrl += `&token=${shareToken}`;
    }

    // Fetch data
    fetch(fetchUrl)
        .then(response => response.json())
        .then(data => {
            vocabularies = data.data || [];
            loadingState.classList.add('hidden');
            if (vocabularies.length < 4) {
                if (isShared) {
                    menuState.innerHTML = `<div class="text-center py-10"><p class="text-xl text-red-500 mb-4">Sổ tay này chưa có đủ từ vựng để luyện tập (cần ít nhất 4 từ)!</p></div>`;
                } else {
                    menuState.innerHTML = `<div class="text-center py-10"><p class="text-xl text-red-500 mb-4">Cần ít nhất 4 từ vựng để luyện tập!</p><a href="/vocabularies?notebook_id=${notebookId}" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground h-10 px-4 py-2">Thêm từ vựng</a></div>`;
                }
                menuState.classList.remove('hidden');
                menuState.classList.add('flex');
            } else {
                showMenu();
            }
        })
        .catch(err => {
            console.error('Error fetching vocabularies:', err);
            loadingState.innerHTML = '<p class="text-red-500">Lỗi khi tải dữ liệu!</p>';
        });

    window.showMenu = function () {
        hideAllStates();
        menuState.classList.remove('hidden');
        menuState.classList.add('flex');
        if (btnBack) btnBack.href = '/notebooks';
    };

    function hideAllStates() {
        loadingState.classList.add('hidden');
        menuState.classList.add('hidden');
        menuState.classList.remove('flex');
        quizState.classList.add('hidden');
        quizState.classList.remove('flex');
        matchingState.classList.add('hidden');
        matchingState.classList.remove('flex');
        summaryState.classList.add('hidden');
        summaryState.classList.remove('flex');
        clearInterval(timerInterval);
    }

    window.startMode = function (mode) {
        currentMode = mode;
        score = { correct: 0, incorrect: 0 };
        if (btnBack) btnBack.href = "javascript:showMenu()";
        const btnBackShared = document.getElementById('btn-back-shared');
        if (btnBackShared) {
            btnBackShared.innerHTML = `<a href="javascript:showMenu()" class="hover:underline">Quay lại menu</a>`;
        }

        if (mode === 'quiz_meaning') {
            initQuizMeaning();
        } else if (mode === 'quiz_article') {
            initQuizArticle();
        } else if (mode === 'matching') {
            initMatching();
        } else if (mode === 'writing') {
            initWriting();
        }
    };

    function shuffle(array) {
        let currentIndex = array.length, randomIndex;
        while (currentIndex > 0) {
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex--;
            [array[currentIndex], array[randomIndex]] = [array[randomIndex], array[currentIndex]];
        }
        return array;
    }

    // --- Quiz Core ---
    function showQuizState() {
        hideAllStates();
        quizState.classList.remove('hidden');
        quizState.classList.add('flex');
        currentQuestionIndex = 0;
        document.getElementById('quiz-score-text').textContent = '0 điểm';
        renderQuizQuestion();
    }

    function renderQuizQuestion() {
        if (currentQuestionIndex >= questions.length) {
            showSummary();
            return;
        }

        const q = questions[currentQuestionIndex];
        document.getElementById('quiz-progress-text').textContent = `Câu ${currentQuestionIndex + 1}/${questions.length}`;
        document.getElementById('quiz-progress-bar').style.width = `${((currentQuestionIndex) / questions.length) * 100}%`;

        document.getElementById('quiz-question').textContent = q.question;
        document.getElementById('quiz-hint').textContent = q.hint || '';

        const optionsContainer = document.getElementById('quiz-options');
        const writingContainer = document.getElementById('quiz-writing-container');
        const feedback = document.getElementById('quiz-feedback');
        const btnNext = document.getElementById('btn-next-quiz');

        feedback.classList.add('hidden');
        btnNext.classList.add('hidden');

        if (currentMode === 'writing') {
            optionsContainer.classList.add('hidden');
            writingContainer.classList.remove('hidden');
            writingContainer.classList.add('flex');
            const input = document.getElementById('quiz-writing-input');
            input.value = '';
            input.disabled = false;
            input.focus();

            // Remove old listeners
            const newBtnSubmit = document.getElementById('btn-submit-writing').cloneNode(true);
            document.getElementById('btn-submit-writing').replaceWith(newBtnSubmit);

            newBtnSubmit.addEventListener('click', () => checkWritingAnswer(input.value.trim(), q.answer, input));
            input.onkeydown = (e) => {
                if (e.key === 'Enter') newBtnSubmit.click();
            };
        } else {
            writingContainer.classList.remove('flex');
            writingContainer.classList.add('hidden');
            optionsContainer.classList.remove('hidden');
            optionsContainer.innerHTML = '';

            q.options.forEach((opt, idx) => {
                const btn = document.createElement('button');
                btn.className = 'w-full p-4 rounded-xl border-2 border-border bg-card text-left font-semibold hover:bg-secondary transition-colors text-lg flex items-center justify-between group';
                btn.innerHTML = `
                    <span>${opt}</span>
                    <span class="w-6 h-6 rounded-full border-2 border-muted-foreground/30 flex items-center justify-center text-xs group-hover:border-primary"></span>
                `;
                btn.onclick = () => checkQuizAnswer(opt, q.answer, btn, optionsContainer);
                optionsContainer.appendChild(btn);
            });
        }
    }

    function handleQuizResult(isCorrect, feedbackMsg) {
        const feedback = document.getElementById('quiz-feedback');
        const btnNext = document.getElementById('btn-next-quiz');

        feedback.classList.remove('hidden');
        if (isCorrect) {
            score.correct++;
            feedback.className = 'mt-4 w-full p-4 rounded-xl font-bold text-center text-green-700 bg-green-100 border border-green-200';
            feedback.innerHTML = `Chính xác! <span class="text-sm font-normal block mt-1">${feedbackMsg || ''}</span>`;
            document.getElementById('quiz-score-text').textContent = `${score.correct} điểm`;
        } else {
            score.incorrect++;
            feedback.className = 'mt-4 w-full p-4 rounded-xl font-bold text-center text-red-700 bg-red-100 border border-red-200';
            feedback.innerHTML = `Sai rồi! <span class="text-sm font-normal block mt-1">${feedbackMsg || ''}</span>`;
        }

        btnNext.classList.remove('hidden');
        btnNext.onclick = () => {
            currentQuestionIndex++;
            renderQuizQuestion();
        };
        // Auto focus next
        setTimeout(() => btnNext.focus(), 100);
    }

    function checkQuizAnswer(selected, correct, btn, container) {
        Array.from(container.children).forEach(b => b.disabled = true);
        const isCorrect = selected === correct;

        if (isCorrect) {
            btn.classList.replace('border-border', 'border-green-500');
            btn.classList.replace('bg-card', 'bg-green-50');
            btn.querySelector('span:last-child').innerHTML = '✓';
            btn.querySelector('span:last-child').classList.add('text-green-600', 'border-green-500');
        } else {
            btn.classList.replace('border-border', 'border-red-500');
            btn.classList.replace('bg-card', 'bg-red-50');
            btn.querySelector('span:last-child').innerHTML = '✗';
            btn.querySelector('span:last-child').classList.add('text-red-600', 'border-red-500');

            // Highlight correct answer
            Array.from(container.children).forEach(b => {
                if (b.innerText.trim() === correct) {
                    b.classList.replace('border-border', 'border-green-500');
                    b.classList.replace('bg-card', 'bg-green-50');
                }
            });
        }

        handleQuizResult(isCorrect, isCorrect ? 'Tuyệt vời!' : `Đáp án đúng là: ${correct}`);
    }

    function checkWritingAnswer(typed, correctAnswers, inputEl) {
        inputEl.disabled = true;
        // Accept exact match or match without article
        let isCorrect = false;
        let matchedAns = '';

        const normalizedTyped = typed.toLowerCase().trim();
        for (let ans of correctAnswers) {
            if (normalizedTyped === ans.toLowerCase().trim()) {
                isCorrect = true;
                matchedAns = ans;
                break;
            }
        }

        if (isCorrect) {
            inputEl.classList.replace('border-input', 'border-green-500');
            inputEl.classList.add('bg-green-50', 'text-green-700');
        } else {
            inputEl.classList.replace('border-input', 'border-red-500');
            inputEl.classList.add('bg-red-50', 'text-red-700');
        }

        handleQuizResult(isCorrect, isCorrect ? 'Chính xác!' : `Đáp án đúng là: ${correctAnswers[0]}`);
    }

    // --- Mode: Quiz Meaning ---
    function initQuizMeaning() {
        let pool = shuffle([...vocabularies]).slice(0, 20); // Max 20 qs
        questions = pool.map(v => {
            let type = Math.random() > 0.5 ? 'vi2de' : 'de2vi';
            let q = {
                question: type === 'vi2de' ? v.translation_vn : (v.article ? `${v.article} ${v.word}` : v.word),
                answer: type === 'vi2de' ? (v.article ? `${v.article} ${v.word}` : v.word) : v.translation_vn,
                hint: type === 'vi2de' ? 'Tiếng Đức là gì?' : 'Nghĩa tiếng Việt là gì?',
                options: []
            };

            // Generate wrong options
            let wrongs = vocabularies.filter(x => x.id !== v.id);
            wrongs = shuffle(wrongs).slice(0, 3);
            q.options = wrongs.map(x => type === 'vi2de' ? (x.article ? `${x.article} ${x.word}` : x.word) : x.translation_vn);
            q.options.push(q.answer);
            q.options = shuffle(q.options);
            return q;
        });
        showQuizState();
    }

    // --- Mode: Quiz Article ---
    function initQuizArticle() {
        let nouns = vocabularies.filter(v => v.article && ['der', 'die', 'das'].includes(v.article.toLowerCase()));
        if (nouns.length < 1) {
            alert('Không có danh từ nào có mạo từ (der/die/das) trong sổ tay này để luyện tập!');
            showMenu();
            return;
        }

        let pool = shuffle([...nouns]).slice(0, 20);
        questions = pool.map(v => {
            return {
                question: v.word,
                answer: v.article.toLowerCase(),
                hint: `Nghĩa: ${v.translation_vn}`,
                options: ['der', 'die', 'das'] // Fixed options
            };
        });
        showQuizState();
    }

    // --- Mode: Writing ---
    function initWriting() {
        let pool = shuffle([...vocabularies]).slice(0, 15); // Max 15 qs
        questions = pool.map(v => {
            let fullWord = v.article ? `${v.article} ${v.word}` : v.word;
            let possibleAnswers = [fullWord, v.word]; // Accept with or without article
            return {
                question: v.translation_vn,
                answer: possibleAnswers,
                hint: `Nhập từ tiếng Đức (có thể có hoặc không kèm mạo từ)`
            };
        });
        showQuizState();
    }

    // --- Mode: Matching ---
    let firstCard = null;
    let matchPairsLeft = 0;

    function initMatching() {
        hideAllStates();
        matchingState.classList.remove('hidden');
        matchingState.classList.add('flex');

        const grid = document.getElementById('matching-grid');
        grid.innerHTML = '';

        // Select 6-8 pairs
        let pairCount = Math.min(8, vocabularies.length);
        let pool = shuffle([...vocabularies]).slice(0, pairCount);
        matchPairsLeft = pairCount;

        let cards = [];
        pool.forEach(v => {
            let deText = v.article ? `${v.article} ${v.word}` : v.word;
            cards.push({ id: v.id, text: deText, type: 'de' });
            cards.push({ id: v.id, text: v.translation_vn, type: 'vi' });
        });
        cards = shuffle(cards);

        cards.forEach((c, idx) => {
            const btn = document.createElement('button');
            btn.className = 'match-card bg-card border-2 border-border rounded-xl p-4 sm:p-6 text-center font-semibold text-sm sm:text-base hover:bg-secondary flex items-center justify-center min-h-[80px] shadow-sm';
            btn.textContent = c.text;
            btn.dataset.id = c.id;
            btn.dataset.type = c.type;
            btn.dataset.idx = idx;

            btn.onclick = () => handleMatchClick(btn, c);
            grid.appendChild(btn);
        });

        // Timer
        let seconds = 0;
        document.getElementById('matching-timer').textContent = '00:00';
        timerInterval = setInterval(() => {
            seconds++;
            let m = Math.floor(seconds / 60).toString().padStart(2, '0');
            let s = (seconds % 60).toString().padStart(2, '0');
            document.getElementById('matching-timer').textContent = `${m}:${s}`;
        }, 1000);
    }

    function handleMatchClick(btn, data) {
        if (btn.classList.contains('selected') || btn.classList.contains('matched')) return;

        btn.classList.add('selected');

        if (!firstCard) {
            firstCard = { btn, data };
        } else {
            // Check match
            const isMatch = firstCard.data.id === data.id && firstCard.data.type !== data.type;
            const card1 = firstCard.btn;
            const card2 = btn;

            if (isMatch) {
                setTimeout(() => {
                    card1.classList.remove('selected');
                    card1.classList.add('matched');
                    card2.classList.remove('selected');
                    card2.classList.add('matched');
                    matchPairsLeft--;
                    if (matchPairsLeft === 0) {
                        clearInterval(timerInterval);
                        score.correct = vocabularies.length; // Fake score
                        setTimeout(showSummary, 500);
                    }
                }, 300);
            } else {
                card1.classList.add('error');
                card2.classList.add('error');
                setTimeout(() => {
                    card1.classList.remove('selected', 'error');
                    card2.classList.remove('selected', 'error');
                }, 800);
            }
            firstCard = null;
        }
    }

    // --- Summary ---
    function showSummary() {
        hideAllStates();
        summaryState.classList.remove('hidden');
        summaryState.classList.add('flex');

        document.getElementById('summary-correct').textContent = score.correct;
        document.getElementById('summary-incorrect').textContent = score.incorrect;

        let msg = '';
        if (currentMode === 'matching') {
            msg = `Bạn đã hoàn thành nối nghĩa trong thời gian ${document.getElementById('matching-timer').textContent}. Tuyệt vời!`;
        } else {
            let percent = score.correct / (score.correct + score.incorrect);
            if (percent === 1) msg = 'Xuất sắc! Bạn trả lời đúng toàn bộ câu hỏi.';
            else if (percent >= 0.8) msg = 'Rất tốt! Bạn đã nắm vững phần lớn từ vựng.';
            else if (percent >= 0.5) msg = 'Khá tốt. Cần ôn tập thêm một chút nhé.';
            else msg = 'Bạn cần cố gắng hơn! Hãy xem lại flashcard trước khi làm tiếp.';
        }
        document.getElementById('summary-message').textContent = msg;
    }
});
