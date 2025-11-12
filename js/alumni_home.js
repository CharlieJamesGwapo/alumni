// AlumniGram Home Page Handler
const AlumniGram = {
    state: {
        currentTab: 'info',
        user: null,
        classmates: [],
        batchmates: [],
        loading: false,
        courses: [],
        currentCourse: null
    },

    init() {
        this.loadUserData();
        this.bindEvents();
        this.showTab('info');
    },

    bindEvents() {
        // Tab switching
        $('.tab').on('click', (e) => {
            const tab = $(e.currentTarget).data('tab');
            this.showTab(tab);
        });

        // Course filter for batchmates
        $(document).on('click', '.course-filter', (e) => {
            const courseId = $(e.currentTarget).data('course');
            this.filterBatchmatesByCourse(courseId);
        });

        // Edit profile button
        $('.edit-profile').on('click', () => {
            window.location.href = 'edit_profile.php';
        });
    },

    showTab(tabId) {
        // Update active tab
        $('.tab').removeClass('active');
        $(`.tab[data-tab="${tabId}"]`).addClass('active');
        
        // Clear content area and show loader
        $('.content-area').empty().append(this.renderLoader());
        
        // Show appropriate content
        switch(tabId) {
            case 'info':
                this.showUserInfo();
                break;
            case 'classmates':
                this.loadClassmates();
                break;
            case 'batchmates':
                this.loadBatchmates();
                break;
        }
    },

    renderLoader() {
        return `<div class="loader">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>`;
    },

    async loadUserData() {
        try {
            const response = await $.ajax({
                url: 'get_user_data.php',
                method: 'GET',
                dataType: 'json'
            });

            if (response.status === 'error') {
                throw new Error(response.message || 'Failed to load user data');
            }

            this.state.user = response.data;
            this.showUserInfo();

            // Update the header info as well
            this.updateHeaderInfo(response.data);
        } catch (error) {
            console.error('Error loading user data:', error);
            
            if (error.status === 401) {
                // Redirect to login if not authenticated
                window.location.href = 'login.php';
                return;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error Loading Data',
                text: error.message || 'Failed to load user data. Please try refreshing the page.',
                confirmButtonColor: '#405DE6'
            });
        }
    },

    updateHeaderInfo(user) {
        // Update profile image
        $('.profile-avatar').attr('src', `uploads/${user.img || 'default.jpg'}`);
        
        // Update username and name
        $('.username').text(user.firstname + ' ' + user.lastname);
        $('.bio-name').text(user.firstname + ' ' + user.lastname);
        
        // Update stats
        if (user.stats) {
            $('.stat-count').each(function(index) {
                const stats = ['posts', 'followers', 'following'];
                $(this).text(user.stats[stats[index]] || 0);
            });
        }
        
        // Update bio information
        $('.bio p').eq(0).text(`Alumni ID: ${user.alumni_id}`);
        $('.bio p').eq(1).text(`Batch ${user.batch} | ${user.course_name}`);
        $('.bio p').eq(2).text(`📍 ${user.company_address || 'Not specified'}`);
        $('.bio p').eq(3).text(`✉️ ${user.email}`);
        $('.bio p').eq(4).text(`📱 ${user.contact_no}`);
    },

    showUserInfo() {
        const user = this.state.user;
        if (!user) return;

        const infoHtml = `
            <div class="user-details">
                <div class="detail-group">
                    <h3>Personal Information</h3>
                    <div class="detail-item">
                        <i class="fas fa-id-card"></i>
                        <span>Alumni ID: ${user.alumni_id}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-user"></i>
                        <span>Name: ${user.firstname} ${user.middlename} ${user.lastname} ${user.suffixname}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-birthday-cake"></i>
                        <span>Birthdate: ${user.birthdate}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-venus-mars"></i>
                        <span>Gender: ${user.gender}</span>
                    </div>
                </div>

                <div class="detail-group">
                    <h3>Contact Information</h3>
                    <div class="detail-item">
                        <i class="fas fa-envelope"></i>
                        <span>Email: ${user.email}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-phone"></i>
                        <span>Contact: ${user.contact_no}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Address: ${user.address}</span>
                    </div>
                </div>

                <div class="detail-group">
                    <h3>Professional Information</h3>
                    <div class="detail-item">
                        <i class="fas fa-building"></i>
                        <span>Company Address: ${user.company_address}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-envelope"></i>
                        <span>Company Email: ${user.company_email}</span>
                    </div>
                </div>

                <div class="detail-group">
                    <h3>Academic Information</h3>
                    <div class="detail-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Batch: ${user.batch}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-book"></i>
                        <span>Course: ${user.course_id}</span>
                    </div>
                </div>
            </div>
        `;

        $('.content-area').html(infoHtml);
    },

    async loadClassmates() {
        try {
            this.state.loading = true;
            const response = await $.ajax({
                url: 'get_classmates.php',
                method: 'GET',
                data: {
                    course_id: this.state.user.course_id,
                    batch: this.state.user.batch
                },
                dataType: 'json'
            });

            if (response.error) {
                throw new Error(response.error);
            }

            this.state.classmates = response;
            this.showClassmates();
        } catch (error) {
            console.error('Error loading classmates:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error Loading Classmates',
                text: error.message || 'Failed to load classmates. Please try again.',
                confirmButtonColor: '#405DE6'
            });
        } finally {
            this.state.loading = false;
        }
    },

    showClassmates() {
        const classmatesHtml = this.state.classmates.length ? `
            <div class="alumni-grid">
                ${this.state.classmates.map(classmate => this.renderAlumniCard(classmate)).join('')}
            </div>
        ` : '<div class="no-results">No classmates found in your batch.</div>';
        
        $('.content-area').html(classmatesHtml);
    },

    async loadBatchmates() {
        try {
            this.state.loading = true;
            
            // Load courses and batchmates in parallel
            const [courses, batchmates] = await Promise.all([
                $.ajax({
                    url: 'get_courses.php',
                    method: 'GET',
                    dataType: 'json'
                }),
                $.ajax({
                    url: 'get_batchmates.php',
                    method: 'GET',
                    data: { batch: this.state.user.batch },
                    dataType: 'json'
                })
            ]);

            if (courses.error) throw new Error(courses.error);
            if (batchmates.error) throw new Error(batchmates.error);

            this.state.batchmates = batchmates;
            this.showBatchmates(courses);
        } catch (error) {
            console.error('Error loading batchmates:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error Loading Batchmates',
                text: error.message || 'Failed to load batchmates. Please try again.',
                confirmButtonColor: '#405DE6'
            });
        } finally {
            this.state.loading = false;
        }
    },

    showBatchmates(courses) {
        const coursesHtml = courses.map(course => `
            <button class="course-filter" data-course="${course.id}">
                ${course.course}
            </button>
        `).join('');

        const batchmatesHtml = this.state.batchmates.length ? `
            <div class="courses-filter">
                <h3>Filter by Course:</h3>
                <div class="course-buttons">
                    <button class="course-filter active" data-course="all">All Courses</button>
                    ${coursesHtml}
                </div>
            </div>
            <div class="alumni-grid">
                ${this.state.batchmates.map(batchmate => this.renderAlumniCard(batchmate)).join('')}
            </div>
        ` : '<div class="no-results">No batchmates found.</div>';

        $('.content-area').html(batchmatesHtml);
    },

    renderAlumniCard(alumni) {
        return `
            <div class="alumni-card">
                <img src="assets/uploads/${alumni.img || 'default.jpg'}" alt="${alumni.firstname} ${alumni.lastname}" onerror="this.src='assets/img/default.jpg'">
                <div class="alumni-info">
                    <h4>${alumni.firstname} ${alumni.lastname}</h4>
                    <p><i class="fas fa-graduation-cap"></i> ${alumni.course_name || 'Course not specified'}</p>
                    <p><i class="fas fa-building"></i> ${alumni.company_address || 'Not specified'}</p>
                    <button onclick="AlumniGram.viewProfile('${alumni.id}')" class="view-profile">
                        View Profile
                    </button>
                </div>
            </div>
        `;
    },

    filterBatchmatesByCourse(courseId) {
        $('.course-filter').removeClass('active');
        $(`.course-filter[data-course="${courseId}"]`).addClass('active');

        const filtered = courseId === 'all' 
            ? this.state.batchmates 
            : this.state.batchmates.filter(b => b.course_id === courseId);

        $('.alumni-grid').html(
            filtered.length 
                ? filtered.map(batchmate => this.renderAlumniCard(batchmate)).join('')
                : '<div class="no-results">No alumni found for this course.</div>'
        );
    },

    viewProfile(alumniId) {
        window.location.href = `view_alumni.php?id=${alumniId}`;
    }
};

// Initialize when document is ready
$(document).ready(() => AlumniGram.init());
