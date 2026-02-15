/**
 * Admin Profile Dropdown Functionality
 * Handles the dropdown menu and its associated modals for admin panel.
 */

function initAdminProfileDropdown() {
    console.log('initAdminProfileDropdown: Initializing...');
    const profileButton = document.getElementById('adminProfileButton');
    const profileMenu = document.getElementById('adminProfileMenu');

    console.log('initAdminProfileDropdown: Elements found:', { profileButton, profileMenu });

    if (profileButton && profileMenu) {
        profileButton.addEventListener('click', function (e) {
            console.log('Admin profile button clicked');
            e.preventDefault();
            e.stopPropagation();
            profileMenu.classList.toggle('active');
            console.log('Admin profile menu active state:', profileMenu.classList.contains('active'));
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!profileButton.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('active');
            }
        });
    }

    // Bind dropdown item links
    const links = {
        'adminAccountLink': showAdminAccountModal,
        'adminSettingsLink': showAdminSettingsModal,
        'adminHelpLink': showAdminHelpModal
    };

    for (const [id, func] of Object.entries(links)) {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                profileMenu.classList.remove('active');
                func();
            });
        }
    }
}

// Modal Helper Function
function createAdminModal(id, title, content, icon = 'info') {
    console.log('Creating modal:', id, title);
    
    // Close ALL existing modals first to prevent duplicates
    const existingModals = document.querySelectorAll('.modal-overlay, .modal.show');
    existingModals.forEach(modal => {
        console.log('Closing existing modal:', modal);
        modal.remove();
    });
    
    // Remove existing modal with same ID if any
    const existing = document.getElementById(id);
    if (existing) {
        console.log('Removing existing modal with same ID:', existing);
        existing.remove();
    }

    const modal = document.createElement('div');
    modal.id = id;
    modal.className = 'modal-overlay show';
    
    // Add inline styles as fallback to ensure centering
    modal.style.cssText = `
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
        backdrop-filter: blur(4px) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 999999 !important;
        opacity: 1 !important;
        visibility: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
    `;
    
    console.log('Modal element created:', modal);
    console.log('Modal classes:', modal.className);
    console.log('Modal inline styles:', modal.style.cssText);
    
    modal.innerHTML = `
        <div class="modal-container">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="material-icons-outlined" style="color: var(--primary);">${icon}</span>
                    <h2 style="margin: 0; color: var(--text-primary);">${title}</h2>
                </div>
                <button class="btn-action" onclick="this.closest('.modal-overlay').remove()" style="background: none; border: none; padding: 8px; cursor: pointer;">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body" id="${id}-body">
                ${content}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    console.log('Modal appended to body');
    
    // Force a reflow to ensure styles are applied
    modal.offsetHeight;
    console.log('Modal styles applied, modal visible:', modal);

    // Close modal on backdrop click
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.remove();
        }
    });

    return modal;
}

function showAdminAccountModal() {
    // Get admin user data from the page or use defaults
    const adminName = document.querySelector('.admin-name')?.textContent || 'Administrator';
    const adminEmail = document.querySelector('.admin-email')?.textContent || 'admin@sjdmtours.com';
    const adminRole = document.querySelector('.admin-role')?.textContent || 'System Administrator';
    
    const nameParts = adminName.split(' ');
    const firstName = nameParts[0] || '';
    const lastName = nameParts.slice(1).join(' ') || '';

    const content = `
        <div class="profile-view-mode">
            <div style="text-align: center; margin-bottom: 24px;">
                <div class="profile-avatar large" style="margin: 0 auto 16px; width: 60px; height: 60px; font-size: 24px;">
                    ${firstName.charAt(0)}
                </div>
                <h3 style="margin: 0 0 8px 0; color: var(--text-primary);">${adminName}</h3>
                <p style="margin: 0 0 4px 0; color: var(--primary); font-weight: 600;">${adminRole}</p>
                <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">System Administrator</p>
            </div>
            
            <div style="display: grid; gap: 16px;">
                <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <span style="color: var(--text-secondary);">Full Name</span>
                    <span style="font-weight: 600;">${adminName}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <span style="color: var(--text-secondary);">Email Address</span>
                    <span style="font-weight: 600;">${adminEmail}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <span style="color: var(--text-secondary);">Role</span>
                    <span style="font-weight: 600;">${adminRole}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                    <span style="color: var(--text-secondary);">Department</span>
                    <span style="font-weight: 600;">System Administration</span>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn-secondary" onclick="document.getElementById('adminAccountModal').remove()">Close</button>
                <button type="button" class="btn-primary" onclick="showAdminEditProfileForm()">
                    <span class="material-icons-outlined" style="font-size: 18px;">edit</span> Edit Profile
                </button>
            </div>
        </div>
    `;
    createAdminModal('adminAccountModal', 'Admin Account', content, 'admin_panel_settings');
}

function showAdminEditProfileForm() {
    const container = document.getElementById('adminAccountModal-body');
    if (!container) return;

    const adminName = document.querySelector('.admin-name')?.textContent || 'Administrator';
    const adminEmail = document.querySelector('.admin-email')?.textContent || 'admin@sjdmtours.com';
    
    const nameParts = adminName.split(' ');
    const firstName = nameParts[0] || '';
    const lastName = nameParts.slice(1).join('') || '';

    container.innerHTML = `
        <form id="adminProfileForm" onsubmit="event.preventDefault(); saveAdminProfile();">
            <div style="display: grid; gap: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">First Name</label>
                    <input type="text" id="adminFirstName" value="${firstName}" required 
                           style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Last Name</label>
                    <input type="text" id="adminLastName" value="${lastName}" required 
                           style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Email Address</label>
                    <input type="email" id="adminEmail" value="${adminEmail}" required 
                           style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
                </div>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn-secondary" onclick="showAdminAccountModal()">Cancel</button>
                <button type="submit" class="btn-primary">
                    <span class="material-icons-outlined" style="font-size: 18px;">save</span> Save Changes
                </button>
            </div>
        </form>
    `;
}

function saveAdminProfile() {
    const fname = document.getElementById('adminFirstName').value.trim();
    const lname = document.getElementById('adminLastName').value.trim();
    const email = document.getElementById('adminEmail').value.trim();

    // Here you would typically send this data to the server
    // For now, we'll just show a success message
    showAdminNotification('Admin profile updated successfully!', 'success');

    // Update the display if elements exist
    const nameElement = document.querySelector('.admin-name');
    const emailElement = document.querySelector('.admin-email');
    
    if (nameElement) {
        nameElement.textContent = `${fname} ${lname}`.trim();
    }
    if (emailElement) {
        emailElement.textContent = email;
    }

    document.getElementById('adminAccountModal').remove();
}


function showAdminSettingsModal() {
    const content = `
        <div style="display: grid; gap: 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--bg-light); border-radius: 8px;">
                <div>
                    <strong style="display: block; margin-bottom: 4px;">Email Notifications</strong>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">Receive system alerts and updates</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="adminEmailToggle" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--bg-light); border-radius: 8px;">
                <div>
                    <strong style="display: block; margin-bottom: 4px;">Two-Factor Authentication</strong>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">Extra security for admin access</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="admin2FAToggle">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--bg-light); border-radius: 8px;">
                <div>
                    <strong style="display: block; margin-bottom: 4px;">Activity Logging</strong>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">Track all admin activities</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="adminLoggingToggle" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div style="display: flex; gap: 12px;">
                <button class="btn-primary" onclick="saveAdminSettings()">
                    <span class="material-icons-outlined" style="font-size: 18px;">check</span> Apply Changes
                </button>
            </div>
        </div>
    `;
    createAdminModal('adminSettingsModal', 'Admin Settings', content, 'settings');
}

function saveAdminSettings() {
    const emailNotifications = document.getElementById('adminEmailToggle').checked;
    const twoFactor = document.getElementById('admin2FAToggle').checked;
    const activityLogging = document.getElementById('adminLoggingToggle').checked;

    // Here you would typically send these settings to the server
    showAdminNotification('Admin settings updated successfully!', 'success');
    document.getElementById('adminSettingsModal').remove();
}

function showAdminHelpModal() {
    const content = `
        <div style="display: grid; gap: 16px;">
            <div style="display: flex; align-items: center; padding: 16px; background: var(--bg-light); border-radius: 8px;">
                <span class="material-icons-outlined" style="color: var(--primary); margin-right: 16px; font-size: 24px;">email</span>
                <div>
                    <strong style="display: block; margin-bottom: 4px;">Admin Support</strong>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">admin-support@sjdmtours.com</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; padding: 16px; background: var(--bg-light); border-radius: 8px;">
                <span class="material-icons-outlined" style="color: var(--primary); margin-right: 16px; font-size: 24px;">call</span>
                <div>
                    <strong style="display: block; margin-bottom: 4px;">Emergency Hotline</strong>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">+63 912 345 6789</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; padding: 16px; background: var(--bg-light); border-radius: 8px;">
                <span class="material-icons-outlined" style="color: var(--primary); margin-right: 16px; font-size: 24px;">help</span>
                <div>
                    <strong style="display: block; margin-bottom: 4px;">Admin Documentation</strong>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">Access comprehensive admin guides</p>
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <button class="btn-primary" onclick="window.open('#', '_blank')">
                    <span class="material-icons-outlined" style="font-size: 18px;">description</span> View Documentation
                </button>
            </div>
        </div>
    `;
    createAdminModal('adminHelpModal', 'Admin Help & Support', content, 'help_outline');
}

function showAdminNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 20px;
        background: ${type === 'success' ? '#dcfce7' : '#dbeafe'};
        color: ${type === 'success' ? '#166534' : '#1e40af'};
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 3000;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    `;
    
    notification.innerHTML = `
        <span class="material-icons-outlined" style="font-size: 20px;">
            ${type === 'success' ? 'check_circle' : 'info'}
        </span>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Add toggle switch styles if not already present
const toggleStyles = `
<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: var(--primary);
}

input:checked + .toggle-slider:before {
    transform: translateX(26px);
}
</style>
`;

// Inject toggle styles
if (!document.querySelector('#admin-toggle-styles')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'admin-toggle-styles';
    styleElement.innerHTML = toggleStyles;
    document.head.appendChild(styleElement.firstElementChild);
}

// Initialize when library loads or DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminProfileDropdown);
} else {
    initAdminProfileDropdown();
}
