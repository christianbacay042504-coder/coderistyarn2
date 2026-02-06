// Fix Profile Dropdown Toggle
// This script will fix the dropdown toggle issue

// Remove all existing event listeners to prevent conflicts
function fixProfileDropdown() {
    console.log('🔧 Fixing profile dropdown...');
    
    const profileButton = document.getElementById('userProfileButton');
    const profileMenu = document.getElementById('userProfileMenu');
    
    if (!profileButton || !profileMenu) {
        console.log('❌ Profile elements not found');
        return;
    }
    
    console.log('✅ Profile elements found');
    
    // Remove existing event listeners by cloning and replacing
    const newProfileButton = profileButton.cloneNode(true);
    profileButton.parentNode.replaceChild(newProfileButton, profileButton);
    
    // Add fresh event listener for toggle
    newProfileButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('🔘 Profile button clicked!');
        
        // Toggle dropdown
        const isActive = profileMenu.classList.contains('active');
        console.log('Current state:', isActive);
        
        if (isActive) {
            profileMenu.classList.remove('active');
            console.log('🔽 Dropdown closed');
        } else {
            profileMenu.classList.add('active');
            console.log('🔼 Dropdown opened');
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!newProfileButton.contains(e.target) && !profileMenu.contains(e.target)) {
            profileMenu.classList.remove('active');
            console.log('🔽 Dropdown closed (outside click)');
        }
    });
    
    // Bind dropdown items to existing modal functions
    const dropdownLinks = {
        'userAccountLink': function() {
            if (typeof showUserAccountModal === 'function') {
                showUserAccountModal();
                profileMenu.classList.remove('active');
                console.log('✅ My Account clicked');
            } else {
                console.log('❌ showUserAccountModal function not found');
            }
        },
        'userSettingsLink': function() {
            if (typeof showUserSettingsModal === 'function') {
                showUserSettingsModal();
                profileMenu.classList.remove('active');
                console.log('✅ Settings clicked');
            } else {
                console.log('❌ showUserSettingsModal function not found');
            }
        },
        'userBookingHistoryLink': function() {
            if (typeof showUserBookingHistoryModal === 'function') {
                showUserBookingHistoryModal();
                profileMenu.classList.remove('active');
                console.log('✅ Booking History clicked');
            } else {
                console.log('❌ showUserBookingHistoryModal function not found');
            }
        },
        'userSavedToursLink': function() {
            if (typeof showUserSavedToursModal === 'function') {
                showUserSavedToursModal();
                profileMenu.classList.remove('active');
                console.log('✅ Saved Tours clicked');
            } else {
                console.log('❌ showUserSavedToursModal function not found');
            }
        },
        'userHelpLink': function() {
            if (typeof showUserHelpModal === 'function') {
                showUserHelpModal();
                profileMenu.classList.remove('active');
                console.log('✅ Help clicked');
            } else {
                console.log('❌ showUserHelpModal function not found');
            }
        }
    };
    
    // Bind events to dropdown items
    for (const [id, func] of Object.entries(dropdownLinks)) {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                func();
            });
            console.log(`✅ Bound ${id} to function`);
        } else {
            console.log(`❌ Element ${id} not found`);
        }
    }
    
    console.log('✅ Profile dropdown fixed!');
}

// Wait a bit for main script to load, then fix
setTimeout(() => {
    fixProfileDropdown();
}, 500);
