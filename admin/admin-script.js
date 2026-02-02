// admin-script.js
class AdminDashboard {
    constructor() {
        this.currentPage = 'dashboard';
        this.selectedUsers = new Set();
        this.init();
    }

    init() {
        this.setupNavigation();
        this.setupSearch();
        this.setupFilters();
        this.setupUserActions();
        this.setupModals();
        this.setupForms();
        this.loadDashboardData();
        this.setupContextMenu();
    }

    setupNavigation() {
        // For multi-file structure, we don't need to intercept navigation clicks
        // Let the browser handle the navigation naturally
        // Only keep the active state management for the current page
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href === currentPath || href === window.location.href.split('/').pop()) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    switchToSection(section) {
        const navItem = document.querySelector(`.nav-item[href="${section}"]`);
        if (navItem) {
            navItem.click();
        }
    }

    updatePageTitle(section) {
        const titles = {
            dashboard: { title: 'Dashboard Overview', subtitle: 'System statistics and analytics' },
            users: { title: 'User Management', subtitle: 'Manage and monitor user accounts' },
            guides: { title: 'Tour Guides', subtitle: 'Manage tour guide profiles' },
            destinations: { title: 'Destinations', subtitle: 'Manage tourist spots and attractions' },
            bookings: { title: 'Booking Management', subtitle: 'View and manage tour bookings' },
            analytics: { title: 'Analytics', subtitle: 'Detailed statistics and reports' },
            reports: { title: 'Reports', subtitle: 'Generate and export reports' },
            settings: { title: 'System Settings', subtitle: 'Configure system preferences' }
        };

        const info = titles[section] || titles.dashboard;
        document.getElementById('pageTitle').textContent = info.title;
        document.getElementById('pageSubtitle').textContent = info.subtitle;
    }

