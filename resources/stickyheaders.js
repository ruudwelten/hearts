const stickyHeaders = (() => {
    const $header = document.querySelector('header');
    const $stickies = document.querySelectorAll('.stickyHeader');
    const $offsetTop = $header.offsetHeight;

    const load = () => {
        if (typeof $stickies === "object" && $stickies.length > 0) {
            $stickies.forEach(sticky => {
                sticky.dataset.originalPosition = sticky.offsetTop - $offsetTop;
                wrap(sticky);
            });

            window.addEventListener('scroll', () => {
                onScroll();
            });
        }
    };

    const wrap = toWrap => {
        const wrapper = document.createElement('div');
        wrapper.classList.add('stickyWrapper');
        wrapper.style.height = toWrap.offsetHeight + 'px';
        toWrap.parentNode.insertBefore(wrapper, toWrap);
        return wrapper.appendChild(toWrap);
    };

    const onScroll = () => {
        $stickies.forEach((sticky, i) => {
            const originalPosition = sticky.dataset.originalPosition;

            if (originalPosition > window.scrollY && i == 0) {
                $stickies.forEach(stuck => stuck.classList.remove('fixed'));
            } else if (originalPosition <= window.scrollY) {
                const nextSticky = $stickies[i + 1];

                if (
                    nextSticky === undefined ||
                    nextSticky.dataset.originalPosition > window.scrollY
                ) {
                    $stickies.forEach(stuck => stuck.classList.remove('fixed'));
                    sticky.classList.add('fixed');

                    if (nextSticky) {
                        const nextStickyPosition = nextSticky.dataset.originalPosition - window.scrollY;
                        const nextStickyDistance = nextStickyPosition - sticky.offsetHeight;

                        if (nextStickyDistance < 0) {
                            sticky.style.top = $offsetTop + nextStickyDistance + 'px';
                        } else {
                            sticky.style.removeProperty('top');
                        }
                    }
                }
            }
        });
    };

    return {
        load: load
    };
})();

window.addEventListener('load', e => stickyHeaders.load());
