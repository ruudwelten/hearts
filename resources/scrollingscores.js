const scrollingScores = (() => {
    const $tricks = document.querySelectorAll('.trick');
    const $playerCount = document.querySelectorAll('.score').length;
    const $footer = document.querySelector('footer');
    let $offsetBottom;
    const $scores = document.querySelectorAll('.score');

    const load = () => {
        if (typeof $tricks === "object" && $tricks.length > 0) {
            $footer.classList.add('fixed');
            $offsetBottom = $footer.offsetHeight;

            $tricks.forEach(trick => {
                trick.dataset.bottomPosition = trick.offsetTop + trick.offsetHeight;
            });

            onScroll();

            window.addEventListener('scroll', () => {
                onScroll();
            });
        }
    }

    const onScroll = () => {
        // When scrolled to bottom, set .bottom on footer
        if (Math.ceil(window.innerHeight + window.scrollY) >= document.body.scrollHeight) {
            $footer.classList.add('bottom');
            $footer.getElementsByTagName('h3')[0].innerText = 'Final Scores:';
        } else {
            $footer.classList.remove('bottom');
            $footer.getElementsByTagName('h3')[0].innerText = 'Scores:';
        }

        for (i = 0; i < $tricks.length; i++) {
            const trick = $tricks[i];
            const bottomPosition = trick.dataset.bottomPosition;
            const scrollPosition = Math.floor(window.scrollY + window.innerHeight - $offsetBottom);

            if (bottomPosition <= scrollPosition) {
                const nextTrick = $tricks[i + 1];

                if (
                    nextTrick === undefined ||
                    nextTrick.dataset.bottomPosition > scrollPosition
                ) {
                    showScores(trick);
                    break;
                }
            }
        }
    }

    const showScores = trick => {
        for (i = 0; i < $playerCount; i++) {
            const prevScore = $scores[i].innerText;
            $scores[i].innerText = trick.dataset['score' + i];

            if ($scores[i].innerText >= 100) {
                $scores[i].style.color = '#f00';
            } else if (prevScore >= 100) {
                $scores[i].style.removeProperty('color');
            }
        }
    }

    return {
        load: load
    };
})();

window.addEventListener('load', e => scrollingScores.load());