    setupSearch() {
        const searchInput = document.getElementById('userSearch');
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const searchTerm = e.target.value;
                    window.location.href = `?search=${encodeURIComponent(searchTerm)}`;
                }, 500);
            });
        }
    }

    setupFilters() {
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', (e) => {
                this.applyFilters();
            });
        });
    }

    applyFilters() {
        const status = document.getElementById('filterStatus')?.value;
        
        if (status && status !== 'all') {
            const rows = document.querySelectorAll('#usersTable tbody tr');
            rows.forEach(row => {
                const statusBadge = row.querySelector('.status-badge');
                if (statusBadge) {
                    const rowStatus = statusBadge.textContent.toLowerCase();
                    row.style.display = rowStatus === status ? '' : 'none';
                }
            });
        } else {
            // Show all rows
            const rows = document.querySelectorAll('#usersTable tbody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        }
    }

    setupUserActions() {
        // Select all checkbox
        const selectAll = document.getElementById('selectAllUsers');
        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
                const checkboxes = document.querySelectorAll('.user-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = e.target.checked;
                    this.toggleUserSelection(checkbox);
                });
            });
        }

        // Individual checkboxes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('user-checkbox')) {
                this.toggleUserSelection(e.target);
            }
        });

        // Row click for selection
        document.addEventListener('click', (e) => {
            const row = e.target.closest('tr[data-user-id]');
            if (row && !e.target.closest('.action-buttons')) {
                const checkbox = row.querySelector('.user-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    this.toggleUserSelection(checkbox);
                }
            }
        });

        // Bulk action buttons
        document.getElementById('bulkActivate')?.addEventListener('click', () => this.bulkUpdateStatus('active'));
        document.getElementById('bulkDeactivate')?.addEventListener('click', () => this.bulkUpdateStatus('inactive'));
        document.getElementById('bulkDelete')?.addEventListener('click', () => this.bulkDeleteUsers());
    }

    toggleUserSelection(checkbox) {
        const userId = checkbox.value;
        const row = checkbox.closest('tr');
        
        if (checkbox.checked) {
            this.selectedUsers.add(userId);
            row.classList.add('selected');
        } else {
            this.selectedUsers.delete(userId);
            row.classList.remove('selected');
            document.getElementById('selectAllUsers').checked = false;
        }
        
        this.updateBulkActions();
    }

    updateBulkActions() {
        const bulkActions = document.getElementById('bulkActionsPanel');
        const count = this.selectedUsers.size;
        
        if (bulkActions) {
            if (count > 0) {
                bulkActions.classList.remove('hidden');
                bulkActions.querySelector('.selected-count').textContent = count;
            } else {
                bulkActions.classList.add('hidden');
            }
        }
    }

    setupModals() {
        // Close modals on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Close modals on outside click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                this.closeModal(e.target);
            }
        });
    }

    showModal(modalId, data = null) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Populate modal with data if provided
            if (data) {
                this.populateModal(modalId, data);
            }
            
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modal) {
        if (typeof modal === 'string') {
            modal = document.getElementById(modal);
        }
        
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    closeAllModals() {
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            this.closeModal(modal);
        });
    }

    populateModal(modalId, data) {
        switch (modalId) {
            case 'editUserModal':
                document.getElementById('editUserId').value = data.id;
                document.getElementById('editFirstName').value = data.first_name;
                document.getElementById('editLastName').value = data.last_name;
                document.getElementById('editEmail').value = data.email;
                document.getElementById('editPhone').value = data.phone || '';
                document.getElementById('editStatus').value = data.status;
                break;
            case 'editGuideModal':
                document.getElementById('editGuideId').value = data.id;
                document.getElementById('editGuideName').value = data.name;
                document.getElementById('editGuideEmail').value = data.email;
                document.getElementById('editGuideContact').value = data.contact_number || '';
                document.getElementById('editGuideSpecialty').value = data.specialty;
                document.getElementById('editGuideCategory').value = data.category;
                document.getElementById('editGuideExperience').value = data.experience_years;
                document.getElementById('editGuideRating').value = data.rating;
                document.getElementById('editGuideReviews').value = data.review_count;
                document.getElementById('editGuidePriceRange').value = data.price_range;
                document.getElementById('editGuideGroupSize').value = data.group_size;
                document.getElementById('editGuideTotalTours').value = data.total_tours;
                document.getElementById('editGuideLanguages').value = data.languages;
                document.getElementById('editGuideBio').value = data.bio || '';
                document.getElementById('editGuideExpertise').value = data.areas_of_expertise || '';
                document.getElementById('editGuidePhoto').value = data.photo_url || '';
                document.getElementById('editGuideStatus').value = data.status || 'active';
                document.getElementById('editGuideVerified').checked = data.verified ? true : false;
                break;
            case 'editSpotModal':
                document.getElementById('editSpotId').value = data.id;
                document.getElementById('editSpotName').value = data.name;
                document.getElementById('editSpotLocation').value = data.location;
                document.getElementById('editSpotCategory').value = data.category;
                document.getElementById('editSpotRating').value = data.rating;
                document.getElementById('editSpotDescription').value = data.description || '';
                break;
            case 'editHotelModal':
                document.getElementById('editHotelId').value = data.id;
                document.getElementById('editHotelName').value = data.name;
                document.getElementById('editHotelLocation').value = data.location;
                document.getElementById('editHotelRating').value = data.rating;
                document.getElementById('editHotelPriceRange').value = data.price_range;
                document.getElementById('editHotelDescription').value = data.description || '';
                break;
        }
    }

    setupForms() {
        // Add User Form
        const addUserForm = document.getElementById('addUserForm');
        if (addUserForm) {
            addUserForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitAddUser();
            });
        }

        // Edit User Form
        const editUserForm = document.getElementById('editUserForm');
        if (editUserForm) {
            editUserForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitEditUser();
            });
        }

        // Add Booking Form
        const addBookingForm = document.getElementById('addBookingForm');
        if (addBookingForm) {
            addBookingForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitAddBooking();
            });
        }

        // Add Guide Form
        const addGuideForm = document.getElementById('addGuideForm');
        if (addGuideForm) {
            addGuideForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitAddGuide();
            });
        }

        // Edit Guide Form
        const editGuideForm = document.getElementById('editGuideForm');
        if (editGuideForm) {
            editGuideForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitEditGuide();
            });
        }

        // Add Spot Form
        const addSpotForm = document.getElementById('addSpotForm');
        if (addSpotForm) {
            addSpotForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitAddSpot();
            });
        }

        // Edit Spot Form
        const editSpotForm = document.getElementById('editSpotForm');
        if (editSpotForm) {
            editSpotForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitEditSpot();
            });
        }

        // Add Hotel Form
        const addHotelForm = document.getElementById('addHotelForm');
        if (addHotelForm) {
            addHotelForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitAddHotel();
            });
        }

        // Edit Hotel Form
        const editHotelForm = document.getElementById('editHotelForm');
        if (editHotelForm) {
            editHotelForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitEditHotel();
            });
        }
    }

    async submitAddUser() {
        const form = document.getElementById('addUserForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('?action=add_user', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('User added successfully', 'success');
                this.closeModal('addUserModal');
                form.reset();
                location.reload();
            } else {
                this.showToast(result.message || 'Error adding user', 'error');
            }
        } catch (error) {
            this.showToast('Error adding user', 'error');
        }
    }

    async submitEditUser() {
        const form = document.getElementById('editUserForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('?action=edit_user', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('User updated successfully', 'success');
                this.closeModal('editUserModal');
                location.reload();
            } else {
                this.showToast(result.message || 'Error updating user', 'error');
            }
        } catch (error) {
            this.showToast('Error updating user', 'error');
        }
    }

    async submitAddBooking() {
        const form = document.getElementById('addBookingForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('?action=add_booking', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Booking added successfully', 'success');
                this.closeModal('addBookingModal');
                form.reset();
                location.reload();
            } else {
                this.showToast(result.message || 'Error adding booking', 'error');
            }
        } catch (error) {
            this.showToast('Error adding booking', 'error');
        }
    }

    async bulkUpdateStatus(status) {
        if (this.selectedUsers.size === 0) return;
        
        if (!confirm(`Are you sure you want to ${status} ${this.selectedUsers.size} user(s)?`)) {
            return;
        }
        
        try {
            const response = await fetch('?action=bulk_update_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_ids: Array.from(this.selectedUsers),
                    status: status
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast(`${this.selectedUsers.size} user(s) updated to ${status}`, 'success');
                this.selectedUsers.clear();
                location.reload();
            } else {
                this.showToast(result.message || 'Error updating users', 'error');
            }
        } catch (error) {
            this.showToast('Error updating users', 'error');
        }
    }

    async bulkDeleteUsers() {
        if (this.selectedUsers.size === 0) return;
        
        if (!confirm(`Are you sure you want to delete ${this.selectedUsers.size} user(s)? This action cannot be undone.`)) {
            return;
        }
        
        try {
            const response = await fetch('?action=bulk_delete_users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_ids: Array.from(this.selectedUsers)
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast(`${this.selectedUsers.size} user(s) deleted successfully`, 'success');
                this.selectedUsers.clear();
                location.reload();
            } else {
                this.showToast(result.message || 'Error deleting users', 'error');
            }
        } catch (error) {
            this.showToast('Error deleting users', 'error');
        }
    }

    async viewUser(userId) {
        try {
            const response = await fetch(`?action=get_user&user_id=${userId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showUserDetailsModal(result.data);
            } else {
                this.showToast(result.message || 'Error loading user details', 'error');
            }
        } catch (error) {
            this.showToast('Error loading user details', 'error');
        }
    }

    async editUserModal(userId) {
        try {
            const response = await fetch(`?action=get_user&user_id=${userId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showModal('editUserModal', result.data);
            } else {
                this.showToast(result.message || 'Error loading user data', 'error');
            }
        } catch (error) {
            this.showToast('Error loading user data', 'error');
        }
    }

    async deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            return;
        }
        
        try {
            const response = await fetch(`?action=delete_user&user_id=${userId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showToast('User deleted successfully', 'success');
                location.reload();
            } else {
                this.showToast(result.message || 'Error deleting user', 'error');
            }
        } catch (error) {
            this.showToast('Error deleting user', 'error');
        }
    }

    async viewBooking(bookingId) {
        try {
            const response = await fetch(`?action=get_booking&booking_id=${bookingId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showBookingDetailsModal(result.data);
            } else {
                this.showToast(result.message || 'Error loading booking details', 'error');
            }
        } catch (error) {
            this.showToast('Error loading booking details', 'error');
        }
    }

    async updateBookingStatus(bookingId) {
        const newStatus = prompt('Enter new status (pending, confirmed, cancelled, completed):');
        if (newStatus && ['pending', 'confirmed', 'cancelled', 'completed'].includes(newStatus)) {
            try {
                const response = await fetch('?action=update_booking_status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `booking_id=${bookingId}&status=${newStatus}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showToast('Booking status updated successfully', 'success');
                    location.reload();
                } else {
                    this.showToast(result.message || 'Error updating booking status', 'error');
                }
            } catch (error) {
                this.showToast('Error updating booking status', 'error');
            }
        }
    }

    async deleteBooking(bookingId) {
        if (!confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
            return;
        }
        
        try {
            const response = await fetch(`?action=delete_booking&booking_id=${bookingId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Booking deleted successfully', 'success');
                location.reload();
            } else {
                this.showToast(result.message || 'Error deleting booking', 'error');
            }
        } catch (error) {
            this.showToast('Error deleting booking', 'error');
        }
    }

    // Tourist Spots Functions
    async viewSpot(spotId) {
        try {
            const response = await fetch(`?action=get_spot&spot_id=${spotId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showSpotDetailsModal(result.data);
            } else {
                this.showToast(result.message || 'Error loading spot details', 'error');
            }
        } catch (error) {
            this.showToast('Error loading spot details', 'error');
        }
    }

    async editSpot(spotId) {
        try {
            const response = await fetch(`?action=get_spot&spot_id=${spotId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showModal('editSpotModal', result.data);
            } else {
                this.showToast(result.message || 'Error loading spot data', 'error');
            }
        } catch (error) {
            this.showToast('Error loading spot data', 'error');
        }
    }

    async deleteSpot(spotId) {
        if (!confirm('Are you sure you want to delete this tourist spot? This action cannot be undone.')) {
            return;
        }
        
        try {
            const response = await fetch(`?action=delete_spot&spot_id=${spotId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Tourist spot deleted successfully', 'success');
                location.reload();
            } else {
                this.showToast(result.message || 'Error deleting tourist spot', 'error');
            }
        } catch (error) {
            this.showToast('Error deleting tourist spot', 'error');
        }
    }

    async submitAddSpot() {
        const form = document.getElementById('addSpotForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('?action=add_spot', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Tourist spot added successfully', 'success');
                this.closeModal('addSpotModal');
                form.reset();
                location.reload();
            } else {
                this.showToast(result.message || 'Error adding tourist spot', 'error');
            }
        } catch (error) {
            this.showToast('Error adding tourist spot', 'error');
        }
    }

    async submitEditSpot() {
        const form = document.getElementById('editSpotForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('?action=edit_spot', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Tourist spot updated successfully', 'success');
                this.closeModal('editSpotModal');
                location.reload();
            } else {
                this.showToast(result.message || 'Error updating tourist spot', 'error');
            }
        } catch (error) {
            this.showToast('Error updating tourist spot', 'error');
        }
    }

    // Tour Guides Functions
    async viewGuide(guideId) {
        try {
            const response = await fetch(`?action=get_guide&guide_id=${guideId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showGuideDetailsModal(result.data);
            } else {
                this.showToast(result.message || 'Error loading guide details', 'error');
            }
        } catch (error) {
            this.showToast('Error loading guide details', 'error');
        }
    }

    async editGuide(guideId) {
        try {
            const response = await fetch(`?action=get_guide&guide_id=${guideId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showModal('editGuideModal', result.data);
            } else {
                this.showToast(result.message || 'Error loading guide data', 'error');
            }
        } catch (error) {
            this.showToast('Error loading guide data', 'error');
        }
    }

    async deleteGuide(guideId) {
        if (!confirm('Are you sure you want to delete this tour guide? This action cannot be undone.')) {
            return;
        }
        
        try {
            const response = await fetch(`?action=delete_guide&guide_id=${guideId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Tour guide deleted successfully', 'success');
                location.reload();
            } else {
                this.showToast(result.message || 'Error deleting tour guide', 'error');
            }
        } catch (error) {
            this.showToast('Error deleting tour guide', 'error');
        }
    }

    async submitAddGuide() {
        const form = document.getElementById('addGuideForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('?action=add_guide', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Tour guide added successfully', 'success');
                this.closeModal('addGuideModal');
                form.reset();
                location.reload();
            } else {
                this.showToast(result.message || 'Error adding tour guide', 'error');
            }
        } catch (error) {
            this.showToast('Error adding tour guide', 'error');
        }
    }

    async submitEditGuide() {
        const form = document.getElementById('editGuideForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('?action=edit_guide', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Tour guide updated successfully', 'success');
                this.closeModal('editGuideModal');
                location.reload();
            } else {
                this.showToast(result.message || 'Error updating tour guide', 'error');
            }
        } catch (error) {
            this.showToast('Error updating tour guide', 'error');
        }
    }

    // Hotels Functions
    async viewHotel(hotelId) {
        try {
            const response = await fetch(`?action=get_hotel&hotel_id=${hotelId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showHotelDetailsModal(result.data);
            } else {
                this.showToast(result.message || 'Error loading hotel details', 'error');
            }
        } catch (error) {
            this.showToast('Error loading hotel details', 'error');
        }
    }

    async editHotel(hotelId) {
        try {
            const response = await fetch(`?action=get_hotel&hotel_id=${hotelId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showModal('editHotelModal', result.data);
            } else {
                this.showToast(result.message || 'Error loading hotel data', 'error');
            }
        } catch (error) {
            this.showToast('Error loading hotel data', 'error');
        }
    }

    async deleteHotel(hotelId) {
        if (!confirm('Are you sure you want to delete this hotel? This action cannot be undone.')) {
            return;
        }
        
        try {
            const response = await fetch(`?action=delete_hotel&hotel_id=${hotelId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Hotel deleted successfully', 'success');
                location.reload();
            } else {
                this.showToast(result.message || 'Error deleting hotel', 'error');
            }
        } catch (error) {
            this.showToast('Error deleting hotel', 'error');
        }
    }

    async submitAddHotel() {
        const form = document.getElementById('addHotelForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('?action=add_hotel', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Hotel added successfully', 'success');
                this.closeModal('addHotelModal');
                form.reset();
                location.reload();
            } else {
                this.showToast(result.message || 'Error adding hotel', 'error');
            }
        } catch (error) {
            this.showToast('Error adding hotel', 'error');
        }
    }

    async submitEditHotel() {
        const form = document.getElementById('editHotelForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('?action=edit_hotel', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Hotel updated successfully', 'success');
                this.closeModal('editHotelModal');
                location.reload();
            } else {
                this.showToast(result.message || 'Error updating hotel', 'error');
            }
        } catch (error) {
            this.showToast('Error updating hotel', 'error');
        }
    }

    showContextMenu(event, userId) {
        event.preventDefault();
        event.stopPropagation();
        
        // Remove any existing context menu
        const existingMenu = document.querySelector('.context-menu');
        if (existingMenu) existingMenu.remove();
        
        // Create new context menu
        const menu = document.createElement('div');
        menu.className = 'context-menu';
        menu.style.top = `${event.clientY}px`;
        menu.style.left = `${event.clientX}px`;
        
        menu.innerHTML = `
            <div class="context-item" onclick="admin.sendEmail(${userId})">
                <span class="material-icons-outlined">email</span>
                Send Email
            </div>
            <div class="context-item" onclick="admin.resetPassword(${userId})">
                <span class="material-icons-outlined">lock_reset</span>
                Reset Password
            </div>
            <div class="context-divider"></div>
            <div class="context-item" onclick="admin.viewActivity(${userId})">
                <span class="material-icons-outlined">history</span>
                View Activity
            </div>
        `;
        
        document.body.appendChild(menu);
        menu.classList.add('show');
        
        // Close menu on click outside
        const closeMenu = () => {
            menu.remove();
            document.removeEventListener('click', closeMenu);
        };
        
        setTimeout(() => {
            document.addEventListener('click', closeMenu);
        }, 100);
    }

    async sendEmail(userId) {
        const subject = prompt('Enter email subject:');
        if (!subject) return;
        
        const message = prompt('Enter email message:');
        if (!message) return;
        
        this.showToast('Email functionality requires backend setup', 'info');
    }

    async resetPassword(userId) {
        const newPassword = prompt('Enter new password (leave blank to generate random):');
        
        try {
            const response = await fetch('?action=reset_password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId,
                    new_password: newPassword
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast('Password reset successfully', 'success');
                if (result.data.password) {
                    alert(`New password: ${result.data.password}`);
                }
            } else {
                this.showToast(result.message || 'Error resetting password', 'error');
            }
        } catch (error) {
            this.showToast('Error resetting password', 'error');
        }
    }

    async viewActivity(userId) {
        try {
            const response = await fetch(`?action=get_user&user_id=${userId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showUserActivityModal(result.data);
            } else {
                this.showToast(result.message || 'Error loading activity', 'error');
            }
        } catch (error) {
            this.showToast('Error loading activity', 'error');
        }
    }

    showUserDetailsModal(userData) {
        const modal = document.createElement('div');
        modal.id = 'userDetailsModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2><span class="material-icons-outlined">person</span> User Details</h2>
                    <button class="modal-close" onclick="admin.closeModal('userDetailsModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="user-profile-image" style="text-align: center; margin-bottom: 20px;">
                        <div class="user-avatar" style="width: 80px; height: 80px; margin: 0 auto;">
                            ${userData.first_name.charAt(0)}${userData.last_name.charAt(0)}
                        </div>
                    </div>
                    <div style="margin-bottom: 25px; background: #f8f9fa; border-radius: 12px; padding: 20px;">
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">ID:</span>
                            <span style="font-weight: 500; color: #212529;">#${userData.id}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Full Name:</span>
                            <span style="font-weight: 500; color: #212529;">${userData.first_name} ${userData.last_name}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Email:</span>
                            <span style="font-weight: 500; color: #212529;">${userData.email}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Phone:</span>
                            <span style="font-weight: 500; color: #212529;">${userData.phone || 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Status:</span>
                            <span class="status-badge ${userData.status}">${userData.status}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Joined Date:</span>
                            <span style="font-weight: 500; color: #212529;">${new Date(userData.created_at).toLocaleDateString()}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0;">
                            <span style="font-weight: 700; color: #495057;">Last Login:</span>
                            <span style="font-weight: 500; color: #212529;">${userData.last_login ? new Date(userData.last_login).toLocaleDateString() : 'Never'}</span>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; justify-content: flex-end; padding-top: 25px; border-top: 2px solid #e9ecef;">
                        <button class="btn-secondary" onclick="admin.closeModal('userDetailsModal')">
                            <span class="material-icons-outlined">close</span> Close
                        </button>
                        <button class="btn-primary" onclick="admin.editUserModal(${userData.id})">
                            <span class="material-icons-outlined">edit</span> Edit User
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.classList.add('show');
    }

    showUserActivityModal(userData) {
        const modal = document.createElement('div');
        modal.id = 'userActivityModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2><span class="material-icons-outlined">history</span> User Activity Log</h2>
                    <button class="modal-close" onclick="admin.closeModal('userActivityModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h4 style="margin: 0 0 10px 0;">${userData.first_name} ${userData.last_name}</h4>
                        <p style="margin: 0; color: #6c757d;">${userData.email}</p>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${userData.activity && userData.activity.length > 0 ? 
                                    userData.activity.map(activity => `
                                        <tr>
                                            <td>${new Date(activity.login_time).toLocaleString()}</td>
                                            <td>${activity.ip_address || 'N/A'}</td>
                                            <td>
                                                <span class="status-badge ${activity.status}">
                                                    ${activity.status}
                                                </span>
                                            </td>
                                        </tr>
                                    `).join('') : 
                                    `<tr><td colspan="3" style="text-align: center; padding: 40px;">No activity found</td></tr>`
                                }
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.classList.add('show');
    }

    showBookingDetailsModal(bookingData) {
        const modal = document.createElement('div');
        modal.id = 'bookingDetailsModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2><span class="material-icons-outlined">event</span> Booking Details</h2>
                    <button class="modal-close" onclick="admin.closeModal('bookingDetailsModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 25px; background: #f8f9fa; border-radius: 12px; padding: 20px;">
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Booking ID:</span>
                            <span style="font-weight: 500; color: #212529;">#${bookingData.id}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">User:</span>
                            <span style="font-weight: 500; color: #212529;">${bookingData.user_name}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Tour Name:</span>
                            <span style="font-weight: 500; color: #212529;">${bookingData.tour_name}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Booking Date:</span>
                            <span style="font-weight: 500; color: #212529;">${new Date(bookingData.booking_date).toLocaleDateString()}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Number of People:</span>
                            <span style="font-weight: 500; color: #212529;">${bookingData.number_of_people}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Total Amount:</span>
                            <span style="font-weight: 500; color: #212529;">₱${parseFloat(bookingData.total_amount).toFixed(2)}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0;">
                            <span style="font-weight: 700; color: #495057;">Status:</span>
                            <span class="status-badge ${bookingData.status}">${bookingData.status}</span>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; justify-content: flex-end; padding-top: 25px; border-top: 2px solid #e9ecef;">
                        <button class="btn-secondary" onclick="admin.closeModal('bookingDetailsModal')">
                            <span class="material-icons-outlined">close</span> Close
                        </button>
                        <button class="btn-primary" onclick="admin.updateBookingStatus(${bookingData.id})">
                            <span class="material-icons-outlined">edit</span> Update Status
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.classList.add('show');
    }

    showGuideDetailsModal(guideData) {
        const modal = document.createElement('div');
        modal.id = 'guideDetailsModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2><span class="material-icons-outlined">tour</span> Tour Guide Details</h2>
                    <button class="modal-close" onclick="admin.closeModal('guideDetailsModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="user-profile-image" style="text-align: center; margin-bottom: 20px;">
                        <div class="user-avatar" style="width: 80px; height: 80px; margin: 0 auto; background-image: url('${guideData.photo_url}'); background-size: cover; background-position: center;">
                            ${guideData.photo_url ? '' : guideData.name.charAt(0)}
                        </div>
                    </div>
                    <div style="margin-bottom: 25px; background: #f8f9fa; border-radius: 12px; padding: 20px;">
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">ID:</span>
                            <span style="font-weight: 500; color: #212529;">#${guideData.id}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Name:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.name}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Email:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.email}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Contact:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.contact_number || 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Specialty:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.specialty}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Category:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.category}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Experience:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.experience_years} years</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Rating:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.rating} (${guideData.review_count} reviews)</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Price Range:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.price_range}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Languages:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.languages}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Group Size:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.group_size} people</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Total Tours:</span>
                            <span style="font-weight: 500; color: #212529;">${guideData.total_tours}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0;">
                            <span style="font-weight: 700; color: #495057;">Status:</span>
                            <span class="status-badge ${guideData.verified ? 'success' : 'warning'}">
                                ${guideData.verified ? 'Verified' : 'Pending'}
                            </span>
                        </div>
                    </div>
                    ${guideData.bio ? `
                    <div style="margin-bottom: 20px;">
                        <h4 style="margin-bottom: 10px;">Bio</h4>
                        <p style="color: #6c757d; line-height: 1.6;">${guideData.bio}</p>
                    </div>
                    ` : ''}
                    ${guideData.areas_of_expertise ? `
                    <div style="margin-bottom: 20px;">
                        <h4 style="margin-bottom: 10px;">Areas of Expertise</h4>
                        <p style="color: #6c757d; line-height: 1.6;">${guideData.areas_of_expertise}</p>
                    </div>
                    ` : ''}
                    <div style="display: flex; gap: 15px; justify-content: flex-end; padding-top: 25px; border-top: 2px solid #e9ecef;">
                        <button class="btn-secondary" onclick="admin.closeModal('guideDetailsModal')">
                            <span class="material-icons-outlined">close</span> Close
                        </button>
                        <button class="btn-primary" onclick="admin.editGuide(${guideData.id})">
                            <span class="material-icons-outlined">edit</span> Edit Guide
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.classList.add('show');
    }

    showToast(message, type = 'info') {
        // Remove existing toasts
        document.querySelectorAll('.toast').forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <span class="material-icons-outlined">
                ${type === 'success' ? 'check_circle' : 
                  type === 'error' ? 'error' : 
                  type === 'warning' ? 'warning' : 'info'}
            </span>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    exportToCSV() {
        const table = document.querySelector('#usersTable');
        const rows = table.querySelectorAll('tr');
        const csv = [];
        
        rows.forEach(row => {
            const rowData = [];
            const cells = row.querySelectorAll('th, td');
            cells.forEach(cell => {
                // Skip action buttons and checkboxes
                if (!cell.closest('.action-buttons') && !cell.querySelector('input[type="checkbox"]')) {
                    rowData.push(`"${cell.textContent.trim()}"`);
                }
            });
            csv.push(rowData.join(','));
        });
        
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `users_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        this.showToast('CSV exported successfully', 'success');
    }

    exportActivity() {
        this.showToast('Export feature requires backend implementation', 'info');
    }

    loadDashboardData() {
        // You can implement AJAX loading for dashboard data here
        // Example: Fetch real-time data from server
        // fetch('?ajax=1&section=dashboard').then(...)
    }

    loadSectionData(section) {
        // You can implement AJAX loading for section data here
        // Example: Fetch data for specific section
        // fetch(`?ajax=1&section=${section}`).then(...)
    }
}

// Initialize admin dashboard
const admin = new AdminDashboard();

// Make admin available globally
window.admin = admin;

// Helper functions for inline onclick handlers
function viewUser(userId) {
    admin.viewUser(userId);
}

function editUserModal(userId) {
    admin.editUserModal(userId);
}

function deleteUser(userId) {
    admin.deleteUser(userId);
}

function showContextMenu(event, userId) {
    admin.showContextMenu(event, userId);
}

function viewBooking(bookingId) {
    admin.viewBooking(bookingId);
}

function updateBookingStatus(bookingId) {
    admin.updateBookingStatus(bookingId);
}

function deleteBooking(bookingId) {
    admin.deleteBooking(bookingId);
}

function viewGuide(guideId) {
    admin.viewGuide(guideId);
}

function editGuide(guideId) {
    admin.editGuide(guideId);
}

function deleteGuide(guideId) {
    admin.deleteGuide(guideId);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Set default section from URL hash
    const hash = window.location.hash.substring(1);
    if (hash) {
        admin.switchToSection(hash);
    }
    
    // Initialize tooltips
    document.querySelectorAll('[title]').forEach(element => {
        element.setAttribute('data-tooltip', element.getAttribute('title'));
    });
});