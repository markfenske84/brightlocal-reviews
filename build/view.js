/******/ (() => { // webpackBootstrap
/*!*********************!*\
  !*** ./src/view.js ***!
  \*********************/
document.addEventListener('DOMContentLoaded', function () {
  // Read More functionality
  const readMoreButtons = document.querySelectorAll('.bl-review-read-more');

  // Hide buttons that are not needed (content not truncated)
  readMoreButtons.forEach(button => {
    const contentWrapper = button.previousElementSibling;
    if (!contentWrapper) {
      return;
    }

    // If the content wrapper does not overflow, it means text fits and no read more is required
    const needsToggle = contentWrapper.scrollHeight > contentWrapper.clientHeight + 1; // +1 to account for rounding differences
    if (!needsToggle) {
      // Remove truncated class & hide button entirely
      contentWrapper.classList.remove('bl-review-content-truncated');
      button.style.display = 'none';
    }
  });

  // Track if a click is in progress to prevent double-triggering
  let isClickInProgress = false;
  readMoreButtons.forEach((button, index) => {
    // Verify the button structure
    const contentWrapper = button.previousElementSibling;
    if (!contentWrapper) {
      // Button missing content wrapper, skip
      return;
    }

    // Remove any existing click handlers to prevent duplicates
    button.removeEventListener('click', handleClick);

    // Add a test click handler to verify the button is clickable
    button.addEventListener('click', handleClick, true);
    function handleClick(e) {
      // Prevent multiple triggers
      if (isClickInProgress) {
        return;
      }
      isClickInProgress = true;
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      // Get the content wrapper again to ensure we have the latest reference
      const contentWrapper = this.previousElementSibling;
      if (!contentWrapper) {
        // Content wrapper not found: abort
        isClickInProgress = false;
        return;
      }
      const isExpanded = contentWrapper.classList.contains('expanded');
      if (!isExpanded) {
        // expanding content
        contentWrapper.classList.add('expanded');
        this.textContent = 'Show Less';
        this.setAttribute('aria-expanded', 'true');
      } else {
        // collapsing content
        contentWrapper.classList.remove('expanded');
        this.textContent = 'Read More';
        this.setAttribute('aria-expanded', 'false');
      }

      /* If inside carousel, adjust wrapper height after transition */
      const reviewItemEl = this.closest('.bl-review-item');
      const carouselParent = reviewItemEl ? reviewItemEl.closest('.bl-reviews-carousel') : null;
      if (carouselParent) {
        // Wait a bit for CSS transition (content expand) to complete
        setTimeout(() => {
          carouselParent.style.height = reviewItemEl.offsetHeight + 'px';
        }, 350);
      }

      // Reset click in progress after a short delay
      setTimeout(() => {
        isClickInProgress = false;
      }, 100);
    }
  });

  // Carousel functionality
  const carouselWrapper = document.querySelector('.bl-reviews-carousel');
  if (carouselWrapper) {
    const items = carouselWrapper.querySelectorAll('.bl-review-item');
    let currentIndex = 0;
    const showArrows = carouselWrapper.dataset.arrows !== 'false';
    let prevButton, nextButton;
    if (showArrows) {
      prevButton = document.createElement('button');
      prevButton.className = 'bl-carousel-nav bl-carousel-prev';
      prevButton.innerHTML = '←';
      prevButton.setAttribute('aria-label', 'Previous review');
      nextButton = document.createElement('button');
      nextButton.className = 'bl-carousel-nav bl-carousel-next';
      nextButton.innerHTML = '→';
      nextButton.setAttribute('aria-label', 'Next review');
      carouselWrapper.appendChild(prevButton);
      carouselWrapper.appendChild(nextButton);
    }

    // Show first item
    if (items.length) {
      items[0].classList.add('active');
      carouselWrapper.style.height = items[0].offsetHeight + 'px';
    }

    // Navigation functions
    function showSlide(index) {
      items.forEach((item, i) => {
        item.classList.toggle('active', i === index);
      });

      // Adjust wrapper height to match active slide
      const activeItem = items[index];
      if (activeItem) {
        carouselWrapper.style.height = activeItem.offsetHeight + 'px';
      }
    }
    function nextSlide() {
      currentIndex = (currentIndex + 1) % items.length;
      showSlide(currentIndex);
    }
    function prevSlide() {
      currentIndex = (currentIndex - 1 + items.length) % items.length;
      showSlide(currentIndex);
    }

    // Event listeners (buttons)
    if (showArrows && nextButton && prevButton) {
      nextButton.addEventListener('click', nextSlide);
      prevButton.addEventListener('click', prevSlide);
    }

    // Swipe support
    let startX = 0;
    let isTouching = false;
    carouselWrapper.addEventListener('touchstart', e => {
      if (!e.touches || e.touches.length === 0) return;
      startX = e.touches[0].clientX;
      isTouching = true;
    });
    carouselWrapper.addEventListener('touchmove', e => {
      // Prevent vertical scroll being treated as swipe
      if (isTouching) {
        e.preventDefault();
      }
    }, {
      passive: false
    });
    carouselWrapper.addEventListener('touchend', e => {
      if (!isTouching) return;
      const endX = e.changedTouches[0].clientX;
      const diffX = endX - startX;
      if (Math.abs(diffX) > 50) {
        // threshold
        if (diffX < 0) {
          nextSlide();
        } else {
          prevSlide();
        }
      }
      isTouching = false;
    });

    // Auto-advance every 5 seconds
    let autoInterval = setInterval(nextSlide, 5000);

    // Optionally pause on hover—only when arrows are visible (interactive mode)
    if (showArrows) {
      carouselWrapper.addEventListener('mouseenter', () => clearInterval(autoInterval));
      carouselWrapper.addEventListener('mouseleave', () => {
        autoInterval = setInterval(nextSlide, 5000);
      });
    }

    // Mouse drag (desktop) support — mirrors the touch swipe logic above
    let dragStartX = 0;
    let isDragging = false;
    carouselWrapper.addEventListener('mousedown', e => {
      // Only respond to primary button
      if (e.button !== 0) return;
      dragStartX = e.clientX;
      isDragging = true;
      // Prevent image dragging / text selection
      e.preventDefault();
    });
    carouselWrapper.addEventListener('mousemove', e => {
      if (!isDragging) return;
      // Prevent text selection while dragging
      e.preventDefault();
    });
    const endDrag = clientX => {
      const diffX = clientX - dragStartX;
      if (Math.abs(diffX) > 50) {
        // same threshold as touch
        if (diffX < 0) {
          nextSlide();
        } else {
          prevSlide();
        }
      }
      isDragging = false;
    };
    carouselWrapper.addEventListener('mouseup', e => {
      if (!isDragging) return;
      endDrag(e.clientX);
    });
    carouselWrapper.addEventListener('mouseleave', () => {
      // Cancel drag if cursor leaves carousel bounds
      isDragging = false;
    });
  }

  /* Load More functionality for paginated reviews */
  const loadMoreButtons = document.querySelectorAll('.bl-reviews-load-more');
  loadMoreButtons.forEach(btn => {
    // If associated wrapper is carousel, remove button entirely
    let wrapperEl = btn.previousElementSibling;
    if (!wrapperEl || !wrapperEl.classList.contains('bl-reviews-wrapper')) {
      wrapperEl = btn.closest('.bl-reviews-wrapper');
    }
    if (wrapperEl && wrapperEl.classList.contains('bl-reviews-carousel')) {
      btn.style.display = 'none';
      return; // skip attaching listeners
    }
    btn.addEventListener('click', function () {
      if (this.dataset.loading === 'true') {
        return;
      }
      const perPage = parseInt(this.dataset.perPage);
      const offset = parseInt(this.dataset.offset);
      const label = this.dataset.label || 'all';
      const params = new URLSearchParams();
      params.append('action', 'bl_load_more_reviews');
      params.append('nonce', window.blReviews.nonce);
      params.append('per_page', perPage);
      params.append('offset', offset);
      params.append('label', label);
      this.dataset.loading = 'true';
      fetch(window.blReviews.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: params.toString()
      }).then(response => response.text()).then(html => {
        if (!html.trim()) {
          // No more content
          this.style.display = 'none';
          return;
        }
        let wrapper = this.previousElementSibling;
        if (!wrapper || !wrapper.classList.contains('bl-reviews-wrapper')) {
          wrapper = this.closest('.bl-reviews-wrapper');
        }
        if (!wrapper) return;
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const newItems = tempDiv.querySelectorAll('.bl-review-item');
        newItems.forEach(item => wrapper.appendChild(item));

        // Update offset
        const newOffset = offset + perPage;
        this.dataset.offset = newOffset;

        // Hide button if fewer items returned than requested (means no more posts)
        if (newItems.length < perPage) {
          this.style.display = 'none';
        }

        /* -----------------------------------------------------
         * Re-initialise "Read More" functionality for the items
         * that were just appended via "Load More". We replicate
         * the same logic that runs on initial page load so that
         * newly-added reviews get the expandable behaviour.
         * --------------------------------------------------- */
        newItems.forEach(item => {
          const readMoreBtn = item.querySelector('.bl-review-read-more');
          if (!readMoreBtn) {
            return; // No read-more button inside this item
          }

          // Prevent duplicate listeners if, for some reason, we
          // re-initialise the same node again.
          if (readMoreBtn.dataset.rmInitialized === 'true') {
            return;
          }
          const contentWrapper = readMoreBtn.previousElementSibling;
          if (contentWrapper) {
            const needsToggle = contentWrapper.scrollHeight > contentWrapper.clientHeight + 1;
            if (!needsToggle) {
              // Content already fully visible – hide button
              contentWrapper.classList.remove('bl-review-content-truncated');
              readMoreBtn.style.display = 'none';
            }
          }

          // Click handler (mirrors the one attached on DOM load)
          readMoreBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const wrapper = this.previousElementSibling;
            if (!wrapper) {
              return;
            }
            const expanded = wrapper.classList.contains('expanded');
            if (!expanded) {
              wrapper.classList.add('expanded');
              this.textContent = 'Show Less';
              this.setAttribute('aria-expanded', 'true');
            } else {
              wrapper.classList.remove('expanded');
              this.textContent = 'Read More';
              this.setAttribute('aria-expanded', 'false');
            }

            /* If this review lives in a carousel, resize the
             * carousel container so the expanded content is
             * fully visible.
             */
            const reviewItemEl = this.closest('.bl-review-item');
            const carouselParent = reviewItemEl ? reviewItemEl.closest('.bl-reviews-carousel') : null;
            if (carouselParent) {
              setTimeout(() => {
                carouselParent.style.height = reviewItemEl.offsetHeight + 'px';
              }, 350);
            }
          }, true);

          // Mark as initialised so we don't double-bind later.
          readMoreBtn.dataset.rmInitialized = 'true';
        });
        // ---------------------------------------------------------
        // END re-initialise read-more for dynamically loaded items
        // ---------------------------------------------------------
      }).catch(err => {
        console.error('Error loading more reviews', err);
      }).finally(() => {
        this.dataset.loading = 'false';
      });
    });
  });

  // Remove focus after click to prevent sticky :focus styles
  document.addEventListener('click', function (e) {
    if (e.target && (e.target.classList.contains('bl-review-read-more') || e.target.classList.contains('bl-reviews-load-more'))) {
      e.target.blur();
    }
  });
});
/******/ })()
;
//# sourceMappingURL=view.js.map