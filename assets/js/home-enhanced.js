/**
 * MOIST ALUMNI HOME - ENHANCED JAVASCRIPT
 * Real-time, Dynamic, Fully Functional
 */

(function() {
    'use strict';

    // ===== CONFIGURATION =====
    const CONFIG = {
        REAL_TIME_INTERVAL: 30000, // 30 seconds
        TOAST_DURATION: 5000,
        ANIMATION_DURATION: 300,
        DEBOUNCE_DELAY: 500
    };

    // ===== TOAST NOTIFICATION SYSTEM =====
    const Toast = {
        container: null,
        
        init() {
            if (!this.container) {
                this.container = document.createElement('div');
                this.container.id = 'toast-container';
                this.container.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 10000;
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    max-width: 400px;
                `;
                document.body.appendChild(this.container);
            }
        },
        
        show(message, type = 'info') {
            this.init();
            
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.style.cssText = `
                padding: 1rem 1.25rem;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                animation: slideInRight 0.3s ease;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                min-width: 300px;
            `;
            
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8'
            };
            
            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };
            
            toast.style.background = colors[type] || colors.info;
            toast.innerHTML = `
                <span style="font-size: 1.25rem;">${icons[type] || icons.info}</span>
                <span style="flex: 1;">${message}</span>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.25rem; padding: 0; line-height: 1;">×</button>
            `;
            
            this.container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, CONFIG.TOAST_DURATION);
        },
        
        success(message) { this.show(message, 'success'); },
        error(message) { this.show(message, 'error'); },
        warning(message) { this.show(message, 'warning'); },
        info(message) { this.show(message, 'info'); }
    };

    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // ===== REAL-TIME UPDATES =====
    const RealTimeUpdates = {
        intervals: {},
        
        start(key, callback, interval = CONFIG.REAL_TIME_INTERVAL) {
            this.stop(key);
            callback(); // Run immediately
            this.intervals[key] = setInterval(callback, interval);
            console.log(`✓ Real-time updates started: ${key}`);
        },
        
        stop(key) {
            if (this.intervals[key]) {
                clearInterval(this.intervals[key]);
                delete this.intervals[key];
                console.log(`✓ Real-time updates stopped: ${key}`);
            }
        },
        
        stopAll() {
            Object.keys(this.intervals).forEach(key => this.stop(key));
        }
    };

    // ===== LOADING OVERLAY =====
    const Loading = {
        overlay: null,
        
        show(message = 'Loading...') {
            if (!this.overlay) {
                this.overlay = document.createElement('div');
                this.overlay.className = 'loading-overlay';
                this.overlay.innerHTML = `
                    <div style="text-align: center; color: white;">
                        <div class="spinner-border mb-3"></div>
                        <div id="loading-message" style="font-size: 1.125rem; font-weight: 500;">${message}</div>
                    </div>
                `;
                document.body.appendChild(this.overlay);
            } else {
                this.overlay.style.display = 'flex';
                document.getElementById('loading-message').textContent = message;
            }
        },
        
        hide() {
            if (this.overlay) {
                this.overlay.style.display = 'none';
            }
        },
        
        updateMessage(message) {
            const messageEl = document.getElementById('loading-message');
            if (messageEl) {
                messageEl.textContent = message;
            }
        }
    };

    // ===== DEBOUNCE UTILITY =====
    function debounce(func, wait = CONFIG.DEBOUNCE_DELAY) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ===== AJAX HELPER =====
    function ajax(action, data = {}, options = {}) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('ajax_action', action);
            
            Object.keys(data).forEach(key => {
                if (data[key] instanceof File) {
                    formData.append(key, data[key]);
                } else {
                    formData.append(key, data[key]);
                }
            });
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData,
                ...options
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    resolve(data);
                } else {
                    reject(new Error(data.msg || 'Request failed'));
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                reject(error);
            });
        });
    }

    // ===== LIKE FUNCTIONALITY =====
    function initializeLikes() {
        document.addEventListener('click', function(e) {
            const likeBtn = e.target.closest('.like-toggle, .like-btn');
            if (!likeBtn) return;
            
            e.preventDefault();
            const eventId = likeBtn.dataset.event;
            const userId = likeBtn.dataset.userId;
            
            if (eventId) {
                ajax('toggle_like', { event_id: eventId, csrf_token: window.csrfToken })
                    .then(data => {
                        const icon = likeBtn.querySelector('i');
                        const count = likeBtn.querySelector('.likes-count, .like-count');
                        
                        if (data.data.liked) {
                            icon.classList.remove('fa-regular');
                            icon.classList.add('fa-solid');
                            likeBtn.classList.add('liked');
                            Toast.success('Liked!');
                        } else {
                            icon.classList.remove('fa-solid');
                            icon.classList.add('fa-regular');
                            likeBtn.classList.remove('liked');
                            Toast.info('Unliked');
                        }
                        
                        if (count) {
                            count.textContent = data.data.count;
                        }
                    })
                    .catch(error => {
                        Toast.error(error.message || 'Failed to update like');
                    });
            } else if (userId) {
                ajax('like_user', { user_id: userId })
                    .then(data => {
                        const icon = likeBtn.querySelector('i');
                        const count = likeBtn.querySelector('.like-count');
                        
                        if (data.data.liked) {
                            icon.classList.remove('fa-regular');
                            icon.classList.add('fa-solid');
                            Toast.success('Liked!');
                        } else {
                            icon.classList.remove('fa-solid');
                            icon.classList.add('fa-regular');
                            Toast.info('Unliked');
                        }
                        
                        if (count) {
                            count.textContent = data.data.count;
                        }
                    })
                    .catch(error => {
                        Toast.error(error.message || 'Failed to update like');
                    });
            }
        });
    }

    // ===== BOOKMARK FUNCTIONALITY =====
    function initializeBookmarks() {
        document.addEventListener('click', function(e) {
            const bookmarkBtn = e.target.closest('.bookmark-toggle');
            if (!bookmarkBtn) return;
            
            e.preventDefault();
            const eventId = bookmarkBtn.dataset.event;
            
            ajax('toggle_bookmark', { event_id: eventId, csrf_token: window.csrfToken })
                .then(data => {
                    const icon = bookmarkBtn.querySelector('i');
                    
                    if (data.data.bookmarked) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        bookmarkBtn.classList.add('bookmarked');
                        Toast.success('Bookmarked!');
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        bookmarkBtn.classList.remove('bookmarked');
                        Toast.info('Bookmark removed');
                    }
                })
                .catch(error => {
                    Toast.error(error.message || 'Failed to update bookmark');
                });
        });
    }

    // ===== COMMENT FUNCTIONALITY =====
    function initializeComments() {
        document.addEventListener('click', function(e) {
            // Open comments
            const commentBtn = e.target.closest('.comment-open, .comment-btn');
            if (commentBtn) {
                e.preventDefault();
                const eventId = commentBtn.dataset.event;
                const userId = commentBtn.dataset.userId;
                
                if (eventId) {
                    openEventComments(eventId);
                } else if (userId) {
                    toggleUserComments(userId);
                }
            }
            
            // Send comment
            const sendBtn = e.target.closest('.send-comment-btn');
            if (sendBtn) {
                e.preventDefault();
                const userId = sendBtn.dataset.userId;
                const input = document.querySelector(`.comment-input[data-user-id="${userId}"]`);
                const comment = input.value.trim();
                
                if (!comment) {
                    Toast.warning('Please enter a comment');
                    return;
                }
                
                ajax('add_comment', { user_id: userId, comment: comment })
                    .then(data => {
                        Toast.success('Comment added!');
                        input.value = '';
                        loadUserComments(userId);
                    })
                    .catch(error => {
                        Toast.error(error.message || 'Failed to add comment');
                    });
            }
        });
    }

    function toggleUserComments(userId) {
        const commentsSection = document.getElementById(`comments-${userId}`);
        if (!commentsSection) return;
        
        if (commentsSection.style.display === 'none') {
            commentsSection.style.display = 'block';
            loadUserComments(userId);
        } else {
            commentsSection.style.display = 'none';
        }
    }

    function loadUserComments(userId) {
        ajax('get_comments', { user_id: userId })
            .then(data => {
                const commentsList = document.querySelector(`#comments-${userId} .comments-list`);
                if (!commentsList) return;
                
                if (data.data.comments.length === 0) {
                    commentsList.innerHTML = '<p class="text-muted small">No comments yet</p>';
                    return;
                }
                
                commentsList.innerHTML = data.data.comments.map(comment => `
                    <div class="comment mb-2">
                        <div class="d-flex gap-2">
                            ${comment.img ? 
                                `<img src="uploads/${comment.img}" class="avatar avatar-sm">` :
                                `<div class="avatar avatar-sm bg-primary text-white d-flex align-items-center justify-content-center">${comment.firstname[0]}${comment.lastname[0]}</div>`
                            }
                            <div class="flex-grow-1">
                                <div class="comment-body">
                                    <strong>${comment.firstname} ${comment.lastname}</strong>
                                    <p class="mb-0 small">${comment.comment}</p>
                                    <small class="text-muted">${new Date(comment.created_at).toLocaleString()}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => {
                Toast.error('Failed to load comments');
            });
    }

    // ===== INFINITE SCROLL =====
    function initializeInfiniteScroll() {
        let loading = false;
        let page = 1;
        let hasMore = true;
        
        window.addEventListener('scroll', debounce(function() {
            if (loading || !hasMore) return;
            
            const scrollPosition = window.innerHeight + window.scrollY;
            const threshold = document.documentElement.scrollHeight - 500;
            
            if (scrollPosition >= threshold) {
                loading = true;
                page++;
                
                ajax('fetch_timeline', { page: page })
                    .then(data => {
                        const container = document.querySelector('.timeline-container, #eventsGrid');
                        if (container && data.data.html) {
                            container.insertAdjacentHTML('beforeend', data.data.html);
                            Toast.info('More posts loaded');
                        }
                        hasMore = data.data.hasMore;
                        loading = false;
                    })
                    .catch(error => {
                        console.error('Failed to load more posts:', error);
                        loading = false;
                    });
            }
        }, 200));
    }

    // ===== AUTO-REFRESH STATS =====
    function initializeStatsRefresh() {
        RealTimeUpdates.start('stats', function() {
            // Update like counts
            document.querySelectorAll('[data-event]').forEach(el => {
                const eventId = el.dataset.event;
                ajax('get_likes', { event_id: eventId })
                    .then(data => {
                        const count = el.querySelector('.likes-count');
                        if (count && data.data.count !== undefined) {
                            count.textContent = data.data.count;
                        }
                    })
                    .catch(() => {});
            });
        });
    }

    // ===== INITIALIZE ALL =====
    function init() {
        console.log('🚀 Initializing MOIST Alumni Home Enhanced...');
        
        // Store CSRF token globally
        const csrfInput = document.querySelector('input[name="csrf_token"]');
        if (csrfInput) {
            window.csrfToken = csrfInput.value;
        }
        
        // Initialize features
        initializeLikes();
        initializeBookmarks();
        initializeComments();
        initializeInfiniteScroll();
        initializeStatsRefresh();
        
        // Add live indicator
        const header = document.querySelector('.navbar, header');
        if (header) {
            const liveIndicator = document.createElement('div');
            liveIndicator.className = 'live-indicator';
            liveIndicator.innerHTML = '<span class="live-dot"></span> LIVE';
            liveIndicator.style.cssText = 'position: absolute; top: 10px; right: 10px;';
            header.style.position = 'relative';
            header.appendChild(liveIndicator);
        }
        
        console.log('✓ All features initialized successfully!');
        Toast.success('Welcome! Real-time features are active.');
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        RealTimeUpdates.stopAll();
    });

    // Expose utilities globally
    window.MoistHome = {
        Toast,
        Loading,
        RealTimeUpdates,
        ajax
    };

})();
